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
                    bg: "#18191a", // fondo general
                    card: "#242526", // tarjetas, comentarios, etc.
                    text: "#e4e6eb", // texto principal
                    muted: "#b0b3b8", // texto secundario o desactivado
                    border: "#3a3b3c", // bordes sutiles
                    accent: "#1877f2", // color de acción (azul Facebook)
                    hover: "#3a3b3c", // para fondos al hacer hover
                    input: "#3a3b3c", // fondos de inputs
                    like: "#1d9bf0", // tono para "likes" o botones activos
                    danger: "#f02849", // errores o eliminar
                    success: "#31a24c", // mensajes de éxito
                },
            },
        },
    },

    plugins: [forms],
};
