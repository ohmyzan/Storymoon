import React from "react";
import { Link, usePage } from "@inertiajs/react";
import {
    HomeIcon,
    MagnifyingGlassIcon,
    BookOpenIcon,
    UserIcon,
    MegaphoneIcon,
} from "@heroicons/react/24/outline";

export default function MainLayout({ children }) {
    // 🌟 FIX: Menangkap data dari 'globalConfig' sesuai Middleware Anda
    const { globalConfig } = usePage().props;

    return (
        <div className="min-h-screen bg-gray-50 font-sans flex flex-col">
            {/* 📢 PENGUMUMAN BERJALAN (MARQUEE) */}
            {globalConfig?.announcement_text && (
                <div className="bg-primary-600 text-white text-xs md:text-sm font-medium py-2 px-4 flex items-center justify-center text-center">
                    <MegaphoneIcon className="w-4 h-4 mr-2 inline-block animate-pulse" />
                    <span>{globalConfig.announcement_text}</span>
                </div>
            )}

            {/* 🖥️ TOP NAVBAR */}
            <nav className="hidden md:flex items-center justify-between px-8 py-4 bg-white shadow-sm sticky top-0 z-50">
                <div className="flex items-center gap-8">
                    <Link
                        href="/"
                        className="text-2xl font-bold text-primary-600 tracking-tight"
                    >
                        Storymoon
                    </Link>
                    {/* ... Nav Links lainnya ... */}
                </div>
                <div className="flex items-center gap-4">
                    <button className="text-gray-500 hover:text-primary-600">
                        <MagnifyingGlassIcon className="w-6 h-6" />
                    </button>
                    <Link
                        href="#"
                        className="px-5 py-2 text-sm font-semibold text-primary-600 bg-primary-50 rounded-full hover:bg-primary-100 transition"
                    >
                        Masuk
                    </Link>
                </div>
            </nav>

            <main className="flex-grow pb-20 md:pb-12 pt-4 px-4 md:px-8 max-w-7xl mx-auto w-full">
                {children}
            </main>

            {/* 🌍 FOOTER GLOBAL */}
            <footer className="hidden md:block bg-white border-t border-gray-200 mt-auto">
                <div className="max-w-7xl mx-auto px-8 py-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div className="text-sm text-gray-500">
                        &copy; {new Date().getFullYear()} Storymoon.
                    </div>
                    <div className="flex gap-6 text-sm font-medium text-gray-600">
                        {/* 🌟 FIX: Memanggil data dari globalConfig.social */}
                        {globalConfig?.social?.discord && (
                            <a
                                href={globalConfig.social.discord}
                                target="_blank"
                                rel="noreferrer"
                                className="hover:text-primary-600 transition"
                            >
                                Komunitas Discord
                            </a>
                        )}
                        {globalConfig?.social?.email && (
                            <a
                                href={`mailto:${globalConfig.social.email}`}
                                className="hover:text-primary-600 transition"
                            >
                                Bantuan
                            </a>
                        )}
                    </div>
                </div>
            </footer>

            {/* ... Bottom Nav Mobile ... */}
        </div>
    );
}
