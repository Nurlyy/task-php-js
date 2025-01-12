import axios from "axios";

export default function useApi() {
    const fetchData = async (endpoint) => {
        try {
            const response = await axios.get(endpoint);
            return response.data;
        } catch (error) {
            console.error('Ошибка при получении данных:', error);
            throw error;
        }
    };

    const postData = async (endpoint, data) => {
        try {
            const response = await axios.post(endpoint, data);
            return response.data;
        } catch (error) {
            console.error('Ошибка при отправке данных:', error);
            throw error;
        }
    };

    const putData = async (endpoint, data) => {
        try {
            const response = await axios.put(endpoint, data);
            return response.data;
        } catch (error) {
            console.error('Ошибка при обновлении данных: ', error);
            throw error;
        }
    }

    const deleteData = async (endpoint) => {
        try {
            const response = await axios.delete(endpoint);
            return response.data;
        } catch (error) {
            console.error('Ошибка при удалении данных: ', error);
            throw error;
        }
    }

    return {
        fetchData,
        postData,
        putData, 
        deleteData,
    };
}