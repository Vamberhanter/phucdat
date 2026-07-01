    <!-- Footer: Logo, Địa chỉ, Email, Hotline, © 2026 -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-top">
                <?php
                /* Logo footer = logo header (cùng theme mod: dnttvn_header_logo, fallback custom_logo) */
                $footer_logo_id = get_theme_mod('dnttvn_header_logo', '');
                if (!$footer_logo_id) {
                    $footer_logo_id = get_theme_mod('custom_logo', '');
                }
                $footer_dia_chi = get_theme_mod('dnttvn_footer_dia_chi', 'Số 141 Hoà Thành, Xã Ea Knuếc, Đắk Lắk');
                $footer_email  = get_theme_mod('dnttvn_footer_email', 'Congdongdoanhnhantrituevietnam@gmail.com');
                $footer_hotline = get_theme_mod('dnttvn_footer_hotline', '0972.27.88.66');
                ?>
                <div class="footer-logo">
                    <?php if ($footer_logo_id) : ?>
                        <?php $logo_url = wp_get_attachment_image_url($footer_logo_id, 'medium'); ?>
                        <?php if ($logo_url) : ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo-link">
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="footer-logo-img">
                            </a>
                        <?php else : ?>
                            <span class="footer-logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="footer-logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
                    <?php endif; ?>
                </div>
                <div class="footer-contact">
                    <?php if ($footer_dia_chi) : ?>
                        <p class="footer-row footer-address">
                            <span class="footer-icon" aria-hidden="true">📍</span>
                            <span>Trụ sở: <?php echo esc_html($footer_dia_chi); ?></span>
                        </p>
                    <?php endif; ?>
                    <?php if ($footer_email) : ?>
                        <p class="footer-row footer-email">
                            <span class="footer-icon" aria-hidden="true">✉️</span>
                            <a href="mailto:<?php echo esc_attr($footer_email); ?>">Email: <?php echo esc_html($footer_email); ?></a>
                        </p>
                    <?php endif; ?>
                    <?php if ($footer_hotline) : ?>
                        <p class="footer-row footer-hotline">
                            <span class="footer-icon" aria-hidden="true">📞</span>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[\s\.]+/', '', $footer_hotline)); ?>">Điện thoại: <?php echo esc_html($footer_hotline); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">© 2026 - Thiết kế bởi Bộ phận Truyền thông Marketing của CỘNG ĐỒNG DOANH NHÂN TRÍ TUỆ VIỆT NAM</p>
            </div>
        </div>
    </footer>
    
    <script>
        function toggleMenu() {
            var m = document.getElementById('mainMenu') || document.querySelector('.main-navigation .menu');
            if (m) m.classList.toggle('active');
        }
        var sliders = document.querySelectorAll('.business-card-small-image-slider');
        sliders.forEach(function(slider) {
            var slides = slider.querySelectorAll('.business-card-small-image-slide');
            if (!slides.length) return;
            var current = 0;
            slides.forEach(function(s, idx) {
                s.classList.toggle('active', idx === 0);
            });
            function show(index) {
                if (!slides.length) return;
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                slides.forEach(function(s, idx) { s.classList.toggle('active', idx === index); });
                current = index;
            }
            var container = slider.closest('.business-card-small-image');
            if (!container) return;
            var prevBtn = container.querySelector('.business-card-small-image-prev');
            var nextBtn = container.querySelector('.business-card-small-image-next');
            if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); show(current - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); show(current + 1); });
        });
    </script>
    <script>
    (function() {
        document.querySelectorAll('.dnttvn-excel-sheet-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var sheetIdx = this.getAttribute('data-sheet');
                var section = this.closest('.dnttvn-excel-section');
                if (!section) return;
                section.querySelectorAll('.dnttvn-excel-sheet-tab').forEach(function(t) { t.classList.remove('active'); });
                section.querySelectorAll('.dnttvn-excel-sheet-content').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                var target = section.querySelector('.dnttvn-excel-sheet-content[data-sheet="' + sheetIdx + '"]');
                if (target) target.classList.add('active');
            });
        });
        document.querySelectorAll('.dnttvn-table-scroll-wrapper').forEach(function(wrapper) {
            var table = wrapper.querySelector('table');
            if (table && table.scrollWidth > wrapper.clientWidth + 10) {
                wrapper.classList.add('has-scroll');
            }
        });
        window.addEventListener('resize', function() {
            document.querySelectorAll('.dnttvn-table-scroll-wrapper').forEach(function(wrapper) {
                var table = wrapper.querySelector('table');
                if (table && table.scrollWidth > wrapper.clientWidth + 10) {
                    wrapper.classList.add('has-scroll');
                } else {
                    wrapper.classList.remove('has-scroll');
                }
            });
        });
    })();
    </script>
    <?php wp_footer(); ?>
</body>
</html>
