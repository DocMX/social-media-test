<script setup>
import TextInput from "@/Components/TextInput.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import { ref, computed } from "vue";

const searchKeyword = ref("");

const props = defineProps({
  users: Array,
});


// Filtrar usuarios por nombre (o username si quieres)
const filteredUsers = computed(() => {
  if (!searchKeyword.value.trim()) return props.users;
  return props.users.filter((u) =>
    u.name.toLowerCase().includes(searchKeyword.value.toLowerCase())
  );
});
</script>

<template>
  <div>
    <!-- Input de búsqueda -->
    <TextInput
      v-model="searchKeyword"
      placeholder="Search..."
      class="w-full mt-3"
    />

    <!-- Lista de resultados -->
    <div class="mt-3 h-[300px] overflow-auto space-y-2">
      <div
        v-if="filteredUsers.length === 0"
        class="text-gray-400 text-center p-4"
      >
        No users were found.
      </div>
      <div v-else>
        <UserListItem
          v-for="user in filteredUsers"
          :key="user.id"
          :user="user"
          class="rounded-lg shadow-sm hover:shadow-md p-2 transition"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
