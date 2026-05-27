<?php

namespace App\Http\Controllers;

use App\Services\NovelService;
use App\Http\Resources\NovelResource;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        private NovelService $novelService
    ) {}

    public function index()
    {
        return Inertia::render('Home', [
            'banners' => $this->novelService->getActiveBanners(),
            'popularThisWeek' => NovelResource::collection($this->novelService->getPopularThisWeek())->resolve(),
            'latestUpdates' => NovelResource::collection($this->novelService->getLatestUpdates())->resolve(),
            'editorsChoices' => NovelResource::collection($this->novelService->getEditorChoices(6))->resolve(), // Ambil 6 untuk load pertama
            'bestSellers' => NovelResource::collection($this->novelService->getBestSellers())->resolve(),
            'trendingNovels' => NovelResource::collection($this->novelService->getTrendingNovels())->resolve(),
            'hiddenTreasures' => NovelResource::collection($this->novelService->getHiddenTreasures())->resolve(),
            'trendingGenres' => $this->novelService->getTrendingGenres(),
            'genreShelves' => $this->novelService->getGenreShelves(),
        ]);
    }

    // =====================================================================
    // 🌟 API ENDPOINTS
    // =====================================================================

    public function getEditorChoices()
    {
        $choices = $this->novelService->getEditorChoices(10);
        return NovelResource::collection($choices);
    }

    public function loadMoreNovels(Request $request, $slug)
    {
        $page = $request->get('page', 1);
        $books = $this->novelService->loadNovelsByGenreSlug($slug, $page);

        return response()->json($books);
    }
}
