<script setup>
import { ref } from "vue";
import axios from "@/axiosClient";


const image = ref(null);
const uploading = ref(false);
const previewUrl = ref(null);
const caption = ref("");
const videoDuration = ref(null);
const emit = defineEmits(['close']);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        image.value = file;
        previewUrl.value = URL.createObjectURL(file);
        if (file.type.includes("video")) {
            const video = document.createElement("video");
            video.preload = "metadata";
            video.onloadedmetadata = () => {
                videoDuration.value = video.duration.toFixed(2);
            };
            video.src = previewUrl.value;
        } else {
            videoDuration.value = null;
        }
    }
};

const cancelUpload = () => {
    image.value = null;
    previewUrl.value = null;
    caption.value = "";
    videoDuration.value = null;
   
    emit('close');
};

const uploadStory = async () => {
    if (!image.value) return;

    const formData = new FormData();
    formData.append("media", image.value);
    formData.append("caption", caption.value);
    uploading.value = true;

    try {
        await axios.post(route("stories.store"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        window.dispatchEvent(
            new CustomEvent("toast", {
                detail: { type: "success", message: "¡Historia publicada exitosamente!" },
            })
        );

        cancelUpload();
    } catch (error) {
        console.error("Error al subir historia:", error);
        alert("Error al subir historia.");
    } finally {
        uploading.value = false;
    }
};
</script>

<template>
    <div>
        <!-- Modal -->
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white dark:bg-slate-900 rounded-lg shadow-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Nueva Historia</h2>
                    <button @click="cancelUpload" class="text-gray-500 hover:text-gray-800">&times;</button>
                </div>

                <input
                    type="file"
                    accept="image/*,video/*"
                    @change="handleFileChange"
                    class="mb-2 w-full"
                />

                <div v-if="previewUrl" class="mt-3 space-y-2 text-center">
                    <img
                        v-if="image?.type?.includes('image')"
                        :src="previewUrl"
                        class="rounded w-32 h-32 mx-auto object-cover"
                    />

                    <div v-else>
                        <video controls class="w-32 h-32 mx-auto rounded">
                            <source :src="previewUrl" type="video/mp4" />
                        </video>
                        <p class="text-xs text-gray-500 mt-1">
                            Duración: {{ videoDuration }} segundos
                        </p>
                    </div>
                </div>

                <textarea
                    v-model="caption"
                    placeholder="Escribe una descripción..."
                    class="w-full mt-2 p-2 border rounded dark:bg-slate-800 dark:text-white"
                />

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        @click="cancelUpload"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500"
                        v-if="previewUrl || caption"
                    >
                        Cancelar
                    </button>

                    <button
                        @click="uploadStory"
                        :disabled="uploading || !image"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ uploading ? "Subiendo..." : "Publicar" }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
