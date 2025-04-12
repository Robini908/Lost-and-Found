import './bootstrap';
import '../css/app.css';
import "swiper/css/bundle";
import tippy from "tippy.js";
import "tippy.js/dist/tippy.css";
import "tippy.js/animations/scale.css";
import DataTable from 'datatables.net-dt';
import flatpickr from 'flatpickr';


import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";
import ToastComponent from "../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts";
import { Input, initTWE } from "tw-elements";
window.flatpickr = flatpickr

// Initialize Alpine.js stores and plugins
document.addEventListener('alpine:init', () => {
    Alpine.store('darkMode', {
        on: false,
        toggle() {
            this.on = !this.on;
        }
    });

    Alpine.store('sidebar', {
        open: false,
        toggle() {
            this.open = !this.open;
        }
    });
});

// Initialize Tailwind Elements
initTWE({ Input }, { allowReinits: true });

// Register the ToastComponent plugin with Alpine.js
Alpine.plugin(ToastComponent);

// Start Livewire
Livewire.start();

// Function to initialize tooltips
function initializeTooltips() {
    tippy("[data-tippy-content]", {
        appendTo: document.body,
        allowHTML: true,
        hideOnClick: true,
        interactive: true,
        theme: "light-border",
        arrow: true,
    });
}

// Function to initialize DataTables
function initializeDataTables() {
    if (typeof DataTable !== 'undefined') {
        document.querySelectorAll('table.display').forEach(table => {
            if (!table.DataTable) {
                new DataTable(table, {
                    // Initialization options
                });
            }
        });
    }
}

// Initialize tooltips and DataTables on DOMContentLoaded
document.addEventListener("DOMContentLoaded", () => {
    initializeTooltips();
    initializeDataTables();
});

// Reinitialize tooltips and DataTables after Livewire updates the DOM
Livewire.hook('morphed', (el, component) => {
    initializeTooltips();
    initializeDataTables();
});
