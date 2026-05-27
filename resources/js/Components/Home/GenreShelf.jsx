import React, { useState, useEffect, useRef, useCallback } from "react";
import { Link } from "@inertiajs/react";
import axios from "axios";
import NovelCard from "../UI/NovelCard";
import { ChevronRightIcon } from "@heroicons/react/20/solid";

export default function GenreShelf({ shelfData }) {
    const [novels, setNovels] = useState(shelfData.novels);
    const [page, setPage] = useState(2);
    const [hasMore, setHasMore] = useState(true);
    const [isLoading, setIsLoading] = useState(false);

    const observerTarget = useRef(null);
    const isFetchingRef = useRef(false);

    const scrollRef = useRef(null);
    const isDown = useRef(false);
    const startX = useRef(0);
    const scrollLeft = useRef(0);

    const handleMouseDown = (e) => {
        isDown.current = true;
        startX.current = e.pageX - scrollRef.current.offsetLeft;
        scrollLeft.current = scrollRef.current.scrollLeft;
    };

    const handleMouseLeave = () => {
        isDown.current = false;
    };

    const handleMouseUp = () => {
        isDown.current = false;
    };

    const handleMouseMove = (e) => {
        if (!isDown.current) return;
        e.preventDefault();
        const x = e.pageX - scrollRef.current.offsetLeft;
        const walk = (x - startX.current) * 1.2;
        scrollRef.current.scrollLeft = scrollLeft.current - walk;
    };

    const fetchMoreNovels = useCallback(async () => {
        if (isFetchingRef.current || !hasMore) return;
        isFetchingRef.current = true;
        setIsLoading(true);

        try {
            const response = await axios.get(
                `/api/genres/${shelfData.slug}/novels?page=${page}`,
            );
            const newNovels = response.data.data;
            if (!newNovels || newNovels.length === 0) {
                setHasMore(false);
            } else {
                setNovels((prev) => [...prev, ...newNovels]);
                setPage((prev) => prev + 1);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setIsLoading(false);
            isFetchingRef.current = false;
        }
    }, [page, shelfData.slug, hasMore]);

    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                if (
                    entries[0].isIntersecting &&
                    hasMore &&
                    !isFetchingRef.current
                ) {
                    fetchMoreNovels();
                }
            },
            { threshold: 0.1 },
        );
        const el = observerTarget.current;
        if (el) observer.observe(el);
        return () => {
            if (el) observer.unobserve(el);
            observer.disconnect();
        };
    }, [hasMore, fetchMoreNovels]);

    return (
        <section className="relative w-full">
            <div className="flex justify-between items-end mb-6 pr-4 border-b border-gray-100 pb-2">
                <h2 className="text-xl md:text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-1.5">
                    {/* 🌟 FIX: Teks Genre dikembalikan jadi Ungu sesuai request desain Anda */}
                    Pilihan <span>{shelfData.genre_name}</span>
                </h2>

                {/* 🌟 FIX: Meniru persis desain asli "Lihat Semua" */}
                <Link
                    href={`/explore?genre=${shelfData.slug}`}
                    className="flex items-center text-sm md:text-base font-medium text-primary-600 hover:text-primary-800 transition"
                >
                    Lihat Semua{" "}
                    <ChevronRightIcon className="w-5 h-5 ml-0.5 text-primary-600" />
                </Link>
            </div>

            <div
                ref={scrollRef}
                onMouseDown={handleMouseDown}
                onMouseLeave={handleMouseLeave}
                onMouseUp={handleMouseUp}
                onMouseMove={handleMouseMove}
                className="flex gap-4 overflow-x-auto no-scrollbar cursor-grab active:cursor-grabbing select-none pb-6"
            >
                <style>
                    {`
                        .no-scrollbar::-webkit-scrollbar {
                            display: none;
                        }
                    `}
                </style>

                {novels.map((novel, idx) => (
                    <div
                        key={`${novel.id}-${idx}`}
                        className="w-40 flex-shrink-0"
                        onMouseDown={(e) => e.preventDefault()}
                    >
                        <NovelCard
                            novel={novel}
                            shelfGenre={shelfData.genre_name}
                        />
                    </div>
                ))}

                {hasMore && (
                    <div
                        ref={observerTarget}
                        className="w-20 flex items-center justify-center flex-shrink-0"
                    >
                        {isLoading && (
                            <div className="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}
