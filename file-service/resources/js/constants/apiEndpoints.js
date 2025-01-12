export const API_ENDPOINTS = {
    FILES: (page) => `/api/files?page=${page}`,
    FILE_DETAIL: (id) => `/api/files/${id}`,
    CREATE_FILE: '/api/files',
    UPDATE_FILE: (id) => `/api/files/${id}`,
    DELETE_FILE: (id) => `/api/files/${id}`,
    SUGGESTIONS: (enteredData) => `/api/suggestions?q=${enteredData}`,
    SEARCH: (page, enteredData) => `/api/files?page=${page}` + ((enteredData.length > 0) ? `&search=${enteredData}` : ''),
}