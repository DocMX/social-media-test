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
        class="p-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-800 dark:text-dark-text rounded-2xl mb-4 shadow-sm transition-all duration-300 opacity-0 animate-fadeIn"
    >
        <div class="flex items-center gap-3">
            <!-- Avatar -->
            <img
                :src="
                    authUser?.avatar_path
                        ? '/storage/' + authUser.avatar_path
                        : '/img/default-avatar.jpg'
                "
                alt="User Avatar"
                class="w-10 h-10 rounded-full object-cover border"
            />

            <!-- Campo de entrada tipo botón -->
            <button
                @click="showCreatePostModal"
                class="w-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-5 py-2 rounded-full text-left hover:bg-gray-200 dark:hover:bg-gray-500 transition"
            >
                What's on your mind, {{ authUser.name }}?
            </button>
        </div>

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
