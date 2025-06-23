import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dark: {
                    bg: "#18191a",
                    card: "#18191a",
                    text: "#e4e6eb", 
                    muted: "#b0b3b8", 
                    border: "#3a3b3c",
                    accent: "#1877f2",
                    hover: "#3a3b3c", 
                    input: "#3a3b3c",
                    like: "#1d9bf0", 
                    danger: "#f02849",
                    success: "#31a24c", 
                },
            },
        },
    },

    plugins: [forms],
};
