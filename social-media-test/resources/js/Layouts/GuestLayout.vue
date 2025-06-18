<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-between px-4">
    <!-- Contenido principal centrado -->
    <div class="flex-grow flex items-center justify-center">
      <div class="flex flex-col md:flex-row items-center bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden max-w-4xl w-full">
        <!-- Imagen a la izquierda -->
        <div class="hidden md:block w-1/2 bg-gray-200 dark:bg-gray-700">
          <img
            src="/img/web-lox-image-2x.png"
            alt="Instagram Preview"
            class="h-full w-full object-cover"
          />
        </div>

        <!-- Formulario a la derecha -->
        <div class="w-full md:w-1/2 p-8">
          <div class="flex justify-center mb-6">
            <Link href="/">
              <ApplicationLogo class="h-14 w-auto text-indigo-600 dark:text-indigo-300" />
            </Link>
          </div>
          <slot />
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-6 text-sm text-gray-500 dark:text-gray-400">
      <div class="space-x-4 mb-2">
        <a href="#" class="hover:underline">About</a>
        <a href="#" class="hover:underline">Help</a>
        <a href="#" class="hover:underline">Press</a>
        <a href="#" class="hover:underline">API</a>
        <a href="#" class="hover:underline">Jobs</a>
        <a href="#" class="hover:underline">Privacy</a>
        <a href="#" class="hover:underline">Terms</a>
        <a href="#" class="hover:underline">Locations</a>
        <a href="#" class="hover:underline">Language</a>
      </div>
      <p>&copy; 2025 By Jorge Vega ❤️</p>
    </footer>
  </div>
</template>
