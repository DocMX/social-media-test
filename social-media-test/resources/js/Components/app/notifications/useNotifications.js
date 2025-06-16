import { ref, onMounted } from "vue";
import axios from "../../../axiosClient";

const audio = new Audio("/sounds/notification.mp3");
audio.volume = 0.5;

export default function useNotifications() {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const isLoading = ref(false);

    const fetchNotifications = async () => {
        try {
            isLoading.value = true;
            const response = await axios.get(route("notifications"));
            const oldIds = notifications.value.map((n) => n.id);
            const newIds = response.data.notifications.map((n) => n.id);

            // Detectar si hay alguna nueva que no estaba antes
            const hasNew = newIds.some((id) => !oldIds.includes(id));
            notifications.value = response.data.notifications;
            unreadCount.value = response.data.unread_count;
            if (hasNew) {
                audio
                    .play()
                    .catch((err) =>
                        console.warn("No se pudo reproducir el sonido:", err)
                    );
            }
        } catch (error) {
            console.error("Error fetching notifications:", error);
        } finally {
            isLoading.value = false;
        }
    };

    const markAsRead = async (notificationId) => {
        try {
            await axios.post(
                route("notifications.markAsRead", { id: notificationId })
            );
            await fetchNotifications();
        } catch (error) {
            console.error("Error marking notification as read:", error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await axios.post(route("notifications.markAllAsRead"));
            await fetchNotifications();
        } catch (error) {
            console.error("Error marking all notifications as read:", error);
        }
    };

    const clearNotifications = async () => {
        try {
            await axios.post(route("notifications.clear"));
            await fetchNotifications();
        } catch (error) {
            console.error("Error clearing notifications:", error);
        }
    };

    onMounted(() => {
        fetchNotifications();
        const intervalId = setInterval(fetchNotifications, 10000);

        // Limpiar intervalo al desmontar
        return () => clearInterval(intervalId);
    });

    return {
        notifications,
        unreadCount,
        isLoading,
        fetchNotifications,
        markAsRead,
        markAllAsRead,
        clearNotifications,
    };
}
