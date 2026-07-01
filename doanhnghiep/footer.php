    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <?php
                $fi = function_exists('dnttvn_get_footer_info') ? dnttvn_get_footer_info() : array(
                    'site_title' => 'My websites',
                    'address'    => '',
                    'email'      => '',
                    'phone'      => '',
                );
                $brand   = isset($fi['site_title']) ? (string) $fi['site_title'] : 'My websites';
                $addr    = isset($fi['address']) ? trim((string) $fi['address']) : '';
                $email   = isset($fi['email']) ? trim((string) $fi['email']) : '';
                $phone   = isset($fi['phone']) ? trim((string) $fi['phone']) : '';
                $year    = (int) current_time('Y');
                $line2 = array();
                if ($addr !== '') {
                    $line2[] = 'Địa chỉ: ' . esc_html($addr);
                }
                if ($email !== '') {
                    $line2[] = 'Email: <a href="' . esc_attr('mailto:' . $email) . '">' . esc_html($email) . '</a>';
                }
                if ($phone !== '') {
                    $tel_href = preg_replace('/\s+/', '', $phone);
                    $line2[]  = 'Hotline: <a href="' . esc_attr('tel:' . $tel_href) . '">' . esc_html($phone) . '</a>';
                }
                $line2_html = implode(' | ', $line2);
                ?>
                <p><strong>Thông tin chân trang</strong></p>
                <p><?php echo esc_html($brand); ?></p>
                <?php if ($line2_html !== '') : ?>
                    <p><?php echo wp_kses($line2_html, array('a' => array('href' => array()))); ?></p>
                <?php endif; ?>
                <p>&copy; <?php echo esc_html((string) $year); ?> - Bản quyền thuộc về <?php echo esc_html($brand); ?></p>
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

            /* Slider + lightbox Doanh nghiệp: dùng assets/script.js (initBusinessGalleryAndLightbox) */
    </script>
    <?php wp_footer(); ?>
</body>
</html>
