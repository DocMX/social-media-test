<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import GroupListItems from "@/Components/app/GroupListItems.vue";

defineProps({
    groups: Array,
    recommendedGroups: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div
        class="px-3 bg-white dark:bg-slate-950 rounded border dark:border-slate-900 dark:text-gray-100 h-full py-3 overflow-hidden"
    >
        <!-- Vista en móvil -->
        <div class="block lg:hidden space-y-4">
            <!-- Mis grupos -->
            <Disclosure v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">Mis Grupos</h2>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <GroupListItems :groups="groups" :showControls="true" />
                </DisclosurePanel>
            </Disclosure>

            <!-- Grupos recomendados -->
            <Disclosure v-if="recommendedGroups.length" v-slot="{ open }">
                <DisclosureButton class="w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">Grupos Recomendados</h2>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 transition-all"
                            :class="open ? 'rotate-90 transform' : ''"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>
                    </div>
                </DisclosureButton>
                <DisclosurePanel>
                    <GroupListItems :groups="recommendedGroups" :showControls="false" />
                </DisclosurePanel>
            </Disclosure>
        </div>

        <!-- Vista en escritorio -->
        <div class="h-full overflow-hidden flex-col hidden lg:flex space-y-5">
            <div>
                <div class="flex justify-between">
                    <h2 class="text-xl font-bold mb-2">My Groups</h2>
                </div>
                <GroupListItems :groups="groups" :showControls="true" />
            </div>

            <div v-if="recommendedGroups.length">
                <h2 class="text-xl font-bold mb-2">Grupos Recomendados</h2>
                <GroupListItems :groups="recommendedGroups" :showControls="false" />
            </div>
        </div>
    </div>
</template>
