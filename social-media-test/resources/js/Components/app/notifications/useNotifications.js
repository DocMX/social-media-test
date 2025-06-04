import { ref, onMounted } from "vue";
import axios from "../../../axiosClient";

export default function useNotifications() {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const isLoading = ref(false);

    const fetchNotifications = async () => {
        try {
            isLoading.value = true;
            const response = await axios.get(route("notifications"));
            notifications.value = response.data.notifications;
            unreadCount.value = response.data.unread_count;
        } catch (error) {
            console.error("Error fetching notifications:", error);
        } finally {
            isLoading.value = false;
        }
    };

    const markAsRead = async (notificationId) => {
        try {
            await axios.post(route("notifications.markAsRead", { id: notificationId }));
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
        const intervalId = setInterval(fetchNotifications, 60000);
        
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
        clearNotifications
    };
}