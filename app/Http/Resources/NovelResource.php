<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NovelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Logika aman untuk menentukan nama genre utama
        $mainGenre = null;
        if ($this->relationLoaded('genres') && $this->genres->isNotEmpty()) {
            $parentGenre = $this->genres->firstWhere('parent_id', null);
            $mainGenre = $parentGenre ? $parentGenre->name : $this->genres->first()->name;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : asset('images/default-cover.jpg'),
            'synopsis' => $this->synopsis,
            'rating' => (float) $this->rating,
            'views_count' => (int) $this->views_count,
            'status' => $this->status,
            'publish_status' => $this->publish_status,

            // Sertakan jumlah chapter pembelian jika ada (untuk fitur Terlaris)
            'chapter_purchases_count' => $this->whenCounted('chapterPurchases'),

            // Data Penulis (Lebih aman dari Null Pointer Exception di React)
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'pen_name' => $this->author->pen_name,
                ];
            }),

            // Kembalikan daftar genre utuh (untuk keperluan lain)
            'genres' => $this->whenLoaded('genres', function () {
                return $this->genres->map(function ($genre) {
                    return [
                        'id' => $this->id,
                        'name' => $genre->name,
                        'slug' => $genre->slug,
                        'parent_id' => $genre->parent_id,
                    ];
                });
            }),

            // Kirimkan genre utama (Sangat berguna untuk Label di NovelCard)
            'main_genre_name' => $mainGenre ?? 'Umum',

            // Jika ada Review (Untuk fitur Hidden Treasures)
            'latest_review' => $this->whenLoaded('reviews', function () {
                $review = $this->reviews->first();
                if (!$review) return null;
                return [
                    'content' => $review->content,
                    'user_name' => $review->user ? $review->user->name : 'Pembaca',
                ];
            }),
        ];
    }
}
