import React, { useState, useEffect } from "react";
import { Link } from "@inertiajs/react";
import { StarIcon, EyeIcon } from "@heroicons/react/20/solid";
import { formatNumber } from "../../Utils/formatters"; // 🌟 FIX: Hapus getMainGenre

export default function HeroSection({ popularNovels, latestUpdates }) {
    const [currentIndex, setCurrentIndex] = useState(0);

    useEffect(() => {
        if (!popularNovels || popularNovels.length <= 1) return;
        const interval = setInterval(() => {
            setCurrentIndex((prev) => (prev + 1) % popularNovels.length);
        }, 5000);
        return () => clearInterval(interval);
    }, [popularNovels]);

    const activeNovel = popularNovels?.[currentIndex];

    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
            <div className="lg:col-span-2 flex flex-col">
                <h2 className="text-xl md:text-2xl font-bold text-gray-900 font-sans tracking-tight mb-4">
                    Populer Minggu Ini
                </h2>

                {activeNovel ? (
                    <div className="flex flex-col h-full">
                        <div
                            key={activeNovel.id}
                            className="bg-primary-50/50 rounded-2xl p-4 md:p-6 flex flex-col sm:flex-row gap-6 items-start h-full border border-primary-100/50 shadow-sm animate-[fadeIn_0.5s_ease-in-out]"
                        >
                            <div className="w-32 sm:w-40 flex-shrink-0 aspect-[2/3] rounded-lg overflow-hidden shadow-sm bg-gray-200">
                                {/* 🌟 FIX: Langsung pakai url resource */}
                                <img
                                    src={activeNovel.cover_image}
                                    alt={activeNovel.title}
                                    loading="lazy"
                                    className="w-full h-full object-cover"
                                />
                            </div>

                            <div className="flex flex-col flex-grow justify-center py-2">
                                <Link href={`/novel/${activeNovel.slug}`}>
                                    <h3 className="text-xl md:text-2xl font-bold text-gray-900 hover:text-primary-600 transition-colors line-clamp-2">
                                        {activeNovel.title}
                                    </h3>
                                </Link>

                                <div className="flex items-center gap-4 mt-2 text-sm text-gray-600 font-medium">
                                    <span className="flex items-center gap-1">
                                        <StarIcon className="w-4 h-4 text-gray-400" />
                                        {activeNovel.rating || "0.0"}
                                    </span>
                                    <span className="flex items-center gap-1">
                                        <EyeIcon className="w-4 h-4 text-gray-400" />
                                        {formatNumber(activeNovel.views_count)}
                                    </span>
                                </div>

                                <span
                                    className={`inline-block mt-3 px-3 py-1 text-xs font-bold rounded w-max ${activeNovel.status === "ongoing" ? "bg-primary-100 text-primary-700" : "bg-primary-600 text-white shadow-sm"}`}
                                >
                                    {activeNovel.status === "ongoing"
                                        ? "Bersambung"
                                        : "Tamat"}
                                </span>

                                <p className="mt-4 text-sm text-gray-600 line-clamp-3 leading-relaxed hidden sm:block">
                                    {activeNovel.synopsis ||
                                        "Sinopsis belum tersedia."}
                                </p>
                            </div>
                        </div>

                        {popularNovels.length > 1 && (
                            <div className="flex justify-center gap-2 mt-4">
                                {popularNovels.map((_, i) => (
                                    <button
                                        key={i}
                                        onClick={() => setCurrentIndex(i)}
                                        className={`h-2 rounded-full transition-all duration-300 ${currentIndex === i ? "bg-primary-600 w-6" : "bg-gray-300 w-2 hover:bg-primary-400"}`}
                                        aria-label={`Lihat novel populer ke-${i + 1}`}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                ) : (
                    <div className="h-full bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 border border-dashed border-gray-200">
                        Belum ada data populer.
                    </div>
                )}
            </div>

            <div className="flex flex-col">
                <h2 className="text-xl md:text-2xl font-bold text-gray-900 font-sans tracking-tight mb-4">
                    Update Terbaru
                </h2>
                <div className="flex flex-col gap-3">
                    {latestUpdates && latestUpdates.length > 0 ? (
                        latestUpdates.slice(0, 5).map((novel, index) => (
                            <Link
                                key={novel.id || index}
                                href={`/novel/${novel.slug}`}
                                className="flex justify-between items-center p-3 rounded-xl hover:bg-primary-50 transition border border-transparent hover:border-primary-100 group"
                            >
                                <span className="font-semibold text-gray-800 text-sm md:text-base truncate pr-4 group-hover:text-primary-700">
                                    {novel.title}
                                </span>
                                <span className="text-xs font-medium text-primary-600 bg-primary-50 px-2 py-1 rounded-md flex-shrink-0 group-hover:bg-primary-100">
                                    {novel.latest_chapter_number
                                        ? `${novel.latest_chapter_number} bab`
                                        : "Baru"}
                                </span>
                            </Link>
                        ))
                    ) : (
                        <div className="text-sm text-gray-500 italic p-4 bg-gray-50 rounded-xl">
                            Tidak ada update terbaru.
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
