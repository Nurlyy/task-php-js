<template>
    <default-layout>
        <div>
            <h1 class="text-2xl font-bold mb-4 text-white text-center">
                {{ isEditMode ? 'Редактирование файла' : 'Создание файла' }}
            </h1>
            <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">

            <form @submit.prevent="handleSubmit">
                <TextInput v-model="formData.name" label="Название файла" placeholder="Введите название" />

                <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">

                <FileInput v-model="formData.file" label="Загрузите файл *" v-bind:originalName='originalName' maxSize="8" />

                <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">
                <input type="hidden" name="_method" value="PUT" v-if="isEditMode">
                <div class="mt-12 flex items-center justify-center gap-x-6">
                    <a href="/" type="button"
                        class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-800 transition-all bg-slate-600 text-white px-4 py-2 rounded">Отмена</a>
                    <button type="submit"
                        class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-950 transition-all bg-slate-900 text-white px-4 py-2 rounded">
                        {{ isEditMode ? 'Сохранить изменения' : 'Создать файл' }}
                    </button>
                </div>
            </form>
        </div>
    </default-layout>
</template>

<script>
import TextInput from '../components/TextInput.vue';
import FileInput from '../components/FileInput.vue';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import { ref, reactive, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import useApi from '../composables/useApi';
import { API_ENDPOINTS } from '../constants/apiEndpoints';

export default {
    components: { DefaultLayout, TextInput, FileInput },
    setup() {
        const { props } = usePage(); 
        const { fetchData, postData, putData } = useApi();

        const formData = reactive({
            name: '',
            file: null,
        });

        const isEditMode = computed(() => !!props.id || false); 

        const originalName = ref('');
        
        const loadFileDetails = async () => {
            if (!isEditMode.value) return;

            try {
                const response = await fetchData(API_ENDPOINTS.FILE_DETAIL(props.id));
                formData.name = response.name;
                formData.file = null; 
                originalName.value = response.original_file_name;
            } catch (error) {
                console.error('Ошибка при загрузке данных:', error);
            }
        };

        const handleSubmit = async () => {
            try {
                const endpoint = isEditMode.value
                    ? API_ENDPOINTS.UPDATE_FILE(props.id)
                    : API_ENDPOINTS.CREATE_FILE;

                const payload = new FormData();
                payload.append('name', formData.name);
                if (formData.file) {
                    payload.append('file', formData.file);
                }

                if (isEditMode.value) {
                    payload.append('_method', 'PUT');
                }

                if (isEditMode.value == false) {
                    await postData(endpoint, payload);
                } else {
                    await putData(endpoint, payload);
                }

                alert(isEditMode.value ? 'Файл успешно обновлен!' : 'Файл успешно создан!');
            } catch (error) {
                console.error('Ошибка при отправке данных:', error);
            }
        };

        onMounted(loadFileDetails);

        return {
            formData,
            isEditMode,
            handleSubmit,
            originalName,
        };
    },
};
</script>
