import "./globals/theme.js";
import "./globals/modals.js";
import "../../vendor/spatie/livewire-filepond/resources/dist/filepond";
import ApexCharts from "apexcharts";
import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

window.ApexCharts = ApexCharts;

document.addEventListener("livewire:initialized", () => {
    Livewire.on("download-zip", (event) => {
        const link = document.createElement("a");
        link.href = event.url;
        link.download = "";
        document.body.appendChild(link);
        link.click();
        link.remove();
    });
});

Livewire.start();
