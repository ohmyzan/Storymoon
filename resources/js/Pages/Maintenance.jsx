import React, { useEffect } from "react";
import { Head, usePage } from "@inertiajs/react";

export default function Maintenance() {
    const { globalConfig } = usePage().props;

    // ✅ Tambahkan logic polling menggunakan useEffect (React equivalent dari onMounted)
    useEffect(() => {
        // Mulai polling setiap 10 detik
        const interval = setInterval(async () => {
            try {
                const res = await fetch("/api/status");
                const data = await res.json();

                if (!data.maintenance) {
                    clearInterval(interval);
                    window.location.href = "/"; // ✅ Redirect ke beranda jika maintenance selesai
                }
            } catch (e) {
                // Diam saja jika gagal, coba lagi di interval berikutnya
            }
        }, 10000); // 10 detik

        // ✅ Cleanup function (React equivalent dari onUnmounted)
        return () => clearInterval(interval);
    }, []); // Array kosong memastikan ini hanya berjalan 1x saat komponen dipasang

    return (
        <>
            <Head>
                <title>Maintenance — Storymoon</title>
            </Head>

            <div className="min-h-screen bg-primary-50 flex flex-col items-center justify-center p-8 relative overflow-hidden font-sans">
                {/* Dekorasi background */}
                <div className="absolute w-96 h-96 rounded-full bg-primary-100 -top-24 -right-24 z-0" />
                <div className="absolute w-72 h-72 rounded-full bg-primary-200 -bottom-16 -left-16 z-0" />

                <div className="bg-white rounded-3xl border border-primary-100 px-10 py-12 max-w-md w-full text-center relative z-10">
                    {/* Logo */}
                    <div className="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center text-white font-bold text-3xl mx-auto mb-6 shadow-md">
                        S
                    </div>

                    {/* Badge */}
                    <div className="inline-flex items-center gap-2 bg-primary-50 border border-primary-200 text-primary-700 text-xs font-semibold px-3 py-1 rounded-full mb-5 uppercase tracking-wide">
                        <span className="w-1.5 h-1.5 rounded-full bg-primary-600 animate-pulse" />
                        Maintenance Mode
                    </div>

                    <h1 className="text-2xl font-bold text-primary-900 leading-snug mb-3">
                        Storymoon sedang dalam
                        <br />
                        pemeliharaan
                    </h1>

                    {/* Pesan dinamis dari panel admin */}
                    <p className="text-sm text-gray-500 leading-relaxed mb-8">
                        {globalConfig?.maintenance_message ||
                            "Kami sedang melakukan peningkatan sistem agar pengalaman membacamu semakin menyenangkan."}
                    </p>

                    {/* Info rows */}
                    <div className="flex items-center gap-3 bg-primary-50 rounded-xl px-4 py-3 mb-3 text-left">
                        <div className="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0 text-base">
                            🕐
                        </div>

                        <div>
                            <p className="text-xs text-gray-400 font-medium">
                                Estimasi selesai
                            </p>

                            <p className="text-sm text-gray-700 font-semibold">
                                Sebentar lagi — pantau terus!
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-3 bg-primary-50 rounded-xl px-4 py-3 text-left">
                        <div className="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0 text-base">
                            ✉️
                        </div>

                        <div>
                            <p className="text-xs text-gray-400 font-medium">
                                Ada pertanyaan?
                            </p>

                            <p className="text-sm text-gray-700 font-semibold">
                                {globalConfig?.social?.email ||
                                    "support@storymoon.com"}
                            </p>
                        </div>
                    </div>

                    {/* Social links — hanya tampil kalau sudah diisi di panel */}
                    {(globalConfig?.social?.discord !== "#" ||
                        globalConfig?.social?.instagram !== "#") && (
                        <div className="border-t border-gray-100 mt-6 pt-5 text-sm text-gray-400">
                            Ikuti perkembangan kami di{" "}
                            {globalConfig?.social?.discord &&
                                globalConfig.social.discord !== "#" && (
                                    <a
                                        href={globalConfig.social.discord}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="text-primary-600 font-semibold hover:underline"
                                    >
                                        Discord
                                    </a>
                                )}
                            {globalConfig?.social?.discord &&
                                globalConfig.social.discord !== "#" &&
                                globalConfig?.social?.instagram &&
                                globalConfig.social.instagram !== "#" &&
                                " atau "}
                            {globalConfig?.social?.instagram &&
                                globalConfig.social.instagram !== "#" && (
                                    <a
                                        href={globalConfig.social.instagram}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="text-primary-600 font-semibold hover:underline"
                                    >
                                        Instagram
                                    </a>
                                )}
                        </div>
                    )}
                </div>

                <p className="text-xs text-gray-400 mt-6 font-medium">
                    © {new Date().getFullYear()} Storymoon · Semua Hak Cipta
                    Dilindungi
                </p>
            </div>
        </>
    );
}
