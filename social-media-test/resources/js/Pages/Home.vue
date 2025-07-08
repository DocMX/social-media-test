<script setup>
import { Head } from "@inertiajs/vue3";
import GroupList from "@/Components/app/GroupList.vue";
import FollowingList from "@/Components/app/FollowingList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import PostList from "@/Components/app/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StoriesBar from "@/Components/app/stories/StoriesBar.vue";

defineProps({
    posts: Object,
    groups: Array,
    followings: Array,
    recommendedGroups: Array,
    stories: Object,
});
</script>

<template>
    <Head title="Social Media Website" />

    <AuthenticatedLayout>
        <div
            class="grid lg:grid-cols-12 gap-3 p-4 h-screen bg-gray-100 dark:bg-dark-bg text-gray-800 dark:text-dark-text overflow-hidden"
        >
            <div class="lg:col-span-3 lg:order-1 h-full overflow-hidden">
                <div
                    class="sticky top-4 max-h-[calc(100vh-2rem)] overflow-y-auto pr-2"
                >
                    <GroupList
                        :groups="groups"
                        :recommendedGroups="recommendedGroups"
                    />
                </div>
            </div>
            <div class="lg:col-span-3 lg:order-3 h-full overflow-hidden">
                <div
                    class="sticky top-4 max-h-[calc(100vh-2rem)] overflow-y-auto pr-2"
                >
                    <FollowingList :users="followings" />
                </div>
            </div>

            <!-- Feed principal -->
            <div
                class="lg:col-span-6 lg:order-2 h-full overflow-hidden flex flex-col"
            >
                <div class="flex-1 overflow-y-auto pr-2">
                    <CreatePost />
                    <StoriesBar />
                    <PostList :posts="posts.data" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style></style>
