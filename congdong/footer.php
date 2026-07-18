<?php
/**
 * Footer — layout hiện đại đồng bộ header (navy / orange).
 */
$footer_logo_id = get_theme_mod('dnttvn_header_logo', '');
if (!$footer_logo_id) {
    $footer_logo_id = get_theme_mod('custom_logo', '');
}
$footer_dia_chi  = get_theme_mod('dnttvn_footer_dia_chi', 'Số 141 Hoà Thành, Xã Ea Knuếc, Đắk Lắk');
$footer_email    = get_theme_mod('dnttvn_footer_email', 'Congdongdoanhnhantrituevietnam@gmail.com');
$footer_hotline  = get_theme_mod('dnttvn_footer_hotline', '0972.27.88.66');

$footer_logo_url = '';
if ($footer_logo_id) {
    $footer_logo_url = wp_get_attachment_image_url($footer_logo_id, 'medium');
}
if (!$footer_logo_url) {
    $footer_logo_url = get_template_directory_uri() . '/assets/images/logo.png';
}

$facebook_url = get_theme_mod('dnttvn_social_facebook_url', 'https://www.facebook.com/profile.php?id=61587839805007');
$zalo_url     = get_theme_mod('dnttvn_social_zalo_url', 'https://zalo.me/g/qdamqricrs06yepvwayl');
if (!$facebook_url) {
    $facebook_url = 'https://www.facebook.com/profile.php?id=61587839805007';
}
if (!$zalo_url) {
    $zalo_url = 'https://zalo.me/g/qdamqricrs06yepvwayl';
}

$cong_dong_page = get_page_by_path('cong-dong');
$tin_tuc_page   = get_page_by_path('tin-tuc');
$dang_ky_page   = get_page_by_path('dang-ky');
$cong_dong_url  = $cong_dong_page ? get_permalink($cong_dong_page) : home_url('/cong-dong/');
$tin_tuc_url    = $tin_tuc_page ? get_permalink($tin_tuc_page) : home_url('/tin-tuc/');
$dang_ky_url    = function_exists('dnttvn_get_dn_registration_page_url')
    ? dnttvn_get_dn_registration_page_url()
    : ($dang_ky_page ? get_permalink($dang_ky_page) : home_url('/dang-ky/'));
?>
    <footer class="site-footer site-footer--modern" id="lien-he">
        <div class="footer-inner">
            <div class="footer-grid">
                <div class="footer-col footer-col--brand">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-brand">
                        <img src="<?php echo esc_url($footer_logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="footer-brand__logo" width="52" height="52">
                        <span class="footer-brand__text">
                            <span class="footer-brand__line1">CỘNG ĐỒNG DOANH NHÂN</span>
                            <span class="footer-brand__line2">TRÍ TUỆ VIỆT NAM</span>
                        </span>
                    </a>
                    <p class="footer-brand__tagline">Nơi kết nối giá trị – chia sẻ tri thức – hợp tác phát triển – phụng sự xã hội.</p>
                    <div class="footer-socials" aria-label="Mạng xã hội">
                        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="footer-social" title="Facebook" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="<?php echo esc_url($zalo_url); ?>" target="_blank" rel="noopener noreferrer" class="footer-social" title="Zalo" aria-label="Zalo">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.05 2C6.58 2 2.13 6.13 2.13 11.24c0 2.96 1.55 5.58 3.95 7.29L5.2 22l3.93-2.07c.97.27 2 .42 3.07.42h.09c5.47 0 9.91-4.13 9.91-9.24C22.2 6.13 17.51 2 12.05 2zm4.86 13.28H8.45l4.92-5.98H8.6V8.02h8.2l-4.96 5.98h5.07v1.28z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="footer-col footer-col--links">
                    <h2 class="footer-col__title">Liên kết</h2>
                    <ul class="footer-links">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a></li>
                        <li><a href="<?php echo esc_url($cong_dong_url); ?>">Cộng đồng</a></li>
                        <li><a href="<?php echo esc_url($tin_tuc_url); ?>">Tin tức</a></li>
                        <li><a href="<?php echo esc_url($dang_ky_url); ?>">Đăng ký</a></li>
                    </ul>
                </div>

                <div class="footer-col footer-col--contact">
                    <h2 class="footer-col__title">Liên hệ</h2>
                    <ul class="footer-contact-list">
                        <?php if ($footer_dia_chi) : ?>
                        <li>
                            <span class="footer-contact-list__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            </span>
                            <span><strong>Trụ sở:</strong> <?php echo esc_html($footer_dia_chi); ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($footer_email) : ?>
                        <li>
                            <span class="footer-contact-list__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            </span>
                            <a href="mailto:<?php echo esc_attr($footer_email); ?>"><strong>Email:</strong> <?php echo esc_html($footer_email); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if ($footer_hotline) : ?>
                        <li>
                            <span class="footer-contact-list__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </span>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[\s\.]+/', '', $footer_hotline)); ?>"><strong>Điện thoại:</strong> <?php echo esc_html($footer_hotline); ?></a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-copyright">© <?php echo esc_html(gmdate('Y')); ?> — Thiết kế bởi Bộ phận Truyền thông Marketing của CỘNG ĐỒNG DOANH NHÂN TRÍ TUỆ VIỆT NAM</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            var nav = document.getElementById('mainNav');
            var btn = document.getElementById('hamburgerBtn');
            if (nav) nav.classList.toggle('nav--open');
            if (btn) {
                var open = nav && nav.classList.contains('nav--open');
                btn.classList.toggle('hamburger--open', !!open);
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
        }
        (function() {
            var btn = document.getElementById('hamburgerBtn');
            if (btn) btn.addEventListener('click', toggleMenu);
        })();
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
                if (typeof window.initTableHorizontalScroll === 'function') {
                    window.dispatchEvent(new Event('resize'));
                } else {
                    setTimeout(function() {
                        window.dispatchEvent(new Event('resize'));
                    }, 0);
                }
            });
        });
    })();
    </script>
    <?php wp_footer(); ?>
</body>
</html>
