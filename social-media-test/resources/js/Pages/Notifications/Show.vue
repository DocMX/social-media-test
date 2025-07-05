<script setup>
import { computed } from "vue";
import { formatTimeAgo, getNotificationConfig } from "../../Components/app/notifications/notificationTypes";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";




const props = defineProps({
  notification: Object,
});

const config = computed(() => {
  const data = props.notification?.data ?? {};
  const conf = getNotificationConfig(data.type); // <- aquí está la clave
  return {
    icon: conf?.icon || 'div',
    iconClass: conf?.iconClass || '',
    getTitle: conf?.getTitle || (() => 'Sin título'),
    getMessage: conf?.getMessage || (() => 'Sin mensaje'),
    getMeta: conf?.getMeta || (() => ({})),
    badge: conf?.badge || { class: '', text: '' },
    getUrl: conf?.getUrl || (() => '#'),
  };
});

</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-xl mx-auto p-4">
      <div class="block px-4 py-3 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="flex items-start">
          <!-- Icono -->
          <div class="flex-shrink-0 pt-0.5 mr-3">
            <component
              :is="config.icon"
              class="h-6 w-6"
              :class="config.iconClass"
            />
          </div>

          <!-- Contenido -->
          <div class="flex-1 min-w-0">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
              {{ config.getTitle(notification.data) }}
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
              {{ config.getMessage(notification.data) }}
            </p>

            <!-- Información adicional -->
            <div v-if="notification.data?.type === 'comment_reply'" class="mt-1 text-xs text-gray-400">
              En respuesta a tu comentario
            </div>

            <div v-if="notification.data?.type === 'group_invitation'" class="mt-2 space-y-1">
              <div
                v-for="(value, key) in config.getMeta(notification.data)"
                :key="key"
                class="flex text-xs text-gray-500 dark:text-gray-400"
              >
                <span class="font-medium w-24">{{ key }}:</span>
                <span>{{ value }}</span>
              </div>
            </div>

            <div class="mt-1 flex items-center justify-between">
              <p class="text-xs text-gray-400">
                {{ formatTimeAgo(notification.created_at) }}
              </p>
              <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                :class="config.badge.class"
              >
                {{ config.badge.text }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


