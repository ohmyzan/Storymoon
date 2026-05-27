import React, { useState, useEffect } from "react";
import axios from "axios";
import SectionTitle from "../UI/SectionTitle";
import EditorNovelCard from "../UI/EditorNovelCard";

export default function EditorChoicesSection({ novels: initialNovels = [] }) {
    const [novels, setNovels] = useState(initialNovels);
    const [isRefreshing, setIsRefreshing] = useState(false);

    // Pastikan data awal dari Inertia diset
    useEffect(() => {
        setNovels(initialNovels);
    }, [initialNovels]);

    const handleRefresh = async () => {
        setIsRefreshing(true);
        try {
            // Ambil data baru secara diam-diam di background
            const response = await axios.get("/api/editor-choices");
            setNovels(response.data);
        } catch (error) {
            console.error("Gagal menyegarkan pilihan editor", error);
        } finally {
            // Beri sedikit delay (300ms) agar efek visual transparansinya terlihat
            setTimeout(() => setIsRefreshing(false), 300);
        }
    };

    return (
        <section className="mt-12">
            <SectionTitle
                title="Pilihan Editor"
                actionType="refresh"
                onRefresh={handleRefresh}
            />

            <div className="max-w-6xl">
                <div
                    className={`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 transition-opacity duration-300 ${isRefreshing ? "opacity-30" : "opacity-100"}`}
                >
                    {novels.slice(0, 6).map((novel) => (
                        <EditorNovelCard key={novel.id} novel={novel} />
                    ))}
                </div>
            </div>
        </section>
    );
}
