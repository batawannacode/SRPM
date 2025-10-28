<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>
<script>
    // Load dark mode before page renders to prevent flicker
    const loadDarkMode = () => {
        const theme = localStorage.getItem('theme') ?? 'light'

        if (
            theme === 'dark' ||
            (theme === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)')
                    .matches)
        ) {
            document.documentElement.classList.add('dark')
        }
    }

    // Initialize on page load
    loadDarkMode();

    // Reinitialize after Livewire navigation (for spa mode)
    document.addEventListener('livewire:navigated', function () {
        loadDarkMode();
    });

</script>
