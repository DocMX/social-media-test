<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
  conversations: Array,
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Conversaciones" />

    <template #header>
      <h2>💬 Conversaciones</h2>
    </template>

    <div>
      <div v-for="conv in conversations" :key="conv.user_id">
        <Link :href="route('messages.chat', conv.user_id)">
          <img :src="conv.avatar" />
          {{ conv.name }} - {{ conv.last_message }}
        </Link>
      </div>
      <div v-if="conversations.length === 0">No hay conversaciones</div>
    </div>
  </AuthenticatedLayout>
</template>

