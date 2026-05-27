import React from "react";
import { Link, usePage } from "@inertiajs/react";

export default function Footer() {
    const { globalConfig } = usePage().props;

    return (
        <footer className="hidden md:block bg-white border-t border-gray-200 mt-auto">
            <div className="max-w-7xl mx-auto px-8 py-8">
                <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                    {/* Kiri: Copyright & Logo Text */}
                    <div className="flex items-center gap-2 text-sm text-gray-500 font-medium">
                        <div className="w-6 h-6 bg-gradient-to-br from-primary-500 to-purple-700 rounded flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            S
                        </div>
                        &copy; {new Date().getFullYear()} Storymoon. Semua Hak
                        Cipta Dilindungi.
                    </div>

                    {/* Kanan: Tautan Legal & Sosial */}
                    <div className="flex flex-wrap justify-center gap-6 text-sm font-medium text-gray-500">
                        <Link
                            href="/terms"
                            className="hover:text-primary-600 transition"
                        >
                            Syarat & Ketentuan
                        </Link>

                        <Link
                            href="/privacy"
                            className="hover:text-primary-600 transition"
                        >
                            Kebijakan Privasi
                        </Link>

                        {/* Discord */}
                        {globalConfig?.social?.discord &&
                            globalConfig.social.discord !== "#" && (
                                <a
                                    href={globalConfig.social.discord}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="hover:text-primary-600 transition"
                                >
                                    Komunitas Discord
                                </a>
                            )}

                        {/* Instagram */}
                        {globalConfig?.social?.instagram &&
                            globalConfig.social.instagram !== "#" && (
                                <a
                                    href={globalConfig.social.instagram}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="hover:text-primary-600 transition"
                                >
                                    Instagram
                                </a>
                            )}

                        {/* Email */}
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
            </div>
        </footer>
    );
}
