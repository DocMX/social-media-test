import {
    BellIcon,
    ChatBubbleBottomCenterTextIcon,
    ArrowUturnLeftIcon,
    UserIcon,
    DocumentTextIcon,
    UserGroupIcon,
} from "@heroicons/vue/24/outline";

export const notificationTypes = {
    comment_reply: {
        icon: ArrowUturnLeftIcon,
        badge: {
            text: "Respuesta",
            class: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
        },
        bgClass: "bg-purple-50 dark:bg-purple-900/30",
        iconClass:
            "text-purple-500 group-hover:text-purple-600 dark:text-purple-400",
        getTitle: () => "Respondieron a tu comentario",
        getMessage: (data) =>
            `${data.comment_author} respondió: "${data.comment_excerpt}"`,
        getUrl: (data) =>
            route("post.viewIndividual", {
                post: data.post_slug || data.post_id,
                id: data.post_id,
            }),
    },
    post_comment: {
        icon: ChatBubbleBottomCenterTextIcon,
        badge: {
            text: "Comentario",
            class: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        },
        bgClass: "bg-blue-50 dark:bg-blue-900/30",
        iconClass: "text-blue-500 group-hover:text-blue-600 dark:text-blue-400",
        getTitle: () => "Nuevo comentario en tu publicación",
        getMessage: (data) =>
            `${data.comment_author} comentó: "${data.comment_excerpt}"`,
        getUrl: (data) =>
            route("post.viewIndividual", {
                post: data.post_slug || data.post_id,
                id: data.post_id,
            }),
    },
    post_created: {
        icon: DocumentTextIcon, // Asegúrate de importar este icono
        badge: {
            text: "Nuevo Post",
            class: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        },
        bgClass: "bg-blue-50 dark:bg-blue-900/30",
        iconClass: "text-blue-500 group-hover:text-blue-600 dark:text-blue-400",
        getTitle: (data) =>
            data.group_id
                ? `Nuevo post en ${data.group_name || "el grupo"}`
                : "Nuevo post publicado",
        getMessage: (data) =>
            `${data.author.name} publicó: ${
                data.post_title || "un nuevo post"
            }`,
        getUrl: (data) =>
            route("post.view", {
                post: data.post_slug || data.post_id,
                id: data.post_id,
            }),
        getMeta: (data) => {
            const meta = {
                Autor: data.author.name,
                Fecha: formatDateTime(data.created_at),
            };
            if (data.group_id) {
                meta["Grupo"] = data.group_name;
            }
            return meta;
        },
    },
    user_followed: {
        icon: UserIcon,
        badge: {
            text: "Seguidor",
            class: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        },
        bgClass: "bg-green-50 dark:bg-green-900/30",
        iconClass:
            "text-green-500 group-hover:text-green-600 dark:text-green-400",
        getTitle: () => "Nuevo seguidor",
        getMessage: (data) => `${data.user_username} empezó a seguirte`,
        getUrl: (data) => route("profile", data.user_username),
    },
    post_reaction: {
        icon: BellIcon,
        badge: {
            text: "Reacción",
            class: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        },
        bgClass: "bg-yellow-50 dark:bg-yellow-900/30",
        iconClass:
            "text-yellow-500 group-hover:text-yellow-600 dark:text-yellow-400",
        getTitle: () => "Le dio me gusta a tu publicación",
        getMessage: (data) =>
            `${data.user_username} Dio me gusta a tu publicación "${data.post_excerpt}"`,
        getUrl: (data) =>
            route("post.viewIndividual", {
                post: data.post_slug || data.post_id,
                id: data.post_id,
            }),
    },
    comment_reaction: {
        icon: BellIcon,
        badge: {
            text: "Reacción",
            class: "bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200",
        },
        bgClass: "bg-pink-50 dark:bg-pink-900/30",
        iconClass: "text-pink-500 group-hover:text-pink-600 dark:text-pink-400",
        getTitle: () => "Le gustó tu comentario",
        getMessage: (data) =>
            `${data.user_username} reaccionó a tu comentario: "${data.comment_excerpt}"`,
        getUrl: (data) =>
            route("post.viewIndividual", {
                post: data.post_slug || data.post_id,
                id: data.post_id,
            }),
    },

    group_invitation: {
        icon: UserGroupIcon,
        badge: {
            text: "Invitación",
            class: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200",
        },
        bgClass: "bg-indigo-50 dark:bg-indigo-900/30",
        iconClass:
            "text-indigo-500 group-hover:text-indigo-600 dark:text-indigo-400",
        getTitle: () => "Invitación a grupo",
        getMessage: (data) => `Te han invitado al grupo "${data.group_name}"`,
        getUrl: (data) => data.invitation_url || "#",
        getMeta: (data) => ({
            "Tiempo válido": `${data.hours_valid} horas`,
            "Invitado por": data.invited_by || "Administrador",
        }),
    },
    group_join_request: {
        icon: UserGroupIcon,
        badge: {
            text: "Solicitud",
            class: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200",
        },
        bgClass: "bg-indigo-50 dark:bg-indigo-900/30",
        iconClass:
            "text-indigo-500 group-hover:text-indigo-600 dark:text-indigo-400",
        getTitle: () => "Solicitud para unirse al grupo",
        getMessage: (data) =>
            `Usuario "${data.user_name}" quiere unirse a "${data.group_name}"`,
        getUrl: (data) => data.action_url || "#",
        getMeta: (data) => ({
            Grupo: data.group_name,
            Solicitante: data.user_name,
        }),
    },
    group_invitation_approved: {
        icon: UserGroupIcon,
        badge: {
            text: "Nuevo Miembro",
            class: "bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200",
        },
        bgClass: "bg-teal-50 dark:bg-teal-900/30",
        iconClass: "text-teal-500 group-hover:text-teal-600 dark:text-teal-400",
        getTitle: () => "Nuevo miembro en grupo",
        getMessage: (data) =>
            `El usuario ${data.user_name} se unió al grupo "${data.group_name}"`,
        getUrl: (data) => data.group_url,
        getMeta: (data) => ({
            Grupo: data.group_name,
            Usuario: data.user_name,
        }),
    },
    group_request_approved: {
        icon: UserGroupIcon,
        badge: {
            text: "Solicitud Aprobada",
            class: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        },
        bgClass: "bg-green-50 dark:bg-green-900/30",
        iconClass:
            "text-green-500 group-hover:text-green-600 dark:text-green-400",
        getTitle: () => "Solicitud aprobada",
        getMessage: (data) =>
            `${data.processed_by_name} aprobó tu solicitud al grupo "${data.group_name}"`,
        getUrl: (data) => data.group_url,
        getMeta: (data) => ({
            Grupo: data.group_name,
            "Procesado por": data.processed_by_name,
            Fecha: formatDateTime(data.processed_at),
        }),
    },
    group_request_rejected: {
        icon: UserGroupIcon,
        badge: {
            text: "Solicitud Rechazada",
            class: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        },
        bgClass: "bg-red-50 dark:bg-red-900/30",
        iconClass: "text-red-500 group-hover:text-red-600 dark:text-red-400",
        getTitle: () => "Solicitud rechazada",
        getMessage: (data) =>
            `${data.processed_by_name} rechazó tu solicitud al grupo "${data.group_name}"`,
        getUrl: (data) => data.group_url,
        getMeta: (data) => ({
            Grupo: data.group_name,
            "Procesado por": data.processed_by_name,
            Fecha: formatDateTime(data.processed_at),
        }),
    },
    default: {
        icon: BellIcon,
        badge: {
            text: "Notificación",
            class: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
        },
        bgClass: "bg-gray-50 dark:bg-gray-900/30",
        iconClass: "text-gray-500 group-hover:text-gray-600 dark:text-gray-400",
        getTitle: () => "Nueva notificación",
        getMessage: () => "Nueva notificación",
        getUrl: () => "#",
    },
};

export const formatTimeAgo = (dateString) => {
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

export const getNotificationConfig = (type) => {
    return notificationTypes[type] || notificationTypes.default;
};
