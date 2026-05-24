/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.jsx",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: "#f5f3ff",
                    100: "#ede9fe",
                    200: "#ddd6fe",
                    300: "#c4b5fd",
                    400: "#a78bfa",
                    500: "#8b5cf6",
                    600: "#7c3aed", // Ungu Dominan Storymoon
                    700: "#6d28d9",
                    800: "#5b21b6",
                    900: "#4c1d95",
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', "sans-serif"], // Font UI
                serif: ['"Literata"', "serif"], // Font Membaca Novel
            },
        },
    },
    plugins: [],
};
