<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, onMounted, nextTick } from "vue";

const props = defineProps({
  user: Object,        // el usuario con quien chateas
  messages: Array,     // mensajes de esa conversación
});

const form = useForm({
  body: "",
  receiver_id: props.user.id,
});

const messagesContainer = ref(null);

function sendMessage() {
  if (!form.body.trim()) return;
  form.post(route("messages.store"), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset("body");
      scrollToBottom();
    },
  });
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
}

onMounted(scrollToBottom);
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Chat con ${props.user.name}`" />
    <template #header>
      <h2>💬 Chat con {{ props.user.name }}</h2>
    </template>

    <div class="flex flex-col h-[70vh] max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded shadow">
      <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900">
        <div
          v-for="msg in props.messages"
          :key="msg.id"
          :class="msg.sender_id === props.user.id ? 'text-left' : 'text-right'"
        >
          <div :class="msg.sender_id === props.user.id ? 'bg-gray-200' : 'bg-indigo-600 text-white'">
            {{ msg.body }}
          </div>
          <div class="text-xs text-gray-500 mt-1">
            {{ new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
          </div>
        </div>
      </div>

      <form @submit.prevent="sendMessage" class="flex border-t p-4 border-gray-200 dark:border-gray-700">
        <input v-model="form.body" placeholder="Escribe un mensaje..." class="flex-1 ..." />
        <button type="submit" class="ml-2 bg-indigo-600 text-white rounded px-4 py-2">Enviar</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
