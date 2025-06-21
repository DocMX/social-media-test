<script setup>
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { PaperClipIcon } from "@heroicons/vue/24/solid"
import { isImage, isVideo } from '@/helpers.js'
import { ref } from 'vue'

defineProps({
    attachments: Array
})

defineEmits(['attachmentClick'])

const isMuted = ref(true)

const toggleMute = (event) => {
    const video = event.currentTarget.previousElementSibling
    isMuted.value = !isMuted.value
    if (video) {
        video.muted = isMuted.value
    }
}
</script>

<template>
    <template v-for="(attachment, ind) of attachments.slice(0, 4)">
        <div
            @click="$emit('attachmentClick', ind)"
            class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative cursor-pointer overflow-hidden rounded-lg"
        >
            <!-- Contador +x -->
            <div
                v-if="ind === 3 && attachments.length > 4"
                class="absolute left-0 top-0 right-0 bottom-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl"
            >
                +{{ attachments.length - 4 }} más
            </div>

            <!-- Botón de descarga -->
            <a
                @click.stop
                :href="route('post.download', attachment)"
                class="z-20 opacity-0 group-hover:opacity-100 transition-all w-8 h-8 flex items-center justify-center text-gray-100 bg-gray-700 rounded absolute right-2 top-2 cursor-pointer hover:bg-gray-800"
            >
                <ArrowDownTrayIcon class="w-4 h-4" />
            </a>

            <!-- Imagen -->
            <img
                v-if="isImage(attachment)"
                :src="attachment.url"
                class="object-cover w-full h-full rounded-lg"
            />

            <!-- Video con estilo Instagram -->
            <div v-else-if="isVideo(attachment)" class="relative w-full h-full">
                <video
                    :src="attachment.url"
                    autoplay
                    muted
                    playsinline
                    loop
                    class="w-full h-full object-cover rounded-lg"
                ></video>

                <!-- Botón mute/unmute -->
                <button
                    @click.stop="toggleMute"
                    class="absolute bottom-3 right-3 z-30 bg-black/60 hover:bg-black/80 p-2 rounded-full text-white transition"
                >
                    <svg
                        v-if="isMuted"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 10l4.553-4.553a1 1 0 00-1.414-1.414L13.586 10H10.414l-2.293-2.293a1 1 0 00-1.707.707V16a1 1 0 001.707.707L10.414 14h3.172l4.553 4.553a1 1 0 001.414-1.414L15 14v-4z"
                        />
                    </svg>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 9v6m-6 0V9m6.707-1.707A1 1 0 0015 6H9a1 1 0 00-.707 1.707L10.586 9H13.414l2.293-2.293zM6 18l12-12"
                        />
                    </svg>
                </button>

                <!-- Overlay negro suave -->
                <div class="absolute top-0 left-0 w-full h-full bg-black/20 z-10 rounded-lg"></div>
            </div>

            <!-- Otro tipo de archivo -->
            <div v-else class="flex flex-col justify-center items-center">
                <PaperClipIcon class="w-10 h-10 mb-3" />
                <small>{{ attachment.name }}</small>
            </div>
        </div>
    </template>
</template>
