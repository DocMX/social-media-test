<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/axiosClient';
import StoriesModal from './StoriesModal.vue';

const storiesByUser = ref({});
const selectedUserIndex = ref(0);
const currentIndex = ref(0);
const showModal = ref(false);

const users = computed(() => Object.values(storiesByUser.value));

const fetchStories = async () => {
  try {
    const response = await axios.get(route('stories'));
    storiesByUser.value = response.data;
  } catch (e) {
    console.error('Error al cargar historias:', e);
  }
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

onMounted(fetchStories);
</script>

<template>
  <div class="flex gap-3 overflow-x-auto p-3 bg-white dark:bg-slate-900 rounded shadow mb-4">
    <div
      v-for="(userStories, idx) in users"
      :key="userStories[0]?.user?.id || idx"
      class="flex flex-col items-center min-w-[80px] max-w-[80px] cursor-pointer"
      @click="openStory(idx)"
    >
      <img
        :src="userStories[0]?.user?.avatar || '/img/default-avatar.jpg'"
        class="w-16 h-16 rounded-full object-cover border-2 border-sky-500"
      />
      <p class="text-xs text-white text-center mt-1 truncate w-full">
        {{ userStories[0]?.user?.name || 'Usuario' }}
      </p>
    </div>
  </div>

  <StoriesModal
    v-if="showModal"
    :users="users"
    :selectedUserIndex="selectedUserIndex"
    :currentIndex="currentIndex"
    @update:index="(val) => currentIndex.value = val"
    @close="closeModal"
    @next="nextStory"
    @prev="prevStory"
  />
</template>