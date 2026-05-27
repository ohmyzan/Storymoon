import React from "react";
import { Head, usePage, Link } from "@inertiajs/react";
import MainLayout from "../Layout/MainLayout";
import SectionTitle from "../Components/UI/SectionTitle";
import NovelCard from "../Components/UI/NovelCard";
import HeroSection from "../Components/Home/HeroSection";
import BannerSlider from "../Components/Home/BannerSlider";
import PodiumSection from "../Components/Home/PodiumSection";
import GenreGrid from "../Components/Home/GenreGrid";
import CommunityTreasure from "../Components/Home/CommunityTreasure";
import EditorChoicesSection from "../Components/Home/EditorChoicesSection";
import GenreShelf from "../Components/Home/GenreShelf";

export default function Home({
    banners,
    popularThisWeek,
    latestUpdates,
    editorsChoices,
    bestSellers,
    trendingNovels,
    hiddenTreasures,
    trendingGenres,
    genreShelves,
}) {
    const { globalConfig } = usePage().props;

    return (
        <MainLayout>
            <Head>
                <title>Beranda</title>
                <meta
                    name="description"
                    content={
                        globalConfig?.seo?.description ||
                        "Platform Web Novel Modern"
                    }
                />
            </Head>

            {/* Hero Section dengan Populer Minggu Ini & Update Terbaru */}
            <HeroSection
                popularNovels={popularThisWeek}
                latestUpdates={latestUpdates}
            />

            {/* Komponen Pilihan Editor Baru dengan Fitur Soft Refresh */}
            <EditorChoicesSection novels={editorsChoices} />

            {banners && banners.length > 0 && (
                <section className="mt-16">
                    <BannerSlider banners={banners} />
                </section>
            )}

            <section className="mt-16">
                <PodiumSection
                    topNovels={bestSellers}
                    trendingList={trendingNovels}
                />
            </section>

            {/* RAK BUKU BERDASARKAN GENRE (CLEAN ARCHITECTURE) */}
            {genreShelves && genreShelves.length > 0 && (
                <div className="mt-16 flex flex-col gap-12">
                    {genreShelves.map((shelf) => (
                        <GenreShelf key={shelf.slug} shelfData={shelf} />
                    ))}
                </div>
            )}

            <section className="mt-16 mb-8">
                <CommunityTreasure treasures={hiddenTreasures} />
            </section>

            <section className="mt-16 mb-16">
                <GenreGrid tags={trendingGenres} />
            </section>
        </MainLayout>
    );
}
