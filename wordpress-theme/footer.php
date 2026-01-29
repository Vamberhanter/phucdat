    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <p><strong>Thông tin chân trang</strong></p>
                <p><?php bloginfo('name'); ?></p>
                <p>Địa chỉ: [Địa chỉ liên hệ] | Email: [Email liên hệ] | Hotline: [Số điện thoại]</p>
                <p>&copy; <?php echo date('Y'); ?> - Bản quyền thuộc về <?php bloginfo('name'); ?></p>
            </div>
        </div>
    </footer>
    
    <script>
        // Toggle Menu Function
        function toggleMenu() {
            const menu = document.getElementById('mainMenu');
            if (menu) {
                menu.classList.toggle('active');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Banner Carousel Auto-play
            const slides = document.querySelectorAll('.banner-slide');
            if (slides.length > 0) {
                let currentSlide = 0;

                function showSlide(index) {
                    slides.forEach((slide, i) => {
                        slide.classList.remove('active');
                        if (i === index) {
                            slide.classList.add('active');
                        }
                    });
                }

                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }

                // Auto-play every 5 seconds
                setInterval(nextSlide, 5000);
            }

            // Mobile Accordion for Both Sidebars
            const mobileToggles = document.querySelectorAll('.column-header.mobile-toggle');
            mobileToggles.forEach(function(mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    this.classList.toggle('collapsed');
                    if (content) {
                        content.classList.toggle('mobile-collapsed');
                    }
                });
            });

            // Small image sliders (business gallery) with arrows
            const sliders = document.querySelectorAll('.business-card-small-image-slider');
            sliders.forEach(function(slider) {
                const slides = slider.querySelectorAll('.business-card-small-image-slide');
                if (!slides.length) return;

                let current = 0;
                // Ensure exactly one active
                slides.forEach(function(s, idx) {
                    if (idx === 0) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });

                function show(index) {
                    if (!slides.length) return;
                    if (index < 0) index = slides.length - 1;
                    if (index >= slides.length) index = 0;
                    slides.forEach(function(s, idx) {
                        if (idx === index) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                    current = index;
                }

                const container = slider.closest('.business-card-small-image');
                if (!container) return;
                const prevBtn = container.querySelector('.business-card-small-image-prev');
                const nextBtn = container.querySelector('.business-card-small-image-next');

                if (prevBtn) {
                    prevBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        show(current - 1);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        show(current + 1);
                    });
                }
            });
        });
    </script>
    <?php wp_footer(); ?>
</body>
</html>
