import React from "react";
import { Link } from "@inertiajs/react";
import { StarIcon, EyeIcon } from "@heroicons/react/20/solid";

export default function NovelCard({ novel }) {
    // Helper untuk memformat angka ribuan (misal: 60500 -> 60.5K)
    const formatNumber = (num) => {
        if (num >= 1000) return (num / 1000).toFixed(1) + "K";
        return num;
    };

    return (
        <Link
            href={`/novel/${novel.slug}`}
            className="group flex flex-col w-full"
        >
            {/* Pembungkus Cover dengan rasio aspek buku (2:3) */}
            <div className="relative w-full aspect-[2/3] rounded-lg overflow-hidden bg-gray-200 shadow-sm transition-transform duration-300 group-hover:-translate-y-1 group-hover:shadow-md">
                <img
                    src={
                        novel.cover_image
                            ? `/storage/${novel.cover_image}`
                            : "/images/default-cover.jpg"
                    }
                    alt={`Cover ${novel.title}`}
                    loading="lazy" // ATURAN #3: WAJIB LAZY LOAD
                    className="w-full h-full object-cover transition-opacity duration-300"
                />
                {/* Efek gradient bayangan saat di-hover */}
                <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            </div>

            {/* Area Teks & Meta Data */}
            <div className="mt-3 flex flex-col flex-1">
                <h3 className="text-sm md:text-base font-bold text-gray-900 leading-tight line-clamp-2 font-sans group-hover:text-primary-600 transition-colors">
                    {novel.title}
                </h3>

                <p className="text-xs text-gray-500 mt-1 truncate">
                    {/* Asumsi relasi genre array, kita ambil yang pertama. Jika tidak ada, pakai nama Penulis */}
                    {novel.genres && novel.genres.length > 0
                        ? novel.genres[0].name
                        : "Fantasi"}
                    <span className="mx-1">|</span>
                    {novel.author?.pen_name || novel.author?.name}
                </p>

                {/* Rating & Views */}
                <div className="flex items-center gap-3 mt-1.5 text-xs text-gray-600 font-medium">
                    <span className="flex items-center gap-1">
                        <StarIcon className="w-3.5 h-3.5 text-gray-400" />
                        {novel.rating || "0.0"}
                    </span>
                    <span className="flex items-center gap-1">
                        <EyeIcon className="w-3.5 h-3.5 text-gray-400" />
                        {formatNumber(novel.views_count || 0)}
                    </span>
                </div>
            </div>
        </Link>
    );
}
