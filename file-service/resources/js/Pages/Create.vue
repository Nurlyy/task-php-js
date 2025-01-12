<template>
    <default-layout>
        <div class="flex flex-col items-center w-full">
            <h1 class="text-white text-3xl font-bold">Создание записи</h1>
            <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">
            <div class="w-full">
                <form @submit.prevent="handleSubmit">
                    <div class="mt-10">
                        <div class="flex justify-around w-full items-center">
                            <label for="name" class="text-white text-xl font-semibold">Название файла</label>
                            <div class="mt-2">
                                <div class="flex items-center rounded-md bg-white">
                                    <input type="text" name="name" id="name" v-model="formData.name" max="255"
                                        class="w-full px-4 text-slate-900 font-bold py-2 transition-all bg-gray-300 hover:bg-gray-600 focus:bg-gray-600 rounded-md outline-none focus:outline-none"
                                        placeholder="Название" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">
                    <div class="mt-10">
                        <div class="flex justify-around w-full items-center">
                            <label for="cover-photo" class="text-white text-xl font-semibold">Загружаемый файл *</label>
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-white px-6 py-10"
                                @dragover.prevent="dragOver" @dragleave.prevent="dragLeave" @drop.prevent="drop"
                                :class="{ 'border-blue-500': isDragging }">
                                <div class="text-center">
                                    <div class="mt-4 flex text-sm/6 text-white">
                                        <label for="file-upload"
                                            class="relative cursor-pointer rounded-md px-2 bg-white font-semibold text-slate-900 hover:bg-gray-300 transition-all">
                                            <span>Загрузите файл </span>
                                            <input required id="file-upload" name="file-upload" type="file" class="sr-only"
                                                @change="handleFileChange" />
                                        </label>
                                        <p class="pl-1">или перенесите его сюда</p>
                                    </div>
                                    <p class="text-xs/5 text-white">Файл до 8 МБ</p>
                                    <div v-if="formData.file" class=" text-sm/6 text-gray-400 mt-4">
                                        Загруженный файл: <span class=" font-bold text-white">{{formData.file.name}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">

                    <div class="mt-12 flex items-center justify-center gap-x-6">
                        <a href="/" type="button"
                            class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-800 transition-all bg-slate-600 text-white px-4 py-2 rounded">Отмена</a>
                        <button type="submit"
                            class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-950 transition-all bg-slate-900 text-white px-4 py-2 rounded">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </default-layout>
</template>

<script>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import DefaultLayout from "../layouts/DefaultLayout.vue";
import useApi from '../composables/useApi';
import { API_ENDPOINTS } from '../constants/apiEndpoints';

export default {
    components: { DefaultLayout },
    name: "Create",
    setup() {
        const formData = ref({
            name: '',
            file: null,
        });

        const { postData } = useApi();

        const isDragging = ref(false);

        const handleFileChange = (event) => {
            formData.value.file = event.target.files[0];
        };

        const dragOver = () => {
            isDragging.value = true;
        };

        const dragLeave = () => {
            isDragging.value = false;
        };

        const drop = (event) => {
            isDragging.value = false;
            formData.value.file = event.dataTransfer.files[0];
        };

        const handleSubmit = () => {
            const formDataToSend = new FormData();
            formDataToSend.append('name', formData.value.name);
            formDataToSend.append('file', formData.value.file);

            try {
                const response = postData(API_ENDPOINTS.CREATE_FILE, formDataToSend);
                if(response.data) {
                    this.$inertia.get(route("index"))
                }
            } catch(error) {
                console.error("Произошла ошибка при отправке данных", error)
            }

        };

        return {
            formData,
            isDragging,
            handleFileChange,
            dragOver,
            dragLeave,
            drop,
            handleSubmit,
        };
    },
};
</script>

<style scoped>
.dragging {
    border-color: blue !important;
}
</style>