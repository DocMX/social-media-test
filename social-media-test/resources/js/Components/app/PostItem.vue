<script setup>
import {
    ChatBubbleLeftRightIcon,
    HandThumbUpIcon,
    MapPinIcon,
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

const emit = defineEmits(["editClick", "attachmentClick", 'updatePost']);

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
        router.delete(route("post.destroy", props.post), {
            preserveScroll: true,
        });
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
            props.post.reactions_users = data.reactions_users || [];
            playLikeSound();
            emit("updatePost", { ...props.post });
        });
}

function playLikeSound() {
    const audio = new Audio("/sounds/like.wav");
    audio.play().catch((e) => console.warn("No se pudo reproducir el sonido:", e));
    if (navigator.vibrate) navigator.vibrate(50);
}
</script>

<template>
    <div class="bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-800 dark:text-dark-text rounded-2xl p-4 mb-4 shadow-sm transition-colors duration-300 max-w-full sm:max-w-xl mx-auto">
        <!-- Cabecera -->
        <div class="flex items-center justify-between mb-3">
            <PostUserHeader :post="post" />
            <div class="flex items-center gap-2">
                <div v-if="isPinned" class="flex items-center text-xs">
                    <MapPinIcon class="h-3 w-3" aria-hidden="true" /> pinned
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

        <!-- Contenido -->
        <div class="mb-3">
            <ReadMoreReadLess :content="postBody" />
            <UrlPreview :preview="post.preview" :url="post.preview_url" />
        </div>

        <!-- Carrusel -->
        <div v-if="post.attachments.length" class="mb-4">
            <PostCarousel :media="post.attachments" />
        </div>

        <!-- Reacciones -->
        <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400 mb-1 px-1">
            <div class="flex items-center gap-1">
                <HandThumbUpIcon class="h-4 w-4 text-sky-600" />
                <span>{{ post.num_of_reactions }} like<span v-if="post.num_of_reactions !== 1">s</span></span>
            </div>
            <div class="text-xs">
                {{ post.num_of_comments }} comment<span v-if="post.num_of_comments !== 1">s</span>
            </div>
        </div>

        <!-- Usuarios que dieron like -->
        <div v-if="post.reactions_users?.length" class="text-xs text-gray-600 dark:text-gray-400 mt-1 px-1">
            <span>Liked by </span>
            <span v-for="(user, index) in post.reactions_users.slice(0, 3)" :key="user.id">
                {{ user.name }}<span v-if="index < post.reactions_users.length - 1 && index < 2">, </span>
            </span>
            <span v-if="post.reactions_users.length > 3">
                y {{ post.reactions_users.length - 3 }} más
            </span>
        </div>

        <!-- Botones -->
        <Disclosure v-slot="{ open }">
            <div class="flex justify-around border-t border-b py-2 dark:border-gray-700 text-sm font-semibold">
                <button @click="sendReaction" class="flex items-center gap-2 px-4 py-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                    <HandThumbUpIcon class="w-5 h-5 text-sky-600" />
                    <span>{{ post.current_user_has_reaction ? "Unlike" : "Like" }}</span>
                </button>
                <DisclosureButton class="flex items-center gap-2 px-4 py-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                    <ChatBubbleLeftRightIcon class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                    <span>Comment</span>
                </DisclosureButton>
            </div>
            <DisclosurePanel class="comment-list mt-3 max-h-[400px] overflow-auto">
                <CommentList :post="post" :data="{ comments: post.comments }" />
            </DisclosurePanel>
        </Disclosure>
    </div>
</template>
