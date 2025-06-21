<script setup>
import { Head} from "@inertiajs/vue3";
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
    <div class="grid lg:grid-cols-12 gap-3 p-4 h-full bg-gray-100 dark:bg-dark-bg text-gray-800 dark:text-dark-text">
      <!-- Lista de grupos -->
      <div class="lg:col-span-3 lg:order-1 h-full overflow-hidden">
        <GroupList :groups="groups" :recommendedGroups="recommendedGroups" />
      </div>

      <!-- Lista de personas que sigues -->
      <div class="lg:col-span-3 lg:order-3 h-full overflow-hidden">
        <FollowingList :users="followings" />
      </div>

      <!-- Feed principal -->
      <div class="lg:col-span-6 lg:order-2 h-full overflow-hidden flex flex-col">
        <StoriesBar />
        <CreatePost />
        <PostList :posts="posts.data" class="flex-1" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>


<style></style>
