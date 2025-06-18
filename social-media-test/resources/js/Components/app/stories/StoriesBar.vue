<script setup>
import { onMounted, ref } from "vue";
import axios from "@/axiosClient";

const stories = ref({});

const fetchStories = async () => {
    try {
        const response = await axios.get(route("stories"));
        stories.value = response.data;
    } catch (error) {
        console.error("Error fetching stories:", error);
    }
};

onMounted(fetchStories);
</script>

<template>
    <div
        class="flex gap-3 overflow-x-auto p-3 bg-white dark:bg-slate-900 rounded shadow mb-4"
    >
        <div
            v-for="(userStories, userId) in stories"
            :key="userId"
            class="flex flex-col items-center min-w-[80px] max-w-[80px] cursor-pointer"
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
</template>
