<script setup>
import { isImage } from "@/helpers.js";
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PaperClipIcon, HeartIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";

defineProps({
    photos: Array, // [{ url, name, likes }]
});

const currentPhotoIndex = ref(0);
const showModal = ref(false);

function openPhoto(index) {
    currentPhotoIndex.value = index;
    showModal.value = true;
}
</script>

<template>
    <div class="grid gap-3 grid-cols-2 sm:grid-cols-3">
        <template v-for="(attachment, ind) in photos" :key="ind">
            <div
                @click="openPhoto(ind)"
                class="group aspect-square relative bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition transform hover:scale-105"
            >
                <!-- Botón de descarga -->
                <a
                    @click.stop
                    :href="route('post.download', attachment)"
                    class="absolute top-2 right-2 z-20 opacity-0 group-hover:opacity-100 transition-all bg-gray-700 text-white rounded-full p-1 hover:bg-gray-800"
                >
                    <ArrowDownTrayIcon class="w-4 h-4" />
                </a>

                <!-- Imagen -->
                <img
                    v-if="isImage(attachment)"
                    :src="attachment.url"
                    alt="Attachment"
                    class="w-full h-full object-cover"
                />

                <!-- Archivo genérico -->
                <div
                    v-else
                    class="flex flex-col justify-center items-center h-full text-gray-500"
                >
                    <PaperClipIcon class="w-10 h-10 mb-2" />
                    <small class="text-xs truncate px-2">{{
                        attachment.name
                    }}</small>
                </div>

                <!-- Likes -->
                <div
                    v-if="attachment.likes !== undefined"
                    class="absolute bottom-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full flex items-center gap-1 z-10"
                >
                    <HeartIcon class="w-4 h-4 text-pink-400" />
                    <span>{{ attachment.likes }}</span>
                </div>
            </div>
        </template>
    </div>

    <!-- Mensajes vacíos -->
    <div
        v-if="!photos"
        class="py-8 text-center text-gray-600 dark:text-gray-100"
    >
        You don't have permission to view these photos.
    </div>
    <div
        v-else-if="!photos.length"
        class="py-8 text-center text-gray-600 dark:text-gray-100"
    >
        There are no photos.
    </div>

    <!-- Modal de vista previa -->
    <AttachmentPreviewModal
        :attachments="photos || []"
        v-model:index="currentPhotoIndex"
        v-model="showModal"
    />
</template>
