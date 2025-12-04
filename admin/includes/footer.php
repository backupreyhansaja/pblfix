</main>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    // Mobile Menu Toggle
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const backdrop = document.getElementById('backdrop');
    const closeMobileMenu = document.getElementById('closeMobileMenu');

    // Open mobile menu
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function () {
            mobileSidebar.classList.add('active');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        });
    }

    // Close mobile menu
    function closeMenu() {
        mobileSidebar.classList.remove('active');
        backdrop.classList.remove('active');
        document.body.style.overflow = ''; // Restore body scroll
    }

    if (closeMobileMenu) {
        closeMobileMenu.addEventListener('click', closeMenu);
    }

    // Close menu when clicking backdrop
    if (backdrop) {
        backdrop.addEventListener('click', closeMenu);
    }

    // Close menu when clicking any menu link (mobile only)
    const mobileMenuLinks = mobileSidebar.querySelectorAll('a');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function () {
            // Small delay to allow navigation
            setTimeout(closeMenu, 150);
        });
    });

    // Confirm delete
    function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
        return confirm(message);
    }

    // Image preview
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    const quill = new Quill('#editor', {
        theme: 'snow'
    });
</script>
</body>

</html>