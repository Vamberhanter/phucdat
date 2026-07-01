<?php
/**
 * Đăng ký doanh nghiệp (form công khai) + duyệt + tạo bài Doanh nghiệp.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DNTTVN_DN_REG_MAX_SEC_IMAGES', 5);
define('DNTTVN_DN_REG_MAX_BYTES', 2097152);
define('DNTTVN_DN_REG_MAX_SIDE', 2000);
/** Tối đa ảnh phụ trên thẻ doanh nghiệp (danh bạ + chi tiết gallery). */
define('DNTTVN_DN_REG_MAX_CARD_PHU', 5);
/** Giới hạn số từ (cách nhau bằng khoảng trắng) cho các ô văn bản đăng ký. */
define('DNTTVN_DN_REG_MAX_WORDS_FIELD', 350);
/** Giới hạn số từ cho mô tả ngắn (Thông tin chung / thẻ). */
define('DNTTVN_DN_REG_MAX_WORDS_MO_TA', 200);

function dnttvn_dn_reg_validate_image_file($file) {
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    if (!empty($file['error']) && (int) $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (empty($file['size']) || (int) $file['size'] > DNTTVN_DN_REG_MAX_BYTES) {
        return false;
    }
    $check = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
    if (empty($check['type']) || strpos($check['type'], 'image/') !== 0) {
        return false;
    }
    $dims = @getimagesize($file['tmp_name']);
    if (is_array($dims) && isset($dims[0], $dims[1])) {
        if ((int) $dims[0] > DNTTVN_DN_REG_MAX_SIDE || (int) $dims[1] > DNTTVN_DN_REG_MAX_SIDE) {
            return false;
        }
    }
    return true;
}

function dnttvn_dn_reg_normalize_files_field_array($field) {
    $field = preg_replace('/[^a-z0-9_]/i', '', (string) $field);
    if ($field === '' || empty($_FILES[ $field ]) || !is_array($_FILES[ $field ]) || !isset($_FILES[ $field ]['name'])) {
        return null;
    }
    $f = $_FILES[ $field ];
    if (isset($f['full_path'])) {
        unset($f['full_path']);
    }
    if (!is_array($f['name'])) {
        return array(
            'name'     => array( $f['name'] ),
            'type'     => array( $f['type'] ),
            'tmp_name' => array( $f['tmp_name'] ),
            'error'    => array( isset($f['error']) ? $f['error'] : UPLOAD_ERR_OK ),
            'size'     => array( isset($f['size']) ? $f['size'] : 0 ),
        );
    }
    $n = count($f['name']);
    if ($n === 0) {
        return null;
    }
    foreach (array( 'type', 'tmp_name', 'error', 'size' ) as $k) {
        if (!isset($f[ $k ])) {
            $f[ $k ] = array_fill(0, $n, $k === 'error' ? UPLOAD_ERR_NO_FILE : '');
        } elseif (!is_array($f[ $k ])) {
            $f[ $k ] = array_fill(0, $n, $f[ $k ]);
        } elseif (count($f[ $k ]) < $n) {
            $pad = $k === 'error' ? UPLOAD_ERR_NO_FILE : '';
            $f[ $k ] = array_pad(array_values($f[ $k ]), $n, $pad);
        } elseif (count($f[ $k ]) > $n) {
            $f[ $k ] = array_slice(array_values($f[ $k ]), 0, $n);
        }
    }
    return $f;
}

function dnttvn_dn_reg_handle_multi_image_upload($parent_post_id, $field, $max_files = null) {
    $field = preg_replace('/[^a-z0-9_]/i', '', (string) $field);
    if ($field === '') {
        return array();
    }
    $f = dnttvn_dn_reg_normalize_files_field_array($field);
    if ($f === null) {
        return array();
    }
    $max = $max_files !== null ? max(1, min(20, (int) $max_files)) : DNTTVN_DN_REG_MAX_SEC_IMAGES;

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $ids   = array();
    $names = $f['name'];
    $count = is_array($names) ? count($names) : 0;
    $count = min($count, $max);

    for ($i = 0; $i < $count; $i++) {
        if (count($ids) >= $max) {
            break;
        }
        $file = array(
            'name'     => isset($f['name'][ $i ]) ? $f['name'][ $i ] : '',
            'type'     => isset($f['type'][ $i ]) ? $f['type'][ $i ] : '',
            'tmp_name' => isset($f['tmp_name'][ $i ]) ? $f['tmp_name'][ $i ] : '',
            'error'    => isset($f['error'][ $i ]) ? $f['error'][ $i ] : UPLOAD_ERR_NO_FILE,
            'size'     => isset($f['size'][ $i ]) ? $f['size'][ $i ] : 0,
        );
        if (!dnttvn_dn_reg_validate_image_file($file)) {
            continue;
        }
        $overrides = array('test_form' => false);
        $move      = wp_handle_upload($file, $overrides);
        if (empty($move['file']) || empty($move['type'])) {
            continue;
        }
        $attachment = array(
            'post_mime_type' => $move['type'],
            'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );
        $attach_id = wp_insert_attachment($attachment, $move['file'], $parent_post_id);
        if (!is_wp_error($attach_id) && $attach_id) {
            $attach_data = wp_generate_attachment_metadata($attach_id, $move['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);
            $ids[] = (int) $attach_id;
        }
    }
    return $ids;
}

function dnttvn_dn_reg_handle_section_uploads($parent_post_id, $sec_index) {
    return dnttvn_dn_reg_handle_multi_image_upload($parent_post_id, 'dn_sec' . (int) $sec_index);
}

function dnttvn_register_dang_ky_doanh_nghiep_post_type() {
    $labels = array(
        'name'          => 'Đăng ký Doanh nghiệp',
        'singular_name' => 'Hồ sơ đăng ký',
        'menu_name'     => 'Đăng ký DN (form)',
        'edit_item'     => 'Xem hồ sơ đăng ký',
        'search_items'  => 'Tìm hồ sơ',
    );
    register_post_type(
        'dang_ky_doanh_nghiep',
        array(
            'labels'       => $labels,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'edit.php?post_type=doanh_nghiep',
            'menu_icon'    => 'dashicons-feedback',
            'supports'     => array('title'),
            'capability_type' => 'post',
        )
    );
}
add_action('init', 'dnttvn_register_dang_ky_doanh_nghiep_post_type', 11);

function dnttvn_dn_reg_meta_keys() {
    return array(
        1, 2, 4, 5, 6, 7, 8, 9, 10, 11, 19,
        12, 13, 14, 15, 16,
        17, 47, 48, 49,
        25, 27, 28, 29, 30,
        32, 33, 34,
        36, 37, 38, 39, 40,
        42, 43, 45, 46,
    );
}

function dnttvn_handle_dang_ky_doanh_nghiep_form() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['dnttvn_dn_reg_nonce'])) {
        return;
    }
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dnttvn_dn_reg_nonce'])), 'dnttvn_dn_reg_submit')) {
        return;
    }
    $q1 = isset($_POST['dnq_1']) ? sanitize_text_field(wp_unslash($_POST['dnq_1'])) : '';
    $q1 = dnttvn_dn_reg_trim_to_word_limit($q1, DNTTVN_DN_REG_MAX_WORDS_FIELD);
    if ($q1 === '') {
        return;
    }

    $title = $q1 . ' — ' . current_time('Y-m-d H:i');
    $post_id = wp_insert_post(
        array(
            'post_type'    => 'dang_ky_doanh_nghiep',
            'post_title'   => $title,
            'post_status'  => 'publish',
            'post_author'  => 1,
        )
    );
    if (is_wp_error($post_id) || !$post_id) {
        return;
    }

    $sec_images = array();
    for ($s = 1; $s <= 8; $s++) {
        $raw_ids            = dnttvn_dn_reg_handle_section_uploads($post_id, $s);
        $sec_images[ $s ] = array_values(array_filter(array_map('absint', is_array($raw_ids) ? $raw_ids : array())));
    }
    $sec_images = dnttvn_dn_reg_normalize_section_images_meta($sec_images);
    update_post_meta($post_id, '_dn_reg_sec_images', $sec_images);

    $feat_ids = dnttvn_dn_reg_handle_multi_image_upload($post_id, 'dn_anh_chinh', 1);
    if (!empty($feat_ids[0])) {
        update_post_meta($post_id, '_dn_reg_featured_image_id', (int) $feat_ids[0]);
    }

    $card_imgs = dnttvn_dn_reg_handle_multi_image_upload($post_id, 'dn_card_gallery', DNTTVN_DN_REG_MAX_CARD_PHU);
    update_post_meta($post_id, '_dn_reg_card_gallery', array_values(array_filter(array_map('absint', $card_imgs))));

    if (isset($_POST['dn_reg_mo_ta_ngan'])) {
        $mt = sanitize_textarea_field(wp_unslash($_POST['dn_reg_mo_ta_ngan']));
        $mt = dnttvn_dn_reg_trim_to_word_limit($mt, DNTTVN_DN_REG_MAX_WORDS_MO_TA);
        update_post_meta($post_id, '_dn_reg_mo_ta_ngan', $mt);
    }

    $dn_reg_textarea_keys_word = array(8, 10, 12, 13, 14, 15, 16, 17, 25, 27, 28, 29, 30, 32, 33, 34, 36, 37, 38, 39, 40, 42, 43, 47, 48, 49);

    foreach (dnttvn_dn_reg_meta_keys() as $k) {
        $key = 'dnq_' . $k;
        if (!isset($_POST[ $key ])) {
            continue;
        }
        if (in_array($k, array(46), true)) {
            update_post_meta($post_id, '_' . $key, sanitize_text_field(wp_unslash($_POST[ $key ])));
        } elseif (in_array($k, $dn_reg_textarea_keys_word, true)) {
            $tv = sanitize_textarea_field(wp_unslash($_POST[ $key ]));
            $tv = dnttvn_dn_reg_trim_to_word_limit($tv, DNTTVN_DN_REG_MAX_WORDS_FIELD);
            update_post_meta($post_id, '_' . $key, $tv);
        } else {
            $sv = sanitize_text_field(wp_unslash($_POST[ $key ]));
            update_post_meta($post_id, '_' . $key, dnttvn_dn_reg_trim_to_word_limit($sv, DNTTVN_DN_REG_MAX_WORDS_FIELD));
        }
    }

    if (isset($_POST['dnq_11']) && sanitize_text_field(wp_unslash($_POST['dnq_11'])) === 'khac') {
        $k11 = isset($_POST['dnq_11_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_11_khac'])) : '';
        update_post_meta($post_id, '_dnq_11', dnttvn_dn_reg_trim_to_word_limit($k11, DNTTVN_DN_REG_MAX_WORDS_FIELD));
    }
    if (isset($_POST['dnq_19']) && sanitize_text_field(wp_unslash($_POST['dnq_19'])) === 'khac') {
        $k19 = isset($_POST['dnq_19_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_19_khac'])) : '';
        update_post_meta($post_id, '_dnq_19', dnttvn_dn_reg_trim_to_word_limit($k19, DNTTVN_DN_REG_MAX_WORDS_FIELD));
    }

    $q44 = isset($_POST['dnq_44']) ? sanitize_key(wp_unslash($_POST['dnq_44'])) : '';
    if (in_array($q44, array('dong_y', 'khong'), true)) {
        update_post_meta($post_id, '_dnq_44', $q44);
    }

    if (isset($_POST['dnq_41'])) {
        $v41 = sanitize_key(wp_unslash($_POST['dnq_41']));
        if ($v41 !== '') {
            update_post_meta($post_id, '_dnq_41', $v41);
        }
        $k41 = isset($_POST['dnq_41_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_41_khac'])) : '';
        update_post_meta($post_id, '_dnq_41_khac', ($v41 === 'khac') ? dnttvn_dn_reg_trim_to_word_limit($k41, DNTTVN_DN_REG_MAX_WORDS_FIELD) : '');
    }

    if (isset($_POST['dnq_18'])) {
        $q18 = sanitize_key(wp_unslash($_POST['dnq_18']));
        if (!in_array($q18, array('offline', 'online', 'b2b', 'khac'), true)) {
            $q18 = '';
        }
        if ($q18 !== '') {
            update_post_meta($post_id, '_dnq_18', $q18);
        }
        $q18k = isset($_POST['dnq_18_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_18_khac'])) : '';
        $q18k = dnttvn_dn_reg_trim_to_word_limit($q18k, DNTTVN_DN_REG_MAX_WORDS_FIELD);
        update_post_meta($post_id, '_dnq_18_khac', ($q18 === 'khac') ? $q18k : '');
    }

    $preview_key = wp_generate_password(48, false, false);
    update_post_meta($post_id, '_dn_reg_preview_key', $preview_key);

    $ref = wp_get_referer() ? wp_get_referer() : home_url('/dang-ky-doanh-nghiep/');
    $ref = remove_query_arg(array('dn_err', 'dn_submitted', 'dn_preview_id', 'dn_preview_key'), $ref);
    wp_safe_redirect(
        add_query_arg(
            array(
                'dn_submitted'     => '1',
                'dn_preview_id'    => $post_id,
                'dn_preview_key'   => $preview_key,
            ),
            $ref
        )
    );
    exit;
}
add_action('template_redirect', 'dnttvn_handle_dang_ky_doanh_nghiep_form');

function dnttvn_dn_reg_format_html_block($title, $pairs, $show_inner_title = true) {
    $out = '<div class="dn-sc-form-block">';
    if ($show_inner_title && $title !== '') {
        $out .= '<h3 class="dn-sc-form-block__title">' . esc_html($title) . '</h3>';
    }
    foreach ($pairs as $label => $val) {
        if ($val === '' || $val === array()) {
            continue;
        }
        if (is_array($val)) {
            $val = implode(', ', array_map('sanitize_text_field', $val));
        }
        $out .= '<p class="dn-sc-form-block__row"><strong class="dn-sc-form-block__q">' . esc_html($label) . ':</strong><br>' . nl2br(esc_html((string) $val)) . '</p>';
    }
    return $out . '</div>';
}

/**
 * Cắt chuỗi theo số từ (dùng cho mô tả ngắn / ô văn bản).
 *
 * @param string $text
 * @param int    $max_words
 * @return string
 */
function dnttvn_dn_reg_trim_to_word_limit($text, $max_words) {
    $text = is_string($text) ? trim($text) : '';
    if ($text === '' || $max_words < 1) {
        return '';
    }
    $plain = wp_strip_all_tags($text);
    $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($words) || count($words) <= $max_words) {
        return $text;
    }
    return implode(' ', array_slice($words, 0, $max_words));
}

/**
 * Cắt chuỗi theo số ký tự (UTF-8), dùng cho giới hạn ô nhập đăng ký.
 *
 * @param string $text
 * @param int    $max_chars
 * @return string
 */
function dnttvn_dn_reg_trim_char_limit($text, $max_chars) {
    $text = is_string($text) ? $text : '';
    $text = trim($text);
    if ($text === '' || $max_chars < 1) {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $max_chars) {
            return $text;
        }
        return mb_substr($text, 0, $max_chars, 'UTF-8');
    }
    $len = strlen($text);
    if ($len <= $max_chars) {
        return $text;
    }
    return substr($text, 0, $max_chars);
}

/**
 * Mỗi phần 1–8 phải có ít nhất một ảnh minh họa (sau upload).
 *
 * @param array<int, array<int>> $sec_images
 * @return int[] Danh sách số phần thiếu ảnh
 */
function dnttvn_dn_reg_validate_sec_images_required($sec_images) {
    $missing = array();
    for ($s = 1; $s <= 8; $s++) {
        $n = 0;
        if (!empty($sec_images[ $s ]) && is_array($sec_images[ $s ])) {
            foreach ($sec_images[ $s ] as $id) {
                if (absint($id) > 0) {
                    $n++;
                }
            }
        }
        if ($n < 1) {
            $missing[] = $s;
        }
    }
    return $missing;
}

function dnttvn_dn_reg_get_meta($post_id, $k) {
    return get_post_meta($post_id, '_dnq_' . $k, true);
}

/**
 * Chuỗi hiển thị câu 18 (dropdown hoặc hồ sơ cũ dạng checkbox).
 */
function dnttvn_dn_reg_get_kenh_ban_display($post_id) {
    $post_id = (int) $post_id;
    $v        = get_post_meta($post_id, '_dnq_18', true);
    if (is_array($v)) {
        return implode(', ', array_map('sanitize_text_field', $v));
    }
    $map = array(
        'offline' => 'Offline',
        'online'  => 'Online',
        'b2b'     => 'B2B',
        'khac'    => 'Khác',
    );
    $key = is_string($v) ? $v : '';
    $out = isset($map[ $key ]) ? $map[ $key ] : (string) $v;
    if ($key === 'khac') {
        $extra = get_post_meta($post_id, '_dnq_18_khac', true);
        if (is_string($extra) && $extra !== '') {
            $out .= ': ' . $extra;
        }
    }
    return $out;
}

/**
 * Nội dung câu 40: mô tả tài liệu (hoặc meta cũ dạng checkbox).
 */
function dnttvn_dn_reg_get_tailieu_display_for_html($post_id) {
    $post_id = (int) $post_id;
    $raw     = get_post_meta($post_id, '_dnq_40', true);
    $parts   = array();
    if (is_array($raw)) {
        return implode(', ', array_map('sanitize_text_field', $raw));
    }
    if (is_string($raw) && $raw !== '') {
        $parts[] = $raw;
    }
    return implode("\n", $parts);
}

/**
 * Nhãn hiển thị câu 41 (dropdown hoặc meta cũ dạng checkbox).
 */
function dnttvn_dn_reg_get_nhu_cau_labels() {
    return array(
        'thuong_hieu'   => 'Thương hiệu',
        'chien_luoc_kd' => 'Chiến lược kinh doanh',
        'chien_luoc_sp' => 'Chiến lược sản phẩm',
        'van_hanh'      => 'Hệ thống vận hành',
        'marketing'     => 'Marketing',
        'kenh_ban'      => 'Kênh bán hàng',
        'chuyen_doi_so' => 'Chuyển đổi số',
        'dao_tao'       => 'Đào tạo nhân sự',
        'hsnl2'         => 'Hồ sơ năng lực',
        'khac'          => 'Khác',
    );
}

function dnttvn_dn_reg_get_nhu_cau_display($post_id) {
    $post_id = (int) $post_id;
    $v        = get_post_meta($post_id, '_dnq_41', true);
    if (is_array($v)) {
        $map = dnttvn_dn_reg_get_nhu_cau_labels();
        $out = array();
        foreach ($v as $one) {
            $k = sanitize_key($one);
            $out[] = isset($map[ $k ]) ? $map[ $k ] : $one;
        }
        return implode(', ', $out);
    }
    $map = dnttvn_dn_reg_get_nhu_cau_labels();
    $key = is_string($v) ? $v : '';
    $out = isset($map[ $key ]) ? $map[ $key ] : (string) $v;
    if ($key === 'khac') {
        $extra = get_post_meta($post_id, '_dnq_41_khac', true);
        if (is_string($extra) && $extra !== '') {
            $out .= ': ' . $extra;
        }
    }
    return $out;
}

function dnttvn_dn_reg_term_name_from_slug($taxonomy, $slug) {
    $slug = sanitize_title((string) $slug);
    if ($slug === '') {
        return '';
    }
    $term = get_term_by('slug', $slug, $taxonomy);
    if ($term && !is_wp_error($term)) {
        return $term->name;
    }
    return '';
}

/**
 * Ảnh phụ thẻ (upload riêng) + ảnh các phần form, không trùng ảnh đại diện, tối đa theo DNTTVN_DN_REG_MAX_CARD_PHU.
 */
function dnttvn_dn_reg_collect_public_gallery_ids($app_id, $sec_imgs, $featured_id) {
    $app_id       = (int) $app_id;
    $featured_id  = (int) $featured_id;
    $max          = defined('DNTTVN_DN_REG_MAX_CARD_PHU') ? (int) DNTTVN_DN_REG_MAX_CARD_PHU : 5;
    $ids          = array();

    $card = get_post_meta($app_id, '_dn_reg_card_gallery', true);
    if (!is_array($card)) {
        $card = array();
    }
    foreach ($card as $cid) {
        $cid = absint($cid);
        if ($cid && $cid !== $featured_id && ! in_array($cid, $ids, true)) {
            $ids[] = $cid;
        }
        if (count($ids) >= $max) {
            return $ids;
        }
    }
    foreach ($sec_imgs as $bucket) {
        if (!is_array($bucket)) {
            continue;
        }
        foreach ($bucket as $cid) {
            $cid = absint($cid);
            if (!$cid || $cid === $featured_id) {
                continue;
            }
            if (! in_array($cid, $ids, true)) {
                $ids[] = $cid;
            }
            if (count($ids) >= $max) {
                return $ids;
            }
        }
    }
    return $ids;
}

/**
 * Chuẩn hóa meta _dn_reg_sec_images (key 1–8, mảng ID).
 *
 * @param mixed $sec_imgs
 * @return array<int, array<int>>
 */
function dnttvn_dn_reg_normalize_section_images_meta($sec_imgs) {
    $out = array();
    for ($i = 1; $i <= 8; $i++) {
        $out[ $i ] = array();
        $raw       = null;
        if (is_array($sec_imgs) && isset($sec_imgs[ $i ]) && is_array($sec_imgs[ $i ])) {
            $raw = $sec_imgs[ $i ];
        } elseif (is_array($sec_imgs) && isset($sec_imgs[ (string) $i ]) && is_array($sec_imgs[ (string) $i ])) {
            $raw = $sec_imgs[ (string) $i ];
        }
        if (is_array($raw)) {
            $out[ $i ] = array_values(array_filter(array_map('absint', $raw)));
        }
    }
    return $out;
}

/**
 * Gom tối đa $max ảnh cho album cuối trang (ưu phần 3, 4, rồi các phần còn lại), không trùng ảnh đại diện.
 *
 * @param array<int, array<int>> $sec_imgs
 * @return array<int>
 */
function dnttvn_dn_reg_slider_ids_from_sections($sec_imgs, $thumb, $max = 5) {
    $thumb = (int) $thumb;
    $max   = max(1, min(10, (int) $max));
    $out   = array();
    $order = array(3, 4, 1, 2, 5, 6, 7, 8);
    foreach ($order as $si) {
        if (empty($sec_imgs[ $si ]) || !is_array($sec_imgs[ $si ])) {
            continue;
        }
        foreach ($sec_imgs[ $si ] as $sid) {
            $sid = absint($sid);
            if (!$sid || $sid === $thumb || in_array($sid, $out, true)) {
                continue;
            }
            $out[] = $sid;
            if (count($out) >= $max) {
                return $out;
            }
        }
    }
    return $out;
}

/**
 * Dữ liệu “sau duyệt” dựng từ hồ sơ đăng ký — dùng chung khi duyệt và trang xem trước.
 *
 * @param int $app_id ID bài dang_ky_doanh_nghiep.
 * @return array<string,mixed>|null
 */
function dnttvn_dn_reg_build_approval_payload($app_id) {
    if (!function_exists('dnttvn_get_doanh_nghiep_default_structured_items')) {
        return null;
    }
    $app_id = (int) $app_id;
    $post   = get_post($app_id);
    if (!$post || $post->post_type !== 'dang_ky_doanh_nghiep') {
        return null;
    }
    $m = function ($k) use ($app_id) {
        return dnttvn_dn_reg_get_meta($app_id, $k);
    };

    $title = $m(1) ?: $post->post_title;

    $nganh_label = '';
    if ($m(11) !== '') {
        $slug_n = sanitize_title($m(11));
        $tn     = $slug_n ? get_term_by('slug', $slug_n, 'nganh_hang') : false;
        if ($tn && !is_wp_error($tn)) {
            $nganh_label = $tn->name;
        } else {
            $nganh_label = sanitize_text_field($m(11));
        }
    }

    $khu_vuc_label = '';
    if ($m(19) !== '') {
        $slug_k = sanitize_title($m(19));
        $tk     = $slug_k ? get_term_by('slug', $slug_k, 'khu_vuc') : false;
        if ($tk && !is_wp_error($tk)) {
            $khu_vuc_label = $tk->name;
        } else {
            $khu_vuc_label = sanitize_text_field($m(19));
        }
    }

    $mo_ta_tu_form = get_post_meta($app_id, '_dn_reg_mo_ta_ngan', true);
    $mo_ta_tu_form = is_string($mo_ta_tu_form) ? trim($mo_ta_tu_form) : '';
    if ($mo_ta_tu_form !== '') {
        $mo_ta_final = $mo_ta_tu_form;
    } else {
        $ex_parts = array_filter(array($m(1), $nganh_label, $khu_vuc_label));
        $excerpt  = wp_trim_words(implode(' · ', $ex_parts), 28, '…');
        if ($excerpt === '') {
            $excerpt = wp_trim_words(wp_strip_all_tags($m(8)), 24, '…');
        }
        $mo_ta_final = $excerpt;
    }
    /** Mô tả ngắn: cùng giới hạn từ với form đăng ký (Thông tin chung + meta thẻ). */
    $mo_ta_cho_thong_tin_chung = dnttvn_dn_reg_trim_to_word_limit($mo_ta_final, DNTTVN_DN_REG_MAX_WORDS_MO_TA);
    $mo_ta_final                 = $mo_ta_cho_thong_tin_chung;

    $sec_imgs = dnttvn_dn_reg_normalize_section_images_meta(get_post_meta($app_id, '_dn_reg_sec_images', true));

    $thumb = (int) get_post_meta($app_id, '_dn_reg_featured_image_id', true);
    if (!$thumb) {
        foreach ($sec_imgs as $ids) {
            if (is_array($ids) && !empty($ids[0])) {
                $thumb = absint($ids[0]);
                break;
            }
        }
    }

    $slider_ids     = dnttvn_dn_reg_slider_ids_from_sections($sec_imgs, $thumb, 5);
    $gallery_public = dnttvn_dn_reg_collect_public_gallery_ids($app_id, $sec_imgs, $thumb);

    $hinh_anh_phu = 0;
    if (!empty($gallery_public)) {
        if (isset($gallery_public[1])) {
            $hinh_anh_phu = (int) $gallery_public[1];
        } elseif (isset($gallery_public[0]) && (int) $gallery_public[0] !== (int) $thumb) {
            $hinh_anh_phu = (int) $gallery_public[0];
        }
    }

    $structured = dnttvn_get_doanh_nghiep_default_structured_items();

    $dn_reg_slice_sec = function ($sec_n) use ($sec_imgs, $thumb) {
        $sec_n = (int) $sec_n;
        $raw   = isset($sec_imgs[ $sec_n ]) && is_array($sec_imgs[ $sec_n ]) ? $sec_imgs[ $sec_n ] : array();
        $ids   = array_values(
            array_filter(
                array_unique(array_map('absint', $raw)),
                function ($id) use ($thumb) {
                    return $id > 0 && $id !== (int) $thumb;
                }
            )
        );
        return array_slice($ids, 0, 5);
    };

    $card_struct = get_post_meta($app_id, '_dn_reg_card_gallery', true);
    if (!is_array($card_struct)) {
        $card_struct = array();
    }
    $imgs_phan_1_card = array_values(
        array_filter(
            array_unique(array_map('absint', $card_struct)),
            function ($id) use ($thumb) {
                return $id > 0 && $id !== (int) $thumb;
            }
        )
    );
    $imgs_phan_1_pub = array_slice(array_merge($imgs_phan_1_card, $dn_reg_slice_sec(1)), 0, 5);

    $sec1_pairs = array();
    $mt_ngan_blk = is_string($mo_ta_cho_thong_tin_chung) ? trim($mo_ta_cho_thong_tin_chung) : '';
    if ($mt_ngan_blk !== '') {
        $sec1_pairs['Mô tả ngắn'] = $mt_ngan_blk;
    }
    $p0_mo_ta_block = dnttvn_dn_reg_format_html_block('', $sec1_pairs, false);
    $structured[0]['content_items'] = array(
        array(
            'text'           => $p0_mo_ta_block,
            'images'         => $imgs_phan_1_pub,
            'image_captions' => array(),
        ),
    );

    $p2_su_menh = dnttvn_dn_reg_format_html_block(
        '',
        array(
            'Sứ mệnh'                      => $m(12),
            'Tầm nhìn'                     => $m(13),
            'Giá trị cốt lõi'              => $m(14),
            'Thương hiệu trong tâm trí KH' => $m(15),
            'Điểm khác biệt'               => $m(16),
        ),
        false
    );
    $structured[1]['content_items'] = array(
        array(
            'text'           => $p2_su_menh,
            'images'         => $dn_reg_slice_sec(2),
            'image_captions' => array(),
        ),
    );
    $structured[2]['content_items'] = array(
        array(
            'text'           => dnttvn_dn_reg_format_html_block(
                '',
                array(
                    'Mô hình kinh doanh'   => $m(17),
                    'Mô hình quản trị'     => $m(47),
                    'Mô hình doanh nghiệp' => $m(48),
                    'Mô hình bán hàng'     => $m(49),
                ),
                false
            ),
            'images'         => $dn_reg_slice_sec(3),
            'image_captions' => array(),
        ),
    );
    $structured[3]['content_items'] = array(
        array(
            'text'           => dnttvn_dn_reg_format_html_block(
                '',
                array(
                    'Cơ sở vật chất'       => $m(25),
                    'Nhân sự'              => $m(27),
                    'Công nghệ / hệ thống' => $m(28),
                    'Pháp lý'              => $m(29),
                    'Năng lực mạnh'        => $m(30),
                ),
                false
            ),
            'images'         => $dn_reg_slice_sec(4),
            'image_captions' => array(),
        ),
    );
    $structured[4]['content_items'] = array(
        array(
            'text'           => dnttvn_dn_reg_format_html_block(
                '',
                array(
                    'Triết lý / nguyên tắc vận hành' => $m(32),
                    'Quy trình bán / cung cấp dịch vụ' => $m(33),
                    'Chăm sóc khách hàng sau bán'    => $m(34),
                ),
                false
            ),
            'images'         => $dn_reg_slice_sec(5),
            'image_captions' => array(),
        ),
    );

    $q40_txt = dnttvn_dn_reg_get_tailieu_display_for_html($app_id);
    $pairs6  = array(
        'Khách hàng thường đánh giá cao ở điểm nào?' => $m(36),
        'Phản hồi / góp ý thường gặp'                => $m(37),
        'Khách hàng / đối tác tiêu biểu'             => $m(38),
        'Dự án / case study'                         => $m(39),
    );
    if ($q40_txt !== '') {
        $pairs6['Tài liệu có thể gửi kèm (mô tả)'] = $q40_txt;
    }
    $structured[5]['content_items'] = array(
        array(
            'text'           => dnttvn_dn_reg_format_html_block('', $pairs6, false),
            'images'         => $dn_reg_slice_sec(6),
            'image_captions' => array(),
        ),
    );

    $structured[6]['content_items'] = array(
        array(
            'text'           => dnttvn_dn_reg_format_html_block(
                '',
                array(
                    'Đang cần cải thiện hoặc xây dựng' => dnttvn_dn_reg_get_nhu_cau_display($app_id),
                    'Ba vấn đề ưu tiên'                => $m(42),
                    'Kỳ vọng khi hợp tác'              => $m(43),
                ),
                false
            ),
            'images'         => $dn_reg_slice_sec(7),
            'image_captions' => array(),
        ),
    );

    $v44     = $m(44);
    $disp44  = '';
    if ($v44 === 'dong_y') {
        $disp44 = 'Đồng ý';
    } elseif ($v44 === 'khong') {
        $disp44 = 'Không đồng ý';
    } elseif ($v44 !== '' && $v44 !== null) {
        $disp44 = (string) $v44;
    }
    $structured[7]['content_items'] = array(
        array(
            'text'           => dnttvn_dn_reg_format_html_block(
                '',
                array(
                    'Xác nhận thông tin đúng và có thể dùng cho tư vấn / triển khai' => $disp44,
                    'Họ và tên người xác nhận'                                       => $m(45),
                    'Ngày điền form'                                                 => $m(46),
                ),
                false
            ),
            'images'         => $dn_reg_slice_sec(8),
            'image_captions' => array(),
        ),
    );

    $structured_public = function_exists('dnttvn_doanh_nghiep_filter_public_structured')
        ? dnttvn_doanh_nghiep_filter_public_structured($structured)
        : $structured;

    return array(
        'app_id'           => $app_id,
        'title'            => $title,
        'ten_day_du'       => $m(1),
        'dia_chi'          => $m(8),
        'dien_thoai'       => $m(5),
        'email_lien_he'    => $m(6),
        'website'          => $m(7),
        'nganh_slug'       => $m(11) !== '' ? sanitize_title($m(11)) : '',
        'khu_slug'         => $m(19) !== '' ? sanitize_title($m(19)) : '',
        'nganh_label'      => $nganh_label,
        'khu_vuc_label'    => $khu_vuc_label,
        'mo_ta_ngan'       => $mo_ta_final,
        'mo_ta_tu_form'    => $mo_ta_tu_form,
        'sec_imgs'         => $sec_imgs,
        'thumb'            => $thumb,
        'slider_ids'       => $slider_ids,
        'gallery_public'   => $gallery_public,
        'hinh_anh_phu'     => $hinh_anh_phu,
        'structured'       => $structured_public,
    );
}

function dnttvn_dn_reg_create_doanh_nghiep_from_application($app_id) {
    $payload = dnttvn_dn_reg_build_approval_payload($app_id);
    if ($payload === null) {
        return 0;
    }
    $app_id = (int) $app_id;
    $post   = get_post($app_id);
    if (!$post) {
        return 0;
    }

    $title = $payload['title'];

    $dn_id = wp_insert_post(
        array(
            'post_type'    => 'doanh_nghiep',
            'post_title'   => $title,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => 1,
        )
    );
    if (is_wp_error($dn_id) || !$dn_id) {
        return 0;
    }

    update_post_meta($dn_id, '_ten_day_du', $payload['ten_day_du']);
    update_post_meta($dn_id, '_dia_chi', $payload['dia_chi']);
    update_post_meta($dn_id, '_dien_thoai', $payload['dien_thoai']);
    update_post_meta($dn_id, '_email_lien_he', $payload['email_lien_he']);
    update_post_meta($dn_id, '_website_doanh_nghiep', $payload['website']);

    if ($payload['nganh_slug'] !== '') {
        $tn = get_term_by('slug', $payload['nganh_slug'], 'nganh_hang');
        if ($tn && !is_wp_error($tn)) {
            wp_set_object_terms($dn_id, array((int) $tn->term_id), 'nganh_hang', false);
        }
        if ($payload['nganh_label'] !== '') {
            update_post_meta($dn_id, '_nganh_hang', $payload['nganh_label']);
        }
    }

    if ($payload['khu_slug'] !== '') {
        $tk = get_term_by('slug', $payload['khu_slug'], 'khu_vuc');
        if ($tk && !is_wp_error($tk)) {
            wp_set_object_terms($dn_id, array((int) $tk->term_id), 'khu_vuc', false);
        }
        if ($payload['khu_vuc_label'] !== '') {
            update_post_meta($dn_id, '_khu_vuc', $payload['khu_vuc_label']);
        }
    }

    update_post_meta($dn_id, '_doanh_nghiep_mo_ta_ngan', $payload['mo_ta_ngan']);

    $slider_ids = $payload['slider_ids'];
    if (!empty($slider_ids)) {
        update_post_meta($dn_id, '_noi_dung_slider_images', implode(',', $slider_ids));
        update_post_meta($dn_id, '_noi_dung_slider_title', 'Hình ảnh từ hồ sơ đăng ký');
    }
    $thumb = (int) $payload['thumb'];
    if ($thumb) {
        set_post_thumbnail($dn_id, $thumb);
    }

    $gallery_public = $payload['gallery_public'];
    if (!empty($gallery_public)) {
        update_post_meta($dn_id, '_gallery_images', implode(',', $gallery_public));
        if (!empty($payload['hinh_anh_phu'])) {
            update_post_meta($dn_id, '_hinh_anh_phu', (string) (int) $payload['hinh_anh_phu']);
        }
    }

    update_post_meta($dn_id, '_structured_content', $payload['structured']);

    update_post_meta($app_id, '_dn_approved', '1');
    update_post_meta($app_id, '_dn_linked_doanh_nghiep_id', $dn_id);

    return $dn_id;
}

/**
 * Khóa xem trước công khai (tạo nếu chưa có).
 */
function dnttvn_dn_reg_ensure_preview_key($app_id) {
    $app_id = (int) $app_id;
    if ($app_id <= 0) {
        return '';
    }
    $k = get_post_meta($app_id, '_dn_reg_preview_key', true);
    if (!is_string($k) || strlen($k) < 16) {
        $k = wp_generate_password(48, false, false);
        update_post_meta($app_id, '_dn_reg_preview_key', $k);
    }
    return $k;
}

/**
 * URL trang xem trước (thẻ + chi tiết) trước khi duyệt.
 */
function dnttvn_dn_reg_get_preview_url($app_id) {
    $app_id = (int) $app_id;
    if ($app_id <= 0) {
        return '';
    }
    $page = get_page_by_path('xem-truoc-dang-ky-doanh-nghiep');
    if (!$page || $page->post_status !== 'publish') {
        return '';
    }
    $key = dnttvn_dn_reg_ensure_preview_key($app_id);
    return add_query_arg(
        array(
            'dn_preview_id'  => $app_id,
            'dn_preview_key' => $key,
        ),
        get_permalink($page)
    );
}

/**
 * ID hồ sơ đăng ký được phép xem trước (khóa URL hoặc quản trị).
 */
function dnttvn_dn_reg_resolve_preview_app_id() {
    $id = isset($_GET['dn_preview_id']) ? absint($_GET['dn_preview_id']) : 0;
    if ($id <= 0 || get_post_type($id) !== 'dang_ky_doanh_nghiep') {
        return 0;
    }
    if (current_user_can('edit_posts')) {
        return $id;
    }
    $key     = isset($_GET['dn_preview_key']) ? sanitize_text_field(wp_unslash($_GET['dn_preview_key'])) : '';
    $stored  = get_post_meta($id, '_dn_reg_preview_key', true);
    if (!is_string($stored) || $stored === '' || $key === '' || !hash_equals($stored, $key)) {
        return 0;
    }
    return $id;
}

function dnttvn_dn_reg_admin_meta_box() {
    add_meta_box(
        'dang_ky_dn_data',
        'Nội dung đăng ký & duyệt',
        'dnttvn_dn_reg_admin_meta_box_cb',
        'dang_ky_doanh_nghiep',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dnttvn_dn_reg_admin_meta_box');

function dnttvn_dn_reg_admin_meta_box_cb($post) {
    if (isset($_GET['dnttvn_dn_reg_err']) && $_GET['dnttvn_dn_reg_err'] === 'no_consent') {
        echo '<div class="notice notice-error"><p>Không thể duyệt: hồ sơ chưa xác nhận <strong>Đồng ý</strong> ở câu 44.</p></div>';
    }
    if (isset($_GET['dnttvn_dn_reg_err']) && $_GET['dnttvn_dn_reg_err'] === 'no_delete_cap') {
        echo '<div class="notice notice-error"><p>Không đủ quyền xóa bài Doanh nghiệp. Cần quyền xóa bài viết.</p></div>';
    }
    if (isset($_GET['dnttvn_dn_reg_msg']) && $_GET['dnttvn_dn_reg_msg'] === 'unapproved') {
        echo '<div class="notice notice-success"><p>Đã hủy duyệt và xóa bài Doanh nghiệp liên kết (nếu có).</p></div>';
    }
    $linked = (int) get_post_meta($post->ID, '_dn_linked_doanh_nghiep_id', true);
    $approved = get_post_meta($post->ID, '_dn_approved', true) === '1';
    echo '<p><strong>Trạng thái:</strong> ';
    echo $approved ? '<span style="color:green;">Đã duyệt</span>' : '<span>Chưa duyệt</span>';
    echo '</p>';
    if ($linked) {
        $url = home_url('/trang-doanh-nghiep-chi-tiet/?post_id=' . $linked);
        echo '<p><a href="' . esc_url($url) . '" target="_blank" rel="noopener">Xem Doanh nghiệp đã tạo (ID ' . (int) $linked . ')</a> — ';
        echo '<a href="' . esc_url(get_edit_post_link($linked)) . '">Sửa trong admin</a></p>';
    }
    $mo_ta_reg = get_post_meta($post->ID, '_dn_reg_mo_ta_ngan', true);
    $mo_ta_reg = is_string($mo_ta_reg) ? trim($mo_ta_reg) : '';
    if ($mo_ta_reg !== '') {
        echo '<p><strong>Mô tả ngắn (thẻ DN):</strong><br>' . nl2br(esc_html($mo_ta_reg)) . '</p>';
    }
    $feat_id = (int) get_post_meta($post->ID, '_dn_reg_featured_image_id', true);
    if ($feat_id) {
        $fu = wp_get_attachment_image_url($feat_id, 'medium');
        if ($fu) {
            echo '<p><strong>Ảnh chính (đại diện thẻ):</strong><br><img src="' . esc_url($fu) . '" alt="" style="max-width:240px;height:auto;border-radius:6px;border:1px solid #ddd;"></p>';
        }
    }
    if (!$approved) {
        $url = wp_nonce_url(
            admin_url('admin-post.php?action=dnttvn_approve_dn_reg&post_id=' . $post->ID),
            'dnttvn_approve_dn_reg_' . $post->ID
        );
        echo '<p><a class="button button-primary" href="' . esc_url($url) . '">Duyệt và tạo Doanh nghiệp</a></p>';
    } elseif ($approved && $linked) {
        $undo = wp_nonce_url(
            admin_url('admin-post.php?action=dnttvn_unapprove_dn_reg&post_id=' . $post->ID),
            'dnttvn_unapprove_dn_reg_' . $post->ID
        );
        echo '<p><a class="button button-secondary" href="' . esc_url($undo) . '" onclick="return confirm(\'Hủy duyệt và xóa vĩnh viễn bài Doanh nghiệp đã tạo (ID ' . (int) $linked . ')? Hành động không hoàn tác.\');">Hủy duyệt và xóa bài Doanh nghiệp</a></p>';
    }
    $pv = dnttvn_dn_reg_get_preview_url($post->ID);
    if ($pv !== '') {
        echo '<p><a class="button" href="' . esc_url($pv) . '" target="_blank" rel="noopener">Xem trước giao diện (thẻ + chi tiết)</a> — cùng dữ liệu sẽ hiển thị sau khi duyệt.</p>';
    }
    echo '<hr><table class="widefat striped"><tbody>';
    foreach (dnttvn_dn_reg_meta_keys() as $k) {
        $v = get_post_meta($post->ID, '_dnq_' . $k, true);
        if ($v === '' || $v === null) {
            continue;
        }
        echo '<tr><th style="width:220px;">Câu ' . (int) $k . '</th><td>' . nl2br(esc_html(is_string($v) ? $v : print_r($v, true))) . '</td></tr>';
    }
    $v18 = dnttvn_dn_reg_get_kenh_ban_display($post->ID);
    if ($v18 !== '') {
        echo '<tr><th>Câu 18 (Kênh bán)</th><td>' . esc_html($v18) . '</td></tr>';
    }
    $cg_ids = get_post_meta($post->ID, '_dn_reg_card_gallery', true);
    if (is_array($cg_ids) && !empty($cg_ids)) {
        echo '<tr><th>Ảnh phụ thẻ DN</th><td><div style="display:flex;flex-wrap:wrap;gap:8px;">';
        foreach ($cg_ids as $aid) {
            $u = wp_get_attachment_image_url((int) $aid, 'thumbnail');
            if ($u) {
                echo '<img src="' . esc_url($u) . '" alt="" style="max-width:72px;height:auto;border-radius:4px;">';
            }
        }
        echo '</div></td></tr>';
    }
    $v41 = dnttvn_dn_reg_get_nhu_cau_display($post->ID);
    if ($v41 !== '') {
        echo '<tr><th>Câu 41 (Nhu cầu)</th><td>' . esc_html($v41) . '</td></tr>';
    }
    $v44 = get_post_meta($post->ID, '_dnq_44', true);
    if ($v44) {
        echo '<tr><th>Câu 44</th><td>' . esc_html($v44) . '</td></tr>';
    }
    echo '</tbody></table>';
    $sec = get_post_meta($post->ID, '_dn_reg_sec_images', true);
    if (is_array($sec)) {
        echo '<h4>Ảnh theo phần</h4><div style="display:flex;flex-wrap:wrap;gap:10px;">';
        foreach ($sec as $si => $ids) {
            if (!is_array($ids)) {
                if (is_string($ids) && strpos($ids, ',') !== false) {
                    $ids = array_map('absint', explode(',', $ids));
                } elseif (is_numeric($ids)) {
                    $ids = array( absint($ids) );
                } else {
                    continue;
                }
            }
            foreach ($ids as $aid) {
                $u = wp_get_attachment_image_url((int) $aid, 'thumbnail');
                if ($u) {
                    echo '<figure style="text-align:center;"><img src="' . esc_url($u) . '" alt="" style="max-width:80px;"><figcaption>Phần ' . (int) $si . '</figcaption></figure>';
                }
            }
        }
        echo '</div>';
    }
}

function dnttvn_dn_reg_handle_approve() {
    if (!current_user_can('edit_posts')) {
        wp_die('Không có quyền.');
    }
    $post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
    if (!$post_id || get_post_type($post_id) !== 'dang_ky_doanh_nghiep') {
        wp_safe_redirect(admin_url('edit.php?post_type=dang_ky_doanh_nghiep'));
        exit;
    }
    check_admin_referer('dnttvn_approve_dn_reg_' . $post_id);
    if (get_post_meta($post_id, '_dn_approved', true) === '1' && get_post_meta($post_id, '_dn_linked_doanh_nghiep_id', true)) {
        wp_safe_redirect(get_edit_post_link($post_id, 'raw'));
        exit;
    }
    if (get_post_meta($post_id, '_dnq_44', true) !== 'dong_y') {
        wp_safe_redirect(
            add_query_arg('dnttvn_dn_reg_err', 'no_consent', get_edit_post_link($post_id, 'raw'))
        );
        exit;
    }
    dnttvn_dn_reg_create_doanh_nghiep_from_application($post_id);
    wp_safe_redirect(get_edit_post_link($post_id, 'raw'));
    exit;
}
add_action('admin_post_dnttvn_approve_dn_reg', 'dnttvn_dn_reg_handle_approve');

/**
 * Hủy duyệt: xóa bài doanh_nghiep đã liên kết, gỡ cờ duyệt trên hồ sơ đăng ký.
 */
function dnttvn_dn_reg_handle_unapprove() {
    if (!current_user_can('edit_posts')) {
        wp_die('Không có quyền.');
    }
    $post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
    if (!$post_id || get_post_type($post_id) !== 'dang_ky_doanh_nghiep') {
        wp_safe_redirect(admin_url('edit.php?post_type=dang_ky_doanh_nghiep'));
        exit;
    }
    check_admin_referer('dnttvn_unapprove_dn_reg_' . $post_id);
    if (get_post_meta($post_id, '_dn_approved', true) !== '1') {
        wp_safe_redirect(get_edit_post_link($post_id, 'raw'));
        exit;
    }
    $dn_id = (int) get_post_meta($post_id, '_dn_linked_doanh_nghiep_id', true);
    if ($dn_id && get_post_type($dn_id) === 'doanh_nghiep') {
        if (!current_user_can('delete_post', $dn_id)) {
            wp_safe_redirect(
                add_query_arg('dnttvn_dn_reg_err', 'no_delete_cap', get_edit_post_link($post_id, 'raw'))
            );
            exit;
        }
        wp_delete_post($dn_id, true);
    }
    delete_post_meta($post_id, '_dn_approved');
    delete_post_meta($post_id, '_dn_linked_doanh_nghiep_id');
    wp_safe_redirect(
        add_query_arg('dnttvn_dn_reg_msg', 'unapproved', get_edit_post_link($post_id, 'raw'))
    );
    exit;
}
add_action('admin_post_dnttvn_unapprove_dn_reg', 'dnttvn_dn_reg_handle_unapprove');

function dnttvn_dn_reg_list_columns($cols) {
    $cols['dn_approved'] = 'Duyệt';
    return $cols;
}
function dnttvn_dn_reg_list_column($col, $post_id) {
    if ($col !== 'dn_approved') {
        return;
    }
    echo get_post_meta($post_id, '_dn_approved', true) === '1' ? 'Đã duyệt' : '—';
}
add_filter('manage_dang_ky_doanh_nghiep_posts_columns', 'dnttvn_dn_reg_list_columns');
add_action('manage_dang_ky_doanh_nghiep_posts_custom_column', 'dnttvn_dn_reg_list_column', 10, 2);

/* ============================================================
 * ADMIN EDIT FORM — giao diện chỉnh sửa y như trang đăng ký
 * ============================================================ */

/**
 * Thêm meta box chứa form chỉnh sửa đăng ký (giống frontend).
 */
function dnttvn_dn_reg_admin_edit_meta_box() {
    add_meta_box(
        'dang_ky_dn_edit_form',
        'Chỉnh sửa nội dung đăng ký',
        'dnttvn_dn_reg_admin_edit_meta_box_cb',
        'dang_ky_doanh_nghiep',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'dnttvn_dn_reg_admin_edit_meta_box');

/**
 * Render form chỉnh sửa bên trong meta box — giống hệt trang đăng ký.
 */
function dnttvn_dn_reg_admin_edit_meta_box_cb($post) {
    wp_nonce_field('dnttvn_dn_reg_admin_edit_' . $post->ID, 'dnttvn_dn_reg_admin_edit_nonce');

    $pid = $post->ID;

    // Helpers
    $m = function($k) use ($pid) {
        return (string) get_post_meta($pid, '_dnq_' . $k, true);
    };
    $mo_ta_ngan = (string) get_post_meta($pid, '_dn_reg_mo_ta_ngan', true);

    // Taxonomy terms
    $dn_form_nganh_terms = get_terms(array('taxonomy' => 'nganh_hang', 'hide_empty' => false));
    if (is_wp_error($dn_form_nganh_terms)) { $dn_form_nganh_terms = array(); }
    $dn_form_khu_terms = get_terms(array('taxonomy' => 'khu_vuc', 'hide_empty' => false));
    if (is_wp_error($dn_form_khu_terms)) { $dn_form_khu_terms = array(); }

    $dn_reg_cb41 = array(
        'thuong_hieu'   => 'Thương hiệu',
        'chien_luoc_kd' => 'Chiến lược kinh doanh',
        'chien_luoc_sp' => 'Chiến lược sản phẩm',
        'van_hanh'      => 'Hệ thống vận hành',
        'marketing'     => 'Marketing',
        'kenh_ban'      => 'Kênh bán hàng',
        'chuyen_doi_so' => 'Chuyển đổi số',
        'dao_tao'       => 'Đào tạo nhân sự',
        'hsnl2'         => 'Hồ sơ năng lực',
        'khac'          => 'Khác',
    );

    $dn_w_field = defined('DNTTVN_DN_REG_MAX_WORDS_FIELD') ? (int) DNTTVN_DN_REG_MAX_WORDS_FIELD : 350;
    $dn_w_mota  = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;
    $dn_reg_mb  = defined('DNTTVN_DN_REG_MAX_BYTES') ? round(DNTTVN_DN_REG_MAX_BYTES / 1048576, 1) : 2;
    $dn_reg_max_img  = defined('DNTTVN_DN_REG_MAX_SEC_IMAGES') ? (int) DNTTVN_DN_REG_MAX_SEC_IMAGES : 5;
    $dn_reg_max_side = defined('DNTTVN_DN_REG_MAX_SIDE') ? (int) DNTTVN_DN_REG_MAX_SIDE : 2000;
    $dn_reg_card_max = defined('DNTTVN_DN_REG_MAX_CARD_PHU') ? (int) DNTTVN_DN_REG_MAX_CARD_PHU : 5;

    // Ảnh hiện tại
    $feat_id  = (int) get_post_meta($pid, '_dn_reg_featured_image_id', true);
    $card_ids = get_post_meta($pid, '_dn_reg_card_gallery', true);
    if (!is_array($card_ids)) { $card_ids = array(); }
    $sec_imgs = dnttvn_dn_reg_normalize_section_images_meta(get_post_meta($pid, '_dn_reg_sec_images', true));

    // Upload section helper — hiển thị ảnh hiện tại + input upload mới
    $render_upload_section = function($section, $heading) use ($pid, $dn_reg_max_img, $dn_reg_mb, $dn_reg_max_side, $sec_imgs) {
        $field    = 'dn_sec' . (int) $section;
        $cur_ids  = isset($sec_imgs[(int) $section]) ? $sec_imgs[(int) $section] : array();
        ?>
        <div class="dn-reg-upload dn-reg-upload--sec<?php echo (int) $section; ?>" style="margin-bottom:24px;">
            <h4 class="dn-reg-upload__title"><?php echo esc_html($heading); ?></h4>
            <?php if (!empty($cur_ids)) : ?>
                <p style="font-size:13px;color:#555;margin:0 0 8px;">Ảnh hiện tại (giữ nguyên nếu không chọn mới):</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                    <?php foreach ($cur_ids as $aid) :
                        $thumb_u = wp_get_attachment_image_url((int) $aid, 'thumbnail');
                        if ($thumb_u) : ?>
                            <div style="position:relative;display:inline-block;">
                                <img src="<?php echo esc_url($thumb_u); ?>" alt="" style="max-width:80px;border-radius:6px;border:1px solid #ddd;">
                                <label style="display:block;font-size:11px;color:#b91c1c;text-align:center;cursor:pointer;">
                                    <input type="checkbox" name="dn_del_sec<?php echo (int) $section; ?>[]" value="<?php echo (int) $aid; ?>" style="margin-right:3px;">Xóa
                                </label>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
            <?php endif; ?>
            <p class="dn-reg-upload__hint">
                Tải tối đa <strong><?php echo (int) $dn_reg_max_img; ?></strong> hình ảnh mới (PNG, JPG…),
                mỗi file tối đa <strong><?php echo esc_html((string) $dn_reg_mb); ?> MB</strong>,
                cạnh dài tối đa <strong><?php echo (int) $dn_reg_max_side; ?> px</strong>.
            </p>
            <input type="file" name="<?php echo esc_attr($field); ?>[]" id="<?php echo esc_attr($field); ?>_admin" accept="image/*" multiple>
        </div>
        <?php
    };

    ?>
    <div class="dn-reg-form-wrap" style="max-width:100%;padding:0;">
        <div class="dn-reg-intro" style="margin-bottom:20px;padding:14px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
            <p style="margin:0;font-size:13px;color:#64748b;">Các thay đổi được lưu khi bấm <strong>Cập nhật</strong> trang. Ảnh mới sẽ bổ sung vào ảnh cũ; bỏ tích vào ảnh cũ để xóa chúng.</p>
        </div>

        <form id="dn-reg-admin-edit-form" method="post" enctype="multipart/form-data">
            <?php $dn_q = 0; ?>

            <!-- PHẦN 1: Thông tin chung -->
            <fieldset class="dn-reg-fieldset">
                <legend>Thông tin chung</legend>

                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_1"><?php echo (int) ++$dn_q; ?>. Tên doanh nghiệp <span style="color:#b91c1c;">*</span></label>
                    <input type="text" name="dnq_1" id="adm_dnq_1" required class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(1)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_2"><?php echo (int) ++$dn_q; ?>. Tên thương hiệu (nếu có)</label>
                    <input type="text" name="dnq_2" id="adm_dnq_2" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(2)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dn_reg_mo_ta_ngan"><?php echo (int) ++$dn_q; ?>. Mô tả ngắn</label>
                    <p class="dn-reg-field__hint" style="margin:0 0 8px;">Tối đa <strong><?php echo (int) $dn_w_mota; ?> từ</strong>.</p>
                    <textarea name="dn_reg_mo_ta_ngan" id="adm_dn_reg_mo_ta_ngan" rows="3" class="widefat dn-reg-word-cap" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;" data-dn-max-words="<?php echo (int) $dn_w_mota; ?>"><?php echo esc_textarea($mo_ta_ngan); ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_4"><?php echo (int) ++$dn_q; ?>. Người đại diện pháp luật</label>
                    <input type="text" name="dnq_4" id="adm_dnq_4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(4)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_5"><?php echo (int) ++$dn_q; ?>. Số điện thoại</label>
                    <input type="text" name="dnq_5" id="adm_dnq_5" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(5)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_6"><?php echo (int) ++$dn_q; ?>. Email</label>
                    <input type="email" name="dnq_6" id="adm_dnq_6" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(6)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_7"><?php echo (int) ++$dn_q; ?>. Website / Facebook / Kênh liên hệ chính</label>
                    <input type="text" name="dnq_7" id="adm_dnq_7" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(7)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_8"><?php echo (int) ++$dn_q; ?>. Địa chỉ doanh nghiệp</label>
                    <textarea name="dnq_8" id="adm_dnq_8" rows="3" class="widefat dn-reg-word-cap" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;" data-dn-max-words="<?php echo (int) $dn_w_field; ?>"><?php echo esc_textarea($m(8)); ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_9"><?php echo (int) ++$dn_q; ?>. Năm thành lập</label>
                    <input type="text" name="dnq_9" id="adm_dnq_9" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(9)); ?>" style="width:100%;max-width:200px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_10"><?php echo (int) ++$dn_q; ?>. Lĩnh vực hoạt động chính</label>
                    <textarea name="dnq_10" id="adm_dnq_10" rows="3" class="widefat dn-reg-word-cap" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;" data-dn-max-words="<?php echo (int) $dn_w_field; ?>"><?php echo esc_textarea($m(10)); ?></textarea>
                </div>

                <div class="dn-reg-grid dn-reg-grid--2col" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div class="dn-reg-field form-group">
                        <label for="adm_dnq_11"><?php echo (int) ++$dn_q; ?>. Ngành hàng</label>
                        <p class="dn-reg-field__hint">Chọn mục trong danh mục hoặc chọn "Khác" và nhập tay.</p>
                        <?php
                        $cur_nganh = $m(11);
                        $nganh_slugs = array_map(function($t) { return $t->slug; }, $dn_form_nganh_terms);
                        $nganh_is_custom = ($cur_nganh !== '' && !in_array($cur_nganh, $nganh_slugs, true));
                        $nganh_select_val = $nganh_is_custom ? 'khac' : $cur_nganh;
                        ?>
                        <select name="dnq_11" id="adm_dnq_11" class="dn-reg-control dn-reg-select">
                            <option value="">— Chưa chọn —</option>
                            <?php foreach ($dn_form_nganh_terms as $dn_ft) : ?>
                                <option value="<?php echo esc_attr($dn_ft->slug); ?>" <?php selected($nganh_select_val, $dn_ft->slug); ?>><?php echo esc_html($dn_ft->name); ?></option>
                            <?php endforeach; ?>
                            <option value="khac" <?php selected($nganh_select_val, 'khac'); ?>>Khác (nhập tay)</option>
                        </select>
                        <input type="text" name="dnq_11_khac" id="adm_dnq_11_khac" class="dn-reg-control dn-reg-control--khac dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" placeholder="Nhập ngành / lĩnh vực…" autocomplete="off" value="<?php echo $nganh_is_custom ? esc_attr($cur_nganh) : ''; ?>" style="<?php echo $nganh_is_custom ? '' : 'display:none;'; ?>">
                    </div>
                    <div class="dn-reg-field form-group">
                        <label for="adm_dnq_19"><?php echo (int) ++$dn_q; ?>. Khu vực hoạt động chính</label>
                        <p class="dn-reg-field__hint">Theo tỉnh, thành phố hoặc vùng có trong danh mục.</p>
                        <?php
                        $cur_khu = $m(19);
                        $khu_slugs = array_map(function($t) { return $t->slug; }, $dn_form_khu_terms);
                        $khu_is_custom = ($cur_khu !== '' && !in_array($cur_khu, $khu_slugs, true));
                        $khu_select_val = $khu_is_custom ? 'khac' : $cur_khu;
                        ?>
                        <select name="dnq_19" id="adm_dnq_19" class="dn-reg-control dn-reg-select">
                            <option value="">— Chưa chọn —</option>
                            <?php foreach ($dn_form_khu_terms as $dn_ft) : ?>
                                <option value="<?php echo esc_attr($dn_ft->slug); ?>" <?php selected($khu_select_val, $dn_ft->slug); ?>><?php echo esc_html($dn_ft->name); ?></option>
                            <?php endforeach; ?>
                            <option value="khac" <?php selected($khu_select_val, 'khac'); ?>>Khác (nhập tay)</option>
                        </select>
                        <input type="text" name="dnq_19_khac" id="adm_dnq_19_khac" class="dn-reg-control dn-reg-control--khac dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" placeholder="Nhập khu vực…" autocomplete="off" value="<?php echo $khu_is_custom ? esc_attr($cur_khu) : ''; ?>" style="<?php echo $khu_is_custom ? '' : 'display:none;'; ?>">
                    </div>
                </div>

                <!-- Ảnh chính -->
                <div class="dn-reg-upload dn-reg-upload--anh-chinh dn-reg-field" style="margin-top:20px;margin-bottom:20px;">
                    <h4 class="dn-reg-upload__title">Ảnh chính — đại diện thẻ doanh nghiệp</h4>
                    <?php if ($feat_id) :
                        $feat_u = wp_get_attachment_image_url($feat_id, 'medium');
                        if ($feat_u) : ?>
                            <div style="margin-bottom:10px;">
                                <p style="font-size:13px;color:#555;margin:0 0 6px;">Ảnh hiện tại:</p>
                                <img src="<?php echo esc_url($feat_u); ?>" alt="" style="max-width:200px;border-radius:8px;border:1px solid #ddd;">
                                <label style="display:block;margin-top:6px;font-size:13px;color:#b91c1c;">
                                    <input type="checkbox" name="dn_del_anh_chinh" value="1"> Xóa ảnh chính này
                                </label>
                            </div>
                        <?php endif;
                    endif; ?>
                    <p class="dn-reg-upload__hint">
                        Một ảnh ngang hoặc vuông, rõ nét. Định dạng PNG, JPG…,
                        tối đa <strong><?php echo esc_html((string) $dn_reg_mb); ?> MB</strong>,
                        cạnh dài tối đa <strong><?php echo (int) $dn_reg_max_side; ?> px</strong>.
                        Nếu có ảnh cũ và chọn ảnh mới, ảnh mới sẽ thay thế ảnh cũ.
                    </p>
                    <input type="file" name="dn_anh_chinh" id="adm_dn_anh_chinh" class="dn-reg-file" accept="image/*">
                </div>

                <!-- Ảnh phụ thẻ -->
                <div class="dn-reg-upload dn-reg-upload--card-gallery dn-reg-field" style="margin-bottom:20px;">
                    <h4 class="dn-reg-upload__title">Ảnh phụ trên thẻ doanh nghiệp</h4>
                    <?php if (!empty($card_ids)) : ?>
                        <div style="margin-bottom:10px;">
                            <p style="font-size:13px;color:#555;margin:0 0 6px;">Ảnh hiện tại (bỏ chọn để xóa):</p>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                <?php foreach ($card_ids as $cid) :
                                    $cu = wp_get_attachment_image_url((int) $cid, 'thumbnail');
                                    if ($cu) : ?>
                                        <div style="text-align:center;">
                                            <img src="<?php echo esc_url($cu); ?>" alt="" style="max-width:70px;border-radius:6px;border:1px solid #ddd;display:block;">
                                            <label style="font-size:11px;color:#b91c1c;cursor:pointer;">
                                                <input type="checkbox" name="dn_del_card[]" value="<?php echo (int) $cid; ?>" style="margin-right:2px;">Xóa
                                            </label>
                                        </div>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <p class="dn-reg-upload__hint">
                        Tải tối đa <strong><?php echo (int) $dn_reg_card_max; ?></strong> ảnh phụ mới.
                    </p>
                    <input type="file" name="dn_card_gallery[]" id="adm_dn_card_gallery" class="dn-reg-file" accept="image/*" multiple>
                </div>

                <?php $render_upload_section(1, 'Hình ảnh minh họa (Thông tin chung)'); ?>
            </fieldset>

            <!-- PHẦN 2: Sứ mệnh & tầm nhìn -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 2 – Sứ mệnh &amp; tầm nhìn</legend>
                <?php
                $p2 = array(12 => 'Sứ mệnh của doanh nghiệp là gì?', 13 => 'Tầm nhìn của doanh nghiệp trong 3–5 năm tới là gì?', 14 => 'Giá trị cốt lõi doanh nghiệp theo đuổi là gì?', 15 => 'Doanh nghiệp muốn khách hàng nhớ đến mình như thế nào?', 16 => 'Điểm khác biệt lớn nhất của doanh nghiệp là gì?');
                foreach ($p2 as $num => $lab) : ?>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="adm_dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_q; ?>. <?php echo esc_html($lab); ?></label>
                        <textarea name="dnq_<?php echo (int) $num; ?>" id="adm_dnq_<?php echo (int) $num; ?>" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m($num)); ?></textarea>
                    </div>
                <?php endforeach; ?>
                <?php $render_upload_section(2, 'Hình ảnh minh họa (Phần 2)'); ?>
            </fieldset>

            <!-- PHẦN 3: Mô hình -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 3 – Mô hình</legend>
                <?php
                $p3 = array(17 => 'Mô hình kinh doanh', 47 => 'Mô hình quản trị', 48 => 'Mô hình doanh nghiệp', 49 => 'Mô hình bán hàng');
                foreach ($p3 as $num => $lab) : ?>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="adm_dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_q; ?>. <?php echo esc_html($lab); ?></label>
                        <textarea name="dnq_<?php echo (int) $num; ?>" id="adm_dnq_<?php echo (int) $num; ?>" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m($num)); ?></textarea>
                    </div>
                <?php endforeach; ?>
                <?php $render_upload_section(3, 'Hình ảnh minh họa (Phần 3)'); ?>
            </fieldset>

            <!-- PHẦN 4: Nguồn lực -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 4 – Nguồn lực doanh nghiệp</legend>
                <?php
                $p4 = array(25 => 'Cơ sở vật chất hiện có (văn phòng, cửa hàng, kho, xưởng…)', 27 => 'Nhân sự: số lượng và phân bổ bộ phận', 28 => 'Công nghệ / hệ thống / phần mềm đang dùng', 29 => 'Hồ sơ / pháp lý hiện có', 30 => 'Năng lực nội bộ mạnh nhất');
                foreach ($p4 as $num => $lab) : ?>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="adm_dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_q; ?>. <?php echo esc_html($lab); ?></label>
                        <textarea name="dnq_<?php echo (int) $num; ?>" id="adm_dnq_<?php echo (int) $num; ?>" rows="3" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m($num)); ?></textarea>
                    </div>
                <?php endforeach; ?>
                <?php $render_upload_section(4, 'Hình ảnh minh họa (Phần 4)'); ?>
            </fieldset>

            <!-- PHẦN 5: Vận hành -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 5 – Vận hành doanh nghiệp</legend>
                <?php
                $p5 = array(32 => 'Triết lý hoặc nguyên tắc vận hành', 33 => 'Quy trình bán hàng hoặc cung cấp dịch vụ hiện tại', 34 => 'Chăm sóc khách hàng sau bán');
                foreach ($p5 as $num => $lab) : ?>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="adm_dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_q; ?>. <?php echo esc_html($lab); ?></label>
                        <textarea name="dnq_<?php echo (int) $num; ?>" id="adm_dnq_<?php echo (int) $num; ?>" rows="3" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m($num)); ?></textarea>
                    </div>
                <?php endforeach; ?>
                <?php $render_upload_section(5, 'Hình ảnh minh họa (Phần 5)'); ?>
            </fieldset>

            <!-- PHẦN 6: Khách hàng tiêu biểu -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 6 – Khách hàng tiêu biểu</legend>
                <?php
                $p6 = array(36 => 'Khách hàng thường đánh giá cao ở điểm nào?', 37 => 'Phản hồi / góp ý thường gặp từ khách hàng', 38 => 'Một số khách hàng / đối tác tiêu biểu', 39 => 'Một số dự án / case study đã triển khai', 40 => 'Tài liệu có thể gửi kèm');
                foreach ($p6 as $num => $lab) : ?>
                    <div class="form-group" style="margin-bottom:14px;">
                        <label for="adm_dnq_<?php echo (int) $num; ?>"><?php echo (int) ++$dn_q; ?>. <?php echo esc_html($lab); ?></label>
                        <textarea name="dnq_<?php echo (int) $num; ?>" id="adm_dnq_<?php echo (int) $num; ?>" rows="3" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m($num)); ?></textarea>
                    </div>
                <?php endforeach; ?>
                <?php $render_upload_section(6, 'Hình ảnh minh họa (Phần 6)'); ?>
            </fieldset>

            <!-- PHẦN 7: Nhu cầu hiện tại -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 7 – Nhu cầu hiện tại</legend>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_41" style="display:block;font-weight:600;margin-bottom:8px;"><?php echo (int) ++$dn_q; ?>. Đang cần cải thiện hoặc xây dựng</label>
                    <?php
                    $cur41 = (string) get_post_meta($pid, '_dnq_41', true);
                    $cur41k = (string) get_post_meta($pid, '_dnq_41_khac', true);
                    ?>
                    <select name="dnq_41" id="adm_dnq_41" style="max-width:360px;width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                        <option value="">— Chọn —</option>
                        <?php foreach ($dn_reg_cb41 as $val => $lab) : ?>
                            <option value="<?php echo esc_attr($val); ?>" <?php selected($cur41, $val); ?>><?php echo esc_html($lab); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="dnq_41_khac" id="adm_dnq_41_khac" autocomplete="off" placeholder="Nếu chọn Khác, mô tả cụ thể…" class="dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($cur41k); ?>" style="<?php echo ($cur41 === 'khac') ? 'display:block;' : 'display:none;'; ?>margin-top:10px;max-width:100%;width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_42"><?php echo (int) ++$dn_q; ?>. Ba vấn đề ưu tiên muốn giải quyết ngay</label>
                    <textarea name="dnq_42" id="adm_dnq_42" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m(42)); ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_43"><?php echo (int) ++$dn_q; ?>. Kỳ vọng khi hợp tác</label>
                    <textarea name="dnq_43" id="adm_dnq_43" rows="4" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;"><?php echo esc_textarea($m(43)); ?></textarea>
                </div>
                <?php $render_upload_section(7, 'Hình ảnh minh họa (Phần 7)'); ?>
            </fieldset>

            <!-- PHẦN 8: Xác nhận thông tin -->
            <fieldset class="dn-reg-fieldset">
                <legend>PHẦN 8 – Xác nhận thông tin</legend>
                <?php
                $cur44 = (string) get_post_meta($pid, '_dnq_44', true);
                ?>
                <div class="form-group" style="margin-bottom:14px;">
                    <span class="label-like" style="display:block;font-weight:600;margin-bottom:8px;"><?php echo (int) ++$dn_q; ?>. Xác nhận thông tin đúng và có thể dùng cho tư vấn / triển khai</span>
                    <label style="display:block;margin:6px 0;"><input type="radio" name="dnq_44" value="dong_y" <?php checked($cur44, 'dong_y'); ?>> Đồng ý</label>
                    <label style="display:block;margin:6px 0;"><input type="radio" name="dnq_44" value="khong" <?php checked($cur44, 'khong'); ?>> Không đồng ý</label>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_45"><?php echo (int) ++$dn_q; ?>. Họ và tên người xác nhận</label>
                    <input type="text" name="dnq_45" id="adm_dnq_45" class="widefat dn-reg-word-cap" data-dn-max-words="<?php echo (int) $dn_w_field; ?>" value="<?php echo esc_attr($m(45)); ?>" style="width:100%;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="adm_dnq_46"><?php echo (int) ++$dn_q; ?>. Ngày điền form</label>
                    <input type="date" name="dnq_46" id="adm_dnq_46" class="widefat" value="<?php echo esc_attr($m(46)); ?>" style="max-width:240px;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;">
                </div>
                <?php $render_upload_section(8, 'Hình ảnh minh họa (Phần 8)'); ?>
            </fieldset>
        </form>
    </div>

    <script>
    (function(){
        function wireKhac(selId, inputId){
            var s=document.getElementById(selId),k=document.getElementById(inputId);
            if(!s||!k)return;
            function t(){var show=s.value==='khac';k.style.display=show?'block':'none';if(!show)k.value='';}
            s.addEventListener('change',t);
        }
        wireKhac('adm_dnq_11','adm_dnq_11_khac');
        wireKhac('adm_dnq_19','adm_dnq_19_khac');
        wireKhac('adm_dnq_41','adm_dnq_41_khac');
    })();
    </script>
    <?php
}

/**
 * Lưu dữ liệu form chỉnh sửa admin khi save_post.
 */
function dnttvn_dn_reg_save_admin_edit($post_id, $post) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if ($post->post_type !== 'dang_ky_doanh_nghiep') { return; }
    if (!isset($_POST['dnttvn_dn_reg_admin_edit_nonce'])) { return; }
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dnttvn_dn_reg_admin_edit_nonce'])), 'dnttvn_dn_reg_admin_edit_' . $post_id)) { return; }
    if (!current_user_can('edit_post', $post_id)) { return; }

    // --- Lưu các trường văn bản ---
    $dn_reg_textarea_keys_word = array(8, 10, 12, 13, 14, 15, 16, 17, 25, 27, 28, 29, 30, 32, 33, 34, 36, 37, 38, 39, 40, 42, 43, 47, 48, 49);
    $max_w = defined('DNTTVN_DN_REG_MAX_WORDS_FIELD') ? (int) DNTTVN_DN_REG_MAX_WORDS_FIELD : 350;
    $max_w_mota = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;

    foreach (dnttvn_dn_reg_meta_keys() as $k) {
        $key = 'dnq_' . $k;
        if (!isset($_POST[$key])) { continue; }
        if (in_array($k, array(46), true)) {
            update_post_meta($post_id, '_' . $key, sanitize_text_field(wp_unslash($_POST[$key])));
        } elseif (in_array($k, $dn_reg_textarea_keys_word, true)) {
            $tv = sanitize_textarea_field(wp_unslash($_POST[$key]));
            update_post_meta($post_id, '_' . $key, dnttvn_dn_reg_trim_to_word_limit($tv, $max_w));
        } else {
            $sv = sanitize_text_field(wp_unslash($_POST[$key]));
            update_post_meta($post_id, '_' . $key, dnttvn_dn_reg_trim_to_word_limit($sv, $max_w));
        }
    }

    // Mô tả ngắn
    if (isset($_POST['dn_reg_mo_ta_ngan'])) {
        $mt = sanitize_textarea_field(wp_unslash($_POST['dn_reg_mo_ta_ngan']));
        update_post_meta($post_id, '_dn_reg_mo_ta_ngan', dnttvn_dn_reg_trim_to_word_limit($mt, $max_w_mota));
    }

    // Ngành hàng "khác"
    if (isset($_POST['dnq_11']) && sanitize_text_field(wp_unslash($_POST['dnq_11'])) === 'khac') {
        $k11 = isset($_POST['dnq_11_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_11_khac'])) : '';
        update_post_meta($post_id, '_dnq_11', dnttvn_dn_reg_trim_to_word_limit($k11, $max_w));
    }
    // Khu vực "khác"
    if (isset($_POST['dnq_19']) && sanitize_text_field(wp_unslash($_POST['dnq_19'])) === 'khac') {
        $k19 = isset($_POST['dnq_19_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_19_khac'])) : '';
        update_post_meta($post_id, '_dnq_19', dnttvn_dn_reg_trim_to_word_limit($k19, $max_w));
    }

    // Câu 44 (radio)
    if (isset($_POST['dnq_44'])) {
        $q44 = sanitize_key(wp_unslash($_POST['dnq_44']));
        if (in_array($q44, array('dong_y', 'khong'), true)) {
            update_post_meta($post_id, '_dnq_44', $q44);
        }
    }

    // Câu 41 (dropdown nhu cầu)
    if (isset($_POST['dnq_41'])) {
        $v41 = sanitize_key(wp_unslash($_POST['dnq_41']));
        if ($v41 !== '') { update_post_meta($post_id, '_dnq_41', $v41); }
        $k41 = isset($_POST['dnq_41_khac']) ? sanitize_text_field(wp_unslash($_POST['dnq_41_khac'])) : '';
        update_post_meta($post_id, '_dnq_41_khac', ($v41 === 'khac') ? dnttvn_dn_reg_trim_to_word_limit($k41, $max_w) : '');
    }

    // --- Xử lý ảnh ---
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Xóa ảnh chính nếu được yêu cầu
    if (!empty($_POST['dn_del_anh_chinh'])) {
        delete_post_meta($post_id, '_dn_reg_featured_image_id');
    }
    // Tải ảnh chính mới (nếu có)
    if (!empty($_FILES['dn_anh_chinh']['tmp_name'])) {
        $new_feats = dnttvn_dn_reg_handle_multi_image_upload($post_id, 'dn_anh_chinh', 1);
        if (!empty($new_feats[0])) {
            update_post_meta($post_id, '_dn_reg_featured_image_id', (int) $new_feats[0]);
        }
    }

    // Xóa ảnh phụ thẻ được chọn
    $del_card = isset($_POST['dn_del_card']) && is_array($_POST['dn_del_card'])
        ? array_map('absint', $_POST['dn_del_card']) : array();
    $cur_card = get_post_meta($post_id, '_dn_reg_card_gallery', true);
    if (!is_array($cur_card)) { $cur_card = array(); }
    if (!empty($del_card)) {
        $cur_card = array_values(array_filter($cur_card, function($id) use ($del_card) {
            return !in_array((int) $id, $del_card, true);
        }));
    }
    // Tải thêm ảnh phụ thẻ mới
    $max_card = defined('DNTTVN_DN_REG_MAX_CARD_PHU') ? (int) DNTTVN_DN_REG_MAX_CARD_PHU : 5;
    if (!empty($_FILES['dn_card_gallery']['tmp_name'][0])) {
        $slots = max(0, $max_card - count($cur_card));
        if ($slots > 0) {
            $new_cards = dnttvn_dn_reg_handle_multi_image_upload($post_id, 'dn_card_gallery', $slots);
            $cur_card = array_values(array_unique(array_merge($cur_card, $new_cards)));
        }
    }
    update_post_meta($post_id, '_dn_reg_card_gallery', array_slice($cur_card, 0, $max_card));

    // Ảnh từng phần (1-8)
    $cur_sec = dnttvn_dn_reg_normalize_section_images_meta(get_post_meta($post_id, '_dn_reg_sec_images', true));
    $max_sec = defined('DNTTVN_DN_REG_MAX_SEC_IMAGES') ? (int) DNTTVN_DN_REG_MAX_SEC_IMAGES : 5;
    for ($s = 1; $s <= 8; $s++) {
        // Xóa ảnh phần được chọn
        $del_sec = isset($_POST['dn_del_sec' . $s]) && is_array($_POST['dn_del_sec' . $s])
            ? array_map('absint', $_POST['dn_del_sec' . $s]) : array();
        $bucket = isset($cur_sec[$s]) && is_array($cur_sec[$s]) ? $cur_sec[$s] : array();
        if (!empty($del_sec)) {
            $bucket = array_values(array_filter($bucket, function($id) use ($del_sec) {
                return !in_array((int) $id, $del_sec, true);
            }));
        }
        // Tải ảnh phần mới
        $field_s = 'dn_sec' . $s;
        if (!empty($_FILES[$field_s]['tmp_name'][0])) {
            $slots_s = max(0, $max_sec - count($bucket));
            if ($slots_s > 0) {
                $new_s = dnttvn_dn_reg_handle_multi_image_upload($post_id, $field_s, $slots_s);
                $bucket = array_values(array_unique(array_merge($bucket, $new_s)));
            }
        }
        $cur_sec[$s] = array_slice($bucket, 0, $max_sec);
    }
    update_post_meta($post_id, '_dn_reg_sec_images', dnttvn_dn_reg_normalize_section_images_meta($cur_sec));
}
add_action('save_post', 'dnttvn_dn_reg_save_admin_edit', 10, 2);

/**
 * Enqueue CSS của trang đăng ký vào trang admin khi sửa dang_ky_doanh_nghiep.
 */
function dnttvn_dn_reg_admin_enqueue($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) { return; }
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'dang_ky_doanh_nghiep') { return; }

    wp_enqueue_style(
        'dnttvn-dn-reg-admin',
        get_template_directory_uri() . '/assets/style-gioi-thieu.css',
        array(),
        '1.0.13'
    );

    // Inline CSS để làm cho admin meta box trông giống frontend
    $inline = '
        #dang_ky_dn_edit_form .dn-reg-fieldset {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 20px 22px 16px;
            margin-bottom: 24px;
            background: #fff;
        }
        #dang_ky_dn_edit_form .dn-reg-fieldset legend {
            font-size: 15px;
            font-weight: 700;
            color: #1e3a5f;
            padding: 0 10px;
            background: #fff;
        }
        #dang_ky_dn_edit_form .dn-reg-grid--2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 14px;
        }
        #dang_ky_dn_edit_form label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1e3a5f;
        }
        #dang_ky_dn_edit_form .dn-reg-field__hint {
            font-size: 13px;
            color: #64748b;
            margin: 0 0 8px;
        }
        #dang_ky_dn_edit_form input[type="text"],
        #dang_ky_dn_edit_form input[type="email"],
        #dang_ky_dn_edit_form input[type="date"],
        #dang_ky_dn_edit_form textarea,
        #dang_ky_dn_edit_form select {
            font-family: inherit;
        }
        #dang_ky_dn_edit_form .dn-reg-upload__title {
            font-size: 14px;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0 0 8px;
        }
        #dang_ky_dn_edit_form .dn-reg-upload__hint {
            font-size: 13px;
            color: #475569;
            margin: 0 0 8px;
        }
        #dang_ky_dn_edit_form .dn-reg-control--khac {
            display: none;
            width: 100%;
            margin-top: 8px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
        }
        #dang_ky_dn_edit_form .dn-reg-select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        @media (max-width: 782px) {
            #dang_ky_dn_edit_form .dn-reg-grid--2col {
                grid-template-columns: 1fr;
            }
        }
    ';
    wp_add_inline_style('dnttvn-dn-reg-admin', $inline);
}
add_action('admin_enqueue_scripts', 'dnttvn_dn_reg_admin_enqueue');
