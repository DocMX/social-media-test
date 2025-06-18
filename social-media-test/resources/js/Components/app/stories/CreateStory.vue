<script setup>
import { ref } from 'vue';
import axios from '@/axiosClient';

const image = ref(null);
const uploading = ref(false);
const previewUrl = ref(null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        image.value = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const uploadStory = async () => {
    if (!image.value) return;

    const formData = new FormData();
    formData.append('media', image.value); // 👈 CAMBIO AQUÍ

    uploading.value = true;

    try {
        await axios.post(route('stories.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        alert('¡Historia publicada!');
        image.value = null;
        previewUrl.value = null;
    } catch (error) {
        console.error('Error al subir historia:', error);
        if (error.response?.status === 422) {
            console.warn('Errores de validación:', error.response.data.errors);
        }
        alert('Error al subir historia.');
    } finally {
        uploading.value = false;
    }
};
</script>

<template>
    <div class="bg-white dark:bg-slate-900 p-4 rounded shadow mb-4">
        <h2 class="text-lg font-semibold mb-2">Crear Historia</h2>

        <input type="file" accept="image/*,video/*" @change="handleFileChange" />

        <div v-if="previewUrl" class="mt-3">
            <img v-if="image?.type?.includes('image')" :src="previewUrl" class="rounded w-32 h-32 object-cover" />
            <video v-else controls class="w-32 h-32 rounded">
                <source :src="previewUrl" type="video/mp4" />
            </video>
        </div>

        <button
            @click="uploadStory"
            :disabled="uploading || !image"
            class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
        >
            {{ uploading ? 'Subiendo...' : 'Publicar historia' }}
        </button>
    </div>
</template>
