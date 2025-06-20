<script setup>
import { defineProps, defineEmits, watch, ref, onMounted } from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
dayjs.extend(relativeTime);

const props = defineProps({
    users: Array,
    selectedUserIndex: Number,
    currentIndex: Number,
});

const emit = defineEmits(["close", "update:index", "next", "prev"]);

const story = ref(null);
const progressArray = ref([]);
let timer = null;

const getCurrentStory = () => {
    const userStories = props.users[props.selectedUserIndex] || [];
    return userStories[props.currentIndex] || null;
};

const isStoryActive = (createdAt) => {
    const created = dayjs(createdAt);
    const now = dayjs();
    return now.diff(created, "hour") < 24;
};

const startTimer = () => {
    clearInterval(timer);
    const userStories = props.users[props.selectedUserIndex] || [];

    progressArray.value = userStories.map((_, idx) => {
        return idx < props.currentIndex
            ? 100
            : idx === props.currentIndex
            ? 0
            : 0;
    });

    timer = setInterval(() => {
        if (progressArray.value[props.currentIndex] < 100) {
            progressArray.value[props.currentIndex]++;
        } else {
            clearInterval(timer);
            emit("next");
        }
    }, 50);
};

const markAsViewed = async (storyId) => {
    try {
        await axios.post(route("stories.view"), { story_id: storyId });
    } catch (error) {
        console.error("Error al registrar vista:", error);
    }
};

watch(
    () => [props.selectedUserIndex, props.currentIndex],
    async () => {
        story.value = getCurrentStory();
        if (story.value && isStoryActive(story.value.created_at)) {
            await markAsViewed(story.value.id);
            startTimer();
        } else {
            emit("next");
        }
    },
    { immediate: true }
);
</script>

<template>
    <div
        v-if="story && isStoryActive(story.created_at)"
        class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center"
    >
        <div
            class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-xl p-4 shadow-lg"
        >
            <button
                @click="emit('close')"
                class="absolute top-2 right-2 text-xl text-gray-500 hover:text-red-500"
            >
                &times;
            </button>

            <div class="flex gap-1 mb-3">
                <div
                    v-for="(s, i) in props.users[props.selectedUserIndex] || []"
                    :key="i"
                    class="flex-1 h-1 bg-gray-400 rounded overflow-hidden"
                >
                    <div
                        class="h-full bg-white transition-all duration-100 linear"
                        :style="{ width: `${progressArray[i] || 0}%` }"
                    ></div>
                </div>
            </div>

            <div class="flex items-center gap-3 mb-4">
                <img
                    :src="story.user?.avatar || '/img/default-avatar.jpg'"
                    class="w-10 h-10 rounded-full object-cover border-2 border-sky-500"
                />
                <div>
                    <span
                        class="block text-sm font-semibold text-gray-800 dark:text-white"
                    >
                        {{ story.user?.name || "Usuario" }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ dayjs(story.created_at).fromNow() }}
                    </span>
                </div>
            </div>

            <div class="flex justify-center">
                <img
                    v-if="story.media_type === 'image'"
                    :src="`/storage/${story.media_path}`"
                    class="rounded-lg w-full max-h-[400px] object-contain cursor-pointer"
                    @click="emit('next')"
                />
                <video
                    v-else-if="story.media_type === 'video'"
                    autoplay
                    playsinline
                    @ended="emit('next')"
                    class="rounded-lg w-full max-h-[400px] object-contain"
                >
                    <source
                        :src="`/storage/${story.media_path}`"
                        type="video/mp4"
                    />
                </video>
            </div>
            <p
                v-if="story.views_count !== null"
                class="text-xs text-gray-400 mt-2 text-center"
            >
                {{ story.views_count }} views
            </p>

            <p
                v-if="story.caption"
                class="mt-3 text-sm text-center text-gray-500 dark:text-gray-300"
            >
                {{ story.caption }}
            </p>

            <div class="flex justify-between mt-4 text-sm text-blue-500">
                <button @click="emit('prev')">&laquo; Anterior</button>
                <button @click="emit('next')">Siguiente &raquo;</button>
            </div>
        </div>
    </div>
</template>
