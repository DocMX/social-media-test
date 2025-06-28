<script setup>
import FollowingListItems from "@/Components/app/FollowingListItems.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

defineProps({
    users: Array,
});
</script>

<template>
    <div
        class="px-4 py-4 bg-white dark:bg-dark-card text-gray-800 dark:text-dark-text  rounded-xl shadow-md transition-all"
    >
        <!-- Título para pantallas grandes -->
        <div class="hidden lg:block mb-4">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">My followings</h2>
        </div>

        <!-- Modo Disclosure para móviles -->
        <div class="block lg:hidden">
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full focus:outline-none">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold">My followings</h2>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 transition-transform"
                            :class="{ 'rotate-90': open }"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel class="mt-4 space-y-3">
                    <FollowingListItems :users="users" />
                </DisclosurePanel>
            </Disclosure>
        </div>

        <!-- Lista para pantallas grandes -->
        <div class="hidden lg:flex flex-col gap-3">
            <FollowingListItems :users="users" />
        </div>
    </div>
</template>

<style scoped>
/* Puedes agregar animaciones o hover extra si deseas */
</style>
