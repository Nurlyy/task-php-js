<template>
    <div class="flex justify-around w-full items-center">
        <label :for="id" class="text-white text-xl font-semibold">{{ label }}</label>
        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-white px-6 py-10"
            @dragover.prevent="dragOver" @dragleave.prevent="dragLeave" @drop.prevent="drop"
            :class="{ 'border-blue-500': isDragging }">
            <div class="text-center">
                <div class="mt-4 flex text-sm/6 text-white">
                    <label :for="id"
                        class="relative cursor-pointer rounded-md px-2 bg-white font-semibold text-slate-900 hover:bg-gray-300 transition-all">
                        <span>Загрузите файл</span>
                        <input :id="id" :name="name" type="file" class="sr-only" @change="handleFileChange" />
                    </label>
                    <p class="pl-1">или перенесите его сюда</p>
                </div>
                <p class="text-xs/5 text-white">Файл до {{ maxSize }} МБ</p>
                <div v-if="file || originalName" class="text-sm/6 text-gray-400 mt-4">
                    Загруженный файл: <span class="font-bold text-white">{{ file ? file.name : originalName }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, defineComponent } from 'vue';

export default defineComponent({
    name: 'FileInput',
    props: {
        modelValue: {
            type: File,
            required: false,
        },
        label: {
            type: String,
            default: 'Загрузите файл',
        },
        name: {
            type: String,
            default: 'file-input',
        },
        id: {
            type: String,
            default: () => `file-${Math.random().toString(36).substr(2, 9)}`,
        },
        maxSize: {
            type: Number,
            default: 8,
        },
        originalName: {
            type: String,
            required: false,
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const isDragging = ref(false);
        const file = ref(props.modelValue || null);

        const dragOver = () => {
            isDragging.value = true;
        };

        const dragLeave = () => {
            isDragging.value = false;
        };

        const drop = (event) => {
            isDragging.value = false;
            const droppedFile = event.dataTransfer.files[0];
            updateFile(droppedFile);
        };

        const handleFileChange = (event) => {
            const selectedFile = event.target.files[0];
            updateFile(selectedFile);
        };

        const updateFile = (newFile) => {
            if (newFile.size / 1024 / 1024 > props.maxSize) {
                alert(`Файл превышает максимальный размер ${props.maxSize} МБ.`);
                return;
            }
            file.value = newFile;
            emit('update:modelValue', newFile);
        };

        console.log(props.originalName, 'some')

        return {
            isDragging,
            file,
            dragOver,
            dragLeave,
            drop,
            handleFileChange,
        };
    },
});
</script>
