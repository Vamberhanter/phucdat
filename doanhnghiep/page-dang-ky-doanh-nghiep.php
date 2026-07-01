<?php
/**
 * Template Name: Đăng ký Doanh nghiệp (form)
 * Description: Form thu thập thông tin DN — ảnh tối đa 5/phần, 2MB, cạnh dài 2000px.
 */

get_header();

$dn_reg_max_img   = defined('DNTTVN_DN_REG_MAX_SEC_IMAGES') ? (int) DNTTVN_DN_REG_MAX_SEC_IMAGES : 5;
$dn_reg_max_bytes = defined('DNTTVN_DN_REG_MAX_BYTES') ? (int) DNTTVN_DN_REG_MAX_BYTES : 2097152;
$dn_reg_max_side  = defined('DNTTVN_DN_REG_MAX_SIDE') ? (int) DNTTVN_DN_REG_MAX_SIDE : 2000;
$dn_reg_mb        = round($dn_reg_max_bytes / 1048576, 1);
$dn_reg_card_max  = defined('DNTTVN_DN_REG_MAX_CARD_PHU') ? (int) DNTTVN_DN_REG_MAX_CARD_PHU : 5;
$dn_w_field       = defined('DNTTVN_DN_REG_MAX_WORDS_FIELD') ? (int) DNTTVN_DN_REG_MAX_WORDS_FIELD : 350;
$dn_w_mota        = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;

$dn_form_nganh_terms = get_terms(
    array(
        'taxonomy'   => 'nganh_hang',
        'hide_empty' => false,
    )
);
if (is_wp_error($dn_form_nganh_terms)) {
    $dn_form_nganh_terms = array();
}
$dn_form_khu_terms = get_terms(
    array(
        'taxonomy'   => 'khu_vuc',
        'hide_empty' => false,
    )
);
if (is_wp_error($dn_form_khu_terms)) {
    $dn_form_khu_terms = array();
}

$dn_reg_cb41 = array(
    'thuong_hieu'    => 'Thương hiệu',
    'chien_luoc_kd'  => 'Chiến lược kinh doanh',
    'chien_luoc_sp'  => 'Chiến lược sản phẩm',
    'van_hanh'       => 'Hệ thống vận hành',
    'marketing'      => 'Marketing',
    'kenh_ban'       => 'Kênh bán hàng',
    'chuyen_doi_so'  => 'Chuyển đổi số',
    'dao_tao'        => 'Đào tạo nhân sự',
    'hsnl2'          => 'Hồ sơ năng lực',
    'khac'           => 'Khác',
);

/**
 * @param int    $section 1–8
 * @param string $heading
 * @param bool   $required Nếu true: đánh dấu * và bắt buộc chọn file (mặc định false — tùy chọn).
 */
$dn_reg_section_upload = function ($section, $heading, $required = false) use ($dn_reg_max_img, $dn_reg_mb, $dn_reg_max_side) {
    $field = 'dn_sec' . (int) $section;
    ?>
    <div class="dn-reg-upload dn-reg-upload--sec<?php echo (int) $section; ?>">
        <h4 class="dn-reg-upload__title"><?php echo esc_html($heading); ?><?php if ($required) : ?> <span style="color:#b91c1c;">*</span><?php endif; ?></h4>
        <p class="dn-reg-upload__hint">
            Tải tối đa <strong><?php echo esc_html((string) $dn_reg_max_img); ?></strong> hình ảnh (PNG, JPG…),
            mỗi file tối đa <strong><?php echo esc_html((string) $dn_reg_mb); ?> MB</strong>,
            cạnh dài tối đa <strong><?php echo esc_html((string) $dn_reg_max_side); ?> px</strong>.
            <?php if ($required) : ?>
                <strong>Bắt buộc</strong> ít nhất một ảnh cho phần tương ứng trên hồ sơ.
            <?php endif; ?>
        </p>
        <input type="file" name="<?php echo esc_attr($field); ?>[]" id="<?php echo esc_attr($field); ?>" accept="image/*" multiple data-max-files="<?php echo (int) $dn_reg_max_img; ?>"<?php echo $required ? ' required' : ''; ?>>
    </div>
    <?php
};

$page_dn = get_page_by_path('danh-sach-doanh-nghiep');
if (!$page_dn) {
    $page_dn = get_page_by_path('page-doanh-nghiep');
}
$dn_list_url = $page_dn ? get_permalink($page_dn->ID) : home_url('/danh-sach-doanh-nghiep/');
?>

<div class="dn-directory-outer dn-directory-outer--boxed">
<main class="main-content dn-directory-main dn-directory-main--home">
    <div class="sidebar-column">
        <div class="dn-sidebar-dn-list">
            <div class="column-header mobile-toggle collapsed">Danh sách Doanh nghiệp</div>
            <div class="column-content mobile-collapsed">
                <ul class="linked-websites">
                    <li><a href="<?php echo esc_url($dn_list_url); ?>">Danh sách Doanh nghiệp</a></li>
                    <?php
                    $dn_query = new WP_Query(array('post_type' => 'doanh_nghiep', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order date', 'order' => 'ASC'));
                    if ($dn_query->have_posts()) :
                        while ($dn_query->have_posts()) :
                            $dn_query->the_post();
                            ?>
                            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </ul>
            </div>
        </div>
        <div class="column-header mobile-toggle collapsed">Web liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                foreach ($community_links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="main-center">
        <div class="dn-directory-hero">
            <h1 class="dn-directory-hero__title"><?php echo esc_html(get_the_title()); ?></h1>
        </div>

        <div class="dn-reg-form-wrap">
            <?php if (isset($_GET['dn_err']) && (string) $_GET['dn_err'] === 'no_featured') : ?>
                <div class="dn-reg-error-card" role="alert">
                    <p class="dn-reg-error-card__title">Chưa gửi được hồ sơ</p>
                    <p class="dn-reg-error-card__text">Vui lòng chọn <strong>ảnh chính (đại diện thẻ)</strong> ở mục <strong>Thông tin chung</strong> — đây là ảnh bắt buộc để hiển thị trên danh bạ sau khi duyệt.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['dn_err']) && (string) $_GET['dn_err'] === 'missing_section_images') : ?>
                <div class="dn-reg-error-card" role="alert">
                    <p class="dn-reg-error-card__title">Chưa gửi được hồ sơ</p>
                    <p class="dn-reg-error-card__text">Mỗi phần từ <strong>1 đến 8</strong> cần có <strong>ít nhất một ảnh minh họa</strong> tương ứng. Vui lòng bổ sung ảnh còn thiếu rồi gửi lại.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['dn_submitted']) && (string) $_GET['dn_submitted'] === '1') : ?>
                <div class="dn-reg-success-card" id="dn-reg-success-anchor" role="status" aria-live="polite" tabindex="-1">
                    <div class="dn-reg-success-card__icon" aria-hidden="true">✓</div>
                    <div class="dn-reg-success-card__body">
                        <h2 class="dn-reg-success-card__title">Đăng ký thành công</h2>
                        <p class="dn-reg-success-card__lead">Hồ sơ đăng ký doanh nghiệp của Quý vị đã được gửi tới ban quản trị.</p>
                        <p class="dn-reg-success-card__text">Cảm ơn Quý doanh nghiệp. Chúng tôi sẽ xem xét hồ sơ và liên hệ khi cần. Quý vị có thể đóng trang này hoặc tiếp tục xem lại biểu mẫu bên dưới.</p>
                        <?php
                        $pv_id = isset($_GET['dn_preview_id']) ? absint($_GET['dn_preview_id']) : 0;
                        if ($pv_id && function_exists('dnttvn_dn_reg_get_preview_url')) :
                            $pv_url = dnttvn_dn_reg_get_preview_url($pv_id);
                            if ($pv_url !== '') :
                                ?>
                            <p class="dn-reg-success-card__text" style="margin-top:12px;"><a class="button dn-reg-submit" style="display:inline-block;text-decoration:none;" href="<?php echo esc_url($pv_url); ?>">Xem trước thẻ &amp; trang chi tiết (trước khi duyệt)</a></p>
                                <?php
                            endif;
                        endif;
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="dn-reg-intro">
                <p><strong>Kính gửi Quý Doanh nghiệp,</strong></p>
                <p>Biểu mẫu này được sử dụng để thu thập thông tin tổng quan về doanh nghiệp nhằm phục vụ cho việc đánh giá, tư vấn, xây dựng hồ sơ và đề xuất giải pháp phù hợp. Vui lòng điền đầy đủ và chính xác các nội dung bên dưới. Thông tin càng rõ, việc phân tích và triển khai càng hiệu quả.</p>
                <p><strong>Giới hạn nhập:</strong> các ô chữ (trừ mô tả ngắn) tối đa <strong><?php echo (int) $dn_w_field; ?> từ</strong>; mô tả ngắn tối đa <strong><?php echo (int) $dn_w_mota; ?> từ</strong> (từ được tách theo khoảng trắng). Ảnh chính và ảnh minh họa theo từng phần là <strong>tùy chọn</strong>.</p>
                <p>Xin chân thành cảm ơn.</p>
            </div>

            <p class="dn-reg-upload-order-hint" style="margin:0 0 18px;padding:12px 14px;font-size:14px;line-height:1.55;color:#92400e;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;">
                <strong>Lưu ý ảnh:</strong> Máy chủ PHP thường giới hạn số file tải lên cùng lúc (ví dụ 20). Trình duyệt sẽ tự sắp thứ tệp khi gửi (ảnh chính và ảnh theo từng phần được ưu tiên) để giảm tình trạng mất ảnh. Nếu vẫn thiếu ảnh, vui lòng giảm số file mỗi ô hoặc tăng <code>max_file_uploads</code> trên hosting.
            </p>

            <form class="dn-reg-form" id="dn-reg-form-main" method="post" action="<?php echo esc_url(get_permalink()); ?>" enctype="multipart/form-data">
                <?php wp_nonce_field('dnttvn_dn_reg_submit', 'dnttvn_dn_reg_nonce'); ?>
                <?php $dn_reg_question_num = 0; ?>

                <fieldset class="dn-reg-fieldset">
                    <legend>Thông tin chung</legend>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_1"><?php echo (int) ++$dn_reg_question_num; ?>. Tên doanh nghiệp <span style="color:#b91c1c;">*</span></label>
                        <input type="text" name="dnq_1" id="dnq_1" required class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_2"><?php echo (int) ++$dn_reg_question_num; ?>. Tên thương hiệu (nếu có)</label>
                        <input type="text" name="dnq_2" id="dnq_2" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dn_reg_mo_ta_ngan"><?php echo (int) ++$dn_reg_question_num; ?>. Mô tả ngắn (hiển thị ở mục Thông tin chung sau khi duyệt)</label>
                        <p class="dn-reg-field__hint" style="margin:0 0 8px;">Tối đa <strong><?php echo (int) $dn_w_mota; ?> từ</strong> (các từ cách nhau bằng khoảng trắng). Có thể để trống; hệ thống sẽ tạo mô tả ngắn từ tên, ngành và khu vực.</p>
                        <textarea name="dn_reg_mo_ta_ngan" id="dn_reg_mo_ta_ngan" rows="3" class="widefat dn-reg-word-cap" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;" data-dn-max-words="<?php echo (int) $dn_w_mota; ?>"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_4"><?php echo (int) ++$dn_reg_question_num; ?>. Người đại diện pháp luật</label>
                        <input type="text" name="dnq_4" id="dnq_4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_5"><?php echo (int) ++$dn_reg_question_num; ?>. Số điện thoại</label>
                        <input type="text" name="dnq_5" id="dnq_5" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_6"><?php echo (int) ++$dn_reg_question_num; ?>. Email</label>
                        <input type="email" name="dnq_6" id="dnq_6" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_7"><?php echo (int) ++$dn_reg_question_num; ?>. Website / Facebook / Kênh liên hệ chính</label>
                        <input type="text" name="dnq_7" id="dnq_7" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_8"><?php echo (int) ++$dn_reg_question_num; ?>. Địa chỉ doanh nghiệp</label>
                        <textarea name="dnq_8" id="dnq_8" rows="3" class="widefat dn-reg-word-cap" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;" data-dn-max-words="<?php echo (int) $dn_w_field; ?>"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_9"><?php echo (int) ++$dn_reg_question_num; ?>. Năm thành lập</label>
                        <input type="text" name="dnq_9" id="dnq_9" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;max-width:200px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_10"><?php echo (int) ++$dn_reg_question_num; ?>. Lĩnh vực hoạt động chính</label>
                        <textarea name="dnq_10" id="dnq_10" rows="3" class="widefat dn-reg-word-cap" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;" data-dn-max-words="<?php echo (int) $dn_w_field; ?>"></textarea>
                    </div>

                    <div class="dn-reg-grid dn-reg-grid--2col">
                        <div class="dn-reg-field form-group">
                            <label for="dnq_11"><?php echo (int) ++$dn_reg_question_num; ?>. Ngành hàng</label>
                            <p class="dn-reg-field__hint">Chọn mục trong danh mục để sau duyệt hiển thị và lọc trên danh bạ; hoặc chọn &quot;Khác&quot; và nhập tay.</p>
                            <select name="dnq_11" id="dnq_11" class="dn-reg-control dn-reg-select">
                                <option value="">— Chưa chọn —</option>
                                <?php foreach ($dn_form_nganh_terms as $dn_ft) : ?>
                                    <option value="<?php echo esc_attr($dn_ft->slug); ?>"><?php echo esc_html($dn_ft->name); ?></option>
                                <?php endforeach; ?>
                                <option value="khac">Khác (nhập tay)</option>
                            </select>
                            <input type="text" name="dnq_11_khac" id="dnq_11_khac" class="dn-reg-control dn-reg-control--khac dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" placeholder="Nhập ngành / lĩnh vực…" autocomplete="off">
                        </div>
                        <div class="dn-reg-field form-group">
                            <label for="dnq_19"><?php echo (int) ++$dn_reg_question_num; ?>. Khu vực hoạt động chính</label>
                            <p class="dn-reg-field__hint">Theo tỉnh, thành phố hoặc vùng có trong danh mục.</p>
                            <select name="dnq_19" id="dnq_19" class="dn-reg-control dn-reg-select">
                                <option value="">— Chưa chọn —</option>
                                <?php foreach ($dn_form_khu_terms as $dn_ft) : ?>
                                    <option value="<?php echo esc_attr($dn_ft->slug); ?>"><?php echo esc_html($dn_ft->name); ?></option>
                                <?php endforeach; ?>
                                <option value="khac">Khác (nhập tay)</option>
                            </select>
                            <input type="text" name="dnq_19_khac" id="dnq_19_khac" class="dn-reg-control dn-reg-control--khac dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" placeholder="Nhập khu vực…" autocomplete="off">
                        </div>
                    </div>

                    <div class="dn-reg-upload dn-reg-upload--anh-chinh dn-reg-field">
                        <h4 class="dn-reg-upload__title">Ảnh chính — đại diện thẻ doanh nghiệp (tùy chọn)</h4>
                        <p class="dn-reg-upload__hint">
                            Một ảnh ngang hoặc vuông, rõ nét (logo / sản phẩm / không gian DN). Định dạng PNG, JPG…,
                            tối đa <strong><?php echo esc_html((string) $dn_reg_mb); ?> MB</strong>,
                            cạnh dài tối đa <strong><?php echo esc_html((string) $dn_reg_max_side); ?> px</strong>.
                            Nếu có, ảnh này dùng làm ảnh đại diện trên danh bạ sau khi duyệt; không bắt buộc.
                        </p>
                        <input type="file" name="dn_anh_chinh" id="dn_anh_chinh" class="dn-reg-file" accept="image/*" data-max-files="1">
                    </div>

                    <div class="dn-reg-upload dn-reg-upload--card-gallery dn-reg-field">
                        <h4 class="dn-reg-upload__title">Ảnh phụ trên thẻ doanh nghiệp (tùy chọn)</h4>
                        <p class="dn-reg-upload__hint">
                            Tải tối đa <strong><?php echo esc_html((string) $dn_reg_card_max); ?></strong> ảnh — hiển thị dạng slider / hàng ảnh nhỏ trên danh bạ và trang chi tiết sau khi duyệt
                            (PNG, JPG…, mỗi file tối đa <strong><?php echo esc_html((string) $dn_reg_mb); ?> MB</strong>,
                            cạnh dài tối đa <strong><?php echo esc_html((string) $dn_reg_max_side); ?> px</strong>).
                        </p>
                        <input type="file" name="dn_card_gallery[]" id="dn_card_gallery" class="dn-reg-file" accept="image/*" multiple data-max-files="<?php echo (int) $dn_reg_card_max; ?>">
                    </div>

                    <?php $dn_reg_section_upload(1, 'Hình ảnh minh họa (Thông tin chung)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 2 – Sứ mệnh &amp; tầm nhìn</legend>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_12"><?php echo (int) ++$dn_reg_question_num; ?>. Sứ mệnh của doanh nghiệp là gì?</label>
                        <textarea name="dnq_12" id="dnq_12" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_13"><?php echo (int) ++$dn_reg_question_num; ?>. Tầm nhìn của doanh nghiệp trong 3–5 năm tới là gì?</label>
                        <textarea name="dnq_13" id="dnq_13" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_14"><?php echo (int) ++$dn_reg_question_num; ?>. Giá trị cốt lõi doanh nghiệp theo đuổi là gì?</label>
                        <textarea name="dnq_14" id="dnq_14" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_15"><?php echo (int) ++$dn_reg_question_num; ?>. Doanh nghiệp muốn khách hàng nhớ đến mình như thế nào?</label>
                        <textarea name="dnq_15" id="dnq_15" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_16"><?php echo (int) ++$dn_reg_question_num; ?>. Điểm khác biệt lớn nhất của doanh nghiệp là gì?</label>
                        <textarea name="dnq_16" id="dnq_16" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <?php $dn_reg_section_upload(2, 'Hình ảnh minh họa (Phần 2)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 3 – Mô hình</legend>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_17"><?php echo (int) ++$dn_reg_question_num; ?>. Mô hình kinh doanh</label>
                        <textarea name="dnq_17" id="dnq_17" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_47"><?php echo (int) ++$dn_reg_question_num; ?>. Mô hình quản trị</label>
                        <textarea name="dnq_47" id="dnq_47" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_48"><?php echo (int) ++$dn_reg_question_num; ?>. Mô hình doanh nghiệp</label>
                        <textarea name="dnq_48" id="dnq_48" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_49"><?php echo (int) ++$dn_reg_question_num; ?>. Mô hình bán hàng</label>
                        <textarea name="dnq_49" id="dnq_49" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <script>
                    (function () {
                        function wireKhac(selId, inputId) {
                            var s = document.getElementById(selId), k = document.getElementById(inputId);
                            if (!s || !k) return;
                            function t() {
                                var show = s.value === 'khac';
                                k.style.display = show ? 'block' : 'none';
                                if (!show) k.value = '';
                            }
                            s.addEventListener('change', t);
                            t();
                        }
                        wireKhac('dnq_11', 'dnq_11_khac');
                        wireKhac('dnq_19', 'dnq_19_khac');
                    })();
                    </script>
                    <?php $dn_reg_section_upload(3, 'Hình ảnh minh họa (Phần 3)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 4 – Nguồn lực doanh nghiệp</legend>
                    <?php
                    $p4 = array(
                        25 => 'Cơ sở vật chất hiện có (văn phòng, cửa hàng, kho, xưởng…)',
                        27 => 'Nhân sự: số lượng và phân bổ bộ phận',
                        28 => 'Công nghệ / hệ thống / phần mềm đang dùng',
                        29 => 'Hồ sơ / pháp lý hiện có',
                        30 => 'Năng lực nội bộ mạnh nhất',
                    );
                    foreach ($p4 as $num => $lab) :
                        ?>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label for="dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_reg_question_num; ?>. <?php echo esc_html($lab); ?></label>
                            <textarea name="dnq_<?php echo (int) $num; ?>" id="dnq_<?php echo (int) $num; ?>" rows="3" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                        </div>
                    <?php endforeach; ?>
                    <?php $dn_reg_section_upload(4, 'Hình ảnh minh họa (Phần 4)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 5 – Vận hành doanh nghiệp</legend>
                    <?php
                    $p5 = array(
                        32 => 'Triết lý hoặc nguyên tắc vận hành',
                        33 => 'Quy trình bán hàng hoặc cung cấp dịch vụ hiện tại',
                        34 => 'Chăm sóc khách hàng sau bán',
                    );
                    foreach ($p5 as $num => $lab) :
                        ?>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label for="dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_reg_question_num; ?>. <?php echo esc_html($lab); ?></label>
                            <textarea name="dnq_<?php echo (int) $num; ?>" id="dnq_<?php echo (int) $num; ?>" rows="3" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                        </div>
                    <?php endforeach; ?>
                    <?php $dn_reg_section_upload(5, 'Hình ảnh minh họa (Phần 5)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 6 – Khách hàng tiêu biểu</legend>
                    <?php
                    $p6 = array(
                        36 => 'Khách hàng thường đánh giá cao ở điểm nào?',
                        37 => 'Phản hồi / góp ý thường gặp từ khách hàng',
                        38 => 'Một số khách hàng / đối tác tiêu biểu',
                        39 => 'Một số dự án / case study đã triển khai',
                    );
                    foreach ($p6 as $num => $lab) :
                        ?>
                        <div class="form-group" style="margin-bottom:14px;">
                            <label for="dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_reg_question_num; ?>. <?php echo esc_html($lab); ?></label>
                            <textarea name="dnq_<?php echo (int) $num; ?>" id="dnq_<?php echo (int) $num; ?>" rows="3" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_40" style="display:block;font-weight:600;margin-bottom:8px;"><?php echo (int) ++$dn_reg_question_num; ?>. Tài liệu có thể gửi kèm</label>
                        <p style="font-size:14px;color:#64748b;margin:0 0 8px;">Mô tả ngắn các loại tài liệu (báo cáo, catalogue, chứng nhận…). Hình ảnh minh họa vui lòng tải ở mục <strong>Thông tin chung</strong> (ảnh chính, ảnh phụ thẻ, hình minh họa cùng mục).</p>
                        <textarea name="dnq_40" id="dnq_40" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;"></textarea>
                    </div>
                    <?php $dn_reg_section_upload(6, 'Hình ảnh minh họa (Phần 6)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 7 – Nhu cầu hiện tại</legend>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_41" style="display:block;font-weight:600;margin-bottom:8px;"><?php echo (int) ++$dn_reg_question_num; ?>. Đang cần cải thiện hoặc xây dựng</label>
                        <select name="dnq_41" id="dnq_41" required style="max-width:360px;width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                            <option value="">— Chọn —</option>
                            <?php foreach ($dn_reg_cb41 as $val => $lab) : ?>
                                <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($lab); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="dnq_41_khac" id="dnq_41_khac" autocomplete="off" placeholder="Nếu chọn Khác, mô tả cụ thể…" class="dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="display:none;margin-top:10px;max-width:100%;width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
                    </div>
                    <script>
                    (function(){var s=document.getElementById('dnq_41'),k=document.getElementById('dnq_41_khac');if(!s||!k)return;function t(){k.style.display=s.value==='khac'?'block':'none';if(s.value!=='khac')k.value='';}s.addEventListener('change',t);t();})();
                    </script>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_42"><?php echo (int) ++$dn_reg_question_num; ?>. Ba vấn đề ưu tiên muốn giải quyết ngay</label>
                        <textarea name="dnq_42" id="dnq_42" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_43"><?php echo (int) ++$dn_reg_question_num; ?>. Kỳ vọng khi hợp tác</label>
                        <textarea name="dnq_43" id="dnq_43" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"></textarea>
                    </div>
                    <?php $dn_reg_section_upload(7, 'Hình ảnh minh họa (Phần 7)', false); ?>
                </fieldset>

                <fieldset class="dn-reg-fieldset">
                    <legend>PHẦN 8 – Xác nhận thông tin</legend>
                    <div class="form-group" style="margin-bottom:14px;">
                        <span class="label-like" style="display:block;font-weight:600;margin-bottom:8px;"><?php echo (int) ++$dn_reg_question_num; ?>. Xác nhận thông tin đúng và có thể dùng cho tư vấn / triển khai</span>
                        <label style="display:block;margin:6px 0;"><input type="radio" name="dnq_44" value="dong_y" required> Đồng ý</label>
                        <label style="display:block;margin:6px 0;"><input type="radio" name="dnq_44" value="khong"> Không đồng ý</label>
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_45"><?php echo (int) ++$dn_reg_question_num; ?>. Họ và tên người xác nhận</label>
                        <input type="text" name="dnq_45" id="dnq_45" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="dnq_46"><?php echo (int) ++$dn_reg_question_num; ?>. Ngày điền form</label>
                        <input type="date" name="dnq_46" id="dnq_46" class="widefat" style="max-width:240px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>
                    <?php $dn_reg_section_upload(8, 'Hình ảnh minh họa (Phần 8)', false); ?>
                </fieldset>

                <p style="margin:0;">
                    <button type="submit" class="button dn-reg-submit">
                        Gửi hồ sơ đăng ký
                    </button>
                </p>
            </form>
        </div>
    </div>
</main>
</div>

<?php
if (isset($_GET['dn_submitted']) && (string) $_GET['dn_submitted'] === '1') :
    ?>
<script>
(function () {
    var el = document.getElementById('dn-reg-success-anchor');
    if (el && el.scrollIntoView) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        try { el.focus({ preventScroll: true }); } catch (e) { el.focus(); }
    }
})();
</script>
<?php endif; ?>

<script>
(function () {
    var DN_MAX_SIDE  = <?php echo (int) $dn_reg_max_side; ?>;
    var DN_MAX_BYTES = <?php echo (int) $dn_reg_max_bytes; ?>;

    /* ---- Nén ảnh phía client (canvas resize + JPEG) ---- */
    function compressImageFile(file) {
        return new Promise(function (resolve) {
            if (!file || !file.type || !file.type.startsWith('image/')) {
                resolve(file); return;
            }
            var reader = new FileReader();
            reader.onerror = function () { resolve(file); };
            reader.onload = function (e) {
                var img = new Image();
                img.onerror = function () { resolve(file); };
                img.onload = function () {
                    var w = img.naturalWidth, h = img.naturalHeight;
                    if (!w || !h) { resolve(file); return; }

                    var scale = Math.min(1, DN_MAX_SIDE / Math.max(w, h));
                    var cw = Math.round(w * scale);
                    var ch = Math.round(h * scale);

                    var canvas = document.createElement('canvas');
                    canvas.width  = cw;
                    canvas.height = ch;
                    var ctx = canvas.getContext('2d');
                    if (!ctx) { resolve(file); return; }
                    ctx.drawImage(img, 0, 0, cw, ch);

                    /* PNG nhỏ (<200 KB) giữ nguyên PNG, còn lại dùng JPEG */
                    var usePng = file.type === 'image/png' && file.size < 200 * 1024;
                    var mime   = usePng ? 'image/png' : 'image/jpeg';

                    function tryQ(q) {
                        canvas.toBlob(function (blob) {
                            if (!blob) { resolve(file); return; }
                            if (!usePng && blob.size > DN_MAX_BYTES && q > 0.35) {
                                tryQ(Math.max(0.35, q - 0.1));
                            } else {
                                var newName = file.name.replace(/\.[^.]+$/, usePng ? '.png' : '.jpg');
                                resolve(new File([blob], newName, { type: mime, lastModified: Date.now() }));
                            }
                        }, mime, usePng ? undefined : q);
                    }
                    tryQ(0.85);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    function compressFileList(fileList) {
        if (!fileList || !fileList.length) return Promise.resolve([]);
        var ps = [];
        for (var i = 0; i < fileList.length; i++) ps.push(compressImageFile(fileList[i]));
        return Promise.all(ps);
    }

    /* ---- Progress overlay ---- */
    function showProgress(msg) {
        var ov = document.getElementById('dn-upload-progress-overlay');
        if (!ov) {
            ov = document.createElement('div');
            ov.id = 'dn-upload-progress-overlay';
            ov.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.65);z-index:99999;display:flex;align-items:center;justify-content:center;';
            ov.innerHTML = '<div style="background:#1e3a5f;color:#fff;padding:32px 40px;border-radius:12px;text-align:center;max-width:380px;box-shadow:0 8px 32px rgba(0,0,0,.4);">'
                + '<div style="font-size:36px;margin-bottom:14px;">⏳</div>'
                + '<div id="dn-upload-progress-msg" style="font-size:15px;line-height:1.6;"></div>'
                + '</div>';
            document.body.appendChild(ov);
        }
        document.getElementById('dn-upload-progress-msg').textContent = msg;
        ov.style.display = 'flex';
    }
    function hideProgress() {
        var ov = document.getElementById('dn-upload-progress-overlay');
        if (ov) ov.style.display = 'none';
    }

    /* ---- Submit handler ---- */
    var form = document.getElementById('dn-reg-form-main');
    if (!form || !window.FormData || !window.fetch) return;

    function fileFieldIds() {
        var ids = ['dn_anh_chinh'];
        for (var s = 1; s <= 8; s++) ids.push('dn_sec' + s);
        ids.push('dn_card_gallery');
        return ids;
    }

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('data-dn-native-submit') === '1') return;
        if (!form.checkValidity()) return;
        e.preventDefault();

        showProgress('Đang xử lý và nén ảnh, vui lòng chờ…');

        var fd = new FormData();

        /* Gắn tất cả field không phải file */
        var els = form.querySelectorAll('input, textarea, select');
        for (var k = 0; k < els.length; k++) {
            var el = els[k];
            if (!el.name || el.disabled || el.type === 'file') continue;
            var tp = (el.type || '').toLowerCase();
            if (tp === 'checkbox' || tp === 'radio') {
                if (el.checked) fd.append(el.name, el.value);
            } else if (tp === 'select-multiple') {
                for (var j = 0; j < el.options.length; j++) {
                    if (el.options[j].selected) fd.append(el.name, el.options[j].value);
                }
            } else {
                fd.append(el.name, el.value);
            }
        }

        /* Nén rồi gắn file ảnh (ưu tiên fileStore nếu có — từ hệ thống preview) */
        var fileIds = fileFieldIds();
        var filePromises = fileIds.map(function (fid) {
            var inp = document.getElementById(fid);
            if (!inp) return Promise.resolve();
            var stored = window.dnRegFileStore && window.dnRegFileStore[fid];
            var files  = (stored && stored.length) ? stored : inp.files;
            if (!files || !files.length) return Promise.resolve();
            return compressFileList(files).then(function (compressed) {
                compressed.forEach(function (f) { fd.append(inp.name, f); });
            });
        });

        Promise.all(filePromises).then(function () {
            showProgress('Đang gửi hồ sơ lên máy chủ…');
            return fetch(form.action, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                redirect: 'follow'
            });
        }).then(function (res) {
            hideProgress();
            if (res.redirected && res.url) { window.location.href = res.url; return; }
            if (res.ok)                    { window.location.reload();        return; }
            throw new Error('submit');
        }).catch(function () {
            hideProgress();
            form.setAttribute('data-dn-native-submit', '1');
            HTMLFormElement.prototype.submit.call(form);
        });
    });
})();
</script>
<script>
/* ---- Xem trước ảnh & xóa ảnh đã chọn ---- */
(function () {
    window.dnRegFileStore = {};

    var DN_MAX_IMG  = <?php echo (int) $dn_reg_max_img; ?>;
    var DN_MAX_CARD = <?php echo (int) $dn_reg_card_max; ?>;
    void DN_MAX_IMG; void DN_MAX_CARD; /* referenced via data-max-files attr */

    function store(id) {
        if (!window.dnRegFileStore[id]) window.dnRegFileStore[id] = [];
        return window.dnRegFileStore[id];
    }

    /* Ghi lại FileList vào input (để native validation vẫn hoạt động) */
    function syncInput(input) {
        try {
            var dt = new DataTransfer();
            store(input.id).forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;
        } catch (e) { /* fallback: submit handler dùng dnRegFileStore trực tiếp */ }
    }

    /* Lightbox xem ảnh lớn */
    function openPreviewLightbox(objUrl, name) {
        var lb = document.createElement('div');
        lb.className = 'dn-pv-lightbox';
        lb.setAttribute('role', 'dialog');
        lb.setAttribute('aria-label', name);
        lb.style.cssText = 'position:fixed;inset:0;z-index:999999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.88);';
        var img = document.createElement('img');
        img.src = objUrl;
        img.alt = name;
        img.style.cssText = 'max-width:92vw;max-height:88vh;object-fit:contain;border-radius:6px;box-shadow:0 8px 40px rgba(0,0,0,.5);';
        var cls = document.createElement('button');
        cls.type = 'button';
        cls.className = 'dn-pv-lightbox-close';
        cls.innerHTML = '&times;';
        cls.setAttribute('aria-label', 'Đóng');
        cls.style.cssText = 'position:absolute;top:16px;right:18px;width:42px;height:42px;border:none;border-radius:50%;background:rgba(255,255,255,.2);color:#fff;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center;';
        function close() {
            if (lb.parentNode) document.body.removeChild(lb);
            document.removeEventListener('keydown', onKey);
            document.body.style.overflow = '';
        }
        function onKey(e) { if (e.key === 'Escape') close(); }
        cls.addEventListener('click', close);
        lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
        document.addEventListener('keydown', onKey);
        lb.appendChild(img);
        lb.appendChild(cls);
        document.body.appendChild(lb);
        document.body.style.overflow = 'hidden';
    }

    function render(input) {
        var grid = document.getElementById('dn-pv-' + input.id);
        if (!grid) return;
        var files = store(input.id);
        grid.innerHTML = '';

        function forceSize(el, prop, val) {
            if (!el || !el.style || !el.style.setProperty) return;
            el.style.setProperty(prop, val, 'important');
        }

        // Khóa cứng layout ngang cho dải preview, tránh bị CSS khác ghi đè.
        forceSize(grid, 'display', 'flex');
        forceSize(grid, 'flex-direction', 'row');
        forceSize(grid, 'flex-wrap', 'nowrap');
        forceSize(grid, 'gap', '30px');
        forceSize(grid, 'overflow-x', 'auto');
        forceSize(grid, 'overflow-y', 'hidden');
        forceSize(grid, 'align-items', 'flex-start');
        forceSize(grid, 'width', '100%');

        files.forEach(function (file, idx) {
            var item = document.createElement('div');
            item.className = 'dn-img-pv-item';
            forceSize(item, 'position', 'relative');
            forceSize(item, 'width', '75px');
            forceSize(item, 'min-width', '75px');
            forceSize(item, 'max-width', '75px');
            forceSize(item, 'height', '75px');
            forceSize(item, 'min-height', '75px');
            forceSize(item, 'max-height', '75px');
            forceSize(item, 'overflow', 'visible');
            forceSize(item, 'display', 'block');
            forceSize(item, 'flex', '0 0 auto');

            /* Box clip ảnh — giữ đúng 30×30, overflow:hidden ngăn ảnh tràn */
            var box = document.createElement('div');
            box.className = 'dn-img-pv-img-box';
            box.title = file.name + ' — bấm để xem lớn';
            forceSize(box, 'width', '75px');
            forceSize(box, 'min-width', '75px');
            forceSize(box, 'max-width', '75px');
            forceSize(box, 'height', '75px');
            forceSize(box, 'min-height', '75px');
            forceSize(box, 'max-height', '75px');
            forceSize(box, 'overflow', 'hidden');
            forceSize(box, 'display', 'block');

            var objUrl = URL.createObjectURL(file);
            var img = document.createElement('img');
            img.className = 'dn-img-pv-thumb';
            img.src = objUrl;
            img.alt = file.name;
            forceSize(img, 'width', '75px');
            forceSize(img, 'min-width', '75px');
            forceSize(img, 'max-width', '75px');
            forceSize(img, 'height', '75px');
            forceSize(img, 'min-height', '75px');
            forceSize(img, 'max-height', '75px');
            forceSize(img, 'object-fit', 'cover');
            forceSize(img, 'display', 'block');
            img.addEventListener('load', function () { URL.revokeObjectURL(objUrl); }, { once: true });

            box.appendChild(img);
            box.addEventListener('click', function () {
                var fresh = URL.createObjectURL(file);
                openPreviewLightbox(fresh, file.name);
                setTimeout(function () { URL.revokeObjectURL(fresh); }, 60000);
            });

            /* Nút xóa — nằm ngoài box để không bị overflow:hidden cắt */
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dn-img-pv-remove';
            btn.innerHTML = '&times;';
            btn.setAttribute('aria-label', 'Xóa ' + file.name);
            forceSize(btn, 'position', 'absolute');
            forceSize(btn, 'top', '-6px');
            forceSize(btn, 'right', '-6px');
            forceSize(btn, 'width', '20px');
            forceSize(btn, 'height', '20px');
            forceSize(btn, 'font-size', '13px');
            forceSize(btn, 'border', '2px solid #fff');
            forceSize(btn, 'z-index', '3');
            btn.addEventListener('click', (function (i) {
                return function (e) {
                    e.stopPropagation();
                    store(input.id).splice(i, 1);
                    syncInput(input);
                    render(input);
                };
            })(idx));

            item.appendChild(box);
            item.appendChild(btn);
            grid.appendChild(item);
        });

        updateCounter(input);
    }

    function updateCounter(input) {
        var el = document.getElementById('dn-pv-count-' + input.id);
        var clearBtn = document.getElementById('dn-pv-clear-' + input.id);
        if (!el) return;
        var n   = store(input.id).length;
        var max = parseInt(input.getAttribute('data-max-files') || '99', 10);
        if (n === 0) {
            el.textContent = '';
            el.style.display = 'none';
            if (clearBtn) clearBtn.style.display = 'none';
        } else {
            el.textContent = n + '\u00a0/\u00a0' + max + ' \u1ea3nh \u0111\u00e3 ch\u1ecdn';
            el.style.display = 'block';
            if (clearBtn) clearBtn.style.display = 'inline-flex';
        }
    }

    function initInput(input) {
        window.dnRegFileStore[input.id] = [];

        /* Tạo thanh thông tin + nút xóa tất cả + grid ngay sau input */
        var actions = document.createElement('div');
        actions.className = 'dn-img-pv-actions';

        var counter = document.createElement('div');
        counter.id = 'dn-pv-count-' + input.id;
        counter.className = 'dn-img-pv-counter';
        counter.style.display = 'none';

        var clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.id = 'dn-pv-clear-' + input.id;
        clearBtn.className = 'dn-img-pv-clear-btn';
        clearBtn.textContent = 'Xóa ảnh đã chọn';
        clearBtn.style.display = 'none';
        clearBtn.addEventListener('click', function () {
            window.dnRegFileStore[input.id] = [];
            syncInput(input);
            render(input);
        });

        actions.appendChild(counter);
        actions.appendChild(clearBtn);

        var grid = document.createElement('div');
        grid.id = 'dn-pv-' + input.id;
        grid.className = 'dn-img-pv-grid';

        input.parentNode.insertBefore(actions, input.nextSibling);
        input.parentNode.insertBefore(grid, actions.nextSibling);

        var max = parseInt(input.getAttribute('data-max-files') || '99', 10);

        input.addEventListener('change', function () {
            var s    = store(input.id);
            var newF = Array.prototype.slice.call(input.files || []);
            newF.forEach(function (f) {
                if (s.length >= max) return;
                var dup = s.some(function (x) { return x.name === f.name && x.size === f.size; });
                if (!dup) s.push(f);
            });
            syncInput(input);
            render(input);
        });
    }

    function initAll() {
        var form = document.getElementById('dn-reg-form-main');
        if (!form) return;
        form.querySelectorAll('input[type="file"][data-max-files]').forEach(initInput);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();

/* ---- Giới hạn số từ: CHỈ cắt khi vượt quá, KHÔNG trim khoảng trắng đang gõ ---- */
(function () {
    document.querySelectorAll('.dn-reg-word-cap[data-dn-max-words]').forEach(function (el) {
        var max = parseInt(el.getAttribute('data-dn-max-words'), 10);
        if (!max || max < 1) return;

        /* input: chỉ cắt nếu quá từ, giữ nguyên khoảng trắng đang gõ */
        el.addEventListener('input', function () {
            var raw = el.value;
            var parts = raw.trim().split(/\s+/).filter(Boolean);
            if (parts.length > max) {
                /* Cắt bỏ phần thừa nhưng giữ khoảng trắng cuối nếu vẫn trong giới hạn */
                el.value = parts.slice(0, max).join(' ');
            }
        });

        /* blur: dọn dẹp khoảng trắng thừa đầu/cuối */
        el.addEventListener('blur', function () {
            var trimmed = el.value.trim();
            var parts   = trimmed ? trimmed.split(/\s+/) : [];
            if (parts.length > max) trimmed = parts.slice(0, max).join(' ');
            if (trimmed !== el.value) el.value = trimmed;
        });
    });
})();
</script>
<?php
get_footer();
