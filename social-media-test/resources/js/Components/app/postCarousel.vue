<script setup>
import { ref } from "vue";
import { isImage } from "@/helpers.js";
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    media: Array // [{ url, type }]
});

const currentIndex = ref(0);

function prev() {
    if (currentIndex.value > 0) currentIndex.value--;
}

function next() {
    if (currentIndex.value < props.media.length - 1) currentIndex.value++;
}
</script>

<template>
    <div class="relative w-full aspect-square overflow-hidden rounded-lg shadow">
        <!-- Medios -->
        <div
            class="w-full h-full flex transition-transform duration-300 ease-in-out"
            :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
        >
            <div
                v-for="(item, i) in media"
                :key="i"
                class="w-full h-full flex-shrink-0"
            >
                <img
                    v-if="isImage(item)"
                    :src="item.url"
                    class="w-full h-full object-cover"
                />
                <video
                    v-else
                    :src="item.url"
                    controls
                    class="w-full h-full object-cover"
                ></video>
            </div>
        </div>

        <!-- Flechas -->
        <button
            v-if="currentIndex > 0"
            @click.stop="prev"
            class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-1 rounded-full"
        >
            <ChevronLeftIcon class="w-5 h-5" />
        </button>
        <button
            v-if="currentIndex < media.length - 1"
            @click.stop="next"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-1 rounded-full"
        >
            <ChevronRightIcon class="w-5 h-5" />
        </button>

        <!-- Indicadores -->
        <div
            v-if="media.length > 1"
            class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1"
        >
            <span
                v-for="(item, i) in media"
                :key="i"
                class="w-2 h-2 rounded-full"
                :class="[
                    i === currentIndex ? 'bg-white' : 'bg-white/50',
                    'transition-all duration-300'
                ]"
            ></span>
        </div>
    </div>
</template>
