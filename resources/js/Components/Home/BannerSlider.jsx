import React, { useState, useEffect } from "react";
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/react/24/outline";
import { isSafeUrl } from "../../Utils/formatters";

export default function BannerSlider({ banners }) {
    const [currentIndex, setCurrentIndex] = useState(0);

    // Auto-slide setiap 5 detik
    useEffect(() => {
        if (!banners || banners.length <= 1) return;

        const interval = setInterval(() => {
            setCurrentIndex((prevIndex) =>
                prevIndex === banners.length - 1 ? 0 : prevIndex + 1,
            );
        }, 5000);

        return () => clearInterval(interval);
    }, [banners]);

    const prevSlide = () => {
        setCurrentIndex((prevIndex) =>
            prevIndex === 0 ? banners.length - 1 : prevIndex - 1,
        );
    };

    const nextSlide = () => {
        setCurrentIndex((prevIndex) =>
            prevIndex === banners.length - 1 ? 0 : prevIndex + 1,
        );
    };

    if (!banners || banners.length === 0) return null;

    return (
        <div className="relative w-full overflow-hidden rounded-2xl shadow-sm group bg-gray-100">
            {/* Wrapper Slider dengan animasi geser (Transform) */}
            <div
                className="flex transition-transform duration-700 ease-in-out"
                style={{ transform: `translateX(-${currentIndex * 100}%)` }}
            >
                {banners.map((banner, index) => {
                    // Cek apakah banner memiliki URL tujuan
                    const isClickable =
                        banner.target_url &&
                        banner.target_url !== "#" &&
                        isSafeUrl(banner.target_url);

                    const BannerContent = (
                        <img
                            src={`/storage/${banner.image_path}`}
                            alt={banner.title}
                            // Banner pertama di-load langsung (eager) untuk LCP (Largest Contentful Paint) yang cepat
                            loading={index === 0 ? "eager" : "lazy"}
                            className="w-full h-auto md:h-48 lg:h-64 object-cover object-center w-screen flex-shrink-0"
                        />
                    );

                    return (
                        <div key={index} className="w-full flex-shrink-0">
                            {isClickable ? (
                                <a
                                    href={banner.target_url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="block w-full cursor-pointer"
                                >
                                    {BannerContent}
                                </a>
                            ) : (
                                BannerContent
                            )}
                        </div>
                    );
                })}
            </div>

            {/* Tombol Navigasi Kiri / Kanan (Muncul saat hover di Desktop) */}
            {banners.length > 1 && (
                <>
                    <button
                        onClick={prevSlide}
                        className="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 w-8 h-8 md:w-10 md:h-10 bg-black/30 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none"
                    >
                        <ChevronLeftIcon className="w-5 h-5 md:w-6 md:h-6" />
                    </button>
                    <button
                        onClick={nextSlide}
                        className="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 w-8 h-8 md:w-10 md:h-10 bg-black/30 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none"
                    >
                        <ChevronRightIcon className="w-5 h-5 md:w-6 md:h-6" />
                    </button>

                    {/* Titik Indikator Bawah (Dots) */}
                    <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2">
                        {banners.map((_, index) => (
                            <button
                                key={index}
                                onClick={() => setCurrentIndex(index)}
                                className={`transition-all duration-300 rounded-full ${
                                    currentIndex === index
                                        ? "w-6 md:w-8 h-2 bg-white"
                                        : "w-2 h-2 bg-white/50 hover:bg-white/80"
                                }`}
                                aria-label={`Pindah ke banner ${index + 1}`}
                            />
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}
