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
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InviteUserModal from "@/Pages/Group/InviteUserModal.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import GroupForm from "@/Components/app/GroupForm.vue";
import PostList from "@/Components/app/PostList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import TabPhotos from "@/Pages/Profile/TabPhotos.vue";

const imagesForm = useForm({
    thumbnail: null,
    cover: null,
});

const showNotification = ref(true);
const coverImageSrc = ref("");
const thumbnailImageSrc = ref("");
const showInviteUserModal = ref(false);
const searchKeyword = ref("");

const authUser = usePage().props.auth.user;

const isCurrentUserAdmin = computed(() => props.group.role === "admin");
const isJoinedToGroup = computed(
    () =>
        props.group.role &&
        props.group.status === "approved" &&
        !props.group.is_owner
);

const props = defineProps({
    errors: Object,
    success: {
        type: String,
    },
    group: {
        type: Object,
    },
    posts: Object,
    users: Array,
    requests: Array,
    photos: Array,
});

const aboutForm = useForm({
    name: usePage().props.group.name,
    auto_approval: !!parseInt(usePage().props.group.auto_approval),
    about: usePage().props.group.about,
    privacy: props.group.privacy || "public",
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

function onThumbnailChange(event) {
    imagesForm.thumbnail = event.target.files[0];
    if (imagesForm.thumbnail) {
        const reader = new FileReader();
        reader.onload = () => {
            thumbnailImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.thumbnail);
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function resetThumbnailImage() {
    imagesForm.thumbnail = null;
    thumbnailImageSrc.value = null;
}

function submitCoverImage() {
    imagesForm.post(route("group.updateImages", props.group.slug), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true;
            resetCoverImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
}

function submitThurmbnailImage() {
    imagesForm.post(route("group.updateImages", props.group.slug), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true;
            resetThumbnailImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 3000);
        },
    });
}

function joinToGroup() {
    const form = useForm({});

    form.post(route("group.join", props.group.slug), {
        preserveScroll: true,
    });
}

function approveUser(user) {
    const form = useForm({
        user_id: user.id,
        action: "approve",
    });
    form.post(route("group.approveRequest", props.group.slug), {
        preserveScroll: true,
    });
}

function rejectUser(user) {
    const form = useForm({
        user_id: user.id,
        action: "reject",
    });
    form.post(route("group.approveRequest", props.group.slug), {
        preserveScroll: true,
    });
}

function deleteUser(user) {
    console.log("111");
    if (
        !window.confirm(
            `Are you sure you want to remove user "${user.name}" from this group?`
        )
    ) {
        return false;
    }

    const form = useForm({
        user_id: user.id,
    });
    form.delete(route("group.removeUser", props.group.slug), {
        preserveScroll: true,
    });
}

function onRoleChange(user, role) {
    console.log(user, role);
    const form = useForm({
        user_id: user.id,
        role,
    });
    form.post(route("group.changeRole", props.group.slug), {
        preserveScroll: true,
    });
}

function updateGroup() {
    aboutForm.put(route("group.update", props.group.slug), {
        preserveScroll: true,
    });
}

function leaveGroup() {
    if (!window.confirm("Are you sure you want to leave this group?")) {
        return false;
    }

    const form = useForm({});
    form.post(route("group.leave", props.group.slug), {
        preserveScroll: true,
        onSuccess: () => {
            window.location.reload();
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto h-full overflow-auto scroll-smooth">
            <div class="px-4">
                <!-- Notificaciones con animaciones -->
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

                <transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <div
                        v-if="errors.cover"
                        class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white rounded-lg animate-[shake_0.5s_ease-in-out]"
                    >
                        {{ errors.cover }}
                    </div>
                </transition>

                <!-- Cover image con efecto parallax -->
                <div
                    class="group relative bg-white dark:bg-slate-950 dark:text-gray-100 rounded-xl overflow-hidden shadow-xl transition-all duration-500 hover:shadow-2xl"
                >
                    <div class="relative h-[200px] overflow-hidden">
                        <img
                            :src="
                                coverImageSrc ||
                                group.cover_url ||
                                '/img/default_cover.jpg'
                            "
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"
                        ></div>
                    </div>

                    <!-- Botones de cover con animación -->
                    <div
                        class="absolute top-2 right-2 space-y-2"
                        v-if="isCurrentUserAdmin"
                    >
                        <transition
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="transform opacity-0 translate-y-2"
                            enter-to-class="transform opacity-100 translate-y-0"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="transform opacity-100 translate-y-0"
                            leave-to-class="transform opacity-0 translate-y-2"
                        >
                            <button
                                v-if="!coverImageSrc"
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
                        </transition>

                        <transition-group
                            tag="div"
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="transform opacity-0 translate-x-2"
                            enter-to-class="transform opacity-100 translate-x-0"
                            leave-active-class="transition ease-in duration-200"
                            leave-from-class="transform opacity-100 translate-x-0"
                            leave-to-class="transform opacity-0 translate-x-2"
                            class="flex gap-2"
                            v-if="coverImageSrc"
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

                    <!-- Thumbnail con animación 3D -->
                    <div class="flex">
                        <div
                            class="flex items-center justify-center relative group/thumbnail -mt-[64px] ml-[48px] w-[128px] h-[128px] rounded-full border-4 border-white dark:border-gray-800 shadow-xl"
                        >
                            <div
                                class="relative w-full h-full rounded-full overflow-hidden transition-transform duration-500 group-hover/thumbnail:rotate-3"
                            >
                                <img
                                    :src="
                                        thumbnailImageSrc ||
                                        group.thumbnail_url ||
                                        '/img/default_avatar.webp'
                                    "
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover/thumbnail:scale-110"
                                />
                                <button
                                    v-if="
                                        isCurrentUserAdmin && !thumbnailImageSrc
                                    "
                                    class="absolute inset-0 bg-black/50 text-gray-200 rounded-full opacity-0 flex items-center justify-center group-hover/thumbnail:opacity-100 transition-opacity duration-300"
                                >
                                    <CameraIcon
                                        class="w-8 h-8 animate-[pulse_2s_infinite]"
                                    />
                                    <input
                                        type="file"
                                        class="absolute inset-0 opacity-0 cursor-pointer"
                                        @change="onThumbnailChange"
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
                                v-if="isCurrentUserAdmin && thumbnailImageSrc"
                            >
                                <button
                                    key="cancel-thumbnail"
                                    @click="resetThumbnailImage"
                                    class="w-7 h-7 flex items-center justify-center bg-red-500/90 hover:bg-red-600 text-white rounded-full shadow-md transform transition-all duration-300 hover:scale-110"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>
                                <button
                                    key="submit-thumbnail"
                                    @click="submitThurmbnailImage"
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
                                    {{ group.name }}
                                </h2>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Grupo
                                    {{
                                        group.privacy === "public"
                                            ? "público"
                                            : "privado"
                                    }}
                                </p>
                            </div>

                            <div class="flex gap-2 flex-wrap justify-end">
                                <PrimaryButton
                                    v-if="!authUser"
                                    :href="route('login')"
                                    class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    Login to join
                                </PrimaryButton>

                                <PrimaryButton
                                    v-if="isCurrentUserAdmin"
                                    @click="showInviteUserModal = true"
                                    class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    Invite Users
                                </PrimaryButton>

                                <PrimaryButton
                                    v-if="
                                        authUser &&
                                        !group.role &&
                                        group.auto_approval
                                    "
                                    @click="joinToGroup"
                                    class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    Join to Group
                                </PrimaryButton>

                                <PrimaryButton
                                    v-if="
                                        authUser &&
                                        !group.role &&
                                        !group.auto_approval
                                    "
                                    @click="joinToGroup"
                                    class="transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    Request to join
                                </PrimaryButton>

                                <PrimaryButton
                                    v-if="
                                        authUser &&
                                        isJoinedToGroup &&
                                        !group.is_owner
                                    "
                                    @click="leaveGroup"
                                    class="bg-red-500 hover:bg-red-600 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    Leave Group
                                </PrimaryButton>
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
                        <Tab
                            v-if="isJoinedToGroup"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem
                                text="Users"
                                :selected="selected"
                                class="transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                            />
                        </Tab>
                        <Tab
                            v-if="isCurrentUserAdmin"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem
                                text="Pending Requests"
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
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem
                                text="About"
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
                                <CreatePost
                                    :group="group"
                                />
                                <PostList
                                    v-if="posts.data.length"
                                    :posts="posts.data"
                                    class="flex-1 space-y-4"
                                />
                                <div
                                    v-else
                                    class="py-8 text-center dark:text-gray-100 animate-[pulse_2s_infinite]"
                                >
                                    There are no posts in this group. Be the
                                    first and create it.
                                </div>
                            </template>
                            <div
                                v-else
                                class="py-8 text-center dark:text-gray-100 animate-[fadeIn_1s_ease-in-out]"
                            >
                                You don't have permission to view these posts.
                            </div>
                        </TabPanel>

                        <TabPanel
                            v-if="isJoinedToGroup"
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <div
                                class="mb-3 animate-[slideInDown_0.3s_ease-in-out]"
                            >
                                <TextInput
                                    :model-value="searchKeyword"
                                    placeholder="Type to search"
                                    class="w-full"
                                />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                                        v-for="(user, index) of users"
                                        :user="user"
                                        :key="user.id"
                                        :show-role-dropdown="isCurrentUserAdmin"
                                        :disable-role-dropdown="
                                            group.user_id === user.id
                                        "
                                        class="shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
                                        :style="`transition-delay: ${
                                            index * 50
                                        }ms`"
                                        @role-change="onRoleChange"
                                        @delete="deleteUser"
                                    />
                                </transition-group>
                            </div>
                        </TabPanel>

                        <TabPanel
                            v-if="isCurrentUserAdmin"
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <div
                                v-if="requests.length"
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
                                        v-for="(user, index) of requests"
                                        :user="user"
                                        :key="user.id"
                                        :for-approve="true"
                                        class="shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
                                        :style="`transition-delay: ${
                                            index * 50
                                        }ms`"
                                        @approve="approveUser"
                                        @reject="rejectUser"
                                    />
                                </transition-group>
                            </div>
                            <div
                                v-else
                                class="py-8 text-center dark:text-gray-100 animate-[fadeIn_1s_ease-in-out]"
                            >
                                There are no pending requests.
                            </div>
                        </TabPanel>

                        <TabPanel
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <TabPhotos :photos="photos" />
                        </TabPanel>

                        <TabPanel
                            class="transition-opacity duration-300 ease-in-out"
                        >
                            <template v-if="isCurrentUserAdmin">
                                <GroupForm
                                    :form="aboutForm"
                                    class="animate-[fadeIn_0.5s_ease-in-out]"
                                />
                                <PrimaryButton
                                    @click="updateGroup"
                                    class="mt-4 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                >
                                    Submit
                                </PrimaryButton>
                            </template>
                            <div
                                v-else
                                class="ck-content-output dark:text-gray-100 animate-[fadeIn_0.5s_ease-in-out]"
                                v-html="group.about"
                            ></div>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>

        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <InviteUserModal
                v-if="showInviteUserModal"
                v-model="showInviteUserModal"
            />
        </transition>
    </AuthenticatedLayout>
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
