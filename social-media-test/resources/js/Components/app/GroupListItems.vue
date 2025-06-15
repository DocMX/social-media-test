<script setup>
import TextInput from "@/Components/TextInput.vue";
import GroupItem from "@/Components/app/GroupItem.vue";
import { ref } from "vue";
import GroupModal from "@/Components/app/GroupModal.vue";

const searchKeyword = ref("");
const showNewGroupModal = ref(false);

const props = defineProps({
    groups: Array,
    showControls: {
        type: Boolean,
        default: false,
    },
});

function onGroupCreate(group) {
    props.groups.unshift(group);
}
</script>

<template>
    <!-- Mostrar solo si showControls es true -->
    <div v-if="showControls" class="flex gap-2 mt-4">
        <TextInput
            :model-value="searchKeyword"
            placeholder="Type to search"
            class="w-full"
        />
        <button
            @click="showNewGroupModal = true"
            class="text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded py-1 px-2 w-[120px]"
        >
            new group
        </button>
    </div>

    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="groups.length === 0" class="text-gray-400 text-center p-3">
            No groups to show.
        </div>
        <div v-else>
            <GroupItem v-for="group of groups" :group="group" :key="group.id" />
        </div>
    </div>

    <!-- Modal solo si showControls es true -->
    <GroupModal
        v-if="showControls"
        v-model="showNewGroupModal"
        @create="onGroupCreate"
    />
</template>
