import './bootstrap';

import Alpine from 'alpinejs';

import {
    Chart,
    LineElement,
    PointElement,
    LineController,
    BarElement,
    BarController,
    CategoryScale,
    LinearScale,
    Filler,
    Tooltip,
    Legend,
} from 'chart.js';

Chart.register(
    LineElement, PointElement, LineController,
    BarElement, BarController,
    CategoryScale, LinearScale, Filler, Tooltip, Legend,
);

window.Chart = Chart;
window.Alpine = Alpine;

Alpine.start();
