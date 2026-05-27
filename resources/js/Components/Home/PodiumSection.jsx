import React from "react";
import { Link } from "@inertiajs/react";
import { EyeIcon } from "@heroicons/react/20/solid";
import SectionTitle from "../UI/SectionTitle";
import { formatNumber } from "../../Utils/formatters"; // 🌟 FIX: Hapus getMainGenre

export default function PodiumSection({ topNovels = [], trendingList = [] }) {
    const rank1 = topNovels[0];
    const rank2 = topNovels[1];
    const rank3 = topNovels[2];

    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
            {/* 🏆 KOLOM KIRI: Podium Terlaris */}
            <div className="lg:col-span-2 flex flex-col h-full">
                <SectionTitle
                    title="Terlaris"
                    actionLink="/ranking?category=terlaris"
                />

                <div className="bg-gradient-to-b from-primary-50/20 to-transparent rounded-3xl p-2 md:p-6 mt-4 flex-1 flex flex-col justify-end">
                    <div className="grid grid-cols-3 gap-3 md:gap-6 items-end pt-12 pb-2 md:pb-4">
                        {/* PERINGKAT 2 (Kiri) */}
                        {rank2 && (
                            <div className="flex flex-col items-center justify-end h-full">
                                <Link
                                    href={`/novel/${rank2.slug}`}
                                    className="flex flex-col items-center group w-full px-2"
                                >
                                    <div className="w-20 md:w-28 relative z-10 translate-y-1.5 md:translate-y-2">
                                        <img
                                            src={rank2.cover_image}
                                            alt={rank2.title}
                                            loading="lazy"
                                            className="w-full aspect-[2/3] object-cover rounded-md shadow-md group-hover:-translate-y-2 transition-transform duration-300"
                                        />
                                    </div>
                                    <div className="w-full relative flex flex-col group-hover:-translate-y-2 transition-transform duration-300">
                                        <div className="w-full h-2 md:h-3 bg-pink-600 rounded-t-sm"></div>
                                        <div className="w-full h-8 md:h-10 bg-pink-500 flex items-center justify-center rounded-b-sm shadow-lg">
                                            <span className="font-extrabold text-white text-xs md:text-sm tracking-wider drop-shadow-sm">
                                                2nd
                                            </span>
                                        </div>
                                    </div>
                                </Link>
                                <div className="mt-4 md:mt-5 text-center w-full px-1">
                                    <h3 className="font-bold text-gray-900 text-[11px] md:text-sm line-clamp-2 leading-tight hover:text-pink-600 transition-colors">
                                        {rank2.title}
                                    </h3>
                                    <p className="text-pink-500 text-[10px] md:text-xs font-bold mt-1">
                                        {rank2.main_genre_name}
                                    </p>
                                    <p className="text-gray-500 text-[10px] md:text-xs line-clamp-2 md:line-clamp-3 mt-1.5 leading-relaxed hidden sm:-webkit-box">
                                        {rank2.synopsis ||
                                            "Sinopsis belum tersedia."}
                                    </p>
                                </div>
                            </div>
                        )}

                        {/* PERINGKAT 1 (Tengah) */}
                        {rank1 && (
                            <div className="flex flex-col items-center justify-end h-full z-20">
                                <Link
                                    href={`/novel/${rank1.slug}`}
                                    className="flex flex-col items-center group relative w-full"
                                >
                                    <div className="w-28 md:w-36 relative z-10 translate-y-2 md:translate-y-3">
                                        <div className="absolute -top-7 -right-5 md:-top-9 md:-right-6 text-3xl md:text-5xl drop-shadow-xl z-30 transform rotate-[15deg] group-hover:-translate-y-1 transition-transform duration-300">
                                            👑
                                        </div>
                                        <img
                                            src={rank1.cover_image}
                                            alt={rank1.title}
                                            loading="lazy"
                                            className="w-full aspect-[2/3] object-cover rounded-md shadow-xl group-hover:-translate-y-2 transition-transform duration-300 relative z-20"
                                        />
                                    </div>
                                    <div className="w-full md:w-[110%] relative flex flex-col group-hover:-translate-y-2 transition-transform duration-300">
                                        <div className="w-full h-3 md:h-4 bg-rose-600 rounded-t-sm"></div>
                                        <div className="w-full h-10 md:h-14 bg-rose-500 flex items-center justify-center rounded-b-md shadow-2xl">
                                            <span className="font-extrabold text-white text-sm md:text-lg tracking-widest drop-shadow-md">
                                                1st
                                            </span>
                                        </div>
                                    </div>
                                </Link>
                                <div className="mt-5 md:mt-6 text-center w-full px-1">
                                    <h3 className="font-extrabold text-gray-900 text-xs md:text-base line-clamp-2 leading-tight hover:text-rose-600 transition-colors">
                                        {rank1.title}
                                    </h3>
                                    <p className="text-rose-500 text-[10px] md:text-xs font-bold mt-1">
                                        {rank1.main_genre_name}
                                    </p>
                                    <p className="text-gray-500 text-[10px] md:text-xs line-clamp-3 mt-1.5 leading-relaxed">
                                        {rank1.synopsis ||
                                            "Sinopsis belum tersedia."}
                                    </p>
                                </div>
                            </div>
                        )}

                        {/* PERINGKAT 3 (Kanan) */}
                        {rank3 && (
                            <div className="flex flex-col items-center justify-end h-full">
                                <Link
                                    href={`/novel/${rank3.slug}`}
                                    className="flex flex-col items-center group w-full px-2"
                                >
                                    <div className="w-16 md:w-24 relative z-10 translate-y-1.5 md:translate-y-2">
                                        <img
                                            src={rank3.cover_image}
                                            alt={rank3.title}
                                            loading="lazy"
                                            className="w-full aspect-[2/3] object-cover rounded-md shadow-sm group-hover:-translate-y-2 transition-transform duration-300"
                                        />
                                    </div>
                                    <div className="w-full relative flex flex-col group-hover:-translate-y-2 transition-transform duration-300">
                                        <div className="w-full h-2 md:h-3 bg-purple-600 rounded-t-sm"></div>
                                        <div className="w-full h-6 md:h-8 bg-purple-500 flex items-center justify-center rounded-b-sm shadow-md">
                                            <span className="font-extrabold text-white text-[10px] md:text-xs tracking-wider drop-shadow-sm">
                                                3rd
                                            </span>
                                        </div>
                                    </div>
                                </Link>
                                <div className="mt-4 md:mt-5 text-center w-full px-1">
                                    <h3 className="font-bold text-gray-900 text-[10px] md:text-sm line-clamp-2 leading-tight hover:text-purple-600 transition-colors">
                                        {rank3.title}
                                    </h3>
                                    <p className="text-purple-500 text-[9px] md:text-xs font-bold mt-1">
                                        {rank3.main_genre_name}
                                    </p>
                                    <p className="text-gray-500 text-[9px] md:text-[11px] line-clamp-2 md:line-clamp-3 mt-1.5 leading-relaxed hidden sm:-webkit-box">
                                        {rank3.synopsis ||
                                            "Sinopsis belum tersedia."}
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* 📈 KOLOM KANAN: Daftar Trending */}
            <div className="flex flex-col h-full">
                <SectionTitle
                    title="Trending"
                    actionLink="/ranking?category=trending"
                />
                <div className="bg-primary-50/30 rounded-3xl p-4 md:p-6 mt-4 flex flex-col flex-1 justify-between gap-4 border border-primary-50">
                    {trendingList && trendingList.length > 0 ? (
                        trendingList.map((novel, index) => (
                            <Link
                                key={novel.id}
                                href={`/novel/${novel.slug}`}
                                className="flex items-center gap-4 group"
                            >
                                <div className="w-8 flex-shrink-0 text-center text-xl font-bold text-gray-800 font-serif">
                                    {index + 1}.
                                </div>
                                <div className="w-12 h-16 rounded overflow-hidden shadow-sm flex-shrink-0">
                                    <img
                                        src={novel.cover_image}
                                        alt={novel.title}
                                        loading="lazy"
                                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    />
                                </div>
                                <div className="flex flex-col overflow-hidden">
                                    <h4 className="font-bold text-gray-900 text-sm truncate group-hover:text-primary-600 transition-colors">
                                        {novel.title}
                                    </h4>
                                    <p className="text-xs text-gray-500 truncate mt-0.5">
                                        {novel.main_genre_name}{" "}
                                        <span className="mx-1">|</span>{" "}
                                        {novel.author?.name || "Author"}
                                    </p>
                                    <div className="flex items-center gap-1 mt-1 text-[10px] md:text-xs text-gray-400 font-medium">
                                        <EyeIcon className="w-3.5 h-3.5" />
                                        {formatNumber(novel.views_count)}
                                    </div>
                                </div>
                            </Link>
                        ))
                    ) : (
                        <div className="text-sm text-gray-500 italic text-center py-10 flex-1 flex items-center justify-center">
                            Data trending belum tersedia.
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
