<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    messages: Array,
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Mensajes" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                📬 Mensajes
            </h2>
        </template>

        <div class="py-10 px-4 max-w-7xl mx-auto">
            <div class="space-y-4">
                <div
                    v-for="(msg, index) in messages"
                    :key="index"
                    class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition cursor-pointer border border-gray-200 dark:border-gray-700"
                >
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-800 dark:text-white">
                            {{ msg.from }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ msg.date }}
                        </span>
                    </div>
                    <div class="text-indigo-600 dark:text-indigo-400 font-medium">
                        {{ msg.subject }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 truncate">
                        {{ msg.preview }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
