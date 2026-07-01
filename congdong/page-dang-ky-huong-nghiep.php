<?php
/**
 * Template Name: Đăng ký Hướng nghiệp & Khai mở Trí tuệ
 * Phiếu đăng ký chương trình (con em Thành viên Cộng đồng DNTTVN)
 */

get_header();

$submitted = isset($_GET['submitted']) && $_GET['submitted'] === '1';
?>

<main class="main-content main-content-dang-ky">
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <ul class="about-list">
                <?php
                $cong_dong_args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                );
                $q = new WP_Query($cong_dong_args);
                if ($q->have_posts()) :
                    while ($q->have_posts()) :
                        $q->the_post();
                        $li_class = (get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true) == '1') ? 'highlight-item' : '';
                        ?>
                        <li class="<?php echo esc_attr($li_class); ?>">
                            <a href="<?php echo esc_url(function_exists('dnttvn_get_cong_dong_detail_url') ? dnttvn_get_cong_dong_detail_url(get_the_ID()) : get_permalink()); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?><li><a href="#">Chưa có bài viết Cộng đồng</a></li><?php endif; ?>
            </ul>
        </div>
        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <div class="main-center">
        <div class="content-column content-column-dang-ky">
            <div class="column-header">Phiếu đăng ký Chương trình Hướng nghiệp & Khai mở Trí tuệ</div>
            <div class="column-content">

                <p class="dang-ky-huong-nghiep-intro">(Dành riêng cho con em Thành viên Cộng đồng Doanh nhân Trí tuệ Việt Nam)</p>
                <p class="dang-ky-huong-nghiep-gui">Kính gửi: Ban Đào tạo - Trung ương Cộng đồng Doanh nhân Trí tuệ Việt Nam.</p>

                <?php if ($submitted) : ?>
                    <div class="dang-ky-message dang-ky-success dang-ky-success--card" id="dang-ky-success-anchor" role="status" aria-live="polite" tabindex="-1">
                        <span class="dang-ky-success__icon" aria-hidden="true">✓</span>
                        <div class="dang-ky-success__body">
                            <strong class="dang-ky-success__title">Đăng ký thành công</strong>
                            <p class="dang-ky-success__text">Cảm ơn Quý phụ huynh đã gửi phiếu đăng ký. Ban Đào tạo sẽ xem xét và liên hệ trong thời gian sớm nhất.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="dang-ky-form dang-ky-huong-nghiep-form" method="post" action="" id="dang-ky-huong-nghiep-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('dnttvn_dang_ky_huong_nghiep_submit', 'dnttvn_hn_nonce'); ?>

                    <!-- I. THÔNG TIN PHỤ HUYNH -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">I. THÔNG TIN PHỤ HUYNH (THÀNH VIÊN)</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <label for="hn_ho_ten_phu_huynh">1. Họ và tên Cha/Mẹ <span class="required">*</span></label>
                                <input type="text" id="hn_ho_ten_phu_huynh" name="hn_ho_ten_phu_huynh" required maxlength="200" placeholder="Họ và tên Cha/Mẹ" value="<?php echo isset($_POST['hn_ho_ten_phu_huynh']) ? esc_attr($_POST['hn_ho_ten_phu_huynh']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field dang-ky-field-inline">
                                <label for="hn_ma_so_thanh_vien">2. Mã số Thành viên</label>
                                <input type="text" id="hn_ma_so_thanh_vien" name="hn_ma_so_thanh_vien" maxlength="100" placeholder="Mã số Thành viên" value="<?php echo isset($_POST['hn_ma_so_thanh_vien']) ? esc_attr($_POST['hn_ma_so_thanh_vien']) : ''; ?>">
                                <label for="hn_chi_hoi" class="dang-ky-inline-label">Chi hội:</label>
                                <input type="text" id="hn_chi_hoi" name="hn_chi_hoi" maxlength="150" placeholder="Chi hội" value="<?php echo isset($_POST['hn_chi_hoi']) ? esc_attr($_POST['hn_chi_hoi']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="hn_ten_doanh_nghiep">3. Tên doanh nghiệp</label>
                                <input type="text" id="hn_ten_doanh_nghiep" name="hn_ten_doanh_nghiep" maxlength="255" placeholder="Tên doanh nghiệp" value="<?php echo isset($_POST['hn_ten_doanh_nghiep']) ? esc_attr($_POST['hn_ten_doanh_nghiep']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="hn_sdt_zalo">4. Số điện thoại (Zalo) <span class="required">*</span></label>
                                <input type="tel" id="hn_sdt_zalo" name="hn_sdt_zalo" required maxlength="20" placeholder="Số điện thoại / Zalo" value="<?php echo isset($_POST['hn_sdt_zalo']) ? esc_attr($_POST['hn_sdt_zalo']) : ''; ?>">
                            </div>
                        </div>
                    </section>

                    <!-- II. THÔNG TIN CỦA CON -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">II. THÔNG TIN CỦA CON (HỌC VIÊN)</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <label for="hn_ho_ten_con">1. Họ và tên con <span class="required">*</span></label>
                                <input type="text" id="hn_ho_ten_con" name="hn_ho_ten_con" required maxlength="200" placeholder="Họ và tên con" value="<?php echo isset($_POST['hn_ho_ten_con']) ? esc_attr($_POST['hn_ho_ten_con']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field dang-ky-field-inline">
                                <label for="hn_ngay_sinh_con">2. Ngày tháng năm sinh</label>
                                <input type="date" id="hn_ngay_sinh_con" name="hn_ngay_sinh_con" value="<?php echo isset($_POST['hn_ngay_sinh_con']) ? esc_attr($_POST['hn_ngay_sinh_con']) : ''; ?>">
                                <label for="hn_gioi_tinh_con" class="dang-ky-inline-label">Giới tính:</label>
                                <select id="hn_gioi_tinh_con" name="hn_gioi_tinh_con">
                                    <option value="">-- Chọn --</option>
                                    <option value="Nam" <?php echo (isset($_POST['hn_gioi_tinh_con']) && $_POST['hn_gioi_tinh_con'] === 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                    <option value="Nữ" <?php echo (isset($_POST['hn_gioi_tinh_con']) && $_POST['hn_gioi_tinh_con'] === 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                    <option value="Khác" <?php echo (isset($_POST['hn_gioi_tinh_con']) && $_POST['hn_gioi_tinh_con'] === 'Khác') ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                            <div class="dang-ky-field dang-ky-field-inline">
                                <label for="hn_lop">3. Đang học lớp</label>
                                <input type="text" id="hn_lop" name="hn_lop" maxlength="50" placeholder="Ví dụ: 10A1" value="<?php echo isset($_POST['hn_lop']) ? esc_attr($_POST['hn_lop']) : ''; ?>">
                                <label for="hn_truong" class="dang-ky-inline-label">Trường:</label>
                                <input type="text" id="hn_truong" name="hn_truong" maxlength="255" placeholder="Tên trường" value="<?php echo isset($_POST['hn_truong']) ? esc_attr($_POST['hn_truong']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="hn_tinh_cach_con">4. Tính cách đặc trưng của con</label>
                                <p class="dang-ky-hint">(Ví dụ: Hướng nội/hướng ngoại, điềm tĩnh/sôi nổi, có thiên hướng nghệ thuật/tư duy logic...)</p>
                                <textarea id="hn_tinh_cach_con" name="hn_tinh_cach_con" rows="3" placeholder="Mô tả tính cách..."><?php echo isset($_POST['hn_tinh_cach_con']) ? esc_textarea($_POST['hn_tinh_cach_con']) : ''; ?></textarea>
                            </div>
                            <div class="dang-ky-field">
                                <label for="hn_so_thich_nang_khieu">5. Sở thích hoặc năng khiếu đặc biệt</label>
                                <textarea id="hn_so_thich_nang_khieu" name="hn_so_thich_nang_khieu" rows="3" placeholder="Sở thích, năng khiếu..."><?php echo isset($_POST['hn_so_thich_nang_khieu']) ? esc_textarea($_POST['hn_so_thich_nang_khieu']) : ''; ?></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- III. NỘI DUNG ĐĂNG KÝ (lấy từ quản lý Chương trình đăng ký) -->
                    <?php
                    $chuong_trinh_opts = function_exists('dnttvn_get_huong_nghiep_chuong_trinh_options') ? dnttvn_get_huong_nghiep_chuong_trinh_options() : array();
                    $hn_chuong_trinh_val = isset($_POST['hn_chuong_trinh']) ? sanitize_text_field($_POST['hn_chuong_trinh']) : '';
                    ?>
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">III. NỘI DUNG ĐĂNG KÝ (Chọn 01 chương trình phù hợp với độ tuổi)</h2>
                        <p class="dang-ky-description">Vui lòng đánh dấu (x) vào ô tương ứng:</p>
                        <div class="dang-ky-options dang-ky-program-options">
                            <?php foreach ($chuong_trinh_opts as $i => $item) :
                                $value = (string) ($i + 1);
                                $checked = ($hn_chuong_trinh_val === $value) ? ' checked' : '';
                                $sub = !empty($item['subtitle']) ? ' (' . esc_html($item['subtitle']) . ')' : '';
                            ?>
                                <label class="dang-ky-option-block">
                                    <input type="radio" name="hn_chuong_trinh" value="<?php echo esc_attr($value); ?>"<?php echo $checked; ?>>
                                    <strong><?php echo esc_html($item['title']); ?></strong><?php echo $sub; ?><br>
                                    <?php if (!empty($item['description'])) : ?>
                                        <span class="dang-ky-option-desc"><?php echo esc_html($item['description']); ?></span>
                                    <?php endif; ?>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($chuong_trinh_opts)) : ?>
                                <p class="description">Chưa có chương trình nào. Vào <strong>Phụng sự Con Doanh nhân → Chương trình đăng ký</strong> trong menu quản trị để thêm.</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- IV. PHẦN KHẢO SÁT -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">IV. PHẦN KHẢO SÁT DÀNH CHO PHỤ HUYNH</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <label for="hn_dinh_huong_gia_dinh">1. Gia đình đã có định hướng cụ thể nào cho tương lai của con chưa?</label>
                                <p class="dang-ky-hint">(Ví dụ: Du học, nối nghiệp gia đình, tự do phát triển theo đam mê...)</p>
                                <textarea id="hn_dinh_huong_gia_dinh" name="hn_dinh_huong_gia_dinh" rows="3"><?php echo isset($_POST['hn_dinh_huong_gia_dinh']) ? esc_textarea($_POST['hn_dinh_huong_gia_dinh']) : ''; ?></textarea>
                            </div>
                            <div class="dang-ky-field">
                                <label for="hn_van_de_lon_nhat">2. Vấn đề lớn nhất mà Anh/Chị đang gặp phải trong việc định hướng/kết nối với con là gì?</label>
                                <textarea id="hn_van_de_lon_nhat" name="hn_van_de_lon_nhat" rows="3"><?php echo isset($_POST['hn_van_de_lon_nhat']) ? esc_textarea($_POST['hn_van_de_lon_nhat']) : ''; ?></textarea>
                            </div>
                            <div class="dang-ky-field">
                                <label for="hn_mong_muon_sau_khoa_hoc">3. Anh/Chị mong muốn con thay đổi hoặc đạt được điều gì nhất sau khóa học này?</label>
                                <textarea id="hn_mong_muon_sau_khoa_hoc" name="hn_mong_muon_sau_khoa_hoc" rows="3"><?php echo isset($_POST['hn_mong_muon_sau_khoa_hoc']) ? esc_textarea($_POST['hn_mong_muon_sau_khoa_hoc']) : ''; ?></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- V. CAM KẾT -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">V. CAM KẾT CỦA GIA ĐÌNH</h2>
                        <div class="dang-ky-options dang-ky-checkboxes">
                            <label class="dang-ky-option">
                                <input type="checkbox" name="hn_cam_ket_dong_hanh" value="1" <?php echo !empty($_POST['hn_cam_ket_dong_hanh']) ? 'checked' : ''; ?>>
                                Tôi cam kết đồng hành cùng con trong các buổi thực hành và bài tập về nhà.
                            </label>
                            <label class="dang-ky-option">
                                <input type="checkbox" name="hn_cam_ket_ton_trong" value="1" <?php echo !empty($_POST['hn_cam_ket_ton_trong']) ? 'checked' : ''; ?>>
                                Tôi tôn trọng lộ trình phát triển tự nhiên của con theo tư vấn từ chuyên gia của Cộng đồng.
                            </label>
                        </div>
                    </section>

                    <!-- Chữ ký phụ huynh -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">XÁC NHẬN</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <label for="hn_chu_ky">Phụ huynh đăng ký (ảnh chữ ký)</label>
                                <p class="dang-ky-hint">Vui lòng chụp/scan hình chữ ký (định dạng ảnh: JPG, PNG, GIF, WebP).</p>
                                <input type="file" id="hn_chu_ky" name="hn_chu_ky" accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>
                        </div>
                    </section>

                    <div class="dang-ky-submit-wrap">
                        <button type="submit" class="dang-ky-submit">Gửi phiếu đăng ký</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="sidebar-column">
        <?php get_template_part('template-parts/sidebar-su-kien'); ?>

        <div class="column-header mobile-toggle collapsed">Website liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                $links = array_slice($links, 0, 9);
                foreach ($links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</main>

<?php
if ($submitted) :
    ?>
<script>
(function () {
    var el = document.getElementById('dang-ky-success-anchor');
    if (el && el.scrollIntoView) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        try { el.focus({ preventScroll: true }); } catch (e) { el.focus(); }
    }
})();
</script>
<?php endif; ?>
<?php get_footer(); ?>
