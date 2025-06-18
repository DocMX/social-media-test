<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    story: Object,
    show: Boolean,
});

const emit = defineEmits(['close']);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/70 flex items-center justify-center z-50"
    >
        <div
            class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-lg max-w-md w-full relative"
        >
            <!-- Botón de cerrar -->
            <button
                @click="emit('close')"
                class="absolute top-2 right-2 text-gray-600 hover:text-red-500 text-xl"
            >
                &times;
            </button>

            <div v-if="story">
                <!-- Header con avatar y nombre -->
                <div class="flex items-center gap-3 mb-4">
                    <img
                        :src="story.user.avatar || '/img/default_avatar.webp'"
                        alt="Avatar"
                        class="w-10 h-10 rounded-full object-cover border-2 border-sky-500"
                    />
                    <h2 class="text-md font-semibold text-gray-800 dark:text-white">
                        {{ story.user.name }}
                    </h2>
                </div>

                <!-- Imagen o video -->
                <div class="flex justify-center">
                    <img
                        v-if="story.media_type === 'image'"
                        :src="`/storage/${story.media_path}`"
                        class="rounded-lg w-full max-h-[400px] object-contain"
                    />

                    <video
                        v-else-if="story.media_type === 'video'"
                        controls
                        class="rounded-lg w-full max-h-[400px] object-contain"
                    >
                        <source :src="`/storage/${story.media_path}`" type="video/mp4" />
                        Tu navegador no soporta videos.
                    </video>
                </div>

                <!-- Descripción -->
                <p
                    v-if="story.caption"
                    class="mt-3 text-sm text-center text-gray-500 dark:text-gray-300"
                >
                    {{ story.caption }}
                </p>
            </div>
        </div>
    </div>
</template>
