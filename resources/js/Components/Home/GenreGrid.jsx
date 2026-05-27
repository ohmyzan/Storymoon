import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "../UI/SectionTitle";

export default function GenreGrid({ tags = [] }) {
    if (!tags || tags.length === 0) return null;

    return (
        <div className="flex flex-col">
            <div className="mb-2">
                <h2 className="text-xl md:text-2xl font-bold text-gray-900 font-sans tracking-tight">
                    Tag Populer
                </h2>
            </div>
            <div className="flex flex-wrap gap-3 mt-4">
                {tags.map((tag, index) => (
                    <Link
                        key={index}
                        href={`/explore?genre=${encodeURIComponent(tag)}`}
                        className="px-4 py-2 bg-primary-700 text-white text-xs md:text-sm font-semibold rounded-lg hover:bg-primary-800 hover:-translate-y-0.5 transition-all duration-200 shadow-sm hover:shadow-md"
                    >
                        {tag}
                    </Link>
                ))}
            </div>
        </div>
    );
}
