<template>
    <default-layout>
        <div class="flex flex-col items-center w-full">
            <div class="flex flex-row space-x-4">
                <SearchInput @search="handleSearch" :initialQuery="searchQuery" class="w-[420px]" />
                <a href="/create"
                    class="text-decoration-none font-bold flex items-center justify-center hover:bg-slate-950 transition-all bg-slate-900 text-white px-4 py-2 rounded">
                    <h5>Создать</h5>
                </a>
            </div>
            <hr class="bg-gray-600 h-[1px] mt-12 w-full outline-none border-none">
            <div class="container mx-auto p-4">
                <div v-if="items && items.data.length" class="">
                    <table class="min-w-full divide-y  table-fixed divide-gray-700">
                        <thead class="">
                            <tr>
                                <th scope="col"
                                    class="py-3 px-6 text-xs font-medium tracking-wider text-left uppercase text-gray-400">
                                    Превью
                                </th>
                                <th scope="col"
                                    class="py-3 px-6 text-xs font-medium tracking-wider text-left uppercase text-gray-400">
                                    Название
                                </th>
                                <th scope="col"
                                    class="py-3 px-6 text-xs font-medium tracking-wider text-left uppercase text-gray-400">
                                    Расширение
                                </th>
                                <th scope="col"
                                    class="py-3 px-6 text-xs font-medium tracking-wider text-left uppercase text-gray-400">
                                    Размер
                                </th>
                                <th scope="col" class="p-4">
                                    <span class="sr-only">Действия</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class=" divide-y bg-gray-800 divide-gray-700">
                            <tr class="hover:bg-slate-800" v-for="item in items.data" :key="item.id">
                                <td class="py-4 px-6 text-sm font-medium whitespace-nowrap text-white">
                                    <img :src="'/storage/' + item.preview" alt="">
                                </td>
                                <td class="py-4 px-6 text-sm font-medium whitespace-nowrap text-white">
                                    {{ item.name }}</td>
                                <td class="py-4 px-6 text-sm font-medium whitespace-nowrap text-white">
                                    .{{ item.extension }}</td>
                                <td class="py-4 px-6 text-sm font-medium whitespace-nowrap text-white">
                                    {{ item.size }} МБ</td>
                                <td class="py-4 px-6 text-sm font-medium whitespace-nowrap text-white">
                                    <div class="flex justify-center items-center space-x-4">
                                        <a :href="'/storage/' + item.location" :download="'/storage/' + item.location"
                                            class="w-fit p-2 bg-white rounded transition-all hover:bg-gray-400"><img
                                                class=" w-6 h-6" :src="'/icons/download.svg'" /></a>
                                        <a :href="'/edit/' + item.id"
                                            class="w-fit p-2 bg-white rounded transition-all hover:bg-gray-400"><img
                                                class=" w-6 h-6" :src="'/icons/edit.svg'" /></a>
                                        <button @click="openDeleteModal(item.id)"
                                            class="w-fit p-2 bg-white rounded transition-all hover:bg-gray-400 cursor-pointer">
                                            <img class="w-6 h-6" :src="'/icons/delete.svg'" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div v-else class="text-center mt-4">Объектов не найдено.</div>
                <div v-if="items && items.meta.total > 0" class="flex justify-center mt-4">
                    <template v-for="(link, index) in items.meta.last_page" :key="index">
                        <div class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded bg-slate-600 cursor-pointer hover:bg-slate-800 focus:border-indigo-500 focus:text-indigo-500"
                            :class="{ 'bg-slate-400 text-white': link === items.meta.current_page }" preserve-scroll
                            @click="paginate(link)">
                            {{ link }}
                        </div>
                    </template>
                </div>
            </div>
            <Modal :isOpen="isDeleteModalOpen" :message="deleteModalMessage"
                :confirmButtonText="deleteModalConfirmButtonText" :cancelButtonText="deleteModalCancelButtonText"
                @close="closeDeleteModal" @confirm="handleDeleteConfirm" />
        </div>
    </default-layout>
</template>
  
<script>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import SearchInput from '../components/SearchInput.vue';
import DefaultLayout from '../layouts/DefaultLayout.vue';
import useApi from '../composables/useApi';
import { API_ENDPOINTS } from '../constants/apiEndpoints';
import Modal from '../components/Modal.vue';

export default {
    components: { SearchInput, DefaultLayout, Modal },
    name: 'Index',
    setup() {
        const page = usePage();
        const items = ref(page.props.items);
        const searchQuery = ref('');
        const currentPage = ref(1);

        const { fetchData, postData, putData, deleteData } = useApi();

        const isDeleteModalOpen = ref(false);
        const deleteModalMessage = ref('Вы уверены, что хотите удалить этот файл?');
        const deleteModalConfirmButtonText = ref('Удалить файл');
        const deleteModalCancelButtonText = ref('Отмена');
        const itemIdToDelete = ref(null);

        const urlParams = new URLSearchParams(window.location.search);
        searchQuery.value = urlParams.get('search') || '';

        const fetchItems = async () => {
            try {
                const response = await fetchData(API_ENDPOINTS.SEARCH(currentPage.value, searchQuery.value));
                items.value = response;
                console.log(response.data, response.links, response.meta)
                // totalPages.value = Math.ceil(response.total / perPage);
            } catch (error) {
                console.error("Failed to fetch items: ", error);
                items.value = [];
            }
        };

        fetchItems();

        const handleSearch = (query) => {
            const url = `?search=${encodeURIComponent(query)}`;
            fetchItems();
            window.location.href = url;
        };

        const paginate = (page) => {
            currentPage.value = page;
            fetchItems();
        }

        const openDeleteModal = (id) => {
            itemIdToDelete.value = id;
            deleteModalMessage.value = 'Вы уверены, что хотите удалить этот файл?';
            deleteModalConfirmButtonText.value = 'Удалить файл';
            deleteModalCancelButtonText.value = 'Отмена';
            isDeleteModalOpen.value = true;
        };

        const closeDeleteModal = () => {
            itemIdToDelete.value = null;
            isDeleteModalOpen.value = false;
        };

        const handleDeleteConfirm = async () => {
            if (!itemIdToDelete.value) {
                console.error('Item ID is not set for deletion.');
                closeDeleteModal();
                return;
            }

            try {
                const endpoint = API_ENDPOINTS.DELETE_FILE(itemIdToDelete.value);
                await deleteData(endpoint);
                closeDeleteModal();
                fetchItems(); 
            } catch (error) {
                console.error('Ошибка при удалении файла:', error);
            }
        };

        watch(
            () => page.props.items,
            (newItems) => {
                items.value = newItems;
            }
        );

        return {
            items,
            searchQuery,
            handleSearch,
            paginate,
            isDeleteModalOpen,
            deleteModalMessage,
            deleteModalConfirmButtonText,
            deleteModalCancelButtonText,
            openDeleteModal,
            closeDeleteModal,
            handleDeleteConfirm,
        };
    },
};
</script>
  
<style scoped>
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

li:hover {
    background-color: #f0f0f0;
}
</style>