<script setup>
import { ref, watch, onMounted } from "vue";

const props = defineProps({
  src: String,
  autoplay: { type: Boolean, default: true },
  loop: { type: Boolean, default: true },
});

const isMuted = ref(true);
const videoRef = ref(null);

const toggleMute = () => {
  isMuted.value = !isMuted.value;
  if (videoRef.value) {
    videoRef.value.muted = isMuted.value;
  }
};

watch(isMuted, (val) => {
  if (videoRef.value) {
    videoRef.value.muted = val;
  }
});

onMounted(() => {
  if (videoRef.value) {
    videoRef.value.muted = isMuted.value;
  }
});
</script>

<template>
  <div class="relative rounded-lg overflow-hidden">
    <video
      ref="videoRef"
      :src="src"
      class="w-full max-h-[500px] object-cover rounded-lg"
      :autoplay="autoplay"
      :loop="loop"
      playsinline
    ></video>

    <button
      @click="toggleMute"
      class="absolute bottom-3 right-3 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition"
    >
      <svg
        v-if="isMuted"
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 9v6m-6 0V9m6.707-1.707A1 1 0 0015 6H9a1 1 0 00-.707 1.707L10.586 9H13.414l2.293-2.293zM6 18l12-12"
        />
      </svg>
      <svg
        v-else
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 10l4.553-4.553a1 1 0 00-1.414-1.414L13.586 10H10.414l-2.293-2.293a1 1 0 00-1.707.707V16a1 1 0 001.707.707L10.414 14h3.172l4.553 4.553a1 1 0 001.414-1.414L15 14v-4z"
        />
      </svg>
    </button>
  </div>
</template>
