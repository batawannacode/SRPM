import "./globals/theme.js";
import "./globals/modals.js";
import ApexCharts from "apexcharts";
import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

window.ApexCharts = ApexCharts;

Livewire.start();
