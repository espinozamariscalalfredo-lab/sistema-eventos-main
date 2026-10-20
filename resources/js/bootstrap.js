// FRONTEND. 20/10/2026: prepara Axios para enviar solicitudes HTTP desde el navegador.
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
