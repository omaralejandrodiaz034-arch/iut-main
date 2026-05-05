import axios from 'axios';
import Chart from 'chart.js/auto';

window.axios = axios;
window.Chart = Chart;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
