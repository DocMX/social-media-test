<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <div class="min-h-screen bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-white mb-10 text-center drop-shadow-md">
                PROFILE
            </h2>

            <!-- Card: Update Profile Info -->
            <div class="bg-black/90 backdrop-blur-md shadow-lg rounded-2xl p-6 mb-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-2xl"
                />
            </div>

            <!-- Card: Update Password -->
            <div class="bg-black/90 backdrop-blur-md shadow-lg rounded-2xl p-6 mb-8">
                <UpdatePasswordForm class="max-w-2xl" />
            </div>

            <!-- Card: Delete User -->
            <div class="bg-black/90 backdrop-blur-md shadow-lg rounded-2xl p-6">
                <DeleteUserForm class="max-w-2xl" />
            </div>
        </div>
    </div>
</template>
