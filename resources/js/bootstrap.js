import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Thêm CSRF token vào header của mọi request
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Force reload CSRF token for the first request
document.addEventListener('DOMContentLoaded', function() {
    axios.get('/sanctum/csrf-cookie').then(response => {
        console.log('CSRF token refreshed');
    });
});
