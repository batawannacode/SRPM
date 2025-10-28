@livewireScriptConfig
<script>
    // Ensure dark mode is applied after scripts load, this is also required to prevent flickering when many livewire component changes independently
    loadDarkMode();

    document.addEventListener("DOMContentLoaded", () => {
        window.addEventListener('popstate', function (event) {
            if (event.state) {
                Livewire.navigate(window.location.pathname);
            }
        });
    });

</script>
