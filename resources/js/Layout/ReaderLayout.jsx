import React from "react";
import { Link } from "@inertiajs/react";
import {
    ChevronLeftIcon,
    Bars3CenterLeftIcon,
} from "@heroicons/react/24/outline";

export default function ReaderLayout({ children, novelTitle, chapterTitle }) {
    return (
        // font-serif di sini memicu Literata (sesuai tailwind.config.js yang kita buat)
        <div className="min-h-screen bg-[#Fdfcf8] text-gray-900 font-serif selection:bg-primary-200 selection:text-primary-900">
            {/* HEADER MENGAMBANG (Hanya muncul jika di-scroll ke atas/di-tap. Untuk sekarang kita buat static dulu) */}
            <header className="sticky top-0 bg-[#Fdfcf8] bg-opacity-95 backdrop-blur-sm z-40 px-4 py-3 flex justify-between items-center border-b border-gray-200/50 font-sans">
                <Link
                    href="#"
                    className="flex items-center text-gray-600 hover:text-primary-600 transition"
                >
                    <ChevronLeftIcon className="w-5 h-5 mr-1" />
                    <span className="text-sm font-semibold truncate max-w-[150px] md:max-w-xs">
                        {novelTitle}
                    </span>
                </Link>
                <button className="text-gray-500 hover:text-primary-600 focus:outline-none">
                    <Bars3CenterLeftIcon className="w-6 h-6" />
                </button>
            </header>

            {/* AREA MEMBACA (Tengah layar, lebar dibatasi agar nyaman dibaca seperti buku) */}
            {/* user-select-none mencegah anti-copas untuk pembaca usil */}
            <main className="max-w-2xl mx-auto px-6 md:px-8 py-10 md:py-16 text-lg md:text-xl leading-relaxed md:leading-loose user-select-none">
                <h1 className="text-2xl md:text-3xl font-bold mb-8 text-center">
                    {chapterTitle}
                </h1>
                {children}
            </main>
        </div>
    );
}
