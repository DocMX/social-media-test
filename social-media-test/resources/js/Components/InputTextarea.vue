<template>
  <div>
    <label v-if="label" :for="id" class="block mb-1 font-medium text-gray-700">
      {{ label }}
    </label>
    <textarea
      :id="id"
      :name="name"
      :rows="rows"
      :placeholder="placeholder"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
      :class="inputClass"
      v-model="modelValueLocal"
    ></textarea>
    <p v-if="error" class="text-sm text-red-600 mt-1">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: String,
  label: String,
  name: String,
  id: String,
  rows: {
    type: Number,
    default: 4,
  },
  placeholder: String,
  error: String,
  inputClass: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const modelValueLocal = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})
</script>
