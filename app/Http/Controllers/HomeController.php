<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Novel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // 1. BANNERS
        // Memanfaatkan scopeActive() dan scopeOrdered() dari model Banner
        $banners = Banner::active()->ordered()->get(['title', 'image_path', 'target_url']);

        // 2. POPULER MINGGU INI
        // Memanfaatkan scopePublished() dari model Novel
        $popularThisWeek = Novel::with('author:id,name,pen_name')
            ->published()
            ->orderByDesc('views_count')
            ->take(10)
            ->get(['id', 'title', 'slug', 'cover_image', 'rating', 'views_count', 'author_id']);

        // 3. PILIHAN EDITOR
        // Memanfaatkan scopeFeatured() yang sudah Anda buat dengan brilian di model Novel!
        $editorsChoices = Novel::with('author:id,name,pen_name')
            ->published()
            ->featured()
            ->take(8)
            ->get(['id', 'title', 'slug', 'cover_image', 'rating', 'views_count', 'author_id']);

        // 4. TERLARIS (Sepanjang Masa berdasarkan pembelian bab)
        // Menghitung relasi chapterPurchases yang valid
        $bestSellers = Novel::with('author:id,name,pen_name')
            ->published()
            ->withCount('chapterPurchases')
            ->orderByDesc('chapter_purchases_count')
            ->take(5)
            ->get(['id', 'title', 'slug', 'cover_image', 'author_id']);

        // 5. TRENDING (Naik daun dalam 3 bulan terakhir)
        $trendingNovels = Novel::with('author:id,name,pen_name')
            ->published()
            ->where('created_at', '>=', now()->subMonths(3))
            ->orderByDesc('views_count')
            ->take(5)
            ->get(['id', 'title', 'slug', 'cover_image', 'rating', 'views_count', 'author_id']);

        // 6. MENGGALI HARTA KARUN (Lengkap dengan data User yang me-review)
        // Kita eager load relasi reviews DAN relasi user di dalam review tersebut
        $hiddenTreasures = Novel::with([
            'author:id,name,pen_name',
            'reviews' => function ($query) {
                $query->with('user:id,name') // Ambil nama pembaca yang memberi ulasan
                    ->where('rating', 5)
                    ->latest()
                    ->limit(1);
            }
        ])
            ->published()
            ->where('rating', '>=', 4.5)
            ->where('views_count', '<', 10000)
            ->inRandomOrder()
            ->take(2)
            ->get(['id', 'title', 'slug', 'cover_image', 'author_id']);

        // Kirim payload super ringan ini ke React Inertia
        return Inertia::render('Home', [
            'banners' => $banners,
            'popularThisWeek' => $popularThisWeek,
            'editorsChoices' => $editorsChoices,
            'bestSellers' => $bestSellers,
            'trendingNovels' => $trendingNovels,
            'hiddenTreasures' => $hiddenTreasures,
        ]);
    }
}
