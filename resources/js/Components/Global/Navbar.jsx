import React from "react";
import { Link, usePage } from "@inertiajs/react";
import {
    MagnifyingGlassIcon,
    MoonIcon,
    ChevronDownIcon,
    PencilSquareIcon,
    QueueListIcon, // Untuk ikon library/rak buku
} from "@heroicons/react/24/outline";
import { UserIcon } from "@heroicons/react/24/solid";

export default function Navbar() {
    const { auth } = usePage().props;

    return (
        <nav className="hidden md:flex items-center justify-between px-8 py-3 bg-white shadow-sm sticky top-0 z-50 transition-all duration-300">
            {/* ⬅️ BAGIAN KIRI: Logo & Menu Utama */}
            <div className="flex items-center gap-8">
                {/* Logo Storymoon */}
                <Link href="/" className="flex items-center gap-2 group">
                    <div className="w-9 h-9 bg-gradient-to-br from-primary-500 to-purple-700 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md group-hover:scale-105 transition-transform">
                        S
                    </div>
                    <span className="text-2xl font-bold text-gray-900 tracking-tight hidden lg:block">
                        Storymoon
                    </span>
                </Link>

                {/* Navigasi Utama */}
                <div className="flex items-center gap-6 font-semibold text-gray-700 text-sm">
                    {/* DROPDOWN JELAJAHI (CSS-Only, Sangat Ringan & Cepat) */}
                    <div className="relative group py-2">
                        <button className="flex items-center gap-1 hover:text-primary-600 transition">
                            Jelajahi{" "}
                            <ChevronDownIcon className="w-4 h-4 text-gray-500 group-hover:text-primary-600 transition-transform group-hover:rotate-180" />
                        </button>

                        {/* Isi Dropdown */}
                        <div className="absolute top-full left-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 overflow-hidden">
                            <div className="py-2">
                                <Link
                                    href="/explore?genre=fantasi"
                                    className="block px-4 py-2 hover:bg-primary-50 hover:text-primary-600 transition"
                                >
                                    Fantasi
                                </Link>
                                <Link
                                    href="/explore?genre=perkotaan"
                                    className="block px-4 py-2 hover:bg-primary-50 hover:text-primary-600 transition"
                                >
                                    Perkotaan
                                </Link>
                                <Link
                                    href="/explore?genre=misteri"
                                    className="block px-4 py-2 hover:bg-primary-50 hover:text-primary-600 transition"
                                >
                                    Misteri
                                </Link>
                                <Link
                                    href="/explore?genre=horor"
                                    className="block px-4 py-2 hover:bg-primary-50 hover:text-primary-600 transition"
                                >
                                    Horor
                                </Link>
                            </div>
                        </div>
                    </div>

                    <Link
                        href="/ranking"
                        className="hover:text-primary-600 transition py-2"
                    >
                        Ranking
                    </Link>
                    <Link
                        href="/lomba"
                        className="hover:text-primary-600 transition py-2"
                    >
                        Lomba
                    </Link>

                    {/* Pusat Penulis */}
                    <Link
                        href="/author"
                        className="flex items-center gap-1.5 text-gray-500 hover:text-primary-600 transition py-2 border-l border-gray-300 pl-6 ml-2"
                    >
                        <PencilSquareIcon className="w-4 h-4" /> Pusat Penulis
                    </Link>
                </div>
            </div>

            {/* ➡️ BAGIAN KANAN: Search, Tools, Profile */}
            <div className="flex items-center gap-5">
                {/* Search Bar (Menyerupai desain Anda) */}
                <div className="relative group hidden lg:block">
                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary-500 transition" />
                    <input
                        type="text"
                        placeholder="Cari Novel"
                        className="bg-primary-50/50 border border-transparent focus:border-primary-300 focus:bg-white focus:ring-4 focus:ring-primary-50 text-sm rounded-full pl-10 pr-4 py-2 w-56 xl:w-64 transition-all duration-300 outline-none"
                    />
                </div>

                {/* Rak Buku (Library) */}
                <button
                    className="text-gray-500 hover:text-primary-600 transition"
                    aria-label="Rak Buku"
                >
                    <QueueListIcon className="w-6 h-6" />
                </button>

                {/* Dark Mode Toggle */}
                <button
                    className="text-gray-500 hover:text-primary-600 transition"
                    aria-label="Mode Gelap"
                >
                    <MoonIcon className="w-6 h-6" />
                </button>

                {/* User Profile / Login Button */}
                {auth?.user ? (
                    <Link
                        href="/profile"
                        className="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 hover:ring-2 ring-offset-2 ring-primary-200 transition overflow-hidden"
                    >
                        {/* Jika ada foto profil, render tag <img> di sini. Jika tidak, pakai ikon */}
                        <UserIcon className="w-5 h-5" />
                    </Link>
                ) : (
                    <Link
                        href="/login"
                        className="w-9 h-9 rounded-full bg-primary-500 flex items-center justify-center text-white hover:bg-primary-600 hover:shadow-md transition"
                    >
                        <UserIcon className="w-5 h-5" />
                    </Link>
                )}
            </div>
        </nav>
    );
}
