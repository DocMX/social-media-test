<script setup>
defineProps({
    unreadCount: {
        type: Number,
        default: 0
    },
    notificationsCount: {
        type: Number,
        default: 0
    }
});

defineEmits(['markAllAsRead', 'clearNotifications']);
</script>

<template>
    <div class="px-4 py-2 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Notificaciones
        </p>
        <div>
            <button
                v-if="unreadCount > 0"
                @click="$emit('markAllAsRead')"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
            >
                Marcar todas como leídas
            </button>

            <button
                v-if="notificationsCount > 0"
                @click="$emit('clearNotifications')"
                class="text-xs text-red-600 dark:text-red-400 hover:underline ml-2"
            >
                Limpiar todo
            </button>
        </div>
    </div>
</template>