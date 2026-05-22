<?php

namespace App\Observers;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerObserver
{
    public function deleted(Banner $banner): void
    {
        // Jika file fisiknya ada di folder public, maka hapus!
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
    }
}
