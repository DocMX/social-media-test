<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Log In" />

    <div class="w-full max-w-xs mx-auto">
      <div class="text-center mb-6">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white">MyApp</h1>
        <p class="text-sm text-gray-500 dark:text-gray-300 mt-1">
          Sign in to your account
        </p>
      </div>

      <div v-if="status" class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <TextInput
            id="email"
            type="email"
            v-model="form.email"
            required
            autofocus
            placeholder="Email"
            class="w-full"
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div>
          <TextInput
            id="password"
            type="password"
            v-model="form.password"
            required
            placeholder="Password"
            class="w-full"
            autocomplete="current-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
            <Checkbox v-model:checked="form.remember" /> Remember me
          </label>

          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="text-indigo-600 hover:underline dark:text-indigo-400"
          >
            Forgot your password?
          </Link>
        </div>

        <PrimaryButton
          class="w-full py-2"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          <span v-if="!form.processing">Sign in</span>
          <span v-else class="flex items-center justify-center">
            <svg
              class="animate-spin h-5 w-5 mr-2 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v8H4z"
              />
            </svg>
            Processing...
          </span>
        </PrimaryButton>
      </form>

      <div class="mt-6 text-center text-sm">
        <span class="text-gray-600 dark:text-gray-400">¿No tienes cuenta?</span>
        <Link
          :href="route('register')"
          class="ml-1 text-indigo-600 hover:underline dark:text-indigo-400"
        >
          Create new account
        </Link>
      </div>
    </div>
  </GuestLayout>
</template>
