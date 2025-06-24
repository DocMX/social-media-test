<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "@/axiosClient";
import StoriesModal from "./StoriesModal.vue";
import CreateStory from "./CreateStory.vue";
import { usePage } from "@inertiajs/vue3";

const storiesByUser = ref({});
const selectedUserIndex = ref(0);
const currentIndex = ref(0);
const showModal = ref(false);
const showCreateModal = ref(false);

const authUser = usePage().props.auth.user;
console.log(authUser);
const users = computed(() => Object.values(storiesByUser.value));

const fetchStories = async () => {
    try {
        const response = await axios.get(route("stories"));
        storiesByUser.value = response.data;
    } catch (e) {
        console.error("Error al cargar historias:", e);
    }
};
const isFullyViewed = (userStories) => {
    return userStories.every((story) => story.has_been_viewed);
};

const openStory = (userIdx) => {
    selectedUserIndex.value = userIdx;
    currentIndex.value = 0;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const nextStory = () => {
    const currentUserStories = users.value[selectedUserIndex.value];
    if (currentIndex.value < currentUserStories.length - 1) {
        currentIndex.value++;
    } else if (selectedUserIndex.value < users.value.length - 1) {
        selectedUserIndex.value++;
        currentIndex.value = 0;
    } else {
        closeModal();
    }
};

const prevStory = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    } else if (selectedUserIndex.value > 0) {
        selectedUserIndex.value--;
        const prevUserStories = users.value[selectedUserIndex.value];
        currentIndex.value = prevUserStories.length - 1;
    } else {
        closeModal();
    }
};
const deleteStory = async (storyId) => {
    if (!confirm("¿Estás segura de eliminar esta historia?")) return;

    try {
        await axios.delete(route("stories.destroy", storyId));
        fetchStories();
    } catch (error) {
        console.error("Error eliminando historia:", error);
    }
};
onMounted(() => {
    fetchStories();
    setInterval(fetchStories, 30000);
});
</script>

<template>
    <CreateStory v-if="showCreateModal" @close="showCreateModal = false" />

    <div
        class="flex gap-3 overflow-x-auto px-4 py-3 bg-gray-50 dark:bg-dark-bg rounded-xl shadow-sm"
    >
        <!-- Crear historia estilo Facebook -->
        <div
            class="w-28 h-48 rounded-lg bg-white dark:bg-slate-800 border shadow hover:scale-105 transition cursor-pointer relative overflow-hidden"
            @click="showCreateModal = true"
        >
            <img
                :src="authUser?.avatar_path ? '/storage/' + authUser.avatar_path : '/img/default-avatar.jpg'"
                class="w-full h-32 object-cover"
            />
            <div
                class="absolute bottom-0 left-0 right-0 px-2 py-1 text-sm text-center text-gray-700 dark:text-white font-semibold bg-white dark:bg-slate-800"
            >
                Crear historia
            </div>
            <div
                class="absolute top-2 left-2 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center border-4 border-white dark:border-slate-800"
            >
                +
            </div>
        </div>

        <!-- Historias de otros usuarios -->
        <div
            v-for="(userStories, idx) in users"
            :key="userStories[0]?.user?.id || idx"
            @click="openStory(idx)"
            class="w-28 h-48 rounded-lg relative cursor-pointer overflow-hidden shadow hover:scale-105 transition"
        >
            <!-- Imagen fondo -->
            <img
                :src="
                    userStories[0]?.preview_path
                        ? `/storage/${userStories[0].preview_path}`
                        : `/storage/${userStories[0].media_path}`
                "
                class="w-full h-full object-cover"
            />
            <!-- Solo mostrar botón si es del usuario autenticado -->
            <div
                v-if="userStories.length && userStories[0].user?.id === authUser?.id"
                class="absolute top-2 right-2 z-10"
            >
                <button
                    @click.stop="deleteStory(userStories[0].id)"
                    class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700"
                >
                    Eliminar
                </button>
            </div>
            <!-- Filtro oscuro -->
            <div class="absolute inset-0 bg-black/30"></div>

            <!-- Avatar -->
            <img
                :src="userStories[0]?.user?.avatar || '/img/default-avatar.jpg'"
                class="absolute top-2 left-2 w-8 h-8 rounded-full border-2 border-white"
            />

            <!-- Nombre -->
            <div
                class="absolute bottom-2 left-2 right-2 text-white text-xs font-semibold truncate"
            >
                {{ userStories[0]?.user?.name || "Usuario" }}
            </div>
        </div>
    </div>

    <StoriesModal
        v-if="showModal"
        :users="users"
        :selectedUserIndex="selectedUserIndex"
        :currentIndex="currentIndex"
        @update:index="(val) => (currentIndex.value = val)"
        @close="closeModal"
        @next="nextStory"
        @prev="prevStory"
    />
</template>

<style>
.story-ring {
    background: conic-gradient(from 180deg, #d62976, #fa7e1e, #feda75, #d62976);
    padding: 2px;
    border-radius: 9999px;
}

.story-ring.viewed {
    background: #d1d5db; /* gray-300 */
}
</style>
