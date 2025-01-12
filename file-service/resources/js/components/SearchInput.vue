<template>
    <div class="relative">
        <div
            class="w-full px-4 text-slate-900 font-bold py-2 transition-all bg-gray-300 hover:bg-gray-600 focus:bg-gray-600 rounded-md grid grid-cols-8 gap-4">
            <input type="text" v-model="enteredData" @input="debouncedFetchSuggestions"
                class="bg-transparent outline-none focus:outline-none col-span-6" placeholder="Введите для поиска" />
            <button @click="handleSearch" class="hover:bg-slate-950 bg-slate-900 text-white px-4 py-2 rounded col-span-2">
                Поиск
            </button>
        </div>
        <ul v-if="suggestions.length" class="mt-2 bg-white border rounded shadow absolute w-full">
            <li v-for="(suggestion, index) in suggestions" :key="index">
                <div class="px-4 py-2 hover:bg-gray-200 cursor-pointer"
                @click="selectSuggestion(suggestion)">
                    {{ suggestion.name }}
                </div>
                <div class="h-1 w-full bg-gray-400 border-none outline-none"></div>
            </li>
        </ul>
    </div>
</template>
  
<script>
import { ref, watch } from 'vue';
import useApi from '../composables/useApi';
import { API_ENDPOINTS } from '../constants/apiEndpoints';
import { debounce } from '../utils/debounce';

export default {
    name: "SearchInput",
    props: {
        initialQuery: {
            type: String,
            default: '',
        },
    },
    emits: ['search'],
    setup(props, { emit }) {
        const { fetchData } = useApi();
        const enteredData = ref(props.initialQuery);
        const suggestions = ref([]);

        const fetchSuggestions = async () => {
            if (!enteredData.value) {
                suggestions.value = [];
                return;
            }
            try {
                const response = await fetchData(API_ENDPOINTS.SUGGESTIONS(enteredData.value));
                suggestions.value = response;
            } catch (error) {
                console.error("Failed to fetch suggestions: ", error);
                suggestions.value = [];
            }
        };

        const debouncedFetchSuggestions = debounce(fetchSuggestions, 300);

        const handleSearch = () => {
            emit('search', enteredData.value);
        };

        const selectSuggestion = (suggestion) => {
            enteredData.value = suggestion.name;
            suggestions.value = [];
            emit('search', suggestion.name);
        };

        watch(
            () => props.initialQuery,
            (newQuery) => {
                enteredData.value = newQuery;
                fetchSuggestions();
            }
        );

        return {
            enteredData,
            suggestions,
            debouncedFetchSuggestions,
            selectSuggestion,
            handleSearch,
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