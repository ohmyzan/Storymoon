import React, { useState } from "react";
import { Link } from "@inertiajs/react";
import {
    ChartBarIcon,
    DocumentTextIcon,
    BanknotesIcon,
    Bars3Icon,
    XMarkIcon,
} from "@heroicons/react/24/outline";

export default function AuthorLayout({ children }) {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100 flex font-sans">
            {/* 📱 MOBILE HEADER (Hamburger Menu) */}
            <div className="md:hidden fixed top-0 w-full bg-white shadow-sm z-40 flex items-center px-4 py-3">
                <button
                    onClick={() => setIsSidebarOpen(true)}
                    className="text-gray-600 focus:outline-none"
                >
                    <Bars3Icon className="w-7 h-7" />
                </button>
                <span className="ml-4 font-bold text-lg text-gray-800">
                    Author Center
                </span>
            </div>

            {/* 🖥️ SIDEBAR (Kiri) */}
            <aside
                className={`fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-50 transform transition-transform duration-300 ease-in-out flex flex-col ${isSidebarOpen ? "translate-x-0" : "-translate-x-full"} md:translate-x-0`}
            >
                <div className="flex items-center justify-between p-6 border-b border-gray-100">
                    <Link
                        href="/"
                        className="text-xl font-bold text-primary-600"
                    >
                        Storymoon
                        <span className="text-gray-800 text-sm block font-medium">
                            Author Center
                        </span>
                    </Link>
                    <button
                        onClick={() => setIsSidebarOpen(false)}
                        className="md:hidden text-gray-500"
                    >
                        <XMarkIcon className="w-6 h-6" />
                    </button>
                </div>

                <nav className="flex-1 p-4 space-y-2 overflow-y-auto">
                    <Link
                        href="#"
                        className="flex items-center gap-3 p-3 text-primary-700 bg-primary-50 rounded-lg font-medium"
                    >
                        <ChartBarIcon className="w-5 h-5" /> Dasbor Metrik
                    </Link>
                    <Link
                        href="#"
                        className="flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition"
                    >
                        <DocumentTextIcon className="w-5 h-5" /> Manajemen Karya
                    </Link>
                    <Link
                        href="#"
                        className="flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-50 rounded-lg font-medium transition"
                    >
                        <BanknotesIcon className="w-5 h-5" /> Keuangan & Kontrak
                    </Link>
                </nav>
            </aside>

            {/* 📄 AREA KONTEN (Kanan) */}
            <main className="flex-1 p-6 md:p-8 mt-14 md:mt-0 md:ml-64 transition-all duration-300">
                {children}
            </main>

            {/* Overlay untuk mobile saat sidebar terbuka */}
            {isSidebarOpen && (
                <div
                    className="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
                    onClick={() => setIsSidebarOpen(false)}
                ></div>
            )}
        </div>
    );
}
