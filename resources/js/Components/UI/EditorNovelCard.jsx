import React from "react";
import { Link } from "@inertiajs/react";
import { EyeIcon } from "@heroicons/react/20/solid";
import { formatNumber } from "../../Utils/formatters"; // 🌟 FIX: Hapus getMainGenre

export default function EditorNovelCard({ novel }) {
    return (
        <Link
            href={`/novel/${novel.slug}`}
            className="group flex gap-4 w-full bg-white rounded-xl p-3 hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100"
        >
            <div className="w-24 sm:w-28 flex-shrink-0 aspect-[2/3] rounded-lg overflow-hidden bg-gray-200 shadow-sm group-hover:shadow-md transition-shadow">
                {/* 🌟 FIX: Langsung src={novel.cover_image} */}
                <img
                    src={novel.cover_image}
                    alt={novel.title}
                    loading="lazy"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
            </div>

            <div className="flex flex-col justify-between py-1 overflow-hidden flex-1">
                <div>
                    <h3 className="font-bold text-gray-900 text-sm md:text-base line-clamp-2 leading-tight group-hover:text-primary-600 transition-colors">
                        {novel.title}
                    </h3>

                    <div className="flex items-center text-xs text-gray-500 mt-1.5 gap-2 truncate">
                        <span className="text-primary-600 font-medium">
                            {/* 🌟 FIX: Langsung ambil main_genre_name dari resource */}
                            {novel.main_genre_name}
                        </span>
                        <span className="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                        <span className="truncate">
                            {novel.author?.pen_name || novel.author?.name}
                        </span>
                    </div>

                    <p className="text-xs text-gray-600 mt-2 line-clamp-2 leading-relaxed opacity-90 hidden sm:block">
                        {novel.synopsis ||
                            "Sinopsis belum tersedia untuk karya luar biasa ini."}
                    </p>
                </div>

                <div className="flex items-center gap-4 text-xs text-gray-500 font-medium mt-3">
                    <span className="flex items-center gap-1">
                        <EyeIcon className="w-3.5 h-3.5 text-gray-400" />
                        {formatNumber(novel.views_count || 0)} views
                    </span>
                    <span
                        className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${novel.status === "ongoing" ? "bg-primary-50 text-primary-600" : "bg-gray-100 text-gray-600"}`}
                    >
                        {novel.status === "ongoing" ? "Ongoing" : "Completed"}
                    </span>
                </div>
            </div>
        </Link>
    );
}
