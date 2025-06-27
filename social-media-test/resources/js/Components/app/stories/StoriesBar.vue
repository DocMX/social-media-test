<script setup>
/* Importación de funciones reactivas desde Vue */
import { ref, computed, onMounted } from "vue";

/* Cliente Axios personalizado para peticiones HTTP */
import axios from "@/axiosClient";

/* Componentes internos */
import StoriesModal from "./StoriesModal.vue";
import CreateStory from "./CreateStory.vue";

/* Hook de Inertia para acceder a props compartidos como el usuario autenticado */
import { usePage } from "@inertiajs/vue3";

/* Objeto donde se guardan las historias agrupadas por usuario */
const storiesByUser = ref({});

/* Índice del usuario actualmente seleccionado en el visor */
const selectedUserIndex = ref(0);

/* Índice de la historia actual dentro del usuario seleccionado */
const currentIndex = ref(0);

/* Controla la visibilidad del modal para ver historias */
const showModal = ref(false);

/* Controla la visibilidad del modal para crear historia */
const showCreateModal = ref(false);

/* Índice del usuario que tiene abierto su menú de opciones (⋮) */
const activeMenuIndex = ref(null);

/* Información del usuario autenticado desde Inertia */
const authUser = usePage().props.auth.user;


/* Lista computada de usuarios con historias (convierte objeto en array) */
const users = computed(() => Object.values(storiesByUser.value));

/* Alterna el menú de opciones ⋮ por índice (uno visible a la vez) */
const toggleMenu = (idx) => {
    activeMenuIndex.value = activeMenuIndex.value === idx ? null : idx;
};

/* Función para obtener historias desde el servidor */
const fetchStories = async () => {
    try {
        const response = await axios.get(route("stories")); // Ruta definida en web.php
        storiesByUser.value = response.data; // Guarda las historias
    } catch (e) {
        console.error("Error al cargar historias:", e);
    }
};
const viewStats = (story) => {
    alert(
        `📊 Estadísticas de la historia\n\nVistas: ${
            story.views_count || 0
        }\nReacciones: ${story.reactions_count || 0}`
    );
};

/* Verifica si todas las historias de un usuario fueron vistas */
const isFullyViewed = (userStories) => {
    return userStories.every((story) => story.has_been_viewed);
};

/* Abre el visor de historias y selecciona al usuario dado */
const openStory = (userIdx) => {
    selectedUserIndex.value = userIdx;
    currentIndex.value = 0;
    showModal.value = true;
};

/* Cierra el visor de historias */
const closeModal = () => {
    showModal.value = false;
};

/* Avanza a la siguiente historia, o al siguiente usuario si se acabaron */
const nextStory = () => {
    const currentUserStories = users.value[selectedUserIndex.value];
    if (currentIndex.value < currentUserStories.length - 1) {
        currentIndex.value++;
    } else if (selectedUserIndex.value < users.value.length - 1) {
        selectedUserIndex.value++;
        currentIndex.value = 0;
    } else {
        closeModal(); // Si no hay más, cerrar
    }
};

/* Retrocede a la historia anterior, o al usuario anterior si es la primera */
const prevStory = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    } else if (selectedUserIndex.value > 0) {
        selectedUserIndex.value--;
        const prevUserStories = users.value[selectedUserIndex.value];
        currentIndex.value = prevUserStories.length - 1;
    } else {
        closeModal();
    }
};

/* Elimina una historia si el usuario confirma la acción */
const deleteStory = async (storyId) => {
    if (!confirm("¿Estás segura de eliminar esta historia?")) return;

    try {
        await axios.delete(route("stories.destroy", storyId)); // Elimina en backend
        fetchStories(); // Vuelve a cargar historias
    } catch (error) {
        console.error("Error eliminando historia:", error);
    }
};

/* Al montar el componente, cargar historias y refrescarlas cada 30 segundos */
onMounted(() => {
    fetchStories();
    setInterval(fetchStories, 30000); // Autorefrescar
});
</script>

<template>
    <!-- Componente para crear historia (aparece en un modal si está activado) -->
    <CreateStory v-if="showCreateModal" @close="showCreateModal = false" />
    <!-- Contenedor principal de todas las historias -->
    <div
        class="flex gap-3 overflow-x-auto px-4 mb-4 py-3 bg-gray-50 dark:bg-dark-bg rounded-xl shadow-sm scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200"
        style="scrollbar-width: thin; max-width: 100%; white-space: nowrap"
    >
        <!-- Crear historia estilo Facebook -->
        <div
            class="w-28 h-48 flex-shrink-0 rounded-lg bg-white dark:bg-slate-800 border shadow hover:scale-105 transition cursor-pointer relative overflow-hidden"
            @click="showCreateModal = true"
        >
            <img
                :src="
                    authUser?.avatar_path
                        ? '/storage/' + authUser.avatar_path
                        : '/img/default-avatar.jpg'
                "
                class="w-full h-32 object-cover"
            />
            <div
                class="absolute bottom-0 left-0 right-0 px-2 py-1 text-sm text-center text-gray-700 dark:text-white font-semibold bg-white dark:bg-slate-800"
            >
                Crear historia
            </div>
            <div
                class="absolute top-2 left-2 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center border-4 border-white dark:border-slate-800"
            >
                +
            </div>
        </div>

        <!-- Itera sobre cada usuario con historias -->
        <div
            v-for="(userStories, idx) in users"
            :key="userStories[0]?.user?.id || idx"
            @click="openStory(idx)"
            class="w-28 h-48 flex-shrink-0 rounded-lg relative cursor-pointer overflow-hidden shadow hover:scale-105 transition"
        >
            <!-- Imagen fondo -->
            <img
                :src="
                    userStories[0]?.preview_path
                        ? `/storage/${userStories[0].preview_path}`
                        : `/storage/${userStories[0].media_path}`
                "
                class="w-full h-full object-cover"
            />
            <!-- Menú desplegable solo para el usuario autenticado -->
            <div
                v-if="
                    userStories.length &&
                    userStories[0].user?.id === authUser?.id
                "
                class="absolute top-2 right-2 z-20"
            >
                <!-- Botón de 3 puntos -->
                <button
                    @click.stop="toggleMenu(idx)"
                    class="text-white bg-black/40 hover:bg-black/60 rounded-full px-2 py-1"
                >
                    ⋮
                </button>

                <!-- Menú con más opciones -->
                <!-- Menú con íconos -->
                <div
                    v-if="activeMenuIndex === idx"
                    class="absolute right-0 mt-2 bg-white border rounded shadow text-sm z-30"
                >
                    <button
                        @click.stop="viewStats(userStories[0])"
                        class="flex items-center gap-2 px-3 py-2 text-gray-800 hover:bg-gray-100"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-indigo-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M3 3a1 1 0 012 0v10a1 1 0 102 0V5a1 1 0 112 0v8a1 1 0 102 0V7a1 1 0 112 0v6a1 1 0 102 0V3a1 1 0 112 0v12a1 1 0 01-1 1H3a1 1 0 01-1-1V3z"
                            />
                        </svg>
                    </button>
                    <button
                        @click.stop="deleteStory(userStories[0].id)"
                        class="flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-100"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-red-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H3.5a.5.5 0 000 1H4v11a2 2 0 002 2h8a2 2 0 002-2V5h.5a.5.5 0 000-1H15V3a1 1 0 00-1-1H6zm1 3V3h6v2H7z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Filtro oscuro -->
            <div class="absolute inset-0 bg-black/30"></div>

            <!-- Avatar -->
            <img
                :src="userStories[0]?.user?.avatar || '/img/default-avatar.jpg'"
                class="absolute top-2 left-2 w-8 h-8 rounded-full border-2 border-white"
            />

            <!-- Name -->
            <div
                class="absolute bottom-2 left-2 right-2 text-white text-xs font-semibold truncate"
            >
                {{ userStories[0]?.user?.name || "Usuario" }}
            </div>
        </div>
    </div>
    <!-- Modal de visor de historias (al estilo Instagram) -->
    <StoriesModal
        v-if="showModal"
        :users="users"
        :selectedUserIndex="selectedUserIndex"
        :currentIndex="currentIndex"
        @update:index="(val) => (currentIndex.value = val)"
        @close="closeModal"
        @next="nextStory"
        @prev="prevStory"
    />
</template>

<style>
.story-ring {
    background: conic-gradient(from 180deg, #d62976, #fa7e1e, #feda75, #d62976);
    padding: 2px;
    border-radius: 9999px;
}

.story-ring.viewed {
    background: #d1d5db; /* gray-300 */
}
/* src/assets/css/custom-scroll.css */
::-webkit-scrollbar {
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background-color: #888;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
