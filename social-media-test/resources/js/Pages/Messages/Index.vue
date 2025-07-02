<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SendMessageModal from "@/Components/app/messages/SendMessageModal.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch, nextTick, onMounted } from "vue";
import { ChatBubbleLeftEllipsisIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    conversations: Array,
    messages: Array,
    selectedUser: Object,
    followingUsers: Array,
});

const activeUser = ref(props.selectedUser);
const chatMessages = ref(props.messages || []);
const messageBody = ref("");

const messagesContainer = ref(null);
const showModal = ref(false);

// Detectar vista móvil / desktop
const isMobileView = ref(window.innerWidth < 768);

// Controlar si se muestra chat en móvil
const showChat = ref(!isMobileView.value);

function handleResize() {
    isMobileView.value = window.innerWidth < 768;
    if (!isMobileView.value) {
        showChat.value = true; // desktop: mostrar chat y sidebar
    } else {
        showChat.value = false; // móvil: solo sidebar al iniciar
    }
}
window.addEventListener("resize", handleResize);
onMounted(handleResize);

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    });
}

// Cargar mensajes sin navegar (solo actualizar chatMessages)
async function loadMessages(userId) {
    try {
        const res = await fetch(route("messages.chat", userId), {
            headers: {
                Accept: "application/json",
            },
        });
        if (!res.ok) throw new Error("Error cargando mensajes");
        const data = await res.json();
        chatMessages.value = data.messages;
        scrollToBottom();
        if (isMobileView.value) showChat.value = true;
    } catch (error) {
        console.error(error);
    }
}

function selectUser(conv) {
    activeUser.value = conv;
    loadMessages(conv.user_id ?? conv.id);
}

function backToList() {
    showChat.value = false;
}

// Enviar mensaje
function sendMessage() {
    if (!messageBody.value.trim() || !activeUser.value) return;

    router.post(
        route("messages.store"),
        {
            receiver_id: activeUser.value.user_id ?? activeUser.value.id,
            subject: "Mensaje",
            body: messageBody.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                messageBody.value = "";
                loadMessages(activeUser.value.user_id ?? activeUser.value.id);
            },
        }
    );
}

// Observar cambios en activeUser para cargar mensajes
watch(activeUser, (newUser, oldUser) => {
    if (newUser && newUser !== oldUser) {
        loadMessages(newUser.user_id ?? newUser.id);
    }
});

scrollToBottom();
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Mensajes" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="flex font-semibold text-xl text-gray-800 dark:text-white leading-tight"
                >
                    <ChatBubbleLeftEllipsisIcon class="w-6 h-6" />
                    <span class="ml-2">MESSAGES</span>
                </h2>
                <button
                    @click="showModal = true"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition transform hover:scale-105 active:scale-95"
                >
                    + New message
                </button>
            </div>
        </template>

        <div class="max-w-7xl mx-auto mt-6 flex gap-4 h-[70vh]">
            <!-- Sidebar -->
            <div
                v-show="!isMobileView || (isMobileView && !showChat)"
                class="hidden md:flex md:flex-col md:w-1/3 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-auto transition-all duration-300 ease-in-out"
            >
                <div
                    v-for="(conv, index) in props.conversations"
                    :key="conv.user_id"
                    @click="selectUser(conv)"
                    :class="[
                        'p-4 cursor-pointer flex items-center gap-3 border-b border-gray-200 dark:border-gray-700 transition-all duration-200',
                        (activeUser?.user_id ?? activeUser?.id) === conv.user_id
                            ? 'bg-indigo-100 dark:bg-indigo-700'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-700',
                    ]"
                    :style="`transition-delay: ${index * 50}ms`"
                >
                    <img
                        :src="conv.avatar"
                        alt="avatar"
                        class="w-10 h-10 rounded-full object-cover transition-transform duration-300 hover:scale-110"
                    />
                    <div class="flex-1 min-w-0">
                        <div
                            class="font-semibold text-gray-900 dark:text-white truncate"
                        >
                            {{ conv.name }}
                        </div>
                        <div
                            class="text-sm text-gray-600 dark:text-gray-300 truncate"
                        >
                            {{ conv.last_message }}
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">{{ conv.date }}</div>
                </div>
                <div
                    v-if="conversations.length === 0"
                    class="p-4 text-center text-gray-500 dark:text-gray-400 animate-pulse"
                >
                    No hay conversaciones.
                </div>
            </div>

            <!-- Mobile Sidebar -->
            <div
                v-show="isMobileView && !showChat"
                class="block md:hidden w-full h-full overflow-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg transition-all duration-300 ease-in-out"
            >
                <div
                    v-for="(conv, index) in props.conversations"
                    :key="conv.user_id"
                    @click="selectUser(conv)"
                    :class="[
                        'p-4 cursor-pointer flex items-center gap-3 border-b border-gray-200 dark:border-gray-700 transition-all duration-200',
                        (activeUser?.user_id ?? activeUser?.id) === conv.user_id
                            ? 'bg-indigo-100 dark:bg-indigo-700'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-700',
                    ]"
                    :style="`transition-delay: ${index * 50}ms`"
                >
                    <img
                        :src="conv.avatar"
                        alt="avatar"
                        class="w-10 h-10 rounded-full object-cover transition-transform duration-300 hover:scale-110"
                    />
                    <div class="flex-1 min-w-0">
                        <div
                            class="font-semibold text-gray-900 dark:text-white truncate"
                        >
                            {{ conv.name }}
                        </div>
                        <div
                            class="text-sm text-gray-600 dark:text-gray-300 truncate"
                        >
                            {{ conv.last_message }}
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">{{ conv.date }}</div>
                </div>
                <div
                    v-if="conversations.length === 0"
                    class="p-4 text-center text-gray-500 dark:text-gray-400 animate-pulse"
                >
                    No hay conversaciones.
                </div>
            </div>

            <!-- Chat Area -->
            <div
                v-show="!isMobileView || (isMobileView && showChat)"
                class="flex-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg flex flex-col transition-all duration-300 ease-in-out"
            >
                <template v-if="activeUser">
                    <!-- Back Button (Mobile) -->
                    <button
                        v-if="isMobileView"
                        @click="backToList"
                        class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-indigo-600 font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        ← Back
                    </button>

                    <div
                        class="p-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-lg text-gray-800 dark:text-white flex items-center"
                    >
                        <img
                            :src="activeUser.avatar"
                            alt="avatar"
                            class="w-8 h-8 rounded-full object-cover mr-3 transition-transform duration-300 hover:scale-125"
                        />
                        <span class="animate-[fadeIn_0.5s_ease-in-out]"
                            >Chat With {{ activeUser.name }}</span
                        >
                    </div>

                    <div
                        ref="messagesContainer"
                        class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900"
                    >
                        <div
                            v-for="(msg, index) in chatMessages"
                            :key="msg.id"
                            :class="{
                                'text-right':
                                    msg.sender_id !==
                                    (activeUser.user_id ?? activeUser.id),
                                'text-left':
                                    msg.sender_id ===
                                    (activeUser.user_id ?? activeUser.id),
                            }"
                            class="animate-[fadeInUp_0.3s_ease-in-out]"
                            :style="`animation-delay: ${index * 50}ms`"
                        >
                            <div
                                :class="[
                                    'inline-block px-4 py-2 rounded-lg max-w-[75%] whitespace-pre-line transition-all duration-200',
                                    msg.sender_id ===
                                    (activeUser.user_id ?? activeUser.id)
                                        ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:shadow-md'
                                        : 'bg-indigo-600 text-white hover:shadow-lg',
                                ]"
                            >
                                {{ msg.body }}
                            </div>
                            <div
                                class="text-xs text-gray-500 mt-1 opacity-0 animate-[fadeIn_0.5s_ease-in-out_forwards]"
                            >
                                {{
                                    new Date(msg.created_at).toLocaleTimeString(
                                        [],
                                        {
                                            hour: "2-digit",
                                            minute: "2-digit",
                                        }
                                    )
                                }}
                            </div>
                        </div>
                    </div>

                    <form
                        @submit.prevent="sendMessage"
                        class="flex items-center border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800 transition-all duration-300"
                    >
                        <input
                            v-model="messageBody"
                            type="text"
                            placeholder="Escribe un mensaje..."
                            class="flex-1 rounded-full px-4 py-2 border dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                        />
                        <button
                            type="submit"
                            class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition transform hover:scale-105 active:scale-95"
                        >
                            Enviar
                        </button>
                    </form>
                </template>

                <template v-else>
                    <div
                        class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400 animate-pulse"
                    >
                        <span class="animate-[fadeIn_1s_ease-in-out_infinite]"
                            >Selecciona una conversación para empezar a
                            chatear.</span
                        >
                    </div>
                </template>
            </div>
        </div>

        <SendMessageModal
            :show="showModal"
            :users="followingUsers"
            @close="showModal = false"
        />
    </AuthenticatedLayout>
</template>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-\[fadeInUp_0\.3s_ease-in-out\] {
    animation: fadeInUp 0.3s ease-in-out;
}

.animate-\[fadeIn_0\.5s_ease-in-out_forwards\] {
    animation: fadeIn 0.5s ease-in-out forwards;
}

.animate-\[fadeIn_0\.5s_ease-in-out\] {
    animation: fadeIn 0.5s ease-in-out;
}

.animate-\[fadeIn_1s_ease-in-out_infinite\] {
    animation: fadeIn 1s ease-in-out infinite;
}
</style>
