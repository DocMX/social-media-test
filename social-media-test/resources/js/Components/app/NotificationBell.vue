<script setup>
import { ref, onMounted } from "vue";

import {
    BellIcon,
    ChatBubbleBottomCenterTextIcon,
    ArrowUturnLeftIcon,
} from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";
import axios from "../../axiosClient";

// Estado del componente
const showDropdown = ref(false);
const dropdownRef = ref(null);
const notifications = ref([]);
const unreadCount = ref(0);

// Obtener notificaciones
const fetchNotifications = async () => {
    try {
        const response = await axios.get(route("notifications"));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        console.error("Error fetching notifications:", error);
    }
};

// Marcar como leída
const markAsRead = async (notificationId) => {
    try {
        await axios.post(route("notifications.markAsRead", { notificationId }));
        await fetchNotifications();
    } catch (error) {
        console.error("Error marking notification as read:", error);
    }
};

// Marcar todas como leídas
const markAllAsRead = async () => {
    try {
        await axios.post(route("notifications.markAllAsRead"));
        await fetchNotifications();
    } catch (error) {
        console.error("Error marking all notifications as read:", error);
    }
};

// Eliminar todas las notificaciones
const clearNotifications = async () => {
    try {
        await axios.post(route("notifications.clear"));
        await fetchNotifications();
    } catch (error) {
        console.error("Error clearing notifications:", error);
    }
};

// Formatear tiempo transcurrido
const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    const intervals = {
        año: 31536000,
        mes: 2592000,
        semana: 604800,
        día: 86400,
        hora: 3600,
        minuto: 60,
        segundo: 1,
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `hace ${interval} ${unit}${interval === 1 ? "" : "s"}`;
        }
    }

    return "justo ahora";
};

// Obtener icono según tipo de notificación
const getNotificationIcon = (notification) => {
    const type = notification.data?.type || "";

    const iconMap = {
        comment_reply: ArrowUturnLeftIcon,
        post_comment: ChatBubbleBottomCenterTextIcon,
        default: BellIcon,
    };

    return iconMap[type] || iconMap.default;
};

// Obtener badge según tipo de notificación
const getNotificationBadge = (notification) => {
    const type = notification.data?.type || "";

    const badgeMap = {
        comment_reply: {
            text: "Respuesta",
            class: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
        },
        post_comment: {
            text: "Comentario",
            class: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        },
        default: {
            text: "Notificación",
            class: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
        },
    };

    return badgeMap[type] || badgeMap.default;
};

// Formatear mensaje de notificación
const formatNotificationMessage = (notification) => {
    if (!notification.data) return "Nueva notificación";

    if (notification.data.type === "comment_reply") {
        return `${notification.data.comment_author} respondió: "${notification.data.comment_excerpt}"`;
    }

    if (notification.data.type === "post_comment") {
        return `${notification.data.comment_author} comentó: "${notification.data.comment_excerpt}"`;
    }

    return notification.data.message || "Nueva notificación";
};

// Obtener título de notificación
const getNotificationTitle = (notification) => {
    if (!notification.data) return "Notificación";

    const titleMap = {
        comment_reply: "Respondieron a tu comentario",
        post_comment: "Nuevo comentario en tu publicación",
        default: "Nueva notificación",
    };

    return (
        notification.data.title ||
        titleMap[notification.data.type] ||
        titleMap.default
    );
};

// Obtener URL de notificación
const getNotificationUrl = (notification) => {
    return notification.data?.link || "#";
};

// Obtener clases CSS para el fondo según tipo
const getNotificationBgClass = (notification) => {
    const type = notification.data?.type || "";
    const isUnread = !notification.read_at;

    if (!isUnread) return "";

    const bgMap = {
        comment_reply: "bg-purple-50 dark:bg-purple-900/30",
        post_comment: "bg-blue-50 dark:bg-blue-900/30",
        default: "bg-gray-50 dark:bg-gray-900/30",
    };

    return bgMap[type] || bgMap.default;
};

// Obtener clases CSS para el icono según tipo
const getNotificationIconClass = (notification) => {
    const type = notification.data?.type || "";

    const iconClassMap = {
        comment_reply:
            "text-purple-500 group-hover:text-purple-600 dark:text-purple-400",
        post_comment:
            "text-blue-500 group-hover:text-blue-600 dark:text-blue-400",
        default: "text-gray-500 group-hover:text-gray-600 dark:text-gray-400",
    };

    return iconClassMap[type] || iconClassMap.default;
};

// Inicialización
onMounted(() => {
    fetchNotifications();
    setInterval(fetchNotifications, 60000); // Actualizar cada minuto
});
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
            ></span>
        </button>

        <!-- Dropdown de notificaciones -->
        <div
            v-show="showDropdown"
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden z-50 border border-gray-200 dark:border-gray-700"
        >
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                <!-- Encabezado -->
                <div
                    class="px-4 py-2 flex justify-between items-center bg-gray-50 dark:bg-gray-700"
                >
                    <p
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Notificaciones
                    </p>
                    <button
                        v-if="unreadCount > 0"
                        @click="markAllAsRead"
                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                        Marcar todas como leídas
                    </button>

                    <button
                        v-if="notifications.length"
                        @click="clearNotifications"
                        class="text-xs text-red-600 dark:text-red-400 hover:underline ml-2"
                    >
                        clear notifications
                    </button>
                </div>

                <!-- Lista de notificaciones -->
                <div
                    v-if="notifications.length"
                    class="max-h-96 overflow-y-auto"
                >
                    <a
                        v-for="notification in notifications"
                        :key="notification.id"
                        @click="markAsRead(notification.id)"
                        :href="getNotificationUrl(notification)"
                        class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition group"
                        :class="getNotificationBgClass(notification)"
                    >
                        <div class="flex items-start">
                            <!-- Icono -->
                            <div class="flex-shrink-0 pt-0.5 mr-3">
                                <component
                                    :is="getNotificationIcon(notification)"
                                    class="h-5 w-5"
                                    :class="
                                        getNotificationIconClass(notification)
                                    "
                                />
                            </div>

                            <!-- Contenido -->
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                                >
                                    {{ getNotificationTitle(notification) }}
                                </p>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    {{
                                        formatNotificationMessage(notification)
                                    }}
                                </p>

                                <!-- Información adicional para respuestas -->
                                <div
                                    v-if="
                                        notification.data?.type ===
                                        'comment_reply'
                                    "
                                    class="mt-1 text-xs text-gray-400"
                                >
                                    En respuesta a tu comentario
                                </div>

                                <!-- Pie de notificación -->
                                <div
                                    class="mt-1 flex items-center justify-between"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{
                                            formatTimeAgo(
                                                notification.created_at
                                            )
                                        }}
                                    </p>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                        :class="
                                            getNotificationBadge(notification)
                                                .class
                                        "
                                    >
                                        {{
                                            getNotificationBadge(notification)
                                                .text
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
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
