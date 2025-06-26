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
                    gray: {
                        50: "oklch(0.984 0.003 247.858)",
                        100: "oklch(0.968 0.007 247.896)",
                        200: "oklch(0.929 0.013 255.508)",
                        300: "oklch(0.869 0.022 252.894)",
                        400: "oklch(0.704 0.04 256.788)",
                        500: "oklch(0.554 0.046 257.417)",
                        600: "oklch(0.446 0.043 257.281)",
                        700: "oklch(0.372 0.044 257.287)",
                        800: 'oklch(26.8% 0.007 34.298)',
                        900: "oklch(0.208 0.042 265.755)",
                        950: "oklch(0.129 0.042 264.695)",
                    },
                },
            },
        },
    },

    plugins: [forms],
};
