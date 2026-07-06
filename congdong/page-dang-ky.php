<?php
/**
 * Template Name: Đăng ký
 * Trang form đăng ký gia nhập Cộng đồng DNTTVN
 */

get_header();

$submitted = isset($_GET['submitted']) && $_GET['submitted'] === '1';
?>

<main class="main-content main-content-dang-ky">
    <!-- Left Sidebar: Về Cộng đồng DNTTVN -->
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
                $cong_dong_query = new WP_Query($cong_dong_args);
                if ($cong_dong_query->have_posts()) :
                    while ($cong_dong_query->have_posts()) :
                        $cong_dong_query->the_post();
                        $is_noi_bat = get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);
                        $li_class   = ($is_noi_bat == '1') ? 'highlight-item' : '';
                        ?>
                        <li class="<?php echo esc_attr($li_class); ?>">
                            <a href="<?php echo esc_url(function_exists('dnttvn_get_cong_dong_detail_url') ? dnttvn_get_cong_dong_detail_url(get_the_ID()) : get_permalink()); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <li><a href="#">Chưa có bài viết Cộng đồng</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <!-- Center Content: Form đăng ký -->
    <div class="main-center">
        <div class="content-column content-column-dang-ky">
            <div class="column-header">Đăng ký gia nhập Cộng đồng</div>
            <div class="column-content">

                <?php if ($submitted) : ?>
                    <div class="dang-ky-message dang-ky-success dang-ky-success--card" id="dang-ky-success-anchor" role="status" aria-live="polite" tabindex="-1">
                        <span class="dang-ky-success__icon" aria-hidden="true">✓</span>
                        <div class="dang-ky-success__body">
                            <strong class="dang-ky-success__title">Đăng ký thành công</strong>
                            <p class="dang-ky-success__text">Cảm ơn bạn đã đăng ký gia nhập Cộng đồng. Chúng tôi sẽ liên hệ trong thời gian sớm nhất.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="dang-ky-form" method="post" action="" id="dang-ky-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('dnttvn_dang_ky_submit', 'dnttvn_dang_ky_nonce'); ?>

                    <!-- Phần 1: Lời chào -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">Phần 1: Lời chào</h2>
                        <p class="dang-ky-description">Chào mừng Quý Doanh nhân đến với Cộng đồng Doanh nhân Trí tuệ Việt Nam. Phụng sự và Kiến tạo giá trị bền vững.</p>
                    </section>

                    <!-- Phần 2: Thông tin định danh -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">Phần 2: Thông tin định danh</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <label for="dang_ky_ho_ten">1. Họ và tên <span class="required">*</span></label>
                                <input type="text" id="dang_ky_ho_ten" name="dang_ky_ho_ten" required maxlength="200" placeholder="Nhập họ và tên" value="<?php echo isset($_POST['dang_ky_ho_ten']) ? esc_attr($_POST['dang_ky_ho_ten']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_khu_vuc">2. Khu vực <span class="required">*</span></label>
                                <select id="dang_ky_khu_vuc" name="dang_ky_khu_vuc" required>
                                    <option value="">-- Chọn khu vực --</option>
                                    <?php
                                    $terms = get_terms(array('taxonomy' => 'khu_vuc', 'hide_empty' => false));
                                    foreach ($terms as $term) {
                                        echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_ngay_sinh">3. Ngày tháng năm sinh <span class="required">*</span></label>
                                <input type="date" id="dang_ky_ngay_sinh" name="dang_ky_ngay_sinh" required value="<?php echo isset($_POST['dang_ky_ngay_sinh']) ? esc_attr($_POST['dang_ky_ngay_sinh']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_ten_dn">4. Tên doanh nghiệp <span class="required">*</span></label>
                                <input type="text" id="dang_ky_ten_dn" name="dang_ky_ten_dn" required maxlength="255" placeholder="Tên doanh nghiệp" value="<?php echo isset($_POST['dang_ky_ten_dn']) ? esc_attr($_POST['dang_ky_ten_dn']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_nganh_nghe">5. Ngành nghề kinh doanh <span class="required">*</span></label>
                                <?php
                                $nganh_nghe_predefined = function_exists('dnttvn_get_nganh_nghe_options') ? dnttvn_get_nganh_nghe_options() : array('Sản xuất', 'Dịch vụ', 'Thương mại', 'Công nghệ');
                                $saved_nganh = isset($_POST['dang_ky_nganh_nghe']) ? $_POST['dang_ky_nganh_nghe'] : '';
                                $saved_nganh_khac = isset($_POST['dang_ky_nganh_nghe_khac']) ? $_POST['dang_ky_nganh_nghe_khac'] : '';
                                $show_other_input = ($saved_nganh === '__other__');
                                ?>
                                <select id="dang_ky_nganh_nghe" name="dang_ky_nganh_nghe" required>
                                    <option value="">-- Chọn ngành nghề --</option>
                                    <?php foreach ($nganh_nghe_predefined as $opt) : ?>
                                    <option value="<?php echo esc_attr($opt); ?>" <?php selected($saved_nganh, $opt); ?>><?php echo esc_html($opt); ?></option>
                                    <?php endforeach; ?>
                                    <option value="__other__" <?php selected($saved_nganh, '__other__'); ?>>Khác (ghi rõ bên dưới)</option>
                                </select>
                                <div id="dang_ky_nganh_nghe_khac_wrap" class="dang-ky-nganh-nghe-khac-wrap" style="<?php echo $show_other_input ? '' : 'display:none;'; ?> margin-top: 8px;">
                                    <label for="dang_ky_nganh_nghe_khac" class="dang-ky-inline-label">Nhập ngành nghề: <span class="required">*</span></label>
                                    <input type="text" id="dang_ky_nganh_nghe_khac" name="dang_ky_nganh_nghe_khac" maxlength="255" placeholder="Ví dụ: Xây dựng, Giáo dục..." value="<?php echo esc_attr($saved_nganh_khac); ?>">
                                </div>
                                <script>
                                (function(){
                                    var sel = document.getElementById('dang_ky_nganh_nghe');
                                    var wrap = document.getElementById('dang_ky_nganh_nghe_khac_wrap');
                                    var input = document.getElementById('dang_ky_nganh_nghe_khac');
                                    if (!sel || !wrap || !input) return;
                                    function toggle(){ 
                                        if (sel.value === '__other__') {
                                            wrap.style.display = 'block';
                                            input.setAttribute('required', 'required');
                                        } else {
                                            wrap.style.display = 'none';
                                            input.removeAttribute('required');
                                        }
                                    }
                                    sel.addEventListener('change', toggle);
                                    toggle();
                                })();
                                </script>
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_chuc_vu">6. Chức vụ <span class="required">*</span></label>
                                <input type="text" id="dang_ky_chuc_vu" name="dang_ky_chuc_vu" required maxlength="200" placeholder="Chức vụ" value="<?php echo isset($_POST['dang_ky_chuc_vu']) ? esc_attr($_POST['dang_ky_chuc_vu']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_sdt">7. Số điện thoại / Zalo <span class="required">*</span></label>
                                <input type="tel" id="dang_ky_sdt" name="dang_ky_sdt" required maxlength="20" placeholder="Số điện thoại hoặc Zalo" value="<?php echo isset($_POST['dang_ky_sdt']) ? esc_attr($_POST['dang_ky_sdt']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_dia_chi">8. Địa chỉ <span class="required">*</span></label>
                                <input type="text" id="dang_ky_dia_chi" name="dang_ky_dia_chi" required placeholder="Nhập địa chỉ của bạn" value="<?php echo isset($_POST['dang_ky_dia_chi']) ? esc_attr($_POST['dang_ky_dia_chi']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_email">9. Email <span class="required">*</span></label>
                                <input type="email" id="dang_ky_email" name="dang_ky_email" required placeholder="Nhập email của bạn" value="<?php echo isset($_POST['dang_ky_email']) ? esc_attr($_POST['dang_ky_email']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_portrait">10. Ảnh chân dung (cho trang danh sách) <span class="required">*</span></label>
                                <input type="file" id="dang_ky_portrait" name="dang_ky_portrait" accept="image/*" required>
                                <p class="description" style="font-size: 12px; color: #666; margin-top: 4px;">Vui lòng tải lên ảnh chân dung rõ nét để hiển thị trong Danh sách Doanh nhân.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Phần 3: Sứ mệnh Gia đình -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">Phần 3: Sứ mệnh Gia đình</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <span class="dang-ky-label">11. Tình trạng hôn nhân <span class="required">*</span></span>
                                <div class="dang-ky-options">
                                    <label class="dang-ky-option"><input type="radio" name="dang_ky_hon_nhan" value="Độc thân" required <?php echo (isset($_POST['dang_ky_hon_nhan']) && $_POST['dang_ky_hon_nhan'] === 'Độc thân') ? 'checked' : ''; ?>> Độc thân</label>
                                    <label class="dang-ky-option"><input type="radio" name="dang_ky_hon_nhan" value="Đã kết hôn" required <?php echo (isset($_POST['dang_ky_hon_nhan']) && $_POST['dang_ky_hon_nhan'] === 'Đã kết hôn') ? 'checked' : ''; ?>> Đã kết hôn</label>
                                    <label class="dang-ky-option"><input type="radio" name="dang_ky_hon_nhan" value="Khác" required <?php echo (isset($_POST['dang_ky_hon_nhan']) && $_POST['dang_ky_hon_nhan'] === 'Khác') ? 'checked' : ''; ?>> Khác</label>
                                </div>
                            </div>
                            <div class="dang-ky-field">
                                <label for="dang_ky_so_con">12. Số lượng con <span class="required">*</span></label>
                                <input type="number" id="dang_ky_so_con" name="dang_ky_so_con" required min="0" max="20" step="1" placeholder="0" value="<?php echo isset($_POST['dang_ky_so_con']) ? esc_attr($_POST['dang_ky_so_con']) : ''; ?>">
                            </div>
                            <div class="dang-ky-field">
                                <span class="dang-ky-label">13. Độ tuổi của các con <span class="required" id="dang_ky_do_tuoi_con_required_star" style="display:none;">*</span></span>
                                <div class="dang-ky-options dang-ky-checkboxes">
                                    <?php
                                    $do_tuoi_opts = array(
                                        'Mẫu giáo' => 'Mẫu giáo',
                                        'Tiểu học' => 'Tiểu học',
                                        'THCS' => 'THCS',
                                        'THPT' => 'THPT',
                                        'Sinh viên' => 'Sinh viên',
                                        'Đã đi làm' => 'Đã đi làm'
                                    );
                                    $saved_con = isset($_POST['dang_ky_do_tuoi_con']) && is_array($_POST['dang_ky_do_tuoi_con']) ? $_POST['dang_ky_do_tuoi_con'] : array();
                                    foreach ($do_tuoi_opts as $label => $val) :
                                        $checked = in_array($val, $saved_con, true) ? ' checked' : '';
                                    ?>
                                    <label class="dang-ky-option"><input type="checkbox" name="dang_ky_do_tuoi_con[]" value="<?php echo esc_attr($val); ?>"<?php echo $checked; ?>> <?php echo esc_html($label); ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Phần 4: Tâm thế & Nhu cầu -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">Phần 4: Tâm thế & Nhu cầu</h2>
                        <div class="dang-ky-fields">
                            <div class="dang-ky-field">
                                <span class="dang-ky-label">14. Mục tiêu ưu tiên khi gia nhập (Chọn 1 hoặc nhiều mục tiêu bên dưới) <span class="required">*</span></span>
                                <div class="dang-ky-options dang-ky-checkboxes">
                                    <?php
                                    $muc_tieu_opts = array(
                                        'Nâng tầm bản thân & Rõ đường hành động',
                                        'Kết nối cộng hưởng doanh nghiệp',
                                        'Định hướng khởi nghiệp cho con cái',
                                        'Phụng sự & Đóng góp giá trị quốc gia',
                                    );
                                    $saved_mt = isset($_POST['dang_ky_muc_tieu']) && is_array($_POST['dang_ky_muc_tieu']) ? $_POST['dang_ky_muc_tieu'] : array();
                                    foreach ($muc_tieu_opts as $idx => $label) :
                                        $checked = in_array($label, $saved_mt, true) ? ' checked' : '';
                                    ?>
                                    <label class="dang-ky-option"><input type="checkbox" name="dang_ky_muc_tieu[]" value="<?php echo esc_attr($label); ?>"<?php echo $checked; ?>> <?php echo esc_html($label); ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="dang-ky-field">
                                <span class="dang-ky-label">15. Bạn đã từng tham gia khóa học nào của thầy Ngô Minh Tuấn chưa? <span class="required">*</span></span>
                                <div class="dang-ky-options dang-ky-column">
                                    <label class="dang-ky-option">
                                        <input type="radio" name="dang_ky_khoa_hoc" value="Tôi chưa từng tham gia." required <?php echo (isset($_POST['dang_ky_khoa_hoc']) && $_POST['dang_ky_khoa_hoc'] === 'Tôi chưa từng tham gia.') ? 'checked' : ''; ?>> Tôi chưa từng tham gia.
                                    </label>
                                    <label class="dang-ky-option">
                                        <input type="radio" name="dang_ky_khoa_hoc" value="Tôi đã từng tham gia" required <?php echo (isset($_POST['dang_ky_khoa_hoc']) && $_POST['dang_ky_khoa_hoc'] === 'Tôi đã từng tham gia') ? 'checked' : ''; ?>> Tôi đã từng tham gia (Vui lòng điền tên khóa học bên dưới)
                                    </label>
                                    <label class="dang-ky-option">
                                        <input type="radio" name="dang_ky_khoa_hoc" value="Tôi mới chỉ theo dõi các bài giảng của thầy trên các kênh mạng xã hội" required <?php echo (isset($_POST['dang_ky_khoa_hoc']) && $_POST['dang_ky_khoa_hoc'] === 'Tôi mới chỉ theo dõi các bài giảng của thầy trên các kênh mạng xã hội') ? 'checked' : ''; ?>> Tôi mới chỉ theo dõi các bài giảng của thầy trên các kênh mạng xã hội
                                    </label>
                                </div>
                                <div id="dang_ky_khoa_hoc_ten_wrap" class="dang-ky-nganh-nghe-khac-wrap" style="display:none; margin-top: 8px;">
                                    <label for="dang_ky_khoa_hoc_ten" class="dang-ky-inline-label">Tên khóa học bạn đã tham gia: <span class="required">*</span></label>
                                    <input type="text" id="dang_ky_khoa_hoc_ten" name="dang_ky_khoa_hoc_ten" maxlength="255" placeholder="Ví dụ: CEO Quản trị, CEO Master..." value="<?php echo isset($_POST['dang_ky_khoa_hoc_ten']) ? esc_attr($_POST['dang_ky_khoa_hoc_ten']) : ''; ?>">
                                </div>
                            </div>

                            <div class="dang-ky-field">
                                <span class="dang-ky-label">16. Bạn biết đến cộng đồng qua đâu? <span class="required">*</span></span>
                                <div class="dang-ky-options dang-ky-column">
                                    <?php
                                    $saved_nt = isset($_POST['dang_ky_nguon_tin']) && is_array($_POST['dang_ky_nguon_tin']) ? $_POST['dang_ky_nguon_tin'] : array();
                                    $nt_opts = array(
                                        'Fanpage Facebook Cộng đồng Doanh nhân Trí tuệ Việt Nam',
                                        'Tìm kiếm Google',
                                        'Được bạn bè, đồng nghiệp giới thiệu',
                                        'Sự kiện / Hội thảo / Webinar (Offline & Online)',
                                        'Khác',
                                    );
                                    foreach ($nt_opts as $opt) :
                                        $checked = in_array($opt, $saved_nt, true) ? ' checked' : '';
                                        $label_text = $opt;
                                        if ($opt === 'Khác') {
                                            $label_text = 'Khác (Vui lòng điền thông tin bên dưới)';
                                        }
                                    ?>
                                    <label class="dang-ky-option">
                                        <input type="checkbox" name="dang_ky_nguon_tin[]" value="<?php echo esc_attr($opt); ?>"<?php echo $checked; ?>> <?php echo esc_html($label_text); ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <div id="dang_ky_nguon_tin_khac_wrap" class="dang-ky-nganh-nghe-khac-wrap" style="display:none; margin-top: 8px;">
                                    <label for="dang_ky_nguon_tin_khac" class="dang-ky-inline-label">Thông tin chi tiết: <span class="required">*</span></label>
                                    <input type="text" id="dang_ky_nguon_tin_khac" name="dang_ky_nguon_tin_khac" maxlength="255" placeholder="Nhập thông tin..." value="<?php echo isset($_POST['dang_ky_nguon_tin_khac']) ? esc_attr($_POST['dang_ky_nguon_tin_khac']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Phần 5: Xác nhận cam kết -->
                    <section class="dang-ky-section">
                        <h2 class="dang-ky-section-title">Phần 5: Xác nhận cam kết</h2>
                        <?php
                        $luat_choi = get_option('dnttvn_luat_choi', '');
                        if ($luat_choi !== '') :
                            $luat_choi = apply_filters('dnttvn_display_content', $luat_choi);
                        ?>
                        <div class="dang-ky-luat-choi-box">
                            <h3 class="dang-ky-luat-choi-title">Luật chơi</h3>
                            <div class="dang-ky-luat-choi-content"><?php echo wp_kses_post(wpautop($luat_choi)); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="dang-ky-field">
                            <span class="dang-ky-label">17. Xác nhận "Luật chơi" <span class="required">*</span></span>
                            <div class="dang-ky-options">
                                <label class="dang-ky-option"><input type="radio" name="dang_ky_xac_nhan" value="Đã rõ và Cam kết" required <?php echo (isset($_POST['dang_ky_xac_nhan']) && $_POST['dang_ky_xac_nhan'] === 'Đã rõ và Cam kết') ? 'checked' : ''; ?>> Đã rõ và Cam kết</label>
                            </div>
                        </div>
                    </section>

                    <div class="dang-ky-submit-wrap">
                        <button type="submit" class="dang-ky-submit">Gửi đăng ký</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Website liên kết -->
    <div class="sidebar-column">
        <?php get_template_part('template-parts/sidebar-su-kien'); ?>

        <div class="column-header mobile-toggle collapsed">Website liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                $community_links = array_slice($community_links, 0, 9);
                foreach ($community_links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</main>

<script>
(function () {
    // 1. Scroll to success if submitted
    <?php if ($submitted) : ?>
    var el = document.getElementById('dang-ky-success-anchor');
    if (el && el.scrollIntoView) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        try { el.focus({ preventScroll: true }); } catch (e) { el.focus(); }
    }
    <?php endif; ?>

    // 2. Validate Children's Age checkboxes based on children count
    var soConInput = document.getElementById('dang_ky_so_con');
    var ageCheckboxes = document.querySelectorAll('input[name="dang_ky_do_tuoi_con[]"]');
    var ageRequiredStar = document.getElementById('dang_ky_do_tuoi_con_required_star');
    
    function validateChildrenAge() {
        if (!soConInput || ageCheckboxes.length === 0) return;
        var count = parseInt(soConInput.value, 10) || 0;
        if (count > 0) {
            if (ageRequiredStar) ageRequiredStar.style.display = 'inline';
            var checkedCount = Array.from(ageCheckboxes).filter(function(cb) { return cb.checked; }).length;
            if (checkedCount === 0) {
                ageCheckboxes[0].setCustomValidity('Vui lòng chọn ít nhất một độ tuổi cho các con.');
            } else {
                ageCheckboxes.forEach(function(cb) { cb.setCustomValidity(''); });
            }
        } else {
            if (ageRequiredStar) ageRequiredStar.style.display = 'none';
            ageCheckboxes.forEach(function(cb) { cb.setCustomValidity(''); });
        }
    }
    
    if (soConInput) {
        soConInput.addEventListener('input', validateChildrenAge);
        ageCheckboxes.forEach(function(cb) {
            cb.addEventListener('change', validateChildrenAge);
        });
        validateChildrenAge();
    }

    // 3. Validate Goal checkboxes (at least one must be checked)
    var goalCheckboxes = document.querySelectorAll('input[name="dang_ky_muc_tieu[]"]');
    function validateGoals() {
        if (goalCheckboxes.length === 0) return;
        var checkedCount = Array.from(goalCheckboxes).filter(function(cb) { return cb.checked; }).length;
        if (checkedCount === 0) {
            goalCheckboxes[0].setCustomValidity('Vui lòng chọn ít nhất một mục tiêu gia nhập.');
        } else {
            goalCheckboxes.forEach(function(cb) { cb.setCustomValidity(''); });
        }
    }
    if (goalCheckboxes.length > 0) {
        goalCheckboxes.forEach(function(cb) {
            cb.addEventListener('change', validateGoals);
        });
        validateGoals();
    }

    // 4. Toggle required field for Ngô Minh Tuấn Course survey
    var khRadios = document.querySelectorAll('input[name="dang_ky_khoa_hoc"]');
    var khWrap = document.getElementById('dang_ky_khoa_hoc_ten_wrap');
    var khInput = document.getElementById('dang_ky_khoa_hoc_ten');
    
    function toggleKH() {
        if (!khWrap || !khInput) return;
        var checkedRadio = Array.from(khRadios).find(function(r) { return r.checked; });
        if (checkedRadio && checkedRadio.value === 'Tôi đã từng tham gia') {
            khWrap.style.display = 'block';
            khInput.setAttribute('required', 'required');
        } else {
            khWrap.style.display = 'none';
            khInput.removeAttribute('required');
        }
    }
    if (khRadios.length > 0) {
        khRadios.forEach(function(r) {
            r.addEventListener('change', toggleKH);
        });
        toggleKH();
    }

    // 5. Validate Referral Source checkboxes (at least one must be checked) & Toggle required field
    var ntCheckboxes = document.querySelectorAll('input[name="dang_ky_nguon_tin[]"]');
    var ntWrap = document.getElementById('dang_ky_nguon_tin_khac_wrap');
    var ntInput = document.getElementById('dang_ky_nguon_tin_khac');
    
    function validateNguonTin() {
        if (ntCheckboxes.length === 0) return;
        var checkedCount = Array.from(ntCheckboxes).filter(function(cb) { return cb.checked; }).length;
        if (checkedCount === 0) {
            ntCheckboxes[0].setCustomValidity('Vui lòng chọn ít nhất một nguồn tin.');
        } else {
            ntCheckboxes.forEach(function(cb) { cb.setCustomValidity(''); });
        }
        
        // Toggle required for "Khác"
        if (ntWrap && ntInput) {
            var isKhacChecked = Array.from(ntCheckboxes).some(function(cb) { return cb.checked && cb.value === 'Khác'; });
            if (isKhacChecked) {
                ntWrap.style.display = 'block';
                ntInput.setAttribute('required', 'required');
            } else {
                ntWrap.style.display = 'none';
                ntInput.removeAttribute('required');
            }
        }
    }
    if (ntCheckboxes.length > 0) {
        ntCheckboxes.forEach(function(cb) {
            cb.addEventListener('change', validateNguonTin);
        });
        validateNguonTin();
    }
})();
</script>
<?php get_footer(); ?>
