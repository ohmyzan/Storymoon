<?php

namespace App\Filament\Editor\Widgets;

use App\Models\Novel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class EditorStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $editorId = Auth::id();

        // Olah data spesifik untuk Editor ini
        $totalNovels = Novel::where('editor_id', $editorId)->count();
        $totalViews = Novel::where('editor_id', $editorId)->sum('views_count');

        // Menghitung novel binaan yang sukses menembus rating tinggi (misal di atas 4.0)
        $topRatedNovels = Novel::where('editor_id', $editorId)->where('rating', '>=', 4.0)->count();

        return [
            Stat::make('Total Novel Binaan', $totalNovels)
                ->description('Jumlah naskah di bawah supervisi Anda')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Total View Gabungan', number_format($totalViews))
                ->description('Total pembaca dari seluruh novel Anda')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik dummy (sparkline) untuk mempercantik UI

            Stat::make('Novel Bintang 4+', $topRatedNovels)
                ->description('Novel dengan performa sangat baik')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}
