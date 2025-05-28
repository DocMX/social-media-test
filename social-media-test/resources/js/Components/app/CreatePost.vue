<script setup>
import { ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import PostModal from "@/Components/app/PostModal.vue";

// Obtener usuario autenticado desde Inertia
const authUser = usePage().props.auth.user;

// Estado para mostrar u ocultar el modal
const showModal = ref(false);

// Datos del nuevo post
const newPost = ref({
    id: null,
    body: '',
    user: authUser
});

// Recibir prop de grupo (si aplica)
defineProps({
    group: {
        type: Object,
        default: null
    }
});

// Función para mostrar el modal
function showCreatePostModal() {
    showModal.value = true;
}

// Función para reiniciar los datos del post cuando se cierra el modal
function resetNewPost() {
    newPost.value = {
        id: null,
        body: '',
        user: authUser
    };
}

// Observar el cierre del modal para limpiar los datos
watch(showModal, (value) => {
    if (!value) {
        resetNewPost();
    }
});
</script>

<template>
    <div class="p-4 bg-white dark:bg-slate-950 rounded-lg border dark:border-slate-900 mb-3">
        <!-- Botón accesible para crear nuevo post -->
        <button
            @click="showCreatePostModal"
            class="py-2 px-3 border-2 border-gray-200 dark:border-slate-900 text-gray-500 rounded-md mb-3 w-full text-left hover:bg-gray-50 dark:hover:bg-slate-900 transition"
        >
            Click here to create new post
        </button>

        <!-- Modal para crear el post -->
        <PostModal
            :post="newPost"
            :group="group"
            v-model="showModal"
        />
    </div>
</template>

<style scoped>
</style>
