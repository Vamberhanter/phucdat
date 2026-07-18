    <!-- Footer -->
    <footer class="site-footer site-footer--dn">
        <div class="dn-footer-inner">
            <?php
            $fi = function_exists('dnttvn_get_footer_info') ? dnttvn_get_footer_info() : array();
            $brand = isset($fi['site_title']) ? trim((string) $fi['site_title']) : '';
            $addr  = isset($fi['address']) ? trim((string) $fi['address']) : '';
            $email = isset($fi['email']) ? trim((string) $fi['email']) : '';
            $phone = isset($fi['phone']) ? trim((string) $fi['phone']) : '';
            $year  = (int) current_time('Y');

            if ($brand === '' || strcasecmp($brand, 'My websites') === 0) {
                $brand = 'CỘNG ĐỒNG DOANH NGHIỆP TRÍ TUỆ VIỆT NAM';
            }
            if ($addr === '') {
                $addr = 'Trụ sở: Số 11-13, Đường Số 11, KĐT An Phú An Khánh, Phường Bình Trưng, TP.HCM';
            }
            if ($email === '') {
                $email = 'congdongdoanhnhantrituevietnam@gmail.com';
            }
            if ($phone === '') {
                $phone = '0972.27.88.66';
            }

            $facebook_url = get_theme_mod('dnttvn_social_facebook_url', 'https://www.facebook.com/profile.php?id=61587839805007');
            if (!$facebook_url) {
                $facebook_url = 'https://www.facebook.com/profile.php?id=61587839805007';
            }
            $page_dn = get_page_by_path('danh-sach-doanh-nghiep');
            if (!$page_dn) {
                $page_dn = get_page_by_path('page-doanh-nghiep');
            }
            $dn_list_url = $page_dn ? get_permalink($page_dn->ID) : home_url('/');
            $reg_url     = function_exists('dnttvn_get_dn_registration_page_url')
                ? dnttvn_get_dn_registration_page_url()
                : home_url('/dang-ky-doanh-nghiep/');
            $tel_href    = preg_replace('/[\s\.]+/', '', $phone);
            ?>

            <div class="dn-footer-grid">
                <div class="dn-footer-col dn-footer-col--brand">
                    <p class="dn-footer-label">Thông tin chân trang</p>
                    <p class="dn-footer-brand"><?php echo esc_html($brand); ?></p>
                    <div class="dn-footer-socials" aria-label="Kết nối">
                        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="dn-footer-social dn-footer-social--fb" title="Facebook" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="dn-footer-social dn-footer-social--tt js-dang-cap-nhat" title="TikTok" aria-label="TikTok" data-alert="Đang cập nhật">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                        </a>
                        <a href="#" class="dn-footer-social dn-footer-social--zalo js-dang-cap-nhat" title="Zalo" aria-label="Zalo" data-alert="Đang cập nhật">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.05 2C6.58 2 2.13 6.13 2.13 11.24c0 2.96 1.55 5.58 3.95 7.29L5.2 22l3.93-2.07c.97.27 2 .42 3.07.42h.09c5.47 0 9.91-4.13 9.91-9.24C22.2 6.13 17.51 2 12.05 2zm4.86 13.28H8.45l4.92-5.98H8.6V8.02h8.2l-4.96 5.98h5.07v1.28z"/></svg>
                        </a>
                        <a href="#" class="dn-footer-social dn-footer-social--yt js-dang-cap-nhat" title="YouTube" aria-label="YouTube" data-alert="Đang cập nhật">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="dn-footer-col dn-footer-col--links">
                    <h2 class="dn-footer-heading">Liên kết</h2>
                    <ul class="dn-footer-links">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a></li>
                        <li><a href="<?php echo esc_url($dn_list_url); ?>">Doanh nghiệp</a></li>
                        <li><a href="<?php echo esc_url($reg_url); ?>">Đăng ký doanh nghiệp</a></li>
                        <li><a href="#" class="js-dang-cap-nhat" data-alert="Đang cập nhật">Liên hệ</a></li>
                    </ul>
                </div>

                <div class="dn-footer-col dn-footer-col--contact">
                    <h2 class="dn-footer-heading">Liên hệ</h2>
                    <ul class="dn-footer-contact">
                        <li>
                            <span class="dn-footer-contact__label">Địa chỉ</span>
                            <span class="dn-footer-contact__value"><?php echo esc_html($addr); ?></span>
                        </li>
                        <li>
                            <span class="dn-footer-contact__label">Email</span>
                            <a class="dn-footer-contact__value" href="<?php echo esc_url('mailto:' . $email); ?>"><?php echo esc_html($email); ?></a>
                        </li>
                        <li>
                            <span class="dn-footer-contact__label">Hotline</span>
                            <a class="dn-footer-contact__value" href="<?php echo esc_url('tel:' . $tel_href); ?>"><?php echo esc_html($phone); ?></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="dn-footer-bottom">
                <p class="dn-footer-copy">&copy; <?php echo esc_html((string) $year); ?> – Bản quyền thuộc về <?php echo esc_html($brand); ?></p>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
