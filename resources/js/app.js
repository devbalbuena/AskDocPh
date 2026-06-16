import './bootstrap';

import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;

window.axios = axios;
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

Alpine.start();
