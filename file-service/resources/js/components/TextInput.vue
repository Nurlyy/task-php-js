<template>
    <div class="flex justify-around w-full items-center">
        <label :for="id" class="text-white text-xl font-semibold">{{ label }}</label>
        <div class="mt-2">
            <div class="flex items-center rounded-md bg-white">
                <input :type="type" :id="id" :name="name" :maxlength="maxlength" :placeholder="placeholder"
                    :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
                    class="w-full px-4 text-slate-900 font-bold py-2 transition-all bg-gray-300 hover:bg-gray-600 focus:bg-gray-600 rounded-md outline-none focus:outline-none" />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'TextInput',
    props: {
        modelValue: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: 'Текстовое поле',
        },
        placeholder: {
            type: String,
            default: 'Введите текст',
        },
        name: {
            type: String,
            default: 'text-input',
        },
        id: {
            type: String,
            default: () => `input-${Math.random().toString(36).substr(2, 9)}`,
        },
        maxlength: {
            type: Number,
            default: 255,
        },
        type: {
            type: String,
            default: 'text',
        },
    },
    emits: ['update:modelValue'],
});
</script>
