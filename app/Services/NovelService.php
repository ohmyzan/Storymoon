<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Novel;
use App\Models\Genre;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\NovelResource;

class NovelService
{
  /**
   * Reusable Scope Clause for Contracted Novels
   */
  private function contractedScope(Builder $query): Builder
  {
    return $query->published()->whereHas('activeContract');
  }

  public function getActiveBanners()
  {
    return Cache::remember('home_banners', 3600, fn() => Banner::active()->ordered()->get(['title', 'image_path', 'target_url']));
  }

  public function getPopularThisWeek(int $limit = 10)
  {
    return Cache::remember(
      'home_popular_this_week',
      300,
      fn() =>
      Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
        ->where(fn($q) => $this->contractedScope($q))
        ->orderByDesc('views_count')
        ->take($limit)->get()
    );
  }

  public function getLatestUpdates(int $limit = 5)
  {
    return Cache::remember(
      'home_latest_updates',
      300,
      fn() =>
      Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
        ->where(fn($q) => $this->contractedScope($q))
        ->withMax('chapters', 'created_at')
        ->orderByDesc('chapters_max_created_at')
        ->take($limit)->get()
    );
  }

  public function getEditorChoices(int $limit = 10)
  {
    // Jika limit 10, kemungkinan ini request dari API (Random), simpan lebih singkat
    $cacheTTL = $limit === 10 ? 300 : 3600;

    return Cache::remember(
      "home_editors_choices_{$limit}",
      $cacheTTL,
      fn() =>
      Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
        ->where(fn($q) => $this->contractedScope($q))
        ->featured()
        ->inRandomOrder()
        ->take($limit)->get()
    );
  }

  public function getBestSellers(int $limit = 5)
  {
    return Cache::remember(
      'home_best_sellers',
      300,
      fn() =>
      Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
        ->where(fn($q) => $this->contractedScope($q))
        ->withCount('chapterPurchases')
        ->orderByDesc('chapter_purchases_count')
        ->take($limit)->get()
    );
  }

  public function getTrendingNovels(int $limit = 5)
  {
    return Cache::remember(
      'home_trending_novels',
      300,
      fn() =>
      Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
        ->where(fn($q) => $this->contractedScope($q))
        ->whereHas('activeContract', fn($q) => $q->where('signed_at', '>=', now()->subMonths(3)))
        ->orderByDesc('views_count')
        ->take($limit)->get()
    );
  }

  public function getHiddenTreasures(int $limit = 2)
  {
    return Cache::remember(
      'home_hidden_treasures',
      1800,
      fn() =>
      Novel::with([
        'author:id,name,pen_name',
        'genres:id,name,slug,parent_id',
        'reviews' => fn($q) => $q->with('user:id,name')->where('rating', 5)->latest()->limit(1)
      ])
        ->where(fn($q) => $this->contractedScope($q))
        ->where('rating', '>=', 4.5)
        ->where('views_count', '<', 10000)
        ->inRandomOrder() // TODO: Refactor ini di fase optimasi database jika data sudah jutaan
        ->take($limit)->get()
    );
  }

  public function getTrendingGenres(int $limit = 15)
  {
    return Cache::remember(
      'home_trending_genres',
      3600,
      fn() =>
      Genre::withCount('novels')->orderByDesc('novels_count')->take($limit)->pluck('name')
    );
  }

  public function getGenreShelves()
  {
    $mainGenres = ['fantasi', 'perkotaan', 'misteri', 'horor'];
    $genres = Genre::whereIn('slug', $mainGenres)->get()->keyBy('slug');
    $genreShelves = [];

    foreach ($mainGenres as $slug) {
      $genre = $genres->get($slug);
      if (!$genre) continue;

      $books = Cache::remember("home_shelf_{$slug}", 3600, function () use ($genre) {
        $genreIds = Genre::where('id', $genre->id)->orWhere('parent_id', $genre->id)->pluck('id');
        return Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
          ->where(fn($q) => $this->contractedScope($q))
          ->whereHas('genres', fn($q) => $q->whereIn('genres.id', $genreIds))
          ->orderByDesc('views_count')
          ->take(10)->get();
      });

      if ($books->isNotEmpty()) {
        $genreShelves[] = [
          'genre_name' => $genre->name,
          'slug' => $genre->slug,
          'novels' => NovelResource::collection($books)->resolve(), // Resolve Resource Array
        ];
      }
    }

    return $genreShelves;
  }

  public function loadNovelsByGenreSlug(string $slug, int $page)
  {
    return Cache::remember("api_genre_shelf_{$slug}_page_{$page}", 3600, function () use ($slug) {
      $genre = Genre::where('slug', $slug)->firstOrFail();
      $genreIds = Genre::where('id', $genre->id)->orWhere('parent_id', $genre->id)->pluck('id');

      $books = Novel::with(['author:id,name,pen_name', 'genres:id,name,slug,parent_id'])
        ->where(fn($q) => $this->contractedScope($q))
        ->whereHas('genres', fn($q) => $q->whereIn('genres.id', $genreIds))
        ->orderByDesc('views_count')
        ->simplePaginate(10);

      // Convert paginator items to Resource
      $books->getCollection()->transform(fn($novel) => new NovelResource($novel));
      return $books;
    });
  }
}
