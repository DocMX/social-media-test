<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  getNotificationConfig,
  formatTimeAgo
} from '@/Components/app/notifications/notificationTypes';
import useNotifications from "@/Components/app/notifications/useNotifications";
import NotificationHeader from "@/Components/app/notifications/NotificationHeader.vue";
import NotificationItem from '@/Components/app/notifications/NotificationItem.vue';

const props = defineProps({
  notifications: Array,
  unreadCount: Number,
});
const {
  notifications,
  unreadCount,
  markAsRead,
  markAllAsRead,
  clearNotifications,
} = useNotifications();

</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-xl mx-auto p-4">
      <NotificationHeader
        :unreadCount="unreadCount"
        :notificationsCount="notifications.length"
        @markAllAsRead="markAllAsRead"
        @clearNotifications="clearNotifications"
      />

      <div v-if="notifications.length === 0" class="text-center text-gray-500 mt-4">
        No new notifications
      </div>

      <ul v-else class="space-y-2 mt-4">
        <li
          v-for="notification in notifications"
          :key="notification.id"
        >
          <NotificationItem
            :notification="notification"
            @markAsRead="markAsRead"
          />
        </li>
      </ul>
    </div>
  </AuthenticatedLayout>
</template>
