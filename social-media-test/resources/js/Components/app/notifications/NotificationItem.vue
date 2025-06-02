<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { formatTimeAgo, getNotificationConfig } from "./notificationTypes";

const props = defineProps({
    notification: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["markAsRead"]);

const config = computed(() =>
    getNotificationConfig(props.notification.data?.type)
);

const handleClick = async () => {
    await emit("markAsRead", props.notification.id);
    router.visit(config.value.getUrl(props.notification.data));
};
</script>

<template>
    <div
        @click="handleClick"
        class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition group cursor-pointer"
        :class="[!notification.read_at ? config.bgClass : '']"
    >
        <div class="flex items-start">
            <!-- Icono -->
            <div class="flex-shrink-0 pt-0.5 mr-3">
                <component
                    :is="config.icon"
                    class="h-5 w-5"
                    :class="config.iconClass"
                />
            </div>

            <!-- Contenido -->
            <div class="flex-1 min-w-0">
                <p
                    class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                >
                    {{ config.getTitle(props.notification.data) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ config.getMessage(props.notification.data) }}
                </p>

                <!-- Información adicional para respuestas -->
                <div
                    v-if="notification.data?.type === 'comment_reply'"
                    class="mt-1 text-xs text-gray-400"
                >
                    En respuesta a tu comentario
                </div>

                <!-- Información sobre invitacion a grupo -->
                <div
                    v-if="notification.data?.type === 'group_invitation'"
                    class="mt-2 space-y-1"
                >
                    <div
                        v-for="(value, key) in config.getMeta(
                            notification.data
                        )"
                        :key="key"
                        class="flex text-xs text-gray-500 dark:text-gray-400"
                    >
                        <span class="font-medium w-24">{{ key }}:</span>
                        <span>{{ value }}</span>
                    </div>
                </div>
                
                <!-- Pie de notificación -->
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
</template>
