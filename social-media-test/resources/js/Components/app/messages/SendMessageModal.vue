<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    receiverId: {
        type: Number,
        required: false, // Ahora es opcional
    },
    show: {
        type: Boolean,
        default: false,
    },
    users: {
        type: Array,
        default: () => [], // Lista de usuarios para seleccionar
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    receiver_id: props.receiverId ?? null,
    
    body: "",
});

// En caso de que receiverId se actualice después del montaje
watch(
    () => props.receiverId,
    (newId) => {
        if (newId) form.receiver_id = newId;
    }
);

const send = () => {
    if (!form.receiver_id) return alert("Selecciona un receptor");

    form.post(route("messages.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit("close");
        },
    });
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative"
        >
            <h2
                class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
            >
                Enviar mensaje
            </h2>

            <form @submit.prevent="send" class="space-y-4">
                <!-- Selector de usuario si no se recibe receiverId -->
                <div v-if="!props.receiverId">
                    <label
                        class="block text-sm text-gray-600 dark:text-gray-300"
                        >Destinatario</label
                    >
                    <select
                        v-model="form.receiver_id"
                        class="w-full mt-1 px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 dark:text-white"
                    >
                        <option disabled value="">Selecciona un usuario</option>
                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>
                    <div
                        v-if="form.errors.receiver_id"
                        class="text-sm text-red-500 mt-1"
                    >
                        {{ form.errors.receiver_id }}
                    </div>
                </div>


                <div>
                    <label
                        class="block text-sm text-gray-600 dark:text-gray-300"
                    >
                        Mensaje
                    </label>
                    <textarea
                        v-model="form.body"
                        rows="4"
                        class="w-full mt-1 px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 dark:text-white"
                    ></textarea>
                    <div
                        v-if="form.errors.body"
                        class="text-sm text-red-500 mt-1"
                    >
                        {{ form.errors.body }}
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
