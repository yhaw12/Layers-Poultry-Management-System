// resources/js/app.js

import './bootstrap'; // default from Laravel
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
Alpine.start();

// Example usage
window.Chart = Chart;
window.ApexCharts = ApexCharts;

import '@fortawesome/fontawesome-free/css/all.min.css';

