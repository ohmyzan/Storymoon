import React from "react";
import { Link } from "@inertiajs/react";
import { StarIcon, EyeIcon } from "@heroicons/react/20/solid";
import { formatNumber } from "../../Utils/formatters"; // 🌟 FIX: Hapus getMainGenre

export default function NovelCard({ novel, shelfGenre }) {
    // 🌟 FIX: Cukup panggil dari resource backend, sangat clean!
    const displayGenre = shelfGenre || novel.main_genre_name;

    return (
        <Link
            href={`/novel/${novel.slug}`}
            className="group flex flex-col w-full"
        >
            <div className="relative w-full aspect-[2/3] rounded-lg overflow-hidden bg-gray-200 shadow-sm transition-transform duration-300 group-hover:-translate-y-1 group-hover:shadow-md">
                {/* 🌟 FIX: Resource sudah mengirimkan URL gambar valid, tidak perlu ternary '? :' lagi */}
                <img
                    src={novel.cover_image}
                    alt={`Cover ${novel.title}`}
                    loading="lazy"
                    className="w-full h-full object-cover transition-opacity duration-300"
                />
                <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            </div>

            <div className="mt-3 flex flex-col flex-1">
                <h3 className="text-sm md:text-base font-bold text-gray-900 leading-tight line-clamp-2 font-sans group-hover:text-primary-600 transition-colors">
                    {novel.title}
                </h3>
                <p className="text-xs text-gray-500 mt-1 truncate">
                    {displayGenre} <span className="mx-1">|</span>{" "}
                    {novel.author?.pen_name || novel.author?.name}
                </p>
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
