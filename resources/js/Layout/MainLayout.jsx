import React from "react";
import { Link, usePage, router } from "@inertiajs/react";
import {
    HomeIcon,
    MagnifyingGlassIcon,
    BookOpenIcon,
    UserIcon,
    MegaphoneIcon,
} from "@heroicons/react/24/outline";

// 🌟 IMPORT KOMPONEN GLOBAL
import Navbar from "../Components/Global/Navbar";
import Footer from "../Components/Global/Footer";

export default function MainLayout({ children }) {
    const { globalConfig } = usePage().props;

    // 🌟 Redirect ke halaman maintenance jika aktif
    React.useEffect(() => {
        if (globalConfig?.is_maintenance) {
            router.visit("/maintenance");
        }
    }, [globalConfig?.is_maintenance]);

    return (
        <div className="min-h-screen bg-gray-50 font-sans flex flex-col selection:bg-primary-200 selection:text-primary-900">
            {globalConfig?.announcement_text && (
                <div className="bg-primary-600 text-white text-xs md:text-sm font-medium py-2 px-4 flex items-center justify-center text-center">
                    <MegaphoneIcon className="w-4 h-4 mr-2 inline-block animate-pulse" />
                    <span>{globalConfig.announcement_text}</span>
                </div>
            )}

            <Navbar />

            <main className="flex-grow pb-20 md:pb-12 pt-4 px-4 md:px-8 max-w-7xl mx-auto w-full">
                {children}
            </main>

            <Footer />
        </div>
    );
}
