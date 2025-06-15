<script setup>
import { ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import PostModal from "@/Components/app/PostModal.vue";

// Usuario autenticado
const authUser = usePage().props.auth.user;

// Estado modal
const showModal = ref(false);

// Nuevo post
const newPost = ref({
    id: null,
    body: "",
    user: authUser,
});

// Recibir grupo como prop
defineProps({
    group: {
        type: Object,
        default: null,
    },
});

// Sonido al abrir modal
const openSound = new Audio("/sounds/modal-open.wav");

function showCreatePostModal() {
    openSound.play();
    showModal.value = true;
}

// Resetear cuando se cierra
function resetNewPost() {
    newPost.value = {
        id: null,
        body: "",
        user: authUser,
    };
}

watch(showModal, (value) => {
    if (!value) {
        
        resetNewPost();
    }
});
</script>

<template>
    <div
        class="p-4 bg-white dark:bg-slate-950 rounded-lg border dark:border-slate-900 mb-3 transition-opacity duration-300 opacity-0 animate-fadeIn"
    >
        <button
            @click="showCreatePostModal"
            class="py-2 px-3 border-2 border-gray-200 dark:border-slate-900 text-gray-500 rounded-md mb-3 w-full text-left hover:bg-gray-50 dark:hover:bg-slate-900 transition transform hover:scale-[1.01] active:scale-[0.98]"
        >
            Click here to create new post
        </button>

        <PostModal :post="newPost" :group="group" v-model="showModal" />
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.4s ease forwards;
}
</style>
