<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "@/axiosClient";
import StoriesModal from "./StoriesModal.vue";
import CreateStory from "./CreateStory.vue";

const storiesByUser = ref({});
const selectedUserIndex = ref(0);
const currentIndex = ref(0);
const showModal = ref(false);
const showCreateModal = ref(false);

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

onMounted(() => {
    fetchStories();

    // Refrescar cada 30 segundos
    setInterval(fetchStories, 30000);
});
</script>

<template>
    <!-- Modal Crear Historia -->
    <CreateStory v-if="showCreateModal" @close="showCreateModal = false" />

    <!-- Barra de historias -->
    <div
        class="flex gap-3 overflow-x-auto p-3 bg-white dark:bg-slate-900 rounded shadow mb-4"
    >
        <!-- Botón Crear Historia -->
        <div
            class="flex flex-col items-center min-w-[80px] max-w-[80px] cursor-pointer"
            @click="showCreateModal = true"
        >
            <div
                class="w-16 h-16 rounded-full border-2 border-blue-600 flex items-center justify-center bg-blue-100 hover:bg-blue-200"
            >
                <svg
                    class="w-6 h-6 text-blue-600"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
            </div>
            <p
                class="text-xs mt-1 text-center text-blue-600 dark:text-blue-400"
            >
                Crear
            </p>
        </div>

        <!-- Historias de usuarios -->
        <div
            v-for="(userStories, idx) in users"
            :key="userStories[0]?.user?.id || idx"
            class="flex flex-col items-center min-w-[80px] max-w-[80px] cursor-pointer"
            @click="openStory(idx)"
        >
            <div
                :class="[
                    'p-[2px] rounded-full transition-all duration-500',
                    isFullyViewed(userStories) ? 'bg-gray-300' : 'story-ring',
                ]"
            >
                <img
                    :src="
                        userStories[0]?.user?.avatar ||
                        '/img/default-avatar.jpg'
                    "
                    class="w-16 h-16 rounded-full object-cover"
                />
            </div>

            <p
                class="text-xs text-center mt-1 truncate w-full text-gray-800 dark:text-white"
            >
                {{ userStories[0]?.user?.name || "Usuario" }}
            </p>
        </div>
    </div>

    <!-- Modal Ver historias -->
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
