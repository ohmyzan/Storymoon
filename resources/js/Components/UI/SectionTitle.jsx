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

            {/* Logika Tombol Kanan (Bisa Link 'Lihat Semua' atau Tombol 'Segarkan') */}
            {actionType === "refresh" ? (
                <button
                    onClick={onRefresh}
                    className="flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition"
                >
                    <ArrowPathIcon className="w-4 h-4 mr-1" /> Segarkan
                </button>
            ) : (
                <Link
                    href={actionLink}
                    className="flex items-center text-sm font-medium text-primary-500 hover:text-primary-600 transition"
                >
                    Lihat Semua <ChevronRightIcon className="w-4 h-4 ml-1" />
                </Link>
            )}
        </div>
    );
}
