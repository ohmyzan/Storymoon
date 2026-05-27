import React from "react";
import { Link } from "@inertiajs/react";
import { UserCircleIcon } from "@heroicons/react/24/solid";

export default function CommunityTreasure({ treasures = [] }) {
    if (!treasures || treasures.length === 0) return null;

    return (
        <div className="bg-[#DAB6FC] md:bg-gradient-to-r md:from-[#DAB6FC] md:to-[#E8D1FE] rounded-3xl p-6 md:p-10 flex flex-col lg:flex-row items-center gap-8 shadow-sm">
            <div className="lg:w-1/3 flex flex-col items-start w-full">
                <span className="bg-primary-700 text-white text-[11px] md:text-xs font-bold px-3 py-1.5 rounded-full mb-4 shadow-sm">
                    Review Komunitas
                </span>
                <h2 className="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-4 tracking-tight font-sans">
                    Menggali Harta Karun
                </h2>
                <p className="text-gray-800 text-sm md:text-base leading-relaxed font-medium opacity-90">
                    Permata tersembunyi dengan rating tertinggi dari pembaca
                    setia kami. Jangan lewatkan mahakarya yang kurang dikenal
                    ini.
                </p>
            </div>

            <div className="lg:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                {treasures.map((novel) => {
                    // 🌟 FIX: Langsung ambil data latest_review hasil bentukan Resource Back-End
                    const review = novel.latest_review;
                    if (!review) return null;

                    return (
                        <div
                            key={novel.id}
                            className="bg-white rounded-2xl p-4 md:p-5 shadow-md flex flex-col justify-between hover:-translate-y-1.5 hover:shadow-lg transition-all duration-300"
                        >
                            <div className="flex items-center justify-between mb-3">
                                <div className="flex items-center gap-2">
                                    <div className="w-8 h-8 md:w-10 md:h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-500">
                                        <UserCircleIcon className="w-6 h-6 md:w-8 md:h-8" />
                                    </div>
                                    <span className="font-bold text-gray-900 text-xs md:text-sm truncate max-w-[100px]">
                                        {/* 🌟 FIX: Ambil dari user_name di Resource */}
                                        {review.user_name}
                                    </span>
                                </div>
                                <span className="text-[10px] text-gray-400 font-semibold bg-gray-50 px-2 py-1 rounded">
                                    Terbaru
                                </span>
                            </div>

                            {/* 🌟 FIX: Content sudah dibungkus oleh Resource */}
                            <p className="text-gray-700 text-xs md:text-sm italic mb-4 line-clamp-3 leading-relaxed">
                                "{review.content}"
                            </p>

                            <Link
                                href={`/novel/${novel.slug}`}
                                className="flex items-center gap-3 bg-gray-50 p-2 md:p-3 rounded-xl hover:bg-primary-50 transition border border-transparent hover:border-primary-100 group"
                            >
                                {/* 🌟 FIX: Cover Image */}
                                <img
                                    src={novel.cover_image}
                                    alt={novel.title}
                                    loading="lazy"
                                    className="w-10 h-14 md:w-12 md:h-16 object-cover rounded shadow-sm group-hover:scale-105 transition-transform"
                                />
                                <div className="flex flex-col overflow-hidden">
                                    <span className="font-bold text-gray-900 text-xs md:text-sm truncate group-hover:text-primary-600 transition-colors">
                                        {novel.title}
                                    </span>
                                    <span className="text-[10px] md:text-xs text-gray-500 truncate mt-0.5">
                                        {novel.author?.name || "Author"}
                                    </span>
                                </div>
                            </Link>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
