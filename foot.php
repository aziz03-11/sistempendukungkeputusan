</main> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // 1. Script Toggle Sidebar untuk Mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebarMenu').classList.toggle('show');
    });

    // 2. Script otomatis menandai menu Sidebar yang sedang aktif
    document.addEventListener("DOMContentLoaded", function() {
        var currentUrl = window.location.href;
        var navLinks = document.querySelectorAll('.sidebar .nav-link');
        
        navLinks.forEach(function(link) {
            // Hapus .active dari semua link dulu
            link.classList.remove('active');
            
            // Tambahkan .active jika URL cocok
            if (currentUrl.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            }
        });
    });
</script>

</body>
</html>