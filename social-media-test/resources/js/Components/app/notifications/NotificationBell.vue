<script setup>
import { ref } from 'vue';
import { BellIcon } from '@heroicons/vue/24/outline';
import useNotifications from './useNotifications';
import NotificationHeader from './NotificationHeader.vue';
import NotificationItem from './NotificationItem.vue';

const showDropdown = ref(false);
const dropdownRef = ref(null);

const {
    notifications,
    unreadCount,
    isLoading,
    markAsRead,
    markAllAsRead,
    clearNotifications
} = useNotifications();

const handleNotificationClick = async (notificationId) => {
    await markAsRead(notificationId);
};
</script>

<template>
    <div class="relative">
        <!-- Botón del icono de campana -->
        <button
            @click="showDropdown = !showDropdown"
            class="p-1 rounded-full text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none relative"
            aria-label="Notificaciones"
        >
            <BellIcon class="h-6 w-6" />
            <span
                v-if="unreadCount > 0"
                class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"
            />
        </button>

        <!-- Dropdown de notificaciones -->
        <div
            v-show="showDropdown"
            ref="dropdownRef"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden z-50 border border-gray-200 dark:border-gray-700"
        >
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <NotificationHeader
                    :unread-count="unreadCount"
                    :notifications-count="notifications.length"
                    @mark-all-as-read="markAllAsRead"
                    @clear-notifications="clearNotifications"
                />

                <!-- Lista de notificaciones -->
                <div
                    v-if="notifications.length"
                    class="max-h-96 overflow-y-auto"
                >
                    <NotificationItem
                        v-for="notification in notifications"
                        :key="notification.id"
                        :notification="notification"
                        @mark-as-read="handleNotificationClick"
                    />
                </div>

                <div v-else class="px-4 py-6 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No hay notificaciones nuevas
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>