<script>
    // Pasang tema secepat mungkin (sebelum CSS lain dimuat) agar tidak "kilat"
    // ke tema default saat halaman refresh.
    (function () {
        var theme = localStorage.getItem('tasklab-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>
