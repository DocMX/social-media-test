<script setup>
import { defineProps, defineEmits, ref, onMounted } from 'vue';
import axios from '@/axiosClient';
import StoriesModal from './StoriesModal.vue';

const props = defineProps({
    initialStories: Object,
});

const emit = defineEmits(['story-opened']); // opcional por si quieres usarlo desde Home.vue

const stories = ref(props.initialStories || {});
const selectedStory = ref(null);
const showModal = ref(false);
console.log(stories);
const fetchStories = async () => {
    try {
        const response = await axios.get(route("stories"));
        stories.value = response.data;
    } catch (error) {
        console.error("Error fetching stories:", error);
    }
};

const openStory = (story) => {
    selectedStory.value = story;
    showModal.value = true;
    emit('story-opened', story); // opcional
};

const closeModal = () => {
    selectedStory.value = null;
    showModal.value = false;
};

onMounted(() => {
    if (!props.initialStories) {
        fetchStories();
    }
});

</script>

<template>
    <div class="flex gap-3 overflow-x-auto p-3 bg-white dark:bg-slate-900 rounded shadow mb-4">
        <div
            v-for="(userStories, userId) in stories"
            :key="userId"
            class="flex flex-col items-center min-w-[80px] max-w-[80px] cursor-pointer"
            @click="openStory(userStories[0])"
        >
            <img
                :src="userStories[0].user.avatar || '/img/default-avatar.jpg'"
                alt="Avatar"
                class="w-16 h-16 rounded-full object-cover border-2 border-sky-500"
            />
            <p class="text-xs text-center mt-1 truncate w-full">
                {{ userStories[0].user.name }}
            </p>
        </div>
    </div>

    <StoriesModal :story="selectedStory" :show="showModal" @close="closeModal" />
</template>
