<script setup>
import { computed, ref } from "vue";
import {
    XMarkIcon,
    CheckCircleIcon,
    CameraIcon,
} from "@heroicons/vue/24/solid";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import { usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import PostList from "@/Components/app/PostList.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import PostAttachments from "@/Components/app/PostAttachments.vue";
import TabPhotos from "@/Pages/Profile/TabPhotos.vue";
import CreateStory from "@/Components/app/stories/CreateStory.vue";
import SendMessageModal from "@/Components/app/messages/SendMessageModal.vue";

const imagesForm = useForm({
    avatar: null,
    cover: null,
});

const showNotification = ref(true);
const coverImageSrc = ref("");
const avatarImageSrc = ref("");
const searchFollowersKeyword = ref("");
const searchFollowingsKeyword = ref("");
const showCreateStory = ref(false);
const authUser = usePage().props.auth.user;
const showSendMessage = ref(false);

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);

const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    success: {
        type: String,
    },
    isCurrentUserFollower: Boolean,
    followerCount: Number,
    user: {
        type: Object,
    },
    posts: Object,
    followers: Array,
    followings: Array,
    photos: Array,
});

function onCoverChange(event) {
    imagesForm.cover = event.target.files[0];
    if (imagesForm.cover) {
        const reader = new FileReader();
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.cover);
    }
}

function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0];
    if (imagesForm.avatar) {
        const reader = new FileReader();
        reader.onload = () => {
            avatarImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.avatar);
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function resetAvatarImage() {
    imagesForm.avatar = null;
    avatarImageSrc.value = null;
}

function submitCoverImage() {
    imagesForm.post(route("profile.updateImages"), {
        preserveScroll: true,
        onSuccess: (user) => {
            showNotification.value = true;
            resetCoverImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
}

function submitAvatarImage() {
    imagesForm.post(route("profile.updateImages"), {
        preserveScroll: true,
        onSuccess: (user) => {
            showNotification.value = true;
            resetAvatarImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
}

function followUser() {
    const form = useForm({
        follow: !props.isCurrentUserFollower,
    });

    form.post(route("user.follow", props.user.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div
            class="max-w-full md:max-w-[768px] mx-auto h-full overflow-auto scroll-smooth"
        >
            <div class="px-4">
                <transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="transform opacity-0 translate-y-2"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 translate-y-2"
                >
                    <div
                        v-show="showNotification && success"
                        class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white rounded-lg shadow-lg"
                    >
                        {{ success }}
                    </div>
                </transition>

            
                    <div
                        v-if="errors.cover"
                        class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white rounded-lg animate-[shake_0.5s_ease-in-out]"
                    >
                        {{ errors.cover }}
                    </div>
                <div
                    class="group relative bg-white dark:bg-slate-950 dark:text-gray-100 rounded-xl overflow-hidden shadow-xl transition-all duration-500 hover:shadow-2xl"
                >
                    <div class="relative h-[200px] overflow-hidden">
                        <img
                            :src="
                                coverImageSrc ||
                                user.cover_url ||
                                '/img/default_cover.jpg'
                            "
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"
                        ></div>
                    </div>


                    <div class="absolute top-2 right-2 space-y-2">
                      
                            <button
                                v-if="isMyProfile && !coverImageSrc"
                                class="bg-white/90 hover:bg-white text-gray-800 py-1 px-3 text-xs flex items-center rounded-full shadow-md transform transition-all duration-300 hover:scale-105"
                            >
                                <CameraIcon class="w-4 h-4 mr-1" />
                                Update Cover
                                <input
                                    type="file"
                                    class="absolute left-0 top-0 bottom-0 right-0 opacity-0 cursor-pointer"
                                    @change="onCoverChange"
                                />
                            </button>
                        

                        <transition-group
                            tag="div"
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="transform opacity-0 translate-x-2"
                            enter-to-class="transform opacity-100 translate-x-0"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="transform opacity-100 translate-x-0"
                            leave-to-class="transform opacity-0 translate-x-2"
                            class="flex gap-2"
                            v-if="isMyProfile && coverImageSrc"
                        >
                            <button
                                key="cancel"
                                @click="resetCoverImage"
                                class="bg-white/90 hover:bg-red-500 hover:text-white text-gray-800 py-1 px-3 text-xs flex items-center rounded-full shadow-md transform transition-all duration-300 hover:scale-105"
                            >
                                <XMarkIcon class="h-3 w-3 mr-1" />
                                Cancel
                            </button>
                            <button
                                key="submit"
                                @click="submitCoverImage"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-3 text-xs flex items-center rounded-full shadow-md transform transition-all duration-300 hover:scale-105"
                            >
                                <CheckCircleIcon class="h-3 w-3 mr-1" />
                                Submit
                            </button>
                        </transition-group>
                    </div>

                    <!-- Avatar con animación 3D -->
                    <div class="flex">
                        <div
                            class="flex items-center justify-center relative group/avatar -mt-[64px] ml-[48px] w-[128px] h-[128px] rounded-full border-4 border-white dark:border-gray-800 shadow-xl"
                        >
                            <div
                                class="relative w-full h-full rounded-full overflow-hidden transition-transform duration-500 group-hover/avatar:rotate-3"
                            >
                                <img
                                    :src="
                                        avatarImageSrc ||
                                        user.avatar_url ||
                                        '/img/default_avatar.webp'
                                    "
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover/avatar:scale-110"
                                />
                                <button
                                    v-if="isMyProfile && !avatarImageSrc"
                                    class="absolute inset-0 bg-black/50 text-gray-200 rounded-full opacity-0 flex items-center justify-center group-hover/avatar:opacity-100 transition-opacity duration-300"
                                >
                                    <CameraIcon
                                        class="w-8 h-8 animate-[pulse_2s_infinite]"
                                    />
                                    <input
                                        type="file"
                                        class="absolute inset-0 opacity-0 cursor-pointer"
                                        @change="onAvatarChange"
                                    />
                                </button>
                            </div>

                            <transition-group
                                tag="div"
                                enter-active-class="transition ease-out duration-300"
                                enter-from-class="transform opacity-0 scale-50"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-200"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-50"
                                class="absolute top-0 right-0 flex flex-col gap-2"
                                v-if="isMyProfile && avatarImageSrc"
                            >
                                <button
                                    key="cancel-avatar"
                                    @click="resetAvatarImage"
                                    class="w-7 h-7 flex items-center justify-center bg-red-500/90 hover:bg-red-600 text-white rounded-full shadow-md transform transition-all duration-300 hover:scale-110"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                                <button
                                    key="submit-avatar"
                                    @click="submitAvatarImage"
                                    class="w-7 h-7 flex items-center justify-center bg-emerald-500/90 hover:bg-emerald-600 text-white rounded-full shadow-md transform transition-all duration-300 hover:scale-110"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                </button>
                            </transition-group>
                        </div>

                        <div
                            class="flex justify-between items-center flex-1 p-4"
                        >
                            <div
                                class="transform transition-all duration-300 hover:translate-x-1"
                            >
                                <h2
                                    class="font-bold text-xl md:text-2xl text-gray-800 dark:text-white"
                                >
                                    {{ user.name }}
                                </h2>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ followerCount }} follower{{
                                        followerCount !== 1 ? "s" : ""
                                    }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <PrimaryButton
                                    v-if="isMyProfile"
                                    @click="showCreateStory = true"
                                    class="mt-4 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    New Story
                                </PrimaryButton>

                                <transition
                                    enter-active-class="transition ease-out duration-300"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-200"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <CreateStory
                                        v-if="showCreateStory"
                                        @close="showCreateStory = false"
                                    />
                                </transition>

                                <div v-if="!isMyProfile" class="mt-4">
                                    <PrimaryButton
                                        v-if="!isCurrentUserFollower"
                                        @click="followUser"
                                        class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                    >
                                        Follow
                                    </PrimaryButton>
                                    <DangerButton
                                        v-else
                                        @click="followUser"
                                        class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                    >
                                        Unfollow
                                    </DangerButton>
                                </div>
                                <div
                                    v-if="!isMyProfile"
                                    class="mt-4 flex gap-2"
                                >
                                    <PrimaryButton
                                        @click="showSendMessage = true"
                                        class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                    >
                                        Send Message
                                    </PrimaryButton>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs con animación -->
            <div class="border-t dark:border-gray-700 mx-4 mt-0">
                <TabGroup>
                    <TabList
                        class="flex overflow-x-auto no-scrollbar whitespace-nowrap space-x-2 bg-white dark:bg-slate-950 dark:text-white px-2 sticky top-0 z-10 shadow-sm"
                    >
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem
                                text="Posts"
                                :selected="selected"
                                class="transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem
                                text="Followers"
                                :selected="selected"
                                class="transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem
                                text="Followings"
                                :selected="selected"
                                class="transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem
                                text="Photos"
                                :selected="selected"
                                class="transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            />
                        </Tab>
                        <Tab
                            v-if="isMyProfile"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem
                                text="My Profile"
                                :selected="selected"
                                class="transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <template v-if="posts">
                                <CreatePost v-if="isMyProfile" />
                                <PostList
                                    :posts="posts.data"
                                    class="flex-1 space-y-4"
                                />
                            </template>
                            <div
                                v-else
                                class="py-8 text-center dark:text-gray-100 animate-[pulse_2s_infinite]"
                            >
                                You don't have permission to view these posts.
                            </div>
                        </TabPanel>

                        <TabPanel
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <div
                                class="mb-3 animate-[slideInDown_0.3s_ease-in-out]"
                            >
                                <TextInput
                                    :model-value="searchFollowersKeyword"
                                    placeholder="Type to search"
                                    class="w-full"
                                />
                            </div>
                            <div
                                v-if="followers.length"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3"
                            >
                                <transition-group
                                    enter-active-class="transition ease-out duration-300"
                                    enter-from-class="transform opacity-0 translate-y-4"
                                    enter-to-class="transform opacity-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-200"
                                    leave-from-class="transform opacity-100 translate-y-0"
                                    leave-to-class="transform opacity-0 translate-y-4"
                                    tag="div"
                                    class="contents"
                                >
                                    <UserListItem
                                        v-for="(user, index) of followers"
                                        :user="user"
                                        :key="user.id"
                                        class="shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
                                        :style="`transition-delay: ${
                                            index * 50
                                        }ms`"
                                    />
                                </transition-group>
                            </div>
                            <div
                                v-else
                                class="text-center py-8 animate-[fadeIn_1s_ease-in-out]"
                            >
                                User does not have followers.
                            </div>
                        </TabPanel>

                        <TabPanel
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <div
                                class="mb-3 animate-[slideInDown_0.3s_ease-in-out]"
                            >
                                <TextInput
                                    :model-value="searchFollowingsKeyword"
                                    placeholder="Type to search"
                                    class="w-full"
                                />
                            </div>
                            <div
                                v-if="followings.length"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3"
                            >
                                <transition-group
                                    enter-active-class="transition ease-out duration-300"
                                    enter-from-class="transform opacity-0 translate-y-4"
                                    enter-to-class="transform opacity-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-200"
                                    leave-from-class="transform opacity-100 translate-y-0"
                                    leave-to-class="transform opacity-0 translate-y-4"
                                    tag="div"
                                    class="contents"
                                >
                                    <UserListItem
                                        v-for="(user, index) of followings"
                                        :user="user"
                                        :key="user.id"
                                        class="shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
                                        :style="`transition-delay: ${
                                            index * 50
                                        }ms`"
                                    />
                                </transition-group>
                            </div>
                            <div
                                v-else
                                class="text-center py-8 animate-[fadeIn_1s_ease-in-out]"
                            >
                                The user is not following anybody
                            </div>
                        </TabPanel>

                        <TabPanel
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <TabPhotos :photos="photos" />
                        </TabPanel>

                        <TabPanel
                            v-if="isMyProfile"
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <Edit
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>

    <SendMessageModal
        :show="showSendMessage"
        :receiverId="props.user.id"
        @close="showSendMessage = false"
    />
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideInDown {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes shake {
    0%,
    100% {
        transform: translateX(0);
    }
    20%,
    60% {
        transform: translateX(-5px);
    }
    40%,
    80% {
        transform: translateX(5px);
    }
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-\[fadeIn_0\.5s_ease-in-out\] {
    animation: fadeIn 0.5s ease-in-out;
}

.animate-\[slideInDown_0\.3s_ease-in-out\] {
    animation: slideInDown 0.3s ease-in-out;
}

.animate-\[shake_0\.5s_ease-in-out\] {
    animation: shake 0.5s ease-in-out;
}

.animate-\[pulse_2s_infinite\] {
    animation: pulse 2s infinite;
}

.animate-\[fadeIn_1s_ease-in-out\] {
    animation: fadeIn 1s ease-in-out;
}
</style>
