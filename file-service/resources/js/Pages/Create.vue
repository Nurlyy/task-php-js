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

                <FileInput v-model="formData.file" label="Загрузите файл *" v-bind:originalName='originalName'
                    maxSize="8" />

                <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">
                <input type="hidden" name="_method" value="PUT" v-if="isEditMode">
                <div class="mt-12 flex items-center justify-center gap-x-6">
                    <a href="/" type="button"
                        class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-800 transition-all bg-slate-600 text-white px-4 py-2 rounded">Отмена</a>
                    <button type="button" @click="openSaveModal"
                        :disabled='(oldData.name == formData.name && formData.file == null) || isUploading'
                        :class="{ 'bg-slate-400 hover:bg-slate-400 cursor-default': (isEditMode ? (oldData.name == formData.name && formData.file == null) : (formData.file == null)) }"
                        class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-950 transition-all bg-slate-900 text-white px-4 py-2 rounded">
                        {{ isUploading ? "Загрузка..." : (isEditMode ? 'Сохранить изменения' : 'Создать файл') }}
                    </button>
                    <button v-if="isEditMode" type="button" @click="openDeleteModal"
                        class="text-decoration-none font-bold flex items-center justify-center hover:bg-red-950 transition-all bg-red-900 text-white px-4 py-2 rounded">
                        Удалить файл
                    </button>
                </div>
                <div v-if="progress > 0" class=" mt-10 mx-64">
                    <h1 class=" text-white font-bold text-xl text-center">Загрузка</h1>
                    <div class="w-full rounded-full h-2.5 dark:bg-slate-600 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: progress + '%' }"></div>
                    </div>
                </div>
            </form>

            <Modal :isOpen="isSaveModalOpen" :message="saveModalMessage" :confirmButtonText="saveModalConfirmButtonText"
                :cancelButtonText="saveModalCancelButtonText" @close="closeSaveModal" @confirm="handleSaveConfirm" />

            <Modal :isOpen="isDeleteModalOpen" :message="deleteModalMessage"
                :confirmButtonText="deleteModalConfirmButtonText" :cancelButtonText="deleteModalCancelButtonText"
                @close="closeDeleteModal" @confirm="handleDeleteConfirm" />
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
import Modal from '../components/Modal.vue';

export default {
    components: { DefaultLayout, TextInput, FileInput, Modal },
    setup() {
        const { props } = usePage();
        const { fetchData, postData, putData, deleteData } = useApi();

        const formData = reactive({
            name: '',
            file: null,
        });

        const oldData = reactive({
            name: '',
            file: null,
        });

        const isSaveModalOpen = ref(false);
        const saveModalMessage = ref('Вы уверены что вы хотите продолжить?');
        const saveModalConfirmButtonText = ref('Да, я  уверен');
        const saveModalCancelButtonText = ref('Нет, отмена');

        const isDeleteModalOpen = ref(false);
        const deleteModalMessage = ref('Вы уверены что хотите удалить данный файл?');
        const deleteModalConfirmButtonText = ref('Удалить файл');
        const deleteModalCancelButtonText = ref('Отмена');

        const isEditMode = computed(() => !!props.id || false);

        const originalName = ref('');

        const progress = ref(0);
        const isUploading = ref(false);


        const loadFileDetails = async () => {
            if (!isEditMode.value) return;

            try {
                const response = await fetchData(API_ENDPOINTS.FILE_DETAIL(props.id));
                formData.name = response.name;
                formData.file = null;
                oldData.name = response.name;
                oldData.file = response.file;
                originalName.value = response.original_file_name;
            } catch (error) {
                console.error('Ошибка при загрузке данных:', error);
            }
        };

        const handleSubmit = () => {
            openSaveModal();
        };

        const openSaveModal = () => {
            saveModalMessage.value = isEditMode.value ? 'Вы уверены, что хотите сохранить изменения?' : 'Вы уверены, что хотите создать файл?';
            saveModalConfirmButtonText.value = isEditMode.value ? 'Сохранить изменения' : 'Создать файл';
            saveModalCancelButtonText.value = 'Отмена';
            isSaveModalOpen.value = true;
        };

        const closeSaveModal = () => {
            isSaveModalOpen.value = false;
        };

        const handleSaveConfirm = async () => {
            closeSaveModal();
            isUploading.value = true;
            progress.value = 0;
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
                if (isEditMode.value) {
                    await postData(endpoint, payload, progress);
                } else {
                    await postData(endpoint, payload, progress);
                }
                closeSaveModal();
                window.location.href = '/';
            } catch (error) {
                console.error('Ошибка при отправке данных:', error);
            } finally {
                isUploading.value = false;
            }
        };



        const openDeleteModal = () => {
            deleteModalMessage.value = 'Вы уверены, что хотите удалить этот файл?';
            deleteModalConfirmButtonText.value = 'Удалить файл';
            deleteModalCancelButtonText.value = 'Отмена';
            isDeleteModalOpen.value = true;
        };

        const closeDeleteModal = () => {
            isDeleteModalOpen.value = false;
        };

        const handleDeleteConfirm = async () => {
            try {
                const endpoint = API_ENDPOINTS.DELETE_FILE(props.id);
                await deleteData(endpoint);
                closeDeleteModal();
                window.location.href = '/';
            } catch (error) {
                console.error('Ошибка при удалении файла:', error);
            }
        };

        onMounted(loadFileDetails);

        return {
            formData,
            oldData,
            isEditMode,
            handleSubmit,
            originalName,
            isSaveModalOpen,
            saveModalMessage,
            saveModalConfirmButtonText,
            saveModalCancelButtonText,
            openSaveModal,
            closeSaveModal,
            handleSaveConfirm,
            isDeleteModalOpen,
            deleteModalMessage,
            deleteModalConfirmButtonText,
            deleteModalCancelButtonText,
            openDeleteModal,
            closeDeleteModal,
            handleDeleteConfirm,
            progress,
            isUploading,
        };
    },
};
</script>
