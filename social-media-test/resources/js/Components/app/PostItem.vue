<script setup>
import {
    ChatBubbleLeftRightIcon,
    HandThumbUpIcon,
    MapPinIcon
} from "@heroicons/vue/24/outline";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import PostUserHeader from "@/Components/app/PostUserHeader.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import ReadMoreReadLess from "@/Components/app/ReadMoreReadLess.vue";
import EditDeleteDropdown from "@/Components/app/EditDeleteDropdown.vue";
import CommentList from "@/Components/app/CommentList.vue";
import { computed } from "vue";
import UrlPreview from "@/Components/app/UrlPreview.vue";
import PostCarousel from "./postCarousel.vue";

const props = defineProps({
    post: Object,
});

const authUser = usePage().props.auth.user;
const group = usePage().props.group;

const emit = defineEmits(["editClick", "attachmentClick"]);

const postBody = computed(() => {
    let content = props.post.body.replace(
        /(?:(\s+)|<p>)((#\w+)(?![^<]*<\/a>))/g,
        (match, group1, group2) => {
            const encodedGroup = encodeURIComponent(group2);
            return `${group1 || ""}<a href="/search/${encodedGroup}" class="hashtag">${group2}</a>`;
        }
    );
    return content;
});

const isPinned = computed(() => {
    if (group?.id) return group?.pinned_post_id === props.post.id;
    return authUser?.pinned_post_id === props.post.id;
});

function openEditModal() {
    emit("editClick", props.post);
}

function deletePost() {
    if (window.confirm("Are you sure you want to delete this post?")) {
        router.delete(route("post.destroy", props.post), { preserveScroll: true });
    }
}

function pinUnpinPost() {
    const form = useForm({ forGroup: group?.id });
    const currentlyPinned = isPinned.value;

    form.post(route("post.pinUnpin", props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (group?.id) {
                group.pinned_post_id = currentlyPinned ? null : props.post.id;
            } else {
                authUser.pinned_post_id = currentlyPinned ? null : props.post.id;
            }
        },
    });
}

function sendReaction() {
    axiosClient.post(route("post.reaction", props.post), { reaction: "like" })
        .then(({ data }) => {
            props.post.current_user_has_reaction = data.current_user_has_reaction;
            props.post.num_of_reactions = data.num_of_reactions;
            playLikeSound();
        });
}

function playLikeSound() {
    const audio = new Audio("/sounds/like.wav");
    audio.play().catch((e) => console.warn("No se pudo reproducir el sonido:", e));
    if (navigator.vibrate) navigator.vibrate(50);
}
</script>

<template>
    <div
        class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-800 dark:text-dark-text rounded-2xl p-5 mb-4 shadow-sm transition-colors duration-300"
    >
        <!-- Cabecera con usuario y opciones -->
        <div class="flex items-center justify-between mb-3">
            <PostUserHeader :post="post" />
            <div class="flex items-center gap-2">
                <div v-if="isPinned" class="flex items-center text-xs">
                    <MapPinIcon class="h-3 w-3" aria-hidden="true" />
                    pinned
                </div>
                <EditDeleteDropdown
                    :user="post.user"
                    :post="post"
                    @edit="openEditModal"
                    @delete="deletePost"
                    @pin="pinUnpinPost"
                />
            </div>
        </div>

        <!-- Contenido del post -->
        <div class="mb-3">
            <ReadMoreReadLess :content="postBody" />
            <UrlPreview :preview="post.preview" :url="post.preview_url" />
        </div>

        <!-- Carrusel tipo Instagram -->
        <div v-if="post.attachments.length" class="mb-4">
            <PostCarousel :media="post.attachments" />
        </div>

        <!-- Botones de Like y Comentario -->
        <Disclosure v-slot="{ open }">
            <div class="flex gap-2">
                <button
                    @click="sendReaction"
                    class="text-gray-800 dark:text-gray-100 flex gap-1 items-center justify-center rounded-lg py-2 px-4 flex-1"
                    :class="[
                        post.current_user_has_reaction
                            ? 'bg-sky-100 dark:bg-sky-900 hover:bg-sky-200 dark:hover:bg-sky-950'
                            : 'bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700',
                    ]"
                >
                    <HandThumbUpIcon class="w-5 h-5" />
                    <span class="mr-2">{{ post.num_of_reactions }}</span>
                    {{ post.current_user_has_reaction ? "Unlike" : "Like" }}
                </button>
                <DisclosureButton
                    class="text-gray-800 dark:text-gray-100 flex gap-1 items-center justify-center bg-gray-100 dark:bg-slate-900 dark:hover:bg-slate-800 rounded-lg hover:bg-gray-200 py-2 px-4 flex-1"
                >
                    <ChatBubbleLeftRightIcon class="w-5 h-5" />
                    <span class="mr-2">{{ post.num_of_comments }}</span>
                    Comment
                </DisclosureButton>
            </div>

            <!-- Lista de comentarios -->
            <DisclosurePanel class="comment-list mt-3 max-h-[400px] overflow-auto">
                <CommentList :post="post" :data="{ comments: post.comments }" />
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>
