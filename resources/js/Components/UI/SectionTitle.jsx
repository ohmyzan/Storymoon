import React from "react";
import { Link } from "@inertiajs/react";
import { ArrowPathIcon, ChevronRightIcon } from "@heroicons/react/24/outline";

export default function SectionTitle({
    title,
    actionType = "see-all",
    actionLink = "#",
    onRefresh,
}) {
    return (
        <div className="flex items-center justify-between mb-4 mt-10">
            <h2 className="text-xl md:text-2xl font-bold text-gray-900 font-sans tracking-tight">
                {title}
            </h2>

            {actionType === "refresh" ? (
                <button
                    onClick={onRefresh}
                    className="flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 transition"
                >
                    <ArrowPathIcon className="w-4 h-4 mr-1" /> Segarkan
                </button>
            ) : (
                <Link
                    href={actionLink}
                    /* 🌟 FIX: Samakan warna dengan GenreShelf (primary-600) agar konsisten se-aplikasi */
                    className="flex items-center text-sm md:text-base font-semibold text-primary-600 hover:text-primary-800 transition"
                >
                    Lihat Semua <ChevronRightIcon className="w-5 h-5 ml-0.5" />
                </Link>
            )}
        </div>
    );
}
