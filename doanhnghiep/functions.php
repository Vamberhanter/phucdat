<?php
/**
 * Functions and definitions for Cộng đồng Doanh nhân Trí tuệ Việt Nam Theme
 */

require_once get_template_directory() . '/includes/dang-ky-doanh-nghiep.php';

/**
 * Số ảnh phụ tối đa trên thẻ doanh nghiệp (meta _gallery_images), khớp đăng ký / admin.
 */
function dnttvn_doanh_nghiep_gallery_card_max() {
    if (defined('DNTTVN_DN_REG_MAX_CARD_PHU')) {
        return max(1, min(20, (int) DNTTVN_DN_REG_MAX_CARD_PHU));
    }
    return 5;
}

// Enqueue styles and scripts
function dnttvn_enqueue_styles() {
    wp_enqueue_style('dnttvn-main-style', get_template_directory_uri() . '/assets/style-gioi-thieu.css', array(), '1.0.17');
    wp_enqueue_script('dnttvn-main-script', get_template_directory_uri() . '/assets/script.js', array('jquery'), '1.0.4', true);
}
add_action('wp_enqueue_scripts', 'dnttvn_enqueue_styles');

// Register Custom Post Type: Tin tức
function dnttvn_register_tin_tuc_post_type() {
    $labels = array(
        'name'                  => 'Tin tức',
        'singular_name'         => 'Tin tức',
        'menu_name'             => 'Tin tức',
        'name_admin_bar'        => 'Tin tức',
        'archives'              => 'Danh sách Tin tức',
        'attributes'            => 'Thuộc tính Tin tức',
        'parent_item_colon'     => 'Tin tức cha:',
        'all_items'             => 'Tất cả Tin tức',
        'add_new_item'          => 'Thêm Tin tức mới',
        'add_new'               => 'Thêm mới',
        'new_item'              => 'Tin tức mới',
        'edit_item'             => 'Chỉnh sửa Tin tức',
        'update_item'           => 'Cập nhật Tin tức',
        'view_item'             => 'Xem Tin tức',
        'view_items'            => 'Xem Tin tức',
        'search_items'          => 'Tìm kiếm Tin tức',
        'not_found'             => 'Không tìm thấy',
        'not_found_in_trash'    => 'Không tìm thấy trong thùng rác',
        'featured_image'        => 'Hình ảnh đại diện',
        'set_featured_image'    => 'Đặt hình ảnh đại diện',
        'remove_featured_image' => 'Xóa hình ảnh đại diện',
        'use_featured_image'    => 'Sử dụng làm hình ảnh đại diện',
        'insert_into_item'      => 'Chèn vào Tin tức',
        'uploaded_to_this_item' => 'Tải lên Tin tức này',
        'items_list'            => 'Danh sách Tin tức',
        'items_list_navigation' => 'Điều hướng danh sách Tin tức',
        'filter_items_list'     => 'Lọc danh sách Tin tức',
    );
    $args = array(
        'label'                 => 'Tin tức',
        'description'           => 'Quản lý tin tức của Cộng đồng',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-megaphone',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'       => 'tin_tuc',
            'with_front' => false
        ),
    );
    register_post_type('tin_tuc', $args);
}
add_action('init', 'dnttvn_register_tin_tuc_post_type', 0);

// Add custom rewrite rules for tin_tuc to use ID instead of slug
function dnttvn_add_tin_tuc_rewrite_rules() {
    add_rewrite_rule(
        '^tin_tuc/([0-9]+)/?$',
        'index.php?post_type=tin_tuc&p=$matches[1]',
        'top'
    );
}
add_action('init', 'dnttvn_add_tin_tuc_rewrite_rules');

// Modify the permalink for tin_tuc posts to use ID
function dnttvn_tin_tuc_permalink($permalink, $post) {
    if ($post->post_type === 'tin_tuc') {
        return home_url('/tin_tuc/' . $post->ID . '/');
    }
    return $permalink;
}
add_filter('post_type_link', 'dnttvn_tin_tuc_permalink', 10, 2);

// Manual function to flush rewrite rules (call this if needed)
function dnttvn_manual_flush_rewrite_rules() {
    flush_rewrite_rules();
    update_option('dnttvn_theme_activated', 'yes');
}

// Flush rewrite rules immediately for testing
add_action('init', function() {
    if (isset($_GET['flush_rewrite']) && current_user_can('manage_options')) {
        dnttvn_manual_flush_rewrite_rules();
        wp_die('Rewrite rules flushed! <a href="' . home_url() . '">Go back</a>');
    }

    // Force flush on every load for testing (remove after testing)
    if (isset($_GET['force_flush']) && current_user_can('manage_options')) {
        flush_rewrite_rules(true);
        wp_die('Rewrite rules force flushed! <a href="' . home_url() . '">Go back</a>');
    }
});

// Flush rewrite rules on theme activation to ensure proper permalinks
function dnttvn_flush_rewrite_rules() {
    if (get_option('dnttvn_theme_activated') != 'yes') {
        flush_rewrite_rules();
        update_option('dnttvn_theme_activated', 'yes');
    }
}
add_action('after_switch_theme', 'dnttvn_flush_rewrite_rules');

// Create necessary pages automatically
function dnttvn_create_necessary_pages() {
    // Create tin tức detail page
    $tin_tuc_page_title = 'Trang Tin Tức Chi Tiết';
    $tin_tuc_page_slug = 'trang-tin-tuc-chi-tiet';

    $existing_tin_tuc_page = get_page_by_path($tin_tuc_page_slug);
    if (!$existing_tin_tuc_page) {
        $page_id = wp_insert_post(array(
            'post_title'    => $tin_tuc_page_title,
            'post_name'     => $tin_tuc_page_slug,
            'post_content'  => 'Trang hiển thị chi tiết tin tức. Nội dung sẽ được load dynamically dựa trên tham số post_id.',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
        ));

        if ($page_id) {
            update_post_meta($page_id, '_wp_page_template', 'page-tin-tuc-chi-tiet.php');
        }
    }

    // Create tin tức archive page if doesn't exist
    $tin_tuc_archive_title = 'Tin Tức';
    $tin_tuc_archive_slug = 'tin-tuc';

    $existing_archive_page = get_page_by_path($tin_tuc_archive_slug);
    if (!$existing_archive_page) {
        $archive_page_id = wp_insert_post(array(
            'post_title'    => $tin_tuc_archive_title,
            'post_name'     => $tin_tuc_archive_slug,
            'post_content'  => 'Danh sách tất cả tin tức của cộng đồng.',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
        ));

        if ($archive_page_id) {
            update_post_meta($archive_page_id, '_wp_page_template', 'page-tin-tuc.php');
        }
    }

    // Create "Trang Về cộng đồng" (chi tiết Cộng đồng theo post_id)
    $ve_cong_dong_title = 'Trang Về cộng đồng';
    $ve_cong_dong_slug = 'trang-ve-cong-dong';
    $existing_ve_cong_dong = get_page_by_path($ve_cong_dong_slug);
    if (!$existing_ve_cong_dong) {
        $ve_page_id = wp_insert_post(array(
            'post_title'   => $ve_cong_dong_title,
            'post_name'    => $ve_cong_dong_slug,
            'post_content' => 'Trang hiển thị chi tiết từng mục Cộng đồng. Nội dung load theo tham số post_id.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        if ($ve_page_id) {
            update_post_meta($ve_page_id, '_wp_page_template', 'page-ve-cong-dong.php');
        }
    }

    // Create "Trang Doanh nghiệp chi tiết" (chi tiết Doanh nghiệp theo post_id)
    $doanh_nghiep_detail_title = 'Trang Doanh nghiệp chi tiết';
    $doanh_nghiep_detail_slug = 'trang-doanh-nghiep-chi-tiet';
    $existing_dn_detail = get_page_by_path($doanh_nghiep_detail_slug);
    if (!$existing_dn_detail) {
        $dn_detail_page_id = wp_insert_post(array(
            'post_title'   => $doanh_nghiep_detail_title,
            'post_name'    => $doanh_nghiep_detail_slug,
            'post_content' => 'Trang hiển thị chi tiết doanh nghiệp. Nội dung load theo tham số post_id.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        if ($dn_detail_page_id) {
            update_post_meta($dn_detail_page_id, '_wp_page_template', 'page-doanh-nghiep-chi-tiet.php');
        }
    }

    dnttvn_ensure_page_dang_ky_doanh_nghiep();
}

/**
 * Trang công khai: form Đăng ký doanh nghiệp (slug cố định).
 */
function dnttvn_ensure_page_dang_ky_doanh_nghiep() {
    $slug = 'dang-ky-doanh-nghiep';
    if (get_page_by_path($slug)) {
        return;
    }
    $page_id = wp_insert_post(
        array(
            'post_title'   => 'Đăng ký Doanh nghiệp',
            'post_name'    => $slug,
            'post_content' => 'Form thu thập thông tin doanh nghiệp để tư vấn và xây dựng hồ sơ.',
            'post_status'  => 'publish',
            'post_type'      => 'page',
            'post_author'    => 1,
        )
    );
    if ($page_id && !is_wp_error($page_id)) {
        update_post_meta($page_id, '_wp_page_template', 'page-dang-ky-doanh-nghiep.php');
    }

    dnttvn_ensure_page_preview_dang_ky_doanh_nghiep();
}
add_action('after_switch_theme', 'dnttvn_create_necessary_pages');

/**
 * Trang xem trước hồ sơ đăng ký (slug cố định, có khóa URL).
 */
function dnttvn_ensure_page_preview_dang_ky_doanh_nghiep() {
    $slug = 'xem-truoc-dang-ky-doanh-nghiep';
    if (get_page_by_path($slug)) {
        return;
    }
    $page_id = wp_insert_post(
        array(
            'post_title'   => 'Xem trước đăng ký Doanh nghiệp',
            'post_name'    => $slug,
            'post_content' => 'Trang xem trước giao diện thẻ và chi tiết từ hồ sơ đăng ký (trước khi duyệt). Truy cập qua liên kết có khóa sau khi gửi form.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        )
    );
    if ($page_id && !is_wp_error($page_id)) {
        update_post_meta($page_id, '_wp_page_template', 'page-xem-truoc-dang-ky-doanh-nghiep.php');
    }
}

add_action(
    'init',
    function () {
        dnttvn_ensure_page_dang_ky_doanh_nghiep();
        dnttvn_ensure_page_preview_dang_ky_doanh_nghiep();
    },
    26
);

// Add custom meta boxes for Tin tức
function dnttvn_add_tin_tuc_meta_boxes() {
    // 1. Xem trước (ưu tiên cao nhất)
    add_meta_box(
        'tin_tuc_live_preview',
        'Xem trước Tin tức (gần giống ngoài website)',
        'dnttvn_tin_tuc_live_preview_meta_box_callback',
        'tin_tuc',
        'normal',
        'high'
    );
    
    // 2. Thông tin tin tức (ưu tiên trung bình)
    add_meta_box(
        'tin_tuc_details',
        'Thông tin Tin tức',
        'dnttvn_tin_tuc_meta_box_callback',
        'tin_tuc',
        'normal',
        'default'
    );

    // 3. Thêm mục nội dung (ưu tiên thấp nhất)
    add_meta_box(
        'tin_tuc_structured_content',
        'Thêm mục nội dung',
        'dnttvn_structured_content_meta_box_callback',
        'tin_tuc',
        'normal',
        'low'
    );
    
    add_meta_box(
        'tin_tuc_author_info',
        'Thông tin Tác giả & Nguồn',
        'dnttvn_tin_tuc_author_meta_box_callback',
        'tin_tuc',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_tin_tuc_meta_boxes');

// Live preview meta box for Tin tức
function dnttvn_tin_tuc_live_preview_meta_box_callback($post) {
    ?>
    <div class="dnttvn-live-preview-box dnttvn-live-preview-tin-tuc">
        <p style="margin-top:0; margin-bottom:10px; color:#555;">
            Xem nhanh cách bài Tin tức sẽ hiển thị ở trang chi tiết (demo).
        </p>
        <div class="dnttvn-preview-card">
            <h3 id="dnttvn-tin-tuc-preview-title" class="dnttvn-preview-title" style="font-size:20px; margin:0 0 6px; color:#06202e;">
                <?php echo esc_html(get_the_title($post)); ?>
            </h3>
            <p class="dnttvn-preview-meta" style="font-size:12px; color:#999; margin:0 0 10px;">
                <strong>Ngày đăng:</strong> <span><?php echo esc_html(get_the_date('d/m/Y', $post)); ?></span>
            </p>
            <div id="dnttvn-tin-tuc-preview-excerpt" class="dnttvn-preview-excerpt" style="font-size:14px; color:#555; line-height:1.6;">
                <?php
                $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_shortcodes($post->post_content), 40, '...');
                echo esc_html($excerpt);
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Lấy mục nội dung (structured content) dưới dạng array.
 * Hỗ trợ cả dữ liệu cũ (JSON) và mới (serialized/array).
 */
function dnttvn_get_structured_content_array($post_id) {
    $raw = get_post_meta($post_id, '_structured_content', true);
    if (is_array($raw)) {
        return $raw;
    }
    if (empty($raw) || !is_string($raw)) {
        return array();
    }
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        return $decoded;
    }
    $unserialized = @unserialize($raw);
    return is_array($unserialized) ? $unserialized : array();
}

/**
 * Khối hiển thị "Nội dung doanh nghiệp": nhiều hình chọn cùng lúc, slider + thumbnail (trang chi tiết).
 *
 * @param int $post_id ID bài doanh_nghiep.
 * @return string HTML hoặc chuỗi rỗng.
 */
function dnttvn_render_doanh_nghiep_noi_dung_slider( $post_id, $override_ids = null, $override_title = null, $override_intro = null ) {
    $post_id = (int) $post_id;
    $ids      = array();
    if ( is_array( $override_ids ) ) {
        $ids = array_values( array_filter( array_map( 'absint', $override_ids ) ) );
    } elseif ( $post_id > 0 ) {
        $raw = get_post_meta( $post_id, '_noi_dung_slider_images', true );
        if ( empty( $raw ) || ! is_string( $raw ) ) {
            return '';
        }
        $ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
    }
    if ( empty( $ids ) ) {
        return '';
    }

    if ( is_string( $override_title ) && $override_title !== '' ) {
        $title = $override_title;
    } elseif ( $post_id > 0 ) {
        $title = get_post_meta( $post_id, '_noi_dung_slider_title', true );
        $title = is_string( $title ) ? $title : '';
    } else {
        $title = '';
    }
    $title = $title !== '' ? $title : 'Nội dung hình ảnh';

    if ( is_string( $override_intro ) ) {
        $intro = $override_intro;
    } elseif ( $post_id > 0 ) {
        $intro = get_post_meta( $post_id, '_noi_dung_slider_text', true );
        $intro = is_string( $intro ) ? $intro : '';
    } else {
        $intro = '';
    }

    $alt_base = $post_id > 0 ? get_the_title( $post_id ) : '';

    ob_start();
    ?>
    <div class="dn-noi-dung-slider">
        <h3 class="dn-noi-dung-slider__title"><?php echo esc_html( $title ); ?></h3>
        <?php if ( $intro !== '' ) : ?>
            <div class="dn-noi-dung-slider__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
        <?php endif; ?>
        <div class="business-card-small-image dn-noi-dung-slider__viewport">
            <div class="business-card-small-image-slider">
                <?php
                $first = true;
                foreach ( $ids as $img_id ) {
                    $img_url = wp_get_attachment_image_url( $img_id, 'large' );
                    if ( ! $img_url ) {
                        continue;
                    }
                    $img_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                    if ( $img_alt === '' ) {
                        $img_alt = ( $alt_base !== '' ? $alt_base . ' — ' : '' ) . $img_id;
                    }
                    $full_url = wp_get_attachment_image_url( $img_id, 'full' );
                    if ( ! $full_url ) {
                        $full_url = $img_url;
                    }
                    ?>
                    <div class="business-card-small-image-slide<?php echo $first ? ' active' : ''; ?>"
                         data-full="<?php echo esc_url( $full_url ); ?>"
                         data-alt="<?php echo esc_attr( $img_alt ); ?>">
                        <?php echo wp_get_attachment_image( $img_id, 'large', false, array( 'class' => 'business-small-image', 'loading' => 'lazy' ) ); ?>
                    </div>
                    <?php
                    $first = false;
                }
                ?>
            </div>
            <div class="business-card-small-image-nav">
                <button type="button" class="business-card-small-image-prev" aria-label="Ảnh trước">&#10094;</button>
                <button type="button" class="business-card-small-image-next" aria-label="Ảnh sau">&#10095;</button>
            </div>
        </div>
        <div class="business-card-gallery dn-noi-dung-slider__thumbs" style="margin-top: 12px;">
            <?php
            foreach ( $ids as $img_id ) {
                $thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                if ( ! $thumb_url ) {
                    $thumb_url = wp_get_attachment_image_url( $img_id, 'medium' );
                }
                if ( ! $thumb_url ) {
                    continue;
                }
                $thumb_alt  = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
                $thumb_full = wp_get_attachment_image_url( $img_id, 'full' ) ?: wp_get_attachment_image_url( $img_id, 'large' ) ?: $thumb_url;
                ?>
                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $thumb_alt ); ?>" data-full="<?php echo esc_url( $thumb_full ); ?>" class="business-gallery-thumb" loading="lazy" />
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * 8 mục nội dung mặc định cho Doanh nghiệp (khớp 8 phần form đăng ký).
 */
function dnttvn_get_doanh_nghiep_default_structured_headings() {
    return array(
        '1. Thông tin chung',
        '2. Sứ mệnh & tầm nhìn',
        '3. Mô hình',
        '4. Nguồn lực doanh nghiệp',
        '5. Vận hành doanh nghiệp',
        '6. Khách hàng tiêu biểu',
        '7. Nhu cầu hiện tại',
        '8. Xác nhận thông tin',
    );
}

function dnttvn_get_doanh_nghiep_default_structured_items() {
    $out = array();
    foreach (dnttvn_get_doanh_nghiep_default_structured_headings() as $h) {
        $out[] = array(
            'heading'        => $h,
            'content'        => '',
            'content_items'  => array(),
            'images'         => array(),
            'image_captions' => array(),
        );
    }
    return $out;
}

/**
 * Tiêu đề mục 7–8 (nhu cầu / xác nhận) chỉ dùng nội bộ form đăng ký — không hiển thị xem trước / bài doanh nghiệp công khai.
 *
 * @return array<int, string>
 */
function dnttvn_doanh_nghiep_internal_only_structured_headings() {
    return array_slice(dnttvn_get_doanh_nghiep_default_structured_headings(), 6, 2);
}

/**
 * Bỏ các mục structured có tiêu đề trùng mục 7–8 mặc định (để xem trước & trang chi tiết khớp quy định).
 *
 * @param array<int, mixed> $items
 * @return array<int, array<string, mixed>>
 */
function dnttvn_doanh_nghiep_filter_public_structured($items) {
    if (!is_array($items) || $items === array()) {
        return array();
    }
    $hide = array_flip(dnttvn_doanh_nghiep_internal_only_structured_headings());
    $out  = array();
    foreach ($items as $it) {
        if (!is_array($it)) {
            continue;
        }
        $h = isset($it['heading']) ? (string) $it['heading'] : '';
        if ($h !== '' && isset($hide[ $h ])) {
            continue;
        }
        $out[] = $it;
    }
    return $out;
}

/**
 * Hướng dẫn soạn từng mục (admin) — HTML an toàn.
 */
function dnttvn_get_doanh_nghiep_section_guides() {
    return array(
        0 => '<strong>1. Thông tin chung</strong><ul style="margin:6px 0 0 18px;"><li>Chỉ hiển thị <strong>Mô tả ngắn</strong> đầy đủ theo hồ sơ (không lặp thông tin thẻ, không thêm &quot;Lĩnh vực hoạt động chính&quot; ở đây). Kèm ảnh minh họa mục Thông tin chung.</li></ul>',
        1 => '<strong>2. Sứ mệnh &amp; tầm nhìn</strong><ul style="margin:6px 0 0 18px;"><li>Sứ mệnh, tầm nhìn, giá trị cốt lõi, định vị thương hiệu trong tâm trí khách hàng, điểm khác biệt.</li></ul>',
        2 => '<strong>3. Mô hình</strong><ul style="margin:6px 0 0 18px;"><li>Mô hình kinh doanh, mô hình quản trị, mô hình doanh nghiệp, mô hình bán hàng — mỗi mục ngắn gọn; có ảnh Phần 3.</li></ul>',
        3 => '<strong>4. Nguồn lực doanh nghiệp</strong><ul style="margin:6px 0 0 18px;"><li>Cơ sở vật chất, nhân sự, công nghệ, pháp lý, năng lực mạnh.</li></ul>',
        4 => '<strong>5. Vận hành doanh nghiệp</strong><ul style="margin:6px 0 0 18px;"><li>Triết lý vận hành, quy trình, CSKH sau bán.</li></ul>',
        5 => '<strong>6. Khách hàng tiêu biểu</strong><ul style="margin:6px 0 0 18px;"><li>Đánh giá, phản hồi, khách / đối tác tiêu biểu, dự án &amp; case study; mô tả tài liệu gửi kèm (nếu có).</li></ul>',
        6 => '<strong>7. Nhu cầu hiện tại</strong><ul style="margin:6px 0 0 18px;"><li>Hạng mục cần cải thiện, ba vấn đề ưu tiên, kỳ vọng hợp tác.</li></ul>',
        7 => '<strong>8. Xác nhận thông tin</strong><ul style="margin:6px 0 0 18px;"><li>Đồng ý sử dụng thông tin, họ tên người xác nhận, ngày điền form.</li></ul>',
    );
}

/** Giới hạn ảnh mục nội dung doanh nghiệp: số lượng, dung lượng (byte), cạnh dài tối đa (px). */
function dnttvn_doanh_nghiep_section_image_limits() {
    return array(
        'max_count'   => 5,
        'max_bytes'   => 2097152,
        'max_side_px' => 2000,
    );
}

/**
 * Kiểm tra attachment ảnh có đạt giới hạn kích thước & dung lượng không.
 */
function dnttvn_doanh_nghiep_validate_section_image_id($attachment_id) {
    $attachment_id = absint($attachment_id);
    if ($attachment_id <= 0 || !get_post($attachment_id)) {
        return false;
    }
    $mime = get_post_mime_type($attachment_id);
    if (!$mime || strpos($mime, 'image') !== 0) {
        return false;
    }
    $limits = dnttvn_doanh_nghiep_section_image_limits();
    $file   = get_attached_file($attachment_id);
    if ($file && file_exists($file) && filesize($file) > $limits['max_bytes']) {
        return false;
    }
    $meta = wp_get_attachment_metadata($attachment_id);
    if (is_array($meta)) {
        $w = isset($meta['width']) ? (int) $meta['width'] : 0;
        $h = isset($meta['height']) ? (int) $meta['height'] : 0;
        if ($w > $limits['max_side_px'] || $h > $limits['max_side_px']) {
            return false;
        }
    }
    return true;
}

/**
 * Hiển thị ảnh mục nội dung: 1 ảnh = block đơn; 2–5 ảnh = slider (class dùng chung footer).
 */
function dnttvn_render_doanh_nghiep_ci_images_block($image_ids, $captions = array()) {
    $image_ids = array_values(array_filter(array_map('absint', (array) $image_ids)));
    $image_ids = array_slice($image_ids, 0, 5);
    if (empty($image_ids)) {
        return '';
    }
    $captions = is_array($captions) ? $captions : array();
    ob_start();
    if (count($image_ids) === 1) {
        $id        = $image_ids[0];
        $ci_mime   = get_post_mime_type($id);
        $ci_url    = wp_get_attachment_url($id);
        $ci_caption = isset($captions[0]) ? $captions[0] : '';
        if (!$ci_url) {
            ob_end_clean();
            return '';
        }
        $ci_is_video = strpos((string) $ci_mime, 'video') === 0;
        echo '<div class="dn-ci-images dn-ci-images--single" style="margin-bottom:15px;">';
        if ($ci_is_video) {
            echo '<video style="max-width:100%;max-height:420px;border-radius:8px;" controls><source src="' . esc_url($ci_url) . '" type="' . esc_attr($ci_mime) . '"></video>';
        } else {
            echo '<img src="' . esc_url($ci_url) . '" alt="" style="max-width:100%;max-height:420px;border-radius:8px;object-fit:contain;" loading="lazy" />';
        }
        if ($ci_caption !== '') {
            echo '<p style="margin-top:8px;font-size:14px;color:#666;">' . esc_html($ci_caption) . '</p>';
        }
        echo '</div>';
    } else {
        ob_start();
        $first = true;
        foreach ($image_ids as $idx => $img_id) {
            $img_url = wp_get_attachment_image_url($img_id, 'large');
            if (!$img_url) {
                $img_url = wp_get_attachment_url($img_id);
            }
            if (!$img_url) {
                continue;
            }
            $img_alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
            $full_url = wp_get_attachment_image_url($img_id, 'full') ?: $img_url;
            $cap      = isset($captions[ $idx ]) ? $captions[ $idx ] : '';
            echo '<div class="business-card-small-image-slide' . ($first ? ' active' : '') . '" data-full="' . esc_url($full_url) . '" data-alt="' . esc_attr($img_alt) . '">';
            $first = false;
            echo wp_get_attachment_image($img_id, 'large', false, array('class' => 'business-small-image', 'loading' => 'lazy'));
            if ($cap !== '') {
                echo '<p class="dn-ci-slide-caption" style="margin:6px 0 0;font-size:13px;color:#666;text-align:center;">' . esc_html($cap) . '</p>';
            }
            echo '</div>';
        }
        $slides_inner = ob_get_clean();
        if ($slides_inner === '') {
            ob_end_clean();
            return '';
        }
        echo '<div class="business-card-small-image dn-ci-images-slider" style="margin-bottom:15px;max-width:100%;">';
        echo '<div class="business-card-small-image-slider">';
        echo $slides_inner;
        echo '</div>';
        echo '<div class="business-card-small-image-nav">';
        echo '<button type="button" class="business-card-small-image-prev" aria-label="Ảnh trước">&#10094;</button>';
        echo '<button type="button" class="business-card-small-image-next" aria-label="Ảnh sau">&#10095;</button>';
        echo '</div></div>';
        echo '<div class="business-card-gallery dn-ci-images__thumbs" style="margin-top:8px;">';
        foreach ($image_ids as $img_id) {
            $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
            if (!$thumb_url) {
                $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
            }
            if (!$thumb_url) {
                continue;
            }
            $thumb_alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
            $thumb_full = wp_get_attachment_image_url($img_id, 'full') ?: wp_get_attachment_image_url($img_id, 'large') ?: $thumb_url;
            echo '<img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($thumb_alt) . '" data-full="' . esc_url($thumb_full) . '" class="business-gallery-thumb" loading="lazy" />';
        }
        echo '</div>';
    }
    return (string) ob_get_clean();
}

// Meta box callback for Structured Content (Tin tức & Cộng đồng)
function dnttvn_structured_content_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_structured_content', 'dnttvn_structured_content_nonce');
    
    $items = dnttvn_get_structured_content_array($post->ID);
    $is_dn_sc = (get_post_type($post->ID) === 'doanh_nghiep');
    if ($is_dn_sc && empty($items)) {
        $items = dnttvn_get_doanh_nghiep_default_structured_items();
    }
    
    ?>
    <div class="dnttvn-structured-content-wrapper">
        <?php if ($is_dn_sc) : ?>
        <p class="description" style="margin-bottom: 12px;">
            <strong>Quy cách Profile (ngắn gọn, súc tích):</strong> Dưới đây là <strong>8 mục mặc định</strong> (khớp 8 phần form đăng ký). Mỗi mục có hướng dẫn soạn; ô nội dung có thanh công cụ định dạng. <strong>Hình ảnh:</strong> tối đa <strong>5 ảnh</strong> / mục, tối đa <strong>2 MB</strong>/ảnh, cạnh dài tối đa <strong>2000 px</strong>; nhiều ảnh hiển thị dạng <strong>slider</strong> trên trang chi tiết.
        </p>
        <?php else : ?>
        <p class="description" style="margin-bottom: 15px;">
            <strong>Hướng dẫn:</strong> Mỗi mục gồm tiêu đề, hình/video, caption và nội dung. <?php if (in_array(get_post_type($post->ID), array('tin_tuc', 'doanh_nghiep'), true)) : ?>Ô nội dung có thanh công cụ chỉnh sửa chữ (in đậm, in nghiêng, link, danh sách...); nội dung đã chỉnh sửa sẽ hiển thị đúng trên trang chi tiết. <?php endif; ?>Có thể thêm nhiều mục và kéo thả để sắp xếp.
        </p>
        <?php endif; ?>
        
        <div id="structured-content-items" class="structured-content-items" data-post-id="<?php echo esc_attr($post->ID); ?>">
            <?php if (!empty($items)) : ?>
                <?php foreach ($items as $index => $item) : ?>
                    <div class="structured-item" data-index="<?php echo esc_attr($index); ?>">
                        <div class="structured-item-header">
                            <span class="drag-handle" title="Kéo để sắp xếp">☰</span>
                            <strong>Mục <?php echo esc_html($index + 1); ?></strong>
                            <button type="button" class="button remove-item-btn" style="float: right;">Xóa mục</button>
                        </div>
                        <div class="structured-item-body">
                            <?php
                            $heading = isset($item['heading']) ? (string) $item['heading'] : '';
                            $content = '';
                            $media_id = 0;
                            $media_caption = '';
                            $legacy_texts = [];
                            $legacy_media_id = 0;
                            $legacy_caption = '';

                            if (isset($item['content']) && is_string($item['content'])) {
                                $content = $item['content'];
                            }

                            if (!empty($item['content_items']) && is_array($item['content_items'])) {
                                foreach ($item['content_items'] as $ci) {
                                    if (is_string($ci)) {
                                        $legacy_texts[] = $ci;
                                    } elseif (is_array($ci)) {
                                        $ci_text = $ci['text'] ?? '';
                                        if ($ci_text !== '') {
                                            $legacy_texts[] = $ci_text;
                                        }
                                        if (!$legacy_media_id && !empty($ci['images'][0])) {
                                            $legacy_media_id = absint($ci['images'][0]);
                                            $legacy_caption = $ci['image_captions'][0] ?? '';
                                        }
                                    }
                                }
                            }

                            if ($content === '' && !empty($legacy_texts)) {
                                $content = implode("\n\n", $legacy_texts);
                            }

                            if (!empty($item['media_id'])) {
                                $media_id = absint($item['media_id']);
                            } elseif (!empty($item['images'][0])) {
                                $media_id = absint($item['images'][0]);
                                $media_caption = $item['image_captions'][0] ?? '';
                            } elseif ($legacy_media_id) {
                                $media_id = $legacy_media_id;
                                $media_caption = $legacy_caption;
                            }

                            if (!empty($item['media_caption'])) {
                                $media_caption = $item['media_caption'];
                            } elseif ($media_caption === '' && !empty($item['image_captions'][0])) {
                                $media_caption = $item['image_captions'][0];
                            }

                            $dn_media_ids      = array();
                            $dn_media_captions = array();
                            if (!empty($item['content_items'][0]['images']) && is_array($item['content_items'][0]['images'])) {
                                $caps0 = isset($item['content_items'][0]['image_captions']) && is_array($item['content_items'][0]['image_captions'])
                                    ? $item['content_items'][0]['image_captions'] : array();
                                foreach ($item['content_items'][0]['images'] as $mi => $img_id) {
                                    $img_id = absint($img_id);
                                    if ($img_id <= 0) {
                                        continue;
                                    }
                                    $dn_media_ids[] = $img_id;
                                    $dn_media_captions[] = isset($caps0[ $mi ]) ? (string) $caps0[ $mi ] : '';
                                    if (count($dn_media_ids) >= 5) {
                                        break;
                                    }
                                }
                            }
                            if (empty($dn_media_ids) && $media_id) {
                                $dn_media_ids      = array($media_id);
                                $dn_media_captions = array($media_caption);
                            }
                            ?>
                            <p>
                                <label><strong>Tiêu đề mục:</strong></label><br>
                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][heading]"
                                       value="<?php echo esc_attr($heading); ?>"
                                       class="large-text structured-heading" placeholder="Nhập tiêu đề mục...">
                            </p>
                            <?php
                            if ($is_dn_sc) {
                                $guides = dnttvn_get_doanh_nghiep_section_guides();
                                if (isset($guides[ $index ])) {
                                    echo '<div class="dn-admin-section-guide" style="background:#f9f6ef;border:1px solid #e6d9a8;border-radius:6px;padding:10px 12px;margin:0 0 12px;font-size:13px;line-height:1.5;color:#333;">';
                                    echo wp_kses_post($guides[ $index ]);
                                    echo '</div>';
                                }
                            }
                            ?>
                            <?php if ($is_dn_sc) : ?>
                            <div class="dn-structured-gallery-wrap structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">
                                <label style="font-weight: bold; color: #007cba;">🖼️ Hình ảnh mục (tối đa 5 ảnh, ≤ 2 MB/ảnh, cạnh dài ≤ 2000 px — slider trên web)</label>
                                <input type="hidden" name="structured_content[<?php echo esc_attr($index); ?>][media_id]" value="" class="structured-media-id">
                                <div class="dn-structured-gallery-rows" style="margin-top:10px;">
                                    <?php foreach ($dn_media_ids as $gk => $gid) : ?>
                                        <?php
                                        $gid = absint($gid);
                                        if ($gid <= 0) {
                                            continue;
                                        }
                                        $thumb = wp_get_attachment_image_url($gid, 'thumbnail') ?: wp_get_attachment_image_url($gid, 'medium');
                                        $capv  = isset($dn_media_captions[ $gk ]) ? $dn_media_captions[ $gk ] : '';
                                        ?>
                                        <div class="dn-gallery-row" style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;padding:8px;background:#fff;border:1px solid #ddd;border-radius:4px;">
                                            <input type="hidden" name="structured_content[<?php echo esc_attr($index); ?>][media_ids][]" value="<?php echo esc_attr($gid); ?>" class="dn-gallery-media-id">
                                            <?php if ($thumb) : ?>
                                                <img src="<?php echo esc_url($thumb); ?>" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:4px;flex-shrink:0;" />
                                            <?php endif; ?>
                                            <div style="flex:1;min-width:0;">
                                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][media_captions][]" value="<?php echo esc_attr($capv); ?>" class="regular-text" style="width:100%;" placeholder="Chú thích ảnh (tuỳ chọn)">
                                            </div>
                                            <button type="button" class="button dn-gallery-remove-row">Xóa ảnh</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <p style="margin:8px 0 0;">
                                    <button type="button" class="button button-secondary dn-gallery-add-btn" data-item-index="<?php echo esc_attr($index); ?>"<?php echo count($dn_media_ids) >= 5 ? ' disabled' : ''; ?>>+ Thêm ảnh</button>
                                    <span class="dn-gallery-count-hint" style="margin-left:8px;color:#666;font-size:12px;"><?php echo (int) count($dn_media_ids); ?>/5 ảnh</span>
                                </p>
                                <div class="structured-media-preview dn-dn-legacy-preview" style="display:none;"></div>
                                <button type="button" class="button remove-structured-media-btn" style="display:none;"></button>
                            </div>
                            <?php else : ?>
                            <div class="structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">
                                <label style="font-weight: bold; color: #007cba;">🖼️ Hình/Video:</label>
                                <input type="hidden" name="structured_content[<?php echo esc_attr($index); ?>][media_id]" value="<?php echo esc_attr($media_id); ?>" class="structured-media-id">
                                <div class="structured-media-preview" style="margin: 10px 0; min-height: 60px;">
                                    <?php if ($media_id) :
                                        $mime_type = get_post_mime_type($media_id);
                                        $is_video = strpos((string) $mime_type, 'video') === 0;
                                        $media_url = $is_video ? wp_get_attachment_url($media_id) : wp_get_attachment_image_url($media_id, 'medium');
                                        if (!$media_url) {
                                            $media_url = wp_get_attachment_url($media_id);
                                        }
                                    ?>
                                        <?php if ($is_video) : ?>
                                            <video style="max-width: 220px; max-height: 160px; border-radius: 6px;" controls>
                                                <source src="<?php echo esc_url($media_url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                            </video>
                                        <?php else : ?>
                                            <img src="<?php echo esc_url($media_url); ?>" alt="" style="max-width: 220px; max-height: 160px; border-radius: 6px; object-fit: cover;">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <p style="margin: 0;">
                                    <button type="button" class="button upload-structured-media-btn" data-item-index="<?php echo esc_attr($index); ?>">📁 Chọn hình/video</button>
                                    <button type="button" class="button remove-structured-media-btn" data-item-index="<?php echo esc_attr($index); ?>" style="display: <?php echo $media_id ? 'inline-block' : 'none'; ?>; margin-left: 6px;">🗑️ Xóa</button>
                                </p>
                            </div>

                            <p>
                                <label><strong>Caption:</strong></label><br>
                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][media_caption]"
                                       value="<?php echo esc_attr($media_caption); ?>"
                                       class="large-text structured-media-caption" placeholder="Nhập caption cho hình/video...">
                            </p>
                            <?php endif; ?>

                            <p>
                                <label><strong>Nội dung mục (đoạn văn / nhiều dòng):</strong></label><br>
                                <?php
                                $post_type = get_post_type($post->ID);
                                $use_editor = ($post_type === 'tin_tuc' || $post_type === 'doanh_nghiep');
                                if ($use_editor) {
                                    $editor_id = 'structured_content_editor_' . $post->ID . '_' . $index;
                                    wp_editor($content, $editor_id, array(
                                        'textarea_name' => 'structured_content[' . $index . '][content]',
                                        'textarea_rows' => 10,
                                        'teeny'         => false,
                                        'quicktags'     => array('buttons' => 'strong,em,link,block,ul,ol,li'),
                                        'media_buttons' => false,
                                        'tinymce'       => array(
                                            'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,forecolor,backcolor,link,unlink,bullist,numlist,outdent,indent,blockquote',
                                            'toolbar2' => '',
                                        ),
                                        'wpautop'       => true,
                                        'editor_class'  => 'structured-content-item',
                                        'editor_css'    => '',
                                        'dfw'           => false,
                                    ));
                                } else {
                                    ?>
                                    <textarea name="structured_content[<?php echo esc_attr($index); ?>][content]"
                                              class="large-text structured-content-item" rows="6"
                                              style="min-height: 120px; line-height: 1.6; margin-bottom: 10px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px;"
                                              placeholder="Nhập nội dung chi tiết..."><?php echo esc_textarea($content); ?></textarea>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="structured-item-preview">
                            <strong>Preview:</strong>
                            <div class="preview-content">
                                <strong style="font-size: 18px; color: #333;"><?php echo esc_html($heading !== '' ? $heading : '(Chưa có tiêu đề)'); ?></strong>
                                <div class="preview-content-body" style="margin-top: 8px; color: #666; line-height: 1.6;">
                                    <?php
                                    if ($content !== '') {
                                        $pt = get_post_type($post->ID);
                                        if (($pt === 'tin_tuc' || $pt === 'doanh_nghiep') && preg_match('/\s*</', $content)) {
                                            echo wp_kses_post($content);
                                        } else {
                                            echo wpautop(esc_html($content));
                                        }
                                    } else {
                                        echo '(Chưa có nội dung)';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <p>
            <button type="button" id="add-structured-item" class="button button-primary">+ Thêm mục mới</button>
        </p>
        
    </div>
    
    <style>
        .dnttvn-structured-content-wrapper {
            padding: 15px;
        }
        
        .structured-content-items {
            margin-bottom: 20px;
        }
        
        .structured-item {
            background: #fff;
            border: 2px solid #e5e5e5;
            border-radius: 6px;
            margin-bottom: 15px;
            padding: 15px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .structured-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }
        
        .structured-item-header {
            background: #f7f7f7;
            padding: 10px 15px;
            margin: -15px -15px 15px -15px;
            border-bottom: 1px solid #e5e5e5;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .drag-handle {
            cursor: move;
            color: #666;
            font-size: 18px;
            user-select: none;
        }
        
        .structured-item-body {
            margin-bottom: 15px;
        }
        
        .structured-item-body label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .structured-item-body input[type="text"],
        .structured-item-body textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        /* Ô Nội dung nhỏ: cho phép xuống dòng (Enter) - class thực tế là structured-content-item */
        /* Ô nội dung: font hỗ trợ dấu tiếng Việt */
        .structured-item-body textarea.structured-content,
        .structured-item-body textarea.structured-content-item {
            min-height: 80px;
            resize: vertical;
            white-space: pre-wrap !important;
            font-family: "Segoe UI", Arial, "Helvetica Neue", sans-serif !important;
            line-height: 1.6 !important;
            font-size: 14px !important;
        }
        
        .structured-item-preview {
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .structured-item-preview strong {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        
        .preview-content {
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #667eea;
        }
        .preview-content .preview-content-body {
            word-break: break-word;
            display: block;
            line-height: 1.6;
            margin-top: 10px;
            padding-top: 5px;
        }
        .preview-content .preview-content-body p { margin: 0 0 0.75em 0; }
        .preview-content .preview-content-body p:last-child { margin-bottom: 0; }
        
        .remove-item-btn {
            margin-left: auto;
        }
        
        #add-structured-item {
            margin-top: 10px;
        }

        /* Styles for images section */
        .structured-images-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }

        .images-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .image-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            min-width: 180px;
        }

        .image-preview {
            margin-bottom: 8px;
        }

        .image-preview img {
            display: block;
        }

        .image-details {
            width: 100%;
            text-align: center;
        }

        .image-caption {
            width: 100%;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .remove-image-btn {
            background: #dc3232;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .remove-image-btn:hover {
            background: #c82333;
        }

        .upload-images-btn {
            margin-top: 5px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        if (window.structuredContentInitialized_v3) {
            return;
        }
        window.structuredContentInitialized_v3 = true;
        window.structuredContentInitialized_v2 = true;

        if ($('#structured-content-items').length && !$('#structured-content-items').hasClass('sortable-initialized')) {
            $('#structured-content-items').sortable({
                items: '.structured-item',
                handle: '.drag-handle',
                cursor: 'move',
                opacity: 0.6
            });
            $('#structured-content-items').addClass('sortable-initialized');
        }

        function formatContentForPreview(content) {
            if (!content || !String(content).trim()) return '(Chưa có nội dung)';
            var s = String(content).trim();
            if (s.indexOf('<') !== -1) return s;
            var escaped = s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            var paras = escaped.split(/\n\n+/);
            return paras.map(function(p) { return '<p>' + p.replace(/\n/g, '<br />') + '</p>'; }).join('');
        }
        function getContentFromItem($item) {
            var postId = $('#structured-content-items').data('post-id') || '';
            var index = $item.attr('data-index');
            var editorId = 'structured_content_editor_' + postId + '_' + index;
            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                return tinymce.get(editorId).getContent() || '';
            }
            var $ta = $item.find('textarea.structured-content-item');
            return ($ta.length ? $ta.val() : '') || '';
        }
        function updatePreview($item) {
            var heading = $item.find('.structured-heading').val() || '(Chưa có tiêu đề)';
            var content = getContentFromItem($item);
            var formatted = formatContentForPreview(content);
            $item.find('.preview-content strong').first().text(heading);
            $item.find('.preview-content-body').html(formatted);
        }

        function renderMediaPreview($item, attachment) {
            var $preview = $item.find('.structured-media-preview');
            var isVideo = attachment.type === 'video' || (attachment.mime && attachment.mime.indexOf('video') === 0);
            var url = attachment.url;
            if (!isVideo && attachment.sizes && attachment.sizes.medium) {
                url = attachment.sizes.medium.url;
            }
            var html = '';
            if (isVideo) {
                html = '<video style="max-width: 220px; max-height: 160px; border-radius: 6px;" controls>' +
                    '<source src="' + url + '" type="' + attachment.mime + '">' +
                    '</video>';
            } else {
                html = '<img src="' + url + '" alt="" style="max-width: 220px; max-height: 160px; border-radius: 6px; object-fit: cover;">';
            }
            $preview.html(html);
        }

        function clearMedia($item) {
            $item.find('.structured-media-id').val('');
            $item.find('.structured-media-preview').empty();
            $item.find('.remove-structured-media-btn').hide();
        }

        var isDnBody = $('body').hasClass('post-type-doanh_nghiep');
        $(document).on('click', '#add-structured-item', function(e) {
            e.preventDefault();
            var itemCount = $('.structured-item').length;
            var mediaBlock;
            if (isDnBody) {
                mediaBlock = '<div class="dn-structured-gallery-wrap structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                    '<label style="font-weight: bold; color: #007cba;">🖼️ Hình ảnh mục (tối đa 5 ảnh, ≤ 2 MB, cạnh dài ≤ 2000 px)</label>' +
                    '<input type="hidden" name="structured_content[' + itemCount + '][media_id]" value="" class="structured-media-id">' +
                    '<div class="dn-structured-gallery-rows" style="margin-top:10px;"></div>' +
                    '<p style="margin:8px 0 0;"><button type="button" class="button button-secondary dn-gallery-add-btn" data-item-index="' + itemCount + '">+ Thêm ảnh</button>' +
                    '<span class="dn-gallery-count-hint" style="margin-left:8px;color:#666;font-size:12px;">0/5 ảnh</span></p>' +
                    '<div class="structured-media-preview dn-dn-legacy-preview" style="display:none;"></div>' +
                    '<button type="button" class="button remove-structured-media-btn" style="display:none;"></button></div>';
            } else {
                mediaBlock = '<div class="structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                        '<label style="font-weight: bold; color: #007cba;">🖼️ Hình/Video:</label>' +
                        '<input type="hidden" name="structured_content[' + itemCount + '][media_id]" value="" class="structured-media-id">' +
                        '<div class="structured-media-preview" style="margin: 10px 0; min-height: 60px;"></div>' +
                        '<p style="margin: 0;">' +
                            '<button type="button" class="button upload-structured-media-btn" data-item-index="' + itemCount + '">📁 Chọn hình/video</button>' +
                            '<button type="button" class="button remove-structured-media-btn" data-item-index="' + itemCount + '" style="display:none; margin-left: 6px;">🗑️ Xóa</button>' +
                        '</p></div>' +
                    '<p><label><strong>Caption:</strong></label><br>' +
                        '<input type="text" name="structured_content[' + itemCount + '][media_caption]" class="large-text structured-media-caption" placeholder="Nhập caption cho hình/video..."></p>';
            }
            var newItem = '<div class="structured-item" data-index="' + itemCount + '">' +
                '<div class="structured-item-header">' +
                    '<span class="drag-handle" title="Kéo để sắp xếp">☰</span>' +
                    '<strong>Mục ' + (itemCount + 1) + '</strong>' +
                    '<button type="button" class="button remove-item-btn" style="float: right;">Xóa mục</button>' +
                '</div>' +
                '<div class="structured-item-body">' +
                    '<p>' +
                        '<label><strong>Tiêu đề mục:</strong></label><br>' +
                        '<input type="text" name="structured_content[' + itemCount + '][heading]" class="large-text structured-heading" placeholder="Nhập tiêu đề mục...">' +
                    '</p>' +
                    mediaBlock +
                    '<p>' +
                        '<label><strong>Nội dung mục (đoạn văn / nhiều dòng):</strong></label><br>' +
                        '<textarea name="structured_content[' + itemCount + '][content]" class="large-text structured-content-item" rows="6" style="min-height: 120px; line-height: 1.5; margin-bottom: 10px;" placeholder="Nhập nội dung chi tiết..."></textarea>' +
                    '</p>' +
                '</div>' +
                '<div class="structured-item-preview">' +
                    '<strong>Preview:</strong>' +
                    '<div class="preview-content">' +
                        '<strong style="font-size: 18px; color: #333;">(Chưa có tiêu đề)</strong>' +
                        '<div class="preview-content-body" style="margin-top: 8px; color: #666; line-height: 1.6;">(Chưa có nội dung)</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
            $('#structured-content-items').append(newItem);
        });

        $(document).on('input', '.structured-content-item, .structured-heading', function() {
            updatePreview($(this).closest('.structured-item'));
        });

        $(document).on('click', '.remove-item-btn', function() {
            $(this).closest('.structured-item').remove();
            $('.structured-item').each(function(index) {
                $(this).find('strong').first().text('Mục ' + (index + 1));
                $(this).attr('data-index', index);
                $(this).find('input, textarea').each(function() {
                    var name = $(this).attr('name');
                    if (name && name.includes('structured_content[')) {
                        $(this).attr('name', name.replace(/structured_content\[\d+\]/, 'structured_content[' + index + ']'));
                    }
                });
                $(this).find('.upload-structured-media-btn').attr('data-item-index', index);
                $(this).find('.remove-structured-media-btn').attr('data-item-index', index);
            });
        });

        $(document).on('click', '.upload-structured-media-btn', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.structured-item');
            var uploader = wp.media({
                title: 'Chọn hình/video',
                button: { text: 'Chọn' },
                multiple: false,
                library: { type: ['image', 'video'] }
            });
            uploader.on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                $item.find('.structured-media-id').val(attachment.id);
                renderMediaPreview($item, attachment);
                $item.find('.remove-structured-media-btn').show();
            });
            uploader.open();
        });

        $(document).on('click', '.remove-structured-media-btn', function(e) {
            e.preventDefault();
            clearMedia($(this).closest('.structured-item'));
        });

        var DN_MAX_IMG = 5;
        var DN_MAX_BYTES = 2097152;
        var DN_MAX_SIDE = 2000;
        function dnUpdateGalleryCount($wrap) {
            var n = $wrap.find('.dn-gallery-row').length;
            $wrap.find('.dn-gallery-count-hint').text(n + '/' + DN_MAX_IMG + ' ảnh');
            $wrap.find('.dn-gallery-add-btn').prop('disabled', n >= DN_MAX_IMG);
        }
        $(document).on('click', '.dn-gallery-add-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $wrap = $btn.closest('.dn-structured-gallery-wrap');
            var $item = $btn.closest('.structured-item');
            var itemIndex = $item.attr('data-index');
            if ($wrap.find('.dn-gallery-row').length >= DN_MAX_IMG) {
                window.alert('Tối đa 5 ảnh mỗi mục.');
                return;
            }
            var frame = wp.media({
                title: 'Chọn ảnh (≤ 2 MB, cạnh dài ≤ 2000 px)',
                button: { text: 'Chọn ảnh' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var att = frame.state().get('selection').first().toJSON();
                if (att.type && att.type !== 'image') {
                    window.alert('Chỉ chọn file ảnh.');
                    return;
                }
                if (att.filesizeInBytes && att.filesizeInBytes > DN_MAX_BYTES) {
                    window.alert('Ảnh vượt quá 2 MB. Vui lòng chọn ảnh nhỏ hơn.');
                    return;
                }
                if (att.width && att.height && (att.width > DN_MAX_SIDE || att.height > DN_MAX_SIDE)) {
                    window.alert('Ảnh vượt quá 2000 px (chiều rộng hoặc cao). Vui lòng nén hoặc thu nhỏ.');
                    return;
                }
                var thumb = (att.sizes && att.sizes.thumbnail && att.sizes.thumbnail.url) ? att.sizes.thumbnail.url : att.url;
                var row = '<div class="dn-gallery-row" style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;padding:8px;background:#fff;border:1px solid #ddd;border-radius:4px;">' +
                    '<input type="hidden" name="structured_content[' + itemIndex + '][media_ids][]" value="' + att.id + '" class="dn-gallery-media-id">' +
                    '<img src="' + thumb + '" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:4px;flex-shrink:0;" />' +
                    '<div style="flex:1;min-width:0;"><input type="text" name="structured_content[' + itemIndex + '][media_captions][]" value="" class="regular-text" style="width:100%;" placeholder="Chú thích ảnh (tuỳ chọn)"></div>' +
                    '<button type="button" class="button dn-gallery-remove-row">Xóa ảnh</button></div>';
                $wrap.find('.dn-structured-gallery-rows').append(row);
                dnUpdateGalleryCount($wrap);
            });
            frame.open();
        });
        $(document).on('click', '.dn-gallery-remove-row', function(e) {
            e.preventDefault();
            var $wrap = $(this).closest('.dn-structured-gallery-wrap');
            $(this).closest('.dn-gallery-row').remove();
            dnUpdateGalleryCount($wrap);
        });

        $('.structured-item').each(function() {
            updatePreview($(this));
        });
        setInterval(function() {
            if ($('#structured-content-items').length && $('#structured-content-items').closest('.postbox').is(':visible')) {
                $('.structured-item').each(function() { updatePreview($(this)); });
            }
        }, 1500);
    });
    </script>

    <script>
    jQuery(document).ready(function($) {
        // Prevent duplicate initialization
        if (window.structuredContentInitialized_v2) {
            console.log('Structured content already initialized (v2), skipping...');
            return;
        }
        window.structuredContentInitialized_v2 = true;
        console.log('Initializing structured content (v2)...');

        // Sortable functionality for items
        if ($('#structured-content-items').length && !$('#structured-content-items').hasClass('sortable-initialized')) {
            $('#structured-content-items').sortable({
                items: '.structured-item',
                handle: '.drag-handle',
                cursor: 'move',
                opacity: 0.6
            });
            $('#structured-content-items').addClass('sortable-initialized');
        }

        // Add new structured item
        $(document).on('click', '#add-structured-item', function(e) {
            e.preventDefault();
            // Simple item counting - use length for new index
            var itemCount = $('.structured-item').length;
            // Build HTML step by step to avoid template string issues
            var newItem = '<div class="structured-item" data-index="' + itemCount + '">' +
                '<div class="structured-item-header">' +
                    '<span class="drag-handle" title="Kéo để sắp xếp">☰</span>' +
                    '<strong>Mục ' + (itemCount + 1) + '</strong>' +
                    '<button type="button" class="button remove-item-btn" style="float: right;">Xóa mục</button>' +
                '</div>' +
                '<div class="structured-item-body">' +
                    '<p>' +
                        '<label><strong>Mục lớn (Tiêu đề - hiển thị in đậm):</strong></label><br>' +
                        '<input type="text" name="structured_content[' + itemCount + '][heading]" class="large-text structured-heading" placeholder="Nhập tiêu đề mục lớn...">' +
                    '</p>' +

                    '<!-- Content Items Section -->' +
                    '<div class="content-items-wrapper">' +
                        '<label><strong>Mục nội dung nhỏ (Có thể thêm nhiều):</strong></label>' +
                        
                        '<div class="bulk-paste-section" style="background: #e6f7ff; padding: 10px; margin-bottom: 15px; border: 1px dashed #1890ff; border-radius: 4px;">' +
                            '<div style="display:flex; justify-content:space-between; align-items:center;">' +
                                '<label style="font-weight: 600; color: #0050b3;">✨ Thêm nhanh nhiều nội dung:</label>' +
                                '<button type="button" class="button button-small toggle-bulk-paste" onclick="jQuery(this).closest(\'.bulk-paste-section\').find(\'.bulk-paste-content-wrap\').slideToggle();">Mở/Đóng</button>' +
                            '</div>' +
                            '<div class="bulk-paste-content-wrap" style="display:none; margin-top:5px;">' +
                                '<textarea class="bulk-paste-content" rows="4" placeholder="Dán nội dung dài vào đây. 2 lần enter (dòng trắng) sẽ tự tách thành các ô nội dung nhỏ..." style="width: 100%; margin-bottom: 5px;"></textarea>' +
                                '<button type="button" class="button button-secondary bulk-paste-btn" data-item-index="' + itemCount + '">Tách và thêm tự động</button>' +
                                '<p style="font-size: 12px; color: #666; margin-top: 5px; margin-bottom: 0;">(Nội dung cũ sẽ được giữ nguyên, nội dung mới sẽ thêm vào cuối)</p>' +
                            '</div>' +
                        '</div>' +

                        '<div class="content-items-list" data-item-index="' + itemCount + '">' +
                            '<div class="content-item" data-content-index="0">' +
                                '<textarea name="structured_content[' + itemCount + '][content_items][0][text]" class="large-text structured-content-item" rows="6" style="min-height: 120px; line-height: 1.5; margin-bottom: 10px;" placeholder="Nhập nội dung chi tiết..."></textarea>' +
                                '<div class="content-item-images-section" style="background: #f0f8ff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                                    '<label style="font-weight: bold; color: #007cba;">🖼️ Hình ảnh cho nội dung này:</label>' +
                                    '<div class="content-item-images-container sortable-initialized" id="content-images-' + itemCount + '-0" style="min-height: 50px; display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>' +
                                    '<button type="button" class="button upload-content-images-btn" data-item-index="' + itemCount + '" data-content-index="0">📁 Thêm hình</button>' +
                                '</div>' +
                                '<button type="button" class="button remove-content-item-btn" style="margin-bottom: 15px;">Xóa nội dung này</button>' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" class="button add-content-item-btn" data-item-index="' + itemCount + '">+ Thêm nội dung nhỏ</button>' +
                    '</div>' +
                    '<div class="structured-images-section" style="background: #f0f8ff; border: 2px solid #007cba; padding: 15px; margin: 15px 0;">' +
                        '<label style="color: #007cba; font-weight: bold;"><strong>🖼️ Ảnh/Video (có thể thêm nhiều):</strong></label>' +
                        '<div class="images-container sortable-initialized" id="images-container-' + itemCount + '" style="min-height: 60px; border: 2px dashed #007cba; padding: 10px; margin: 10px 0; background: white;"></div>' +
                        '<p>' +
                            '<button type="button" class="button button-primary upload-images-btn" data-item-index="' + itemCount + '">📁 Thêm ảnh/video</button>' +
                            '<span class="description" style="margin-left: 10px; color: #666;">Chọn nhiều files cùng lúc. Kéo thả để sắp xếp thứ tự.</span>' +
                        '</p>' +
                    '</div>' +
                '</div>' +
                '<div class="structured-item-preview">' +
                    '<strong>Preview:</strong>' +
                    '<div class="preview-content">' +
                        '<strong style="font-size: 18px; color: #333;">(Chưa có tiêu đề)</strong>' +
                        '<div class="preview-content-body" style="margin-top: 8px; color: #666; line-height: 1.6;">(Chưa có nội dung)</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

            $('#structured-content-items').append(newItem);
            console.log('New item appended with index:', itemCount, 'Total items now:', $('.structured-item').length);
        });
        });

        // Add content item to existing structured item
        $(document).on('click', '.add-content-item-btn', function(e) {
            e.preventDefault();
            var $wrapper = $(this).closest('.content-items-wrapper');
            var $list = $wrapper.find('.content-items-list');
            var itemIndex = $(this).data('item-index');
            var contentCount = $list.find('.content-item').length;
            
            var newContentItem = '<div class="content-item" data-content-index="' + contentCount + '">' +
                '<textarea name="structured_content[' + itemIndex + '][content_items][' + contentCount + '][text]" class="large-text structured-content-item" rows="6" style="min-height: 120px; line-height: 1.5; margin-bottom: 10px;" placeholder="Nhập nội dung chi tiết..."></textarea>' +
                '<div class="content-item-images-section" style="background: #f0f8ff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                    '<label style="font-weight: bold; color: #007cba;">🖼️ Hình ảnh cho nội dung này:</label>' +
                    '<div class="content-item-images-container sortable-initialized" id="content-images-' + itemIndex + '-' + contentCount + '" style="min-height: 50px; display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>' +
                    '<button type="button" class="button upload-content-images-btn" data-item-index="' + itemIndex + '" data-content-index="' + contentCount + '">📁 Thêm hình</button>' +
                '</div>' +
                '<button type="button" class="button remove-content-item-btn" style="margin-bottom: 15px;">Xóa nội dung này</button>' +
            '</div>';
            
            $list.append(newContentItem);
            updatePreview($(this).closest('.structured-item'));
        });
        
        // Remove content item
        $(document).on('click', '.remove-content-item-btn', function(e) {
            e.preventDefault();
            var $contentItem = $(this).closest('.content-item');
            var $list = $contentItem.closest('.content-items-list');
            
            // Don't remove if it's the last one
            if ($list.find('.content-item').length > 1) {
                $contentItem.remove();
                updatePreview($list.closest('.structured-item'));
            } else {
                alert('Phải có ít nhất 1 mục nội dung!');
            }
        });
        
        function formatContentForPreviewV2(content) {
            if (!content || !String(content).trim()) return '';
            var s = String(content).trim();
            if (s.indexOf('<') !== -1) return s;
            var escaped = s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            var paras = escaped.split(/\n\n+/);
            return paras.map(function(p) { return '<p>' + p.replace(/\n/g, '<br />') + '</p>'; }).join('');
        }
        // Live update preview function
        function updatePreview($item) {
            var heading = $item.find('.structured-heading').val() || '(Chưa có tiêu đề)';
            var $previewBody = $item.find('.preview-content-body');
            
            var contentHtml = '';
            var hasContent = false;
            $item.find('.structured-content-item').each(function() {
                var val = $(this).val();
                if (val && val.trim()) {
                    hasContent = true;
                    var formattedVal = formatContentForPreviewV2(val);
                    contentHtml += '<div class="preview-content-item" style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed #ddd;">' + formattedVal + '</div>';
                }
            });
            
            if (!hasContent) {
                contentHtml = '(Chưa có nội dung)';
            }
            
            $item.find('.preview-content strong').first().text(heading);
            $previewBody.html(contentHtml);
        }
        
        // Live update on input
        $(document).on('input', '.structured-content-item', function() {
            updatePreview($(this).closest('.structured-item'));
        });
        $(document).on('input', '.structured-heading', function() {
            updatePreview($(this).closest('.structured-item'));
        });

        // Remove structured item
        $(document).on('click', '.remove-item-btn', function() {
            $(this).closest('.structured-item').remove();
            // Re-number items
            $('.structured-item').each(function(index) {
                $(this).find('strong').first().text('Mục ' + (index + 1));
                $(this).attr('data-index', index);
                // Update form field names
                $(this).find('input, textarea').each(function() {
                    var name = $(this).attr('name');
                    if (name && name.includes('structured_content[')) {
                        var newName = name.replace(/structured_content\[\d+\]/, 'structured_content[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
                // Update button data attributes
                $(this).find('.upload-images-btn').attr('data-item-index', index);
            });
        });

        // Upload images for structured content items
        $(document).on('click', '.upload-images-btn', function(e) {
            e.preventDefault();

            var button = $(this);
            var itemIndex = button.data('item-index');
            var container = $('#images-container-' + itemIndex);

            var mediaUploader = wp.media({
                title: 'Chọn ảnh/video cho mục này',
                button: { text: 'Thêm vào mục' },
                multiple: true,
                library: { type: ['image', 'video'] }
            });

            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toJSON();

                attachments.forEach(function(attachment, index) {
                    var mime_type = attachment.mime;
                    var is_video = mime_type.indexOf('video') === 0;
                    var itemHtml = '';

                    if (is_video) {
                        itemHtml = '<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative; display: inline-block; margin: 5px;">' +
                            '<video style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" controls>' +
                            '<source src="' + attachment.url + '" type="' + mime_type + '">' +
                            '</video>' +
                            '<button type="button" class="remove-gallery-item button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;">×</button>' +
                            '<input type="text" name="structured_content[' + itemIndex + '][image_captions][]" value="" placeholder="Chú thích ảnh..." style="width: 120px; margin-top: 2px; font-size: 11px; padding: 2px;">' +
                            '</div>';
                    } else {
                        itemHtml = '<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative; display: inline-block; margin: 5px;">' +
                            '<img src="' + attachment.sizes.medium.url + '" alt="Ảnh mới" style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" />' +
                            '<button type="button" class="remove-gallery-item button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;">×</button>' +
                            '<input type="text" name="structured_content[' + itemIndex + '][image_captions][]" value="" placeholder="Chú thích ảnh..." style="width: 120px; margin-top: 2px; font-size: 11px; padding: 2px;">' +
                            '</div>';
                    }

                    container.append(itemHtml);
                });

                // Make sortable
                container.sortable({
                    items: '.gallery-item',
                    cursor: 'move'
                });
            });

            mediaUploader.open();
        });

        // Remove gallery item from structured content
        $(document).on('click', '.remove-gallery-item', function(e) {
            e.preventDefault();
            $(this).closest('.gallery-item').remove();
        });

        // Upload images for individual content items
        $(document).on('click', '.upload-content-images-btn', function(e) {
            e.preventDefault();
            var button = $(this);
            var itemIndex = button.data('item-index');
            var contentIndex = button.data('content-index');
            var container = $('#content-images-' + itemIndex + '-' + contentIndex);

            var mediaUploader = wp.media({
                title: 'Chọn ảnh/video cho nội dung này',
                button: { text: 'Thêm vào nội dung' },
                multiple: true,
                library: { type: ['image', 'video'] }
            });

            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toJSON();
                attachments.forEach(function(attachment) {
                    var mime_type = attachment.mime;
                    var is_video = mime_type.indexOf('video') === 0;
                    var thumb_url = is_video ? attachment.url : (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
                    
                    var itemHtml = '<div class="content-image-preview-item" data-attachment-id="' + attachment.id + '" style="position: relative; border: 2px solid #007cba; border-radius: 4px; padding: 5px; background: white;">';
                    if (is_video) {
                        itemHtml += '<video style="width: 100px; height: 100px; object-fit: cover; display: block;"><source src="' + attachment.url + '" type="' + mime_type + '"></video>';
                    } else {
                        itemHtml += '<img src="' + thumb_url + '" style="width: 100px; height: 100px; object-fit: cover; display: block;">';
                    }
                    itemHtml += '<input type="hidden" name="structured_content[' + itemIndex + '][content_items][' + contentIndex + '][images][]" value="' + attachment.id + '">';
                    itemHtml += '<input type="text" name="structured_content[' + itemIndex + '][content_items][' + contentIndex + '][image_captions][]" placeholder="Caption..." class="content-image-caption-input" style="width: 100px; margin-top: 5px; font-size: 11px; padding: 3px;">';
                    itemHtml += '<button type="button" class="remove-content-image-btn" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>';
                    itemHtml += '</div>';
                    
                    container.append(itemHtml);
                });

                // Make sortable
                if (!container.hasClass('ui-sortable')) {
                    container.sortable({ items: '.content-image-preview-item', cursor: 'move' });
                }
            });

            mediaUploader.open();
        });

        // Remove content item image
        $(document).on('click', '.remove-content-image-btn', function(e) {
            e.preventDefault();
            $(this).closest('.content-image-preview-item').remove();
        });

        // Bulk Paste Handler
        $(document).on('click', '.bulk-paste-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var wrapper = btn.closest('.bulk-paste-section').parent(); // content-items-wrapper
            var list = wrapper.find('.content-items-list');
            var itemIndex = btn.data('item-index');
            var content = btn.siblings('.bulk-paste-content').val();
            
            if (!content.trim()) {
                alert('Vui lòng nhập nội dung!');
                return;
            }

            // Split by double newline (paragraphs)
            var parts = content.split(/\n\s*\n/);
            
            var addedCount = 0;
            parts.forEach(function(part) {
                part = part.trim();
                if (part) {
                    var contentCount = list.find('.content-item').length;
                    
                    var newContentItem = '<div class="content-item" data-content-index="' + contentCount + '">' +
                        '<textarea name="structured_content[' + itemIndex + '][content_items][' + contentCount + '][text]" class="large-text structured-content-item" rows="6" style="min-height: 120px; line-height: 1.5; margin-bottom: 10px;" placeholder="Nhập nội dung chi tiết...">' + part + '</textarea>' +
                        '<div class="content-item-images-section" style="background: #f0f8ff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                            '<label style="font-weight: bold; color: #007cba;">🖼️ Hình ảnh cho nội dung này:</label>' +
                            '<div class="content-item-images-container sortable-initialized" id="content-images-' + itemIndex + '-' + contentCount + '" style="min-height: 50px; display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; padding: 10px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>' +
                            '<button type="button" class="button upload-content-images-btn" data-item-index="' + itemIndex + '" data-content-index="' + contentCount + '">📁 Thêm hình</button>' +
                        '</div>' +
                        '<button type="button" class="button remove-content-item-btn" style="margin-bottom: 15px;">Xóa nội dung này</button>' +
                    '</div>';
                    
                    list.append(newContentItem);
                    addedCount++;
                }
            });

            if (addedCount > 0) {
                alert('Đã thêm ' + addedCount + ' mục nội dung!');
                btn.siblings('.bulk-paste-content').val(''); // Clear textarea
                updatePreview(btn.closest('.structured-item'));
            } else {
                alert('Không tìm thấy nội dung hợp lệ nào để thêm.');
            }
        });

    });
    </script>
    <?php
}

// Meta box callback for Tin tức details
function dnttvn_tin_tuc_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_tin_tuc_meta', 'dnttvn_tin_tuc_meta_nonce');
    
    $nguon = get_post_meta($post->ID, '_tin_tuc_nguon', true);
    $tac_gia = get_post_meta($post->ID, '_tin_tuc_tac_gia', true);
    $ngay_dang = get_post_meta($post->ID, '_tin_tuc_ngay_dang', true);
    $thoi_gian_dang = get_post_meta($post->ID, '_tin_tuc_thoi_gian_dang', true);
    $noi_bat = get_post_meta($post->ID, '_tin_tuc_noi_bat', true);
    $hinh_phu = get_post_meta($post->ID, '_tin_tuc_hinh_phu', true);
    if (is_string($hinh_phu)) {
        $decoded = json_decode($hinh_phu, true);
        $hinh_phu_array = is_array($decoded) ? $decoded : array();
    } else {
        $hinh_phu_array = is_array($hinh_phu) ? $hinh_phu : (!empty($hinh_phu) ? array($hinh_phu) : array());
    }
    $hinh_phu_array = array_values(array_filter(array_map('absint', (array) $hinh_phu_array)));
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="tin_tuc_tac_gia">Tác giả</label></th>
            <td>
                <input type="text" id="tin_tuc_tac_gia" name="tin_tuc_tac_gia" value="<?php echo esc_attr($tac_gia); ?>" class="regular-text" />
                <p class="description">Tên tác giả viết bài (nếu có)</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_nguon">Nguồn</label></th>
            <td>
                <input type="url" id="tin_tuc_nguon" name="tin_tuc_nguon" value="<?php echo esc_url($nguon); ?>" class="regular-text" placeholder="https://..." />
                <p class="description">Link nguồn bài viết (nếu lấy từ nơi khác)</p>
            </td>
        </tr>
        <tr>
            <th><label for="excerpt">Mô tả ngắn</label></th>
            <td>
                <textarea id="excerpt" name="excerpt" rows="3" class="large-text" placeholder="Viết mô tả ngắn gọn về bài viết..."><?php echo esc_textarea(get_the_excerpt($post->ID)); ?></textarea>
                <p class="description">Mô tả ngắn sẽ hiển thị ở danh sách tin tức. Nếu để trống sẽ tự động cắt từ nội dung bài viết.</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_thoi_gian_dang">Đăng lên lịch có giờ</label></th>
            <td>
                <input type="datetime-local" id="tin_tuc_thoi_gian_dang" name="tin_tuc_thoi_gian_dang" value="<?php echo esc_attr($thoi_gian_dang); ?>" class="regular-text" />
                <p class="description">Thời gian chính xác để đăng bài viết này. Để trống sẽ đăng ngay lập tức khi nhấn "Xuất bản".</p>
                <p class="description" style="color: #007cba;"><strong>💡 Mẹo:</strong> Bạn có thể lên lịch đăng bài vào thời điểm phù hợp để tối ưu hóa lượng người xem.</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_noi_bat">Tin nổi bật</label></th>
            <td>
                <label>
                    <input type="checkbox" id="tin_tuc_noi_bat" name="tin_tuc_noi_bat" value="1" <?php checked($noi_bat, '1'); ?> />
                    Đánh dấu tin này là tin nổi bật
                </label>
                <p class="description">Tin nổi bật sẽ được hiển thị ưu tiên trên trang chủ và không dùng thêm hình ở mục nội dung nữa</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_hinh_phu">Ảnh/Video phụ</label></th>
            <td>
                <input type="hidden" id="tin_tuc_hinh_phu" name="tin_tuc_hinh_phu" value="<?php echo esc_attr(json_encode($hinh_phu_array)); ?>" />
                <div id="tin_tuc_hinh_phu_gallery" class="hinh-phu-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; min-height: 60px; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9;">
                    <?php foreach ($hinh_phu_array as $index => $attachment_id) :
                        $attachment_id = intval($attachment_id);
                        if ($attachment_id > 0) :
                            $mime_type = get_post_mime_type($attachment_id);
                            $is_video = strpos($mime_type, 'video') === 0;
                            $image_url = $is_video ? wp_get_attachment_url($attachment_id) : wp_get_attachment_image_url($attachment_id, 'medium');
                            if (!$image_url) {
                                $image_url = wp_get_attachment_url($attachment_id);
                            }
                    ?>
                        <div class="gallery-item" data-id="<?php echo $attachment_id; ?>" style="position: relative; display: inline-block;">
                            <?php if ($is_video) : ?>
                                <video style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" controls>
                                    <source src="<?php echo esc_url(wp_get_attachment_url($attachment_id)); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                </video>
                            <?php else : ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="Ảnh phụ" style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" />
                            <?php endif; ?>
                            <button type="button" class="remove-gallery-item button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div>
                    <button type="button" id="upload_tin_tuc_hinh_phu" class="button button-primary">
                        📁 <?php echo empty($hinh_phu_array) ? 'Thêm ảnh/video phụ' : 'Thêm ảnh/video nữa'; ?>
                    </button>
                    <button type="button" id="clear_tin_tuc_hinh_phu" class="button" style="display: <?php echo empty($hinh_phu_array) ? 'none' : 'inline-block'; ?>; margin-left: 5px;">
                        🗑️ Xóa tất cả
                    </button>
                </div>
                <p class="description">Ảnh/video phụ sẽ được hiển thị như gallery ở đầu bài viết. Bấm &quot;Thêm ảnh/video phụ&quot; rồi chọn từng ảnh hoặc video (mỗi lần bấm chọn là thêm một mục; có thể chọn nhiều ảnh/video cùng lúc bằng cách giữ Ctrl/Cmd khi bấm). Có thể kéo thả để sắp xếp thứ tự. Kích thước khuyến nghị: 800x600px cho ảnh.</p>
            </td>
        </tr>
    </table>

    <?php
}

// Meta box callback for Author info (Sidebar)
function dnttvn_tin_tuc_author_meta_box_callback($post) {
    $tac_gia = get_post_meta($post->ID, '_tin_tuc_tac_gia', true);
    $nguon = get_post_meta($post->ID, '_tin_tuc_nguon', true);
    ?>
    <div style="padding: 10px 0;">
        <p><strong>Hướng dẫn:</strong></p>
        <ul style="margin-left: 20px; margin-top: 10px;">
            <li>Nhập <strong>Tiêu đề</strong> rõ ràng, hấp dẫn</li>
            <li>Viết <strong>Nội dung</strong> đầy đủ, có format đẹp</li>
            <li>Thêm <strong>Excerpt</strong> (tóm tắt) để hiển thị ở trang chủ</li>
            <li>Chọn <strong>Featured Image</strong> (hình ảnh đại diện)</li>
            <li>Chọn <strong>Categories</strong> và <strong>Tags</strong> phù hợp</li>
        </ul>
        <?php if ($tac_gia) : ?>
            <p style="margin-top: 15px;"><strong>Tác giả:</strong> <?php echo esc_html($tac_gia); ?></p>
        <?php endif; ?>
        <?php if ($nguon) : ?>
            <p><strong>Nguồn:</strong> <a href="<?php echo esc_url($nguon); ?>" target="_blank">Xem nguồn</a></p>
        <?php endif; ?>
    </div>
    <?php
}

// Save Structured Content meta box data
function dnttvn_save_structured_content($post_id) {
    $post_types = array('tin_tuc', 'cong_dong', 'doanh_nghiep');
    
    if (!isset($_POST['dnttvn_structured_content_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['dnttvn_structured_content_nonce'], 'dnttvn_save_structured_content')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, $post_types)) {
        return;
    }
    
            if (isset($_POST['structured_content']) && is_array($_POST['structured_content'])) {
                $items = array();
                foreach ($_POST['structured_content'] as $item) {
                    if ($post_type === 'doanh_nghiep') {
                        $heading = sanitize_text_field(isset($item['heading']) ? wp_unslash($item['heading']) : '');
                        $content = isset($item['content']) ? wp_kses_post(wp_unslash($item['content'])) : '';
                        $final_images = array();
                        $final_caps   = array();
                        if (!empty($item['media_ids']) && is_array($item['media_ids'])) {
                            $caps_in = isset($item['media_captions']) && is_array($item['media_captions']) ? $item['media_captions'] : array();
                            foreach ($item['media_ids'] as $i => $mid) {
                                $mid = absint($mid);
                                if ($mid <= 0 || ! dnttvn_doanh_nghiep_validate_section_image_id($mid)) {
                                    continue;
                                }
                                $final_images[] = $mid;
                                $final_caps[]   = isset($caps_in[ $i ]) ? sanitize_text_field(wp_unslash($caps_in[ $i ])) : '';
                                if (count($final_images) >= 5) {
                                    break;
                                }
                            }
                        }
                        if (empty($final_images) && ! empty($item['media_id'])) {
                            $mid = absint($item['media_id']);
                            if ($mid && dnttvn_doanh_nghiep_validate_section_image_id($mid)) {
                                $final_images = array($mid);
                                $final_caps   = array(sanitize_text_field(isset($item['media_caption']) ? wp_unslash($item['media_caption']) : ''));
                            }
                        }
                        while (count($final_caps) < count($final_images)) {
                            $final_caps[] = '';
                        }
                        $final_caps = array_slice($final_caps, 0, count($final_images));

                        $content_items_dn = array();
                        if ($content !== '' || ! empty($final_images)) {
                            $content_items_dn[] = array(
                                'text'             => $content,
                                'images'           => $final_images,
                                'image_captions'   => $final_caps,
                            );
                        }
                        if ($heading !== '' || ! empty($content_items_dn)) {
                            $items[] = array(
                                'heading'        => $heading,
                                'content'        => $content,
                                'content_items'  => $content_items_dn,
                                'images'         => array(),
                                'image_captions' => array(),
                            );
                        }
                        continue;
                    }

                    $heading = sanitize_text_field(isset($item['heading']) ? wp_unslash($item['heading']) : '');
                    $content = isset($item['content']) ? wp_kses_post(wp_unslash($item['content'])) : '';
                    $media_id = isset($item['media_id']) ? absint($item['media_id']) : 0;
                    if ($media_id && !get_post($media_id)) {
                        $media_id = 0;
                    }
                    $media_caption = sanitize_text_field(isset($item['media_caption']) ? wp_unslash($item['media_caption']) : '');

                    $images = array();
                    $image_captions = array();
                    $content_items = array();

                    $has_new_fields = ($heading !== '' || $content !== '' || $media_id > 0 || $media_caption !== '');

                    if ($has_new_fields) {
                            if ($content !== '') {
                                $content_items[] = array(
                                    'text' => $content,
                                    'images' => array(),
                                    'image_captions' => array()
                                );
                            }
                            if ($media_id) {
                                $images[] = $media_id;
                                $image_captions[] = $media_caption;
                            }
                    } else {
                        // Legacy fallback: preserve old structure if submitted
                        if (isset($item['images']) && is_array($item['images'])) {
                            foreach ($item['images'] as $image_id) {
                                $image_id = absint($image_id);
                                if ($image_id > 0 && get_post($image_id)) {
                                    $images[] = $image_id;
                                }
                            }
                        }

                        if (isset($item['image_captions']) && is_array($item['image_captions'])) {
                            foreach ($item['image_captions'] as $caption) {
                                $image_captions[] = sanitize_text_field($caption);
                            }
                        }

                        if (isset($item['content_items']) && is_array($item['content_items'])) {
                            foreach ($item['content_items'] as $content_data) {
                                $content_obj = array(
                                    'text' => '',
                                    'images' => array(),
                                    'image_captions' => array()
                                );

                                if (is_string($content_data)) {
                                    $content_obj['text'] = wp_kses_post(wp_unslash($content_data));
                                } else {
                                    $content_obj['text'] = wp_kses_post(wp_unslash($content_data['text'] ?? ''));
                                    if (isset($content_data['images']) && is_array($content_data['images'])) {
                                        foreach ($content_data['images'] as $img_id) {
                                            $img_id = absint($img_id);
                                            if ($img_id > 0 && get_post($img_id)) {
                                                $content_obj['images'][] = $img_id;
                                            }
                                        }
                                    }
                                    if (isset($content_data['image_captions']) && is_array($content_data['image_captions'])) {
                                        foreach ($content_data['image_captions'] as $caption) {
                                            $content_obj['image_captions'][] = sanitize_text_field($caption);
                                        }
                                    }
                                }

                                if (!empty(trim($content_obj['text'])) || !empty($content_obj['images'])) {
                                    $content_items[] = $content_obj;
                                }
                            }
                        }
                    }

                    if ($heading !== '' || $content !== '' || $media_id > 0 || !empty($content_items) || !empty($images)) {
                        $items[] = array(
                            'heading' => $heading,
                            'content_items' => $content_items,
                            'images' => $images,
                            'image_captions' => $image_captions
                        );
                    }
                }
                update_post_meta($post_id, '_structured_content', $items);
            } else {
                delete_post_meta($post_id, '_structured_content');
            }
}
add_action('save_post', 'dnttvn_save_structured_content');

// Meta box callback for Cộng đồng details
function dnttvn_cong_dong_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_cong_dong_meta', 'dnttvn_cong_dong_meta_nonce');

    $mo_ta_ngan = get_post_meta($post->ID, '_cong_dong_mo_ta_ngan', true);
    $noi_bat    = get_post_meta($post->ID, '_cong_dong_noi_bat', true);
    $thoi_gian_dang = get_post_meta($post->ID, '_cong_dong_thoi_gian_dang', true);
    $hinh_phu = get_post_meta($post->ID, '_cong_dong_hinh_phu', true);
    if (is_string($hinh_phu)) {
        $decoded = json_decode($hinh_phu, true);
        $hinh_phu_array = is_array($decoded) ? $decoded : array();
    } else {
        $hinh_phu_array = is_array($hinh_phu) ? $hinh_phu : (!empty($hinh_phu) ? array($hinh_phu) : array());
    }
    $hinh_phu_array = array_values(array_filter(array_map('absint', (array) $hinh_phu_array)));
    ?>
    <p>
        <label for="cong_dong_thoi_gian_dang"><strong>Đăng lên lịch có giờ</strong></label><br>
        <input type="datetime-local" id="cong_dong_thoi_gian_dang" name="cong_dong_thoi_gian_dang"
               value="<?php echo esc_attr($thoi_gian_dang); ?>" class="widefat" />
        <span class="description">Thời gian chính xác để đăng bài viết này. Để trống sẽ đăng ngay lập tức.</span>
        <br><span class="description" style="color: #007cba;"><strong>💡 Mẹo:</strong> Lên lịch đăng vào giờ vàng để tăng tương tác.</span>
    </p>

    <p>
        <label for="cong_dong_mo_ta_ngan"><strong>Mô tả ngắn</strong></label><br>
        <textarea id="cong_dong_mo_ta_ngan" name="cong_dong_mo_ta_ngan" rows="3" class="widefat"
                  placeholder="Mô tả ngắn sẽ hiển thị ở danh sách Cộng đồng..."><?php echo esc_textarea($mo_ta_ngan); ?></textarea>
    </p>

    <p>
        <label>
            <input type="checkbox" id="cong_dong_noi_bat" name="cong_dong_noi_bat" value="1" <?php checked($noi_bat, '1'); ?> />
            <strong>Đánh dấu bài viết này là Cộng đồng nổi bật</strong>
        </label>
        <br><span class="description">Bài viết nổi bật sẽ được ưu tiên hiển thị và không dùng thêm hình ở mục nội dung nữa</span>
    </p>

    <p>
        <label for="cong_dong_hinh_phu"><strong>Ảnh/Video phụ</strong></label><br>
        <input type="hidden" id="cong_dong_hinh_phu" name="cong_dong_hinh_phu" value="<?php echo esc_attr(json_encode($hinh_phu_array)); ?>" />
        <div id="cong_dong_hinh_phu_gallery" class="hinh-phu-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; min-height: 60px; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9;">
            <?php foreach ($hinh_phu_array as $index => $attachment_id) :
                $attachment_id = intval($attachment_id);
                if ($attachment_id > 0) :
                    $mime_type = get_post_mime_type($attachment_id);
                    $is_video = strpos($mime_type, 'video') === 0;
                    $image_url = $is_video ? wp_get_attachment_url($attachment_id) : wp_get_attachment_image_url($attachment_id, 'medium');
                    if (!$image_url) {
                        $image_url = wp_get_attachment_url($attachment_id);
                    }
            ?>
                <div class="gallery-item" data-id="<?php echo $attachment_id; ?>" style="position: relative; display: inline-block;">
                    <?php if ($is_video) : ?>
                        <video style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" controls>
                            <source src="<?php echo esc_url(wp_get_attachment_url($attachment_id)); ?>" type="<?php echo esc_attr($mime_type); ?>">
                        </video>
                    <?php else : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="Ảnh phụ" style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" />
                    <?php endif; ?>
                    <button type="button" class="remove-gallery-item button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>
                </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div>
            <button type="button" id="upload_cong_dong_hinh_phu" class="button button-primary">
                📁 <?php echo empty($hinh_phu_array) ? 'Thêm ảnh/video phụ' : 'Thêm ảnh/video nữa'; ?>
            </button>
            <button type="button" id="clear_cong_dong_hinh_phu" class="button" style="display: <?php echo empty($hinh_phu_array) ? 'none' : 'inline-block'; ?>; margin-left: 5px;">
                🗑️ Xóa tất cả
            </button>
        </div>
        <span class="description">Ảnh/video phụ sẽ được hiển thị như gallery ở đầu bài viết. Có thể kéo thả để sắp xếp thứ tự. Hỗ trợ cả ảnh và video. Kích thước khuyến nghị: 800x600px cho ảnh.</span>
    </p>

    <?php
}

// Save Cộng đồng meta box data
function dnttvn_save_cong_dong_meta($post_id) {
    if (!isset($_POST['dnttvn_cong_dong_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['dnttvn_cong_dong_meta_nonce'], 'dnttvn_save_cong_dong_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'cong_dong') {
        return;
    }

    if (isset($_POST['cong_dong_mo_ta_ngan'])) {
        update_post_meta($post_id, '_cong_dong_mo_ta_ngan', sanitize_text_field($_POST['cong_dong_mo_ta_ngan']));
    }

    // Save new datetime field
    if (isset($_POST['cong_dong_thoi_gian_dang'])) {
        $scheduled_time = sanitize_text_field($_POST['cong_dong_thoi_gian_dang']);
        update_post_meta($post_id, '_cong_dong_thoi_gian_dang', $scheduled_time);

        // Handle scheduling if time is set and in the future
        if (!empty($scheduled_time)) {
            $scheduled_timestamp = strtotime($scheduled_time);
            $current_timestamp = current_time('timestamp');

            if ($scheduled_timestamp > $current_timestamp) {
                // Schedule the post
                wp_schedule_single_event($scheduled_timestamp, 'publish_future_post', array($post_id));

                // Set post status to future if not already published
                if (get_post_status($post_id) !== 'publish') {
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_status' => 'future',
                        'post_date' => date('Y-m-d H:i:s', $scheduled_timestamp),
                        'post_date_gmt' => gmdate('Y-m-d H:i:s', $scheduled_timestamp)
                    ));
                }
            }
        }
    }

    if (isset($_POST['cong_dong_noi_bat'])) {
        update_post_meta($post_id, '_cong_dong_noi_bat', '1');
    } else {
        delete_post_meta($post_id, '_cong_dong_noi_bat');
    }

    // Save hình phụ (gallery)
    if (isset($_POST['cong_dong_hinh_phu'])) {
        $hinh_phu_json = sanitize_text_field($_POST['cong_dong_hinh_phu']);
        $hinh_phu_array = json_decode($hinh_phu_json, true);

        if (is_array($hinh_phu_array) && !empty($hinh_phu_array)) {
            // Validate and sanitize attachment IDs
            $valid_ids = array();
            foreach ($hinh_phu_array as $id) {
                $id = absint($id);
                if ($id > 0 && get_post($id)) {
                    $valid_ids[] = $id;
                }
            }
            update_post_meta($post_id, '_cong_dong_hinh_phu', $valid_ids);
        } else {
            delete_post_meta($post_id, '_cong_dong_hinh_phu');
        }
    }
}
add_action('save_post', 'dnttvn_save_cong_dong_meta');

// Save Tin tức meta box data
function dnttvn_save_tin_tuc_meta($post_id) {
    if (!isset($_POST['dnttvn_tin_tuc_meta_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['dnttvn_tin_tuc_meta_nonce'], 'dnttvn_save_tin_tuc_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (get_post_type($post_id) != 'tin_tuc') {
        return;
    }
    
    if (isset($_POST['tin_tuc_tac_gia'])) {
        update_post_meta($post_id, '_tin_tuc_tac_gia', sanitize_text_field($_POST['tin_tuc_tac_gia']));
    }
    
    if (isset($_POST['tin_tuc_nguon'])) {
        update_post_meta($post_id, '_tin_tuc_nguon', esc_url_raw($_POST['tin_tuc_nguon']));
    }
    
    // Save new datetime field
    if (isset($_POST['tin_tuc_thoi_gian_dang'])) {
        $scheduled_time = sanitize_text_field($_POST['tin_tuc_thoi_gian_dang']);
        update_post_meta($post_id, '_tin_tuc_thoi_gian_dang', $scheduled_time);

        // Handle scheduling if time is set and in the future
        if (!empty($scheduled_time)) {
            $scheduled_timestamp = strtotime($scheduled_time);
            $current_timestamp = current_time('timestamp');

            if ($scheduled_timestamp > $current_timestamp) {
                // Schedule the post
                wp_schedule_single_event($scheduled_timestamp, 'publish_future_post', array($post_id));

                // Set post status to future if not already published
                if (get_post_status($post_id) !== 'publish') {
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_status' => 'future',
                        'post_date' => date('Y-m-d H:i:s', $scheduled_timestamp),
                        'post_date_gmt' => gmdate('Y-m-d H:i:s', $scheduled_timestamp)
                    ));
                }
            }
        }
    }
    
    if (isset($_POST['tin_tuc_noi_bat'])) {
        update_post_meta($post_id, '_tin_tuc_noi_bat', '1');
    } else {
        delete_post_meta($post_id, '_tin_tuc_noi_bat');
    }

    // Save hình phụ (gallery)
    if (isset($_POST['tin_tuc_hinh_phu'])) {
        $hinh_phu_json = sanitize_text_field($_POST['tin_tuc_hinh_phu']);
        $hinh_phu_array = json_decode($hinh_phu_json, true);

        if (is_array($hinh_phu_array) && !empty($hinh_phu_array)) {
            // Validate and sanitize attachment IDs
            $valid_ids = array();
            foreach ($hinh_phu_array as $id) {
                $id = absint($id);
                if ($id > 0 && get_post($id)) {
                    $valid_ids[] = $id;
                }
            }
            update_post_meta($post_id, '_tin_tuc_hinh_phu', $valid_ids);
        } else {
            delete_post_meta($post_id, '_tin_tuc_hinh_phu');
        }
    }
}
add_action('save_post', 'dnttvn_save_tin_tuc_meta');

/**
 * Đăng bài theo lịch đã thiết lập: khi đến giờ đăng, chuyển bài tin_tuc/cong_dong từ future sang publish.
 */
function dnttvn_publish_scheduled_post($post_id) {
    $post = get_post($post_id);
    if (!$post || !in_array($post->post_type, array('tin_tuc', 'cong_dong'))) {
        return;
    }
    if (get_post_status($post_id) === 'future') {
        wp_publish_post($post_id);
    }
}
add_action('publish_future_post', 'dnttvn_publish_scheduled_post', 10, 1);

// Enhance editor for Tin tức
function dnttvn_enhance_tin_tuc_editor($settings, $editor_id) {
    if ($editor_id == 'content' && get_current_screen()->post_type == 'tin_tuc') {
        // Enable full toolbar
        $settings['toolbar1'] = 'bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv';
        $settings['toolbar2'] = 'formatselect,fontselect,fontsizeselect,forecolor,backcolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help';
        
        // Enable more features
        $settings['wordpress_adv_hidden'] = false;
        $settings['paste_as_text'] = false;
        $settings['wpautop'] = true;
        $settings['media_buttons'] = true;
    }
    return $settings;
}
add_filter('tiny_mce_before_init', 'dnttvn_enhance_tin_tuc_editor', 10, 2);

// Add admin styles for Tin tức and Cộng đồng editor
function dnttvn_tin_tuc_admin_styles() {
    global $post_type;
    if (in_array($post_type, array('tin_tuc', 'cong_dong', 'doanh_nghiep'))) {
        ?>
        <style>
            /* Improve editor area */
            #tin_tuc_details .inside,
            #cong_dong_structured_content .inside,
            #doanh_nghiep_structured_content .inside {
                padding: 15px;
            }
            
            #tin_tuc_details .form-table th {
                width: 150px;
                font-weight: 600;
            }
            
            #tin_tuc_author_info .inside {
                background: #f9f9f9;
                border-left: 4px solid #667eea;
            }
            
            /* Highlight important fields */
            .postbox#tin_tuc_details,
            .postbox#tin_tuc_structured_content,
            .postbox#cong_dong_structured_content,
            .postbox#doanh_nghiep_structured_content {
                border-left: 4px solid #667eea;
            }
            
            /* Better form styling */
            #tin_tuc_details input[type="text"],
            #tin_tuc_details input[type="url"],
            #tin_tuc_details input[type="date"] {
                width: 100%;
                max-width: 500px;
            }
            
            /* Checkbox styling */
            #tin_tuc_details input[type="checkbox"] {
                margin-right: 8px;
            }
            
            /* Structured content styles */
            .dnttvn-structured-content-wrapper {
                padding: 15px;
            }
            
            .structured-content-items {
                margin-bottom: 20px;
            }
            
            .structured-item {
                background: #fff;
                border: 2px solid #e5e5e5;
                border-radius: 6px;
                margin-bottom: 15px;
                padding: 15px;
                position: relative;
                transition: all 0.3s ease;
            }
            
            .structured-item:hover {
                border-color: #667eea;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
            }
            
            .structured-item.ui-sortable-helper {
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            }
            
            .structured-item-header {
                background: #f7f7f7;
                padding: 10px 15px;
                margin: -15px -15px 15px -15px;
                border-bottom: 1px solid #e5e5e5;
                border-radius: 6px 6px 0 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .drag-handle {
                cursor: move;
                color: #666;
                font-size: 18px;
                user-select: none;
            }
            
            .structured-item-body {
                margin-bottom: 15px;
            }
            
            .structured-item-body label {
                display: block;
                margin-bottom: 5px;
                font-weight: 600;
            }
            
            .structured-item-body input[type="text"],
            .structured-item-body textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            /* Cho phép xuống hàng trong ô Mục nội dung */
            .structured-item-body textarea.structured-content-item {
                white-space: pre-wrap !important;
                min-height: 100px;
                resize: vertical;
            }
            
            .structured-item-preview {
                background: #f9f9f9;
                border: 1px solid #e5e5e5;
                border-radius: 4px;
                padding: 15px;
                margin-top: 15px;
            }
            
            .structured-item-preview strong {
                display: block;
                margin-bottom: 10px;
                color: #333;
            }
            
            .preview-content {
                background: #fff;
                padding: 15px;
                border-radius: 4px;
                border-left: 3px solid #667eea;
            }
        .preview-content .preview-content-body {
            word-break: break-word;
            display: block;
            line-height: 1.6;
            margin-top: 10px;
            padding-top: 5px;
        }
        .preview-content .preview-content-body p { margin: 0 0 0.75em 0; }
        .preview-content .preview-content-body p:last-child { margin-bottom: 0; }
            
            .remove-item-btn {
                margin-left: auto;
            }
            
            #add-structured-item {
                margin-top: 10px;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'dnttvn_tin_tuc_admin_styles');

// Register Custom Post Type: Doanh nghiệp
function dnttvn_register_doanh_nghiep_post_type() {
    $labels = array(
        'name'                  => 'Doanh nghiệp',
        'singular_name'         => 'Doanh nghiệp',
        'menu_name'             => 'Doanh nghiệp',
        'name_admin_bar'        => 'Doanh nghiệp',
        'archives'              => 'Danh sách Doanh nghiệp',
        'attributes'            => 'Thuộc tính Doanh nghiệp',
        'parent_item_colon'     => 'Doanh nghiệp cha:',
        'all_items'             => 'Tất cả Doanh nghiệp',
        'add_new_item'          => 'Thêm Doanh nghiệp mới',
        'add_new'               => 'Thêm mới',
        'new_item'              => 'Doanh nghiệp mới',
        'edit_item'             => 'Chỉnh sửa Doanh nghiệp',
        'update_item'           => 'Cập nhật Doanh nghiệp',
        'view_item'             => 'Xem Doanh nghiệp',
        'view_items'            => 'Xem Doanh nghiệp',
        'search_items'          => 'Tìm kiếm Doanh nghiệp',
        'not_found'             => 'Không tìm thấy',
        'not_found_in_trash'    => 'Không tìm thấy trong thùng rác',
        'featured_image'        => 'Hình ảnh đại diện',
        'set_featured_image'    => 'Đặt hình ảnh đại diện',
        'remove_featured_image' => 'Xóa hình ảnh đại diện',
        'use_featured_image'    => 'Sử dụng làm hình ảnh đại diện',
        'insert_into_item'      => 'Chèn vào Doanh nghiệp',
        'uploaded_to_this_item' => 'Tải lên Doanh nghiệp này',
        'items_list'            => 'Danh sách Doanh nghiệp',
        'items_list_navigation' => 'Điều hướng danh sách Doanh nghiệp',
        'filter_items_list'     => 'Lọc danh sách Doanh nghiệp',
    );
    $args = array(
        'label'                 => 'Doanh nghiệp',
        'description'           => 'Quản lý danh sách doanh nghiệp',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('nganh_hang', 'khu_vuc'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-building',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'       => 'doanh_nghiep',
            'with_front' => false
        ),
    );
    register_post_type('doanh_nghiep', $args);
}
add_action('init', 'dnttvn_register_doanh_nghiep_post_type', 0);

// Add custom rewrite rules for doanh_nghiep to use ID instead of slug
function dnttvn_add_doanh_nghiep_rewrite_rules() {
    add_rewrite_rule(
        '^doanh_nghiep/([0-9]+)/?$',
        'index.php?post_type=doanh_nghiep&p=$matches[1]',
        'top'
    );
}
add_action('init', 'dnttvn_add_doanh_nghiep_rewrite_rules');

// Modify the permalink for doanh_nghiep posts to use trang chi tiết
function dnttvn_doanh_nghiep_permalink($permalink, $post) {
    if ($post->post_type === 'doanh_nghiep') {
        return home_url('/trang-doanh-nghiep-chi-tiet/?post_id=' . $post->ID);
    }
    return $permalink;
}
add_filter('post_type_link', 'dnttvn_doanh_nghiep_permalink', 10, 2);

// Register Custom Post Type: Cộng đồng
function dnttvn_register_cong_dong_post_type() {
    $labels = array(
        'name'                  => 'Cộng đồng',
        'singular_name'         => 'Cộng đồng',
        'menu_name'             => 'Cộng đồng',
        'name_admin_bar'        => 'Cộng đồng',
        'archives'              => 'Danh sách Cộng đồng',
        'attributes'            => 'Thuộc tính Cộng đồng',
        'parent_item_colon'     => 'Cộng đồng cha:',
        'all_items'             => 'Tất cả Cộng đồng',
        'add_new_item'          => 'Thêm Cộng đồng mới',
        'add_new'               => 'Thêm mới',
        'new_item'              => 'Cộng đồng mới',
        'edit_item'             => 'Chỉnh sửa Cộng đồng',
        'update_item'           => 'Cập nhật Cộng đồng',
        'view_item'             => 'Xem Cộng đồng',
        'view_items'            => 'Xem Cộng đồng',
        'search_items'          => 'Tìm kiếm Cộng đồng',
        'not_found'             => 'Không tìm thấy',
        'not_found_in_trash'    => 'Không tìm thấy trong thùng rác',
        'featured_image'        => 'Hình ảnh đại diện',
        'set_featured_image'    => 'Đặt hình ảnh đại diện',
        'remove_featured_image' => 'Xóa hình ảnh đại diện',
        'use_featured_image'    => 'Sử dụng làm hình ảnh đại diện',
        'insert_into_item'      => 'Chèn vào Cộng đồng',
        'uploaded_to_this_item' => 'Tải lên Cộng đồng này',
        'items_list'            => 'Danh sách Cộng đồng',
        'items_list_navigation' => 'Điều hướng danh sách Cộng đồng',
        'filter_items_list'     => 'Lọc danh sách Cộng đồng',
    );
    $args = array(
        'label'                 => 'Cộng đồng',
        'description'           => 'Quản lý bài viết Cộng đồng',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type('cong_dong', $args);
}
add_action('init', 'dnttvn_register_cong_dong_post_type', 0);

// Add custom rewrite rules for cong_dong to use ID instead of slug
function dnttvn_add_cong_dong_rewrite_rules() {
    add_rewrite_rule(
        '^cong_dong/([0-9]+)/?$',
        'index.php?post_type=cong_dong&p=$matches[1]',
        'top'
    );
}
add_action('init', 'dnttvn_add_cong_dong_rewrite_rules');

// Modify the permalink for cong_dong: dẫn tới trang trang-ve-cong-dong/?post_id=ID
function dnttvn_cong_dong_permalink($permalink, $post) {
    if ($post->post_type === 'cong_dong') {
        return home_url('/trang-ve-cong-dong/?post_id=' . $post->ID);
    }
    return $permalink;
}
add_filter('post_type_link', 'dnttvn_cong_dong_permalink', 10, 2);

// Add custom meta boxes for Cộng đồng
function dnttvn_add_cong_dong_meta_boxes() {
    // 1. Xem trước (ưu tiên cao nhất)
    add_meta_box(
        'cong_dong_live_preview',
        'Xem trước Cộng đồng (gần giống ngoài website)',
        'dnttvn_cong_dong_live_preview_meta_box_callback',
        'cong_dong',
        'normal',
        'high'
    );

    // 2. Thông tin cộng đồng (ưu tiên trung bình)
    add_meta_box(
        'cong_dong_details',
        'Thông tin Cộng đồng',
        'dnttvn_cong_dong_meta_box_callback',
        'cong_dong',
        'normal',
        'default'
    );

    // 3. Thêm mục nội dung (ưu tiên thấp nhất)
    add_meta_box(
        'cong_dong_structured_content',
        'Thêm mục nội dung',
        'dnttvn_structured_content_meta_box_callback',
        'cong_dong',
        'normal',
        'low'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_cong_dong_meta_boxes');

// Live preview meta box for Cộng đồng
function dnttvn_cong_dong_live_preview_meta_box_callback($post) {
    ?>
    <div class="dnttvn-live-preview-box dnttvn-live-preview-cong-dong">
        <p style="margin-top:0; margin-bottom:10px; color:#555;">
            Xem nhanh cách bài Cộng đồng sẽ hiển thị ở trang chi tiết (demo).
        </p>
        <div class="dnttvn-preview-card">
            <h3 id="dnttvn-cong-dong-preview-title" class="dnttvn-preview-title" style="font-size:20px; margin:0 0 6px; color:#06202e;">
                <?php echo esc_html(get_the_title($post)); ?>
            </h3>
            <p class="dnttvn-preview-meta" style="font-size:12px; color:#999; margin:0 0 10px;">
                <strong>Ngày đăng:</strong> <span><?php echo esc_html(get_the_date('d/m/Y', $post)); ?></span>
            </p>
            <div id="dnttvn-cong-dong-preview-excerpt" class="dnttvn-preview-excerpt" style="font-size:14px; color:#555; line-height:1.6;">
                <?php
                $excerpt = wp_trim_words(strip_shortcodes($post->post_content), 40, '...');
                echo esc_html($excerpt);
                ?>
            </div>
        </div>
    </div>
    <?php
}

// Register Custom Taxonomies for Doanh nghiệp
function dnttvn_register_doanh_nghiep_taxonomies() {
    // Taxonomy: Ngành hàng
    register_taxonomy('nganh_hang', array('doanh_nghiep'), array(
        'hierarchical'          => true,
        'labels'                => array(
            'name'              => 'Ngành hàng',
            'singular_name'     => 'Ngành hàng',
            'search_items'      => 'Tìm kiếm Ngành hàng',
            'all_items'         => 'Tất cả Ngành hàng',
            'parent_item'       => 'Ngành hàng cha',
            'parent_item_colon' => 'Ngành hàng cha:',
            'edit_item'         => 'Chỉnh sửa Ngành hàng',
            'update_item'       => 'Cập nhật Ngành hàng',
            'add_new_item'      => 'Thêm Ngành hàng mới',
            'new_item_name'     => 'Tên Ngành hàng mới',
            'menu_name'         => 'Ngành hàng',
        ),
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'nganh-hang'),
        'show_in_rest'          => true,
    ));

    // Taxonomy: Khu vực
    register_taxonomy('khu_vuc', array('doanh_nghiep'), array(
        'hierarchical'          => true,
        'labels'                => array(
            'name'              => 'Khu vực',
            'singular_name'     => 'Khu vực',
            'search_items'      => 'Tìm kiếm Khu vực',
            'all_items'         => 'Tất cả Khu vực',
            'parent_item'       => 'Khu vực cha',
            'parent_item_colon' => 'Khu vực cha:',
            'edit_item'         => 'Chỉnh sửa Khu vực',
            'update_item'       => 'Cập nhật Khu vực',
            'add_new_item'      => 'Thêm Khu vực mới',
            'new_item_name'     => 'Tên Khu vực mới',
            'menu_name'         => 'Khu vực',
        ),
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'khu-vuc'),
        'show_in_rest'          => true,
    ));
}
add_action('init', 'dnttvn_register_doanh_nghiep_taxonomies', 0);

// Seed default Khu vực terms cho Việt Nam (theo danh sách mới, chạy 1 lần cho mỗi version)
function dnttvn_seed_vietnam_khu_vuc_terms() {
    // Dùng key version mới để vẫn chạy lại nếu trước đó đã seed danh sách cũ
    if (get_option('dnttvn_seeded_khu_vuc_terms_v2')) {
        return;
    }

    $regions = array(
        'TP. Hải Phòng',
        'TP. Đà Nẵng',
        'TP. Hồ Chí Minh',
        'TP. Cần Thơ',
        'Bắc Ninh',
        'Phú Thọ',
        'Ninh Bình',
        'Lào Cai',
        'Thái Nguyên',
        'Tuyên Quang',
        'Lâm Đồng',
        'Khánh Hòa',
        'Gia Lai',
        'Đắk Lắk',
        'Quảng Ngãi',
        'Quảng Trị',
        'Đồng Nai',
        'Tây Ninh',
        'An Giang',
        'Đồng Tháp',
        'Vĩnh Long',
        'Cà Mau',
        'Hưng Yên',
    );

    foreach ($regions as $name) {
        if (!term_exists($name, 'khu_vuc')) {
            wp_insert_term($name, 'khu_vuc');
        }
    }

    update_option('dnttvn_seeded_khu_vuc_terms_v2', 1);
}
add_action('init', 'dnttvn_seed_vietnam_khu_vuc_terms', 20);

// Add custom meta boxes for Doanh nghiệp
function dnttvn_add_doanh_nghiep_meta_boxes() {
    // 1. Xem trước (ưu tiên cao nhất)
    add_meta_box(
        'doanh_nghiep_live_preview',
        'Xem trước Doanh nghiệp (gần giống ngoài website)',
        'dnttvn_doanh_nghiep_live_preview_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'high'
    );

    // 2. Thông tin doanh nghiệp (ưu tiên trung bình)
    add_meta_box(
        'doanh_nghiep_details',
        'Thông tin Doanh nghiệp',
        'dnttvn_doanh_nghiep_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'default'
    );

    // 3. Thêm mục nội dung (ưu tiên thấp nhất)
    add_meta_box(
        'doanh_nghiep_structured_content',
        'Thêm mục nội dung',
        'dnttvn_structured_content_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'low'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_doanh_nghiep_meta_boxes');

// Thêm label hướng dẫn cho phần mô tả chi tiết doanh nghiệp (trước editor)
function dnttvn_doanh_nghiep_description_label($post) {
    if (get_post_type($post) !== 'doanh_nghiep') {
        return;
    }
    ?>
    <div class="notice notice-info" style="margin: 10px 0 15px; padding: 10px 15px;">
        <p style="margin:0; font-size:13px;">
            <strong>Mô tả chi tiết doanh nghiệp:</strong> Vui lòng nhập nội dung chi tiết của doanh nghiệp ở ô soạn thảo bên dưới (nội dung này sẽ hiển thị ở phần "Mô tả chi tiết doanh nghiệp" trên trang web).
        </p>
    </div>
    <?php
}
add_action('edit_form_after_title', 'dnttvn_doanh_nghiep_description_label');

// Live preview meta box for Doanh nghiệp
function dnttvn_doanh_nghiep_live_preview_meta_box_callback($post) {
    $nganh_hang = get_post_meta($post->ID, '_nganh_hang', true);
    $khu_vuc    = get_post_meta($post->ID, '_khu_vuc', true);
    $dia_chi    = get_post_meta($post->ID, '_dia_chi', true);
    $dien_thoai = get_post_meta($post->ID, '_dien_thoai', true);
    $email_lh   = get_post_meta($post->ID, '_email_lien_he', true);
    $website_dn = get_post_meta($post->ID, '_website_doanh_nghiep', true);
    $mo_ta_ngan = get_post_meta($post->ID, '_doanh_nghiep_mo_ta_ngan', true);
    ?>
    <div class="dnttvn-live-preview-box dnttvn-live-preview-doanh-nghiep">
        <p style="margin-top:0; margin-bottom:10px; color:#555;">
            Xem nhanh cách thẻ Doanh nghiệp sẽ hiển thị ở trang danh sách/chi tiết (demo).
        </p>
        <div class="dnttvn-preview-card dnttvn-preview-business-card" style="display:flex; gap:15px;">
            <div class="dnttvn-preview-business-left" style="flex:0 0 160px;">
                <div class="dnttvn-preview-logo" style="width:100%; height:120px; background:#f0f0f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#999; font-size:12px;">
                    Logo / Hình chính
                </div>
            </div>
            <div class="dnttvn-preview-business-right" style="flex:1;">
                <h3 id="dnttvn-doanh-nghiep-preview-title" style="font-size:18px; margin:0 0 8px; color:#06202e;">
                    <?php echo esc_html(get_the_title($post)); ?>
                </h3>
                <div class="dnttvn-preview-info" style="font-size:13px; color:#555; line-height:1.6;">
                    <p id="dnttvn-doanh-nghiep-preview-nganh"><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                    <p id="dnttvn-doanh-nghiep-preview-khu-vuc"><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                    <p id="dnttvn-doanh-nghiep-preview-dia-chi"><strong>Địa chỉ:</strong> <?php echo esc_html($dia_chi); ?></p>
                    <p id="dnttvn-doanh-nghiep-preview-lien-he">
                        <?php if ($dien_thoai) : ?>
                            <strong>Điện thoại:</strong> <?php echo esc_html($dien_thoai); ?>&nbsp;&nbsp;
                        <?php endif; ?>
                        <?php if ($email_lh) : ?>
                            <strong>Email:</strong> <?php echo esc_html($email_lh); ?>&nbsp;&nbsp;
                        <?php endif; ?>
                        <?php if ($website_dn) : ?>
                            <strong>Website:</strong> <?php echo esc_html($website_dn); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div id="dnttvn-doanh-nghiep-preview-desc" style="margin-top:8px; font-size:13px; color:#666; line-height:1.6;">
                    <?php
                    if (!empty($mo_ta_ngan)) {
                        $mo_pv = is_string($mo_ta_ngan) ? $mo_ta_ngan : '';
        if (function_exists('dnttvn_dn_reg_trim_to_word_limit')) {
            $mo_lim = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;
            $mo_pv  = dnttvn_dn_reg_trim_to_word_limit($mo_pv, $mo_lim);
        }
                        echo '<strong>Mô tả ngắn:</strong> ' . esc_html($mo_pv);
                    } else {
                        $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_shortcodes($post->post_content), 30, '...');
                        echo esc_html($excerpt);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Meta box callback
function dnttvn_doanh_nghiep_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_doanh_nghiep_meta', 'dnttvn_doanh_nghiep_meta_nonce');
    
    $nganh_hang    = get_post_meta($post->ID, '_nganh_hang', true);
    $khu_vuc       = get_post_meta($post->ID, '_khu_vuc', true);
    $hinh_anh_phu  = get_post_meta($post->ID, '_hinh_anh_phu', true);
    $gallery_value = get_post_meta($post->ID, '_gallery_images', true); // comma-separated IDs
    $dia_chi       = get_post_meta($post->ID, '_dia_chi', true);
    $dien_thoai    = get_post_meta($post->ID, '_dien_thoai', true);
    $email_lh      = get_post_meta($post->ID, '_email_lien_he', true);
    $website_dn    = get_post_meta($post->ID, '_website_doanh_nghiep', true);
    $thong_tin_bs  = get_post_meta($post->ID, '_thong_tin_bo_sung', true);
    $mo_ta_ngan    = get_post_meta($post->ID, '_doanh_nghiep_mo_ta_ngan', true);
    $noi_dung_slider_value = get_post_meta($post->ID, '_noi_dung_slider_images', true);
    $noi_dung_slider_title = get_post_meta($post->ID, '_noi_dung_slider_title', true);
    $noi_dung_slider_text  = get_post_meta($post->ID, '_noi_dung_slider_text', true);

    ?>
    <table class="form-table">
        <tr>
            <th><label for="ten_day_du">Tên doanh nghiệp đầy đủ</label></th>
            <td>
                <?php $ten_day_du = get_post_meta($post->ID, '_ten_day_du', true); ?>
                <input type="text" id="ten_day_du" name="ten_day_du" value="<?php echo esc_attr($ten_day_du); ?>" class="large-text" />
                <p class="description">Tên đầy đủ sẽ hiển thị ở tiêu đề trang chi tiết. Tên ngắn (Post Title) dùng cho các danh sách.</p>
            </td>
        </tr>
        <tr>
            <th><label for="nganh_hang_tax">Ngành hàng</label></th>
            <td>
                <?php
                // Dropdown chọn Ngành hàng (taxonomy) đã tạo sẵn
                $nganh_terms = get_terms(array(
                    'taxonomy'   => 'nganh_hang',
                    'hide_empty' => false,
                ));
                $selected_nganh_terms = wp_get_post_terms($post->ID, 'nganh_hang', array('fields' => 'ids'));
                $selected_nganh_id    = !empty($selected_nganh_terms) ? (int) $selected_nganh_terms[0] : 0;
                ?>
                <select id="nganh_hang_tax" name="nganh_hang_tax" class="regular-text">
                    <option value=""><?php esc_html_e('— Chọn Ngành hàng đã tạo —', 'dnttvn'); ?></option>
                    <?php if (!is_wp_error($nganh_terms) && !empty($nganh_terms)) : ?>
                        <?php foreach ($nganh_terms as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($selected_nganh_id, $term->term_id); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="description">Chọn Ngành hàng đã tạo ở phần Quản lý Ngành hàng, hoặc nhập tên mới bên dưới.</p>
                <p style="margin-top: 8px;">
                    <label for="nganh_hang_tax_new"><strong>Thêm Ngành hàng mới (nếu chưa có trong danh sách):</strong></label><br>
                    <input type="text" id="nganh_hang_tax_new" name="nganh_hang_tax_new" class="regular-text" value="" placeholder="Nhập tên Ngành hàng mới..." />
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="khu_vuc_tax">Khu vực</label></th>
            <td>
                <?php
                // Dropdown chọn Khu vực (taxonomy) đã tạo sẵn
                $khu_vuc_terms = get_terms(array(
                    'taxonomy'   => 'khu_vuc',
                    'hide_empty' => false,
                ));
                $selected_khu_vuc_terms = wp_get_post_terms($post->ID, 'khu_vuc', array('fields' => 'ids'));
                $selected_khu_vuc_id    = !empty($selected_khu_vuc_terms) ? (int) $selected_khu_vuc_terms[0] : 0;
                ?>
                <select id="khu_vuc_tax" name="khu_vuc_tax" class="regular-text">
                    <option value=""><?php esc_html_e('— Chọn Khu vực đã tạo —', 'dnttvn'); ?></option>
                    <?php if (!is_wp_error($khu_vuc_terms) && !empty($khu_vuc_terms)) : ?>
                        <?php foreach ($khu_vuc_terms as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($selected_khu_vuc_id, $term->term_id); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="description">Chọn Khu vực đã tạo ở phần Quản lý Khu vực, hoặc nhập tên mới bên dưới.</p>
                <p style="margin-top: 8px;">
                    <label for="khu_vuc_tax_new"><strong>Thêm Khu vực mới (nếu chưa có trong danh sách):</strong></label><br>
                    <input type="text" id="khu_vuc_tax_new" name="khu_vuc_tax_new" class="regular-text" value="" placeholder="Nhập tên Khu vực mới..." />
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="dia_chi">Địa chỉ</label></th>
            <td>
                <input type="text" id="dia_chi" name="dia_chi" value="<?php echo esc_attr($dia_chi); ?>" class="regular-text" />
                <p class="description">Ví dụ: Số nhà, đường, quận/huyện, thành phố.</p>
            </td>
        </tr>
        <tr>
            <th><label for="dien_thoai">Điện thoại</label></th>
            <td>
                <input type="text" id="dien_thoai" name="dien_thoai" value="<?php echo esc_attr($dien_thoai); ?>" class="regular-text" />
                <p class="description">Ví dụ: 0909 000 000.</p>
            </td>
        </tr>
        <tr>
            <th><label for="email_lien_he">Email liên hệ</label></th>
            <td>
                <input type="email" id="email_lien_he" name="email_lien_he" value="<?php echo esc_attr($email_lh); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="website_doanh_nghiep">Website doanh nghiệp</label></th>
            <td>
                <input type="url" id="website_doanh_nghiep" name="website_doanh_nghiep" value="<?php echo esc_attr($website_dn); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="hinh_anh_phu">Hình ảnh phụ</label></th>
            <td>
                <input type="text" id="hinh_anh_phu" name="hinh_anh_phu" value="<?php echo esc_attr($hinh_anh_phu); ?>" class="regular-text" />
                <button type="button" class="button" id="upload_hinh_anh_phu">Chọn hình ảnh</button>
                <button type="button" class="button" id="remove_hinh_anh_phu" style="color:#a00;<?php echo empty($hinh_anh_phu) ? 'display:none;' : ''; ?>">Xóa hình phụ</button>
                <p class="description">
                    <strong>Lưu ý:</strong><br>
                    • <strong>Hình chính:</strong> Sử dụng "Featured Image" (Hình ảnh đại diện) ở sidebar bên phải - đây là logo/ảnh chính của doanh nghiệp<br>
                    • <strong>Hình ảnh phụ:</strong> Ảnh bổ sung hiển thị ở phần mô tả (có thể để trống). Nhập ID hoặc URL của hình ảnh, hoặc click "Chọn hình ảnh" để upload.
                </p>
                <div id="hinh_anh_phu_preview" style="margin-top: 10px;">
                <?php if ($hinh_anh_phu) : ?>
                    <?php
                    $preview_id = is_numeric($hinh_anh_phu) ? absint($hinh_anh_phu) : attachment_url_to_postid($hinh_anh_phu);
                    if ($preview_id) {
                        echo wp_get_attachment_image($preview_id, 'thumbnail');
                    } elseif (filter_var($hinh_anh_phu, FILTER_VALIDATE_URL)) {
                        echo '<img src="' . esc_url($hinh_anh_phu) . '" style="max-width: 150px; height: auto;" />';
                    }
                    ?>
                <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="gallery_images">Thư viện hình (ảnh phụ thẻ)</label></th>
            <td>
                <input type="hidden" id="gallery_images" name="gallery_images" value="<?php echo esc_attr($gallery_value); ?>" />
                <button type="button" class="button" id="upload_gallery_images">Chọn nhiều hình</button>
                <p class="description">
                    Tối đa <strong><?php echo (int) dnttvn_doanh_nghiep_gallery_card_max(); ?> hình</strong> trên thẻ / trang chi tiết (slide + hàng thumbnail). Hình thừa sẽ không được lưu.
                </p>
                <div id="gallery_images_preview" style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <?php
                    if (!empty($gallery_value)) {
                        $ids = array_slice(array_filter(array_map('absint', explode(',', $gallery_value))), 0, dnttvn_doanh_nghiep_gallery_card_max());
                        foreach ($ids as $img_id) {
                            $thumb = wp_get_attachment_image($img_id, 'thumbnail', false, array(
                                'style' => 'border:1px solid #ddd; padding:3px; background:#fff;'
                            ));
                            if ($thumb) {
                                echo '<div>' . $thumb . '</div>';
                            }
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="noi_dung_slider_images">Nội dung doanh nghiệp (slider nhiều hình)</label></th>
            <td>
                <input type="hidden" id="noi_dung_slider_images" name="noi_dung_slider_images" value="<?php echo esc_attr($noi_dung_slider_value); ?>" />
                <button type="button" class="button" id="upload_noi_dung_slider">Chọn nhiều hình cùng lúc</button>
                <p class="description">
                    Album riêng hiển thị trên trang chi tiết: slider có nút qua lại, hàng ảnh nhỏ bên dưới (mở ảnh gốc tab mới). Khác với mục Thư viện hình trong khối thông tin phía trên.
                </p>
                <p><label for="noi_dung_slider_title"><strong>Tiêu đề khối</strong></label><br />
                <input type="text" id="noi_dung_slider_title" name="noi_dung_slider_title" value="<?php echo esc_attr($noi_dung_slider_title); ?>" class="large-text" placeholder="Nội dung hình ảnh" /></p>
                <p><label for="noi_dung_slider_text"><strong>Giới thiệu ngắn (tùy chọn)</strong></label><br />
                <textarea id="noi_dung_slider_text" name="noi_dung_slider_text" rows="3" class="large-text"><?php echo esc_textarea($noi_dung_slider_text); ?></textarea></p>
                <div id="noi_dung_slider_preview" style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <?php
                    if (!empty($noi_dung_slider_value)) {
                        $nd_ids = array_filter(array_map('absint', explode(',', $noi_dung_slider_value)));
                        foreach ($nd_ids as $img_id) {
                            $thumb = wp_get_attachment_image($img_id, 'thumbnail', false, array(
                                'style' => 'border:1px solid #ddd; padding:3px; background:#fff;',
                            ));
                            if ($thumb) {
                                echo '<div>' . $thumb . '</div>';
                            }
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="thong_tin_bo_sung">Thông tin bổ sung</label></th>
            <td>
                <textarea id="thong_tin_bo_sung" name="thong_tin_bo_sung" rows="4" class="large-text"><?php echo esc_textarea($thong_tin_bs); ?></textarea>
                <p class="description">Thêm ghi chú, thông tin chi tiết khác về doanh nghiệp (ví dụ: giờ làm việc, chính sách, người liên hệ...).</p>
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nghiep_mo_ta_ngan">Mô tả ngắn (mục Thông tin chung sau duyệt, tối đa <?php echo defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200; ?> từ)</label></th>
            <td>
                <textarea id="doanh_nghiep_mo_ta_ngan" name="doanh_nghiep_mo_ta_ngan" rows="3" class="large-text"><?php echo esc_textarea($mo_ta_ngan); ?></textarea>
                <p class="description">Dùng trên danh sách thẻ Doanh nghiệp và trong mục <strong>Thông tin chung</strong> (nội dung có cấu trúc) trên trang chi tiết.</p>
            </td>
        </tr>
    </table>
    <?php
}

// Save meta box data
function dnttvn_save_doanh_nghiep_meta($post_id) {
    if (!isset($_POST['dnttvn_doanh_nghiep_meta_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['dnttvn_doanh_nghiep_meta_nonce'], 'dnttvn_save_doanh_nghiep_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['ten_day_du'])) {
        update_post_meta($post_id, '_ten_day_du', sanitize_text_field($_POST['ten_day_du']));
    }
    
    if (isset($_POST['nganh_hang'])) {
        update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_POST['nganh_hang']));
    }
    
    if (isset($_POST['khu_vuc'])) {
        update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_POST['khu_vuc']));
    }
    
    if (isset($_POST['dia_chi'])) {
        update_post_meta($post_id, '_dia_chi', sanitize_text_field($_POST['dia_chi']));
    }

    if (isset($_POST['dien_thoai'])) {
        update_post_meta($post_id, '_dien_thoai', sanitize_text_field($_POST['dien_thoai']));
    }

    if (isset($_POST['email_lien_he'])) {
        update_post_meta($post_id, '_email_lien_he', sanitize_email($_POST['email_lien_he']));
    }

    if (isset($_POST['website_doanh_nghiep'])) {
        // Lưu nguyên chuỗi người dùng nhập (không tự thêm http/https)
        update_post_meta($post_id, '_website_doanh_nghiep', sanitize_text_field($_POST['website_doanh_nghiep']));
    }
    
    if (isset($_POST['hinh_anh_phu'])) {
        update_post_meta($post_id, '_hinh_anh_phu', sanitize_text_field($_POST['hinh_anh_phu']));
    }

    if (isset($_POST['gallery_images'])) {
        $raw  = sanitize_text_field($_POST['gallery_images']);
        $ids  = array_filter(array_map('absint', explode(',', $raw)));
        $ids  = array_slice($ids, 0, dnttvn_doanh_nghiep_gallery_card_max());
        $save = !empty($ids) ? implode(',', $ids) : '';
        update_post_meta($post_id, '_gallery_images', $save);
    }

    if (isset($_POST['noi_dung_slider_images'])) {
        $raw_nd = sanitize_text_field(wp_unslash($_POST['noi_dung_slider_images']));
        $ids_nd = array_filter(array_map('absint', explode(',', $raw_nd)));
        $save_nd = !empty($ids_nd) ? implode(',', $ids_nd) : '';
        update_post_meta($post_id, '_noi_dung_slider_images', $save_nd);
    }

    if (isset($_POST['noi_dung_slider_title'])) {
        update_post_meta($post_id, '_noi_dung_slider_title', sanitize_text_field(wp_unslash($_POST['noi_dung_slider_title'])));
    }

    if (isset($_POST['noi_dung_slider_text'])) {
        update_post_meta($post_id, '_noi_dung_slider_text', wp_kses_post(wp_unslash($_POST['noi_dung_slider_text'])));
    }

    if (isset($_POST['thong_tin_bo_sung'])) {
        update_post_meta($post_id, '_thong_tin_bo_sung', wp_kses_post($_POST['thong_tin_bo_sung']));
    }

    if (isset($_POST['doanh_nghiep_mo_ta_ngan'])) {
        $mta = sanitize_textarea_field(wp_unslash($_POST['doanh_nghiep_mo_ta_ngan']));
        if (function_exists('dnttvn_dn_reg_trim_to_word_limit')) {
            $mo_lim = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;
            $mta    = dnttvn_dn_reg_trim_to_word_limit($mta, $mo_lim);
        }
        update_post_meta($post_id, '_doanh_nghiep_mo_ta_ngan', $mta);
    }

    // Gán taxonomy Ngành hàng từ dropdown / ô tạo mới
    $nganh_term_id = 0;
    if (isset($_POST['nganh_hang_tax_new']) && $_POST['nganh_hang_tax_new'] !== '') {
        $term_name = sanitize_text_field($_POST['nganh_hang_tax_new']);
        $term      = wp_insert_term($term_name, 'nganh_hang');
        if (!is_wp_error($term) && isset($term['term_id'])) {
            $nganh_term_id = (int) $term['term_id'];
        }
    } elseif (isset($_POST['nganh_hang_tax']) && $_POST['nganh_hang_tax'] !== '') {
        $nganh_term_id = (int) $_POST['nganh_hang_tax'];
    }
    if ($nganh_term_id) {
        wp_set_post_terms($post_id, array($nganh_term_id), 'nganh_hang', false);
        $term_obj = get_term($nganh_term_id, 'nganh_hang');
        if ($term_obj && !is_wp_error($term_obj)) {
            update_post_meta($post_id, '_nganh_hang', $term_obj->name);
        }
    }

    // Gán taxonomy Khu vực từ dropdown / ô tạo mới
    $khu_vuc_term_id = 0;
    if (isset($_POST['khu_vuc_tax_new']) && $_POST['khu_vuc_tax_new'] !== '') {
        $term_name = sanitize_text_field($_POST['khu_vuc_tax_new']);
        $term      = wp_insert_term($term_name, 'khu_vuc');
        if (!is_wp_error($term) && isset($term['term_id'])) {
            $khu_vuc_term_id = (int) $term['term_id'];
        }
    } elseif (isset($_POST['khu_vuc_tax']) && $_POST['khu_vuc_tax'] !== '') {
        $khu_vuc_term_id = (int) $_POST['khu_vuc_tax'];
    }
    if ($khu_vuc_term_id) {
        wp_set_post_terms($post_id, array($khu_vuc_term_id), 'khu_vuc', false);
        $term_obj = get_term($khu_vuc_term_id, 'khu_vuc');
        if ($term_obj && !is_wp_error($term_obj)) {
            update_post_meta($post_id, '_khu_vuc', $term_obj->name);
        }
    }
}
add_action('save_post', 'dnttvn_save_doanh_nghiep_meta');

// Theme support
function dnttvn_theme_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');

    // Kích thước banner header cho mobile (360x230, tỷ lệ giống banner mặc định)
    add_image_size('banner-header-mobile', 360, 230, false);

    // Register navigation menu
    register_nav_menus(array(
        'primary' => 'Menu chính',
    ));
}
add_action('after_setup_theme', 'dnttvn_theme_setup');

// Auto-create "Danh sách Doanh nghiệp" page on theme activation
function dnttvn_create_doanh_nghiep_page() {
    // Check if page already exists with new slug
    $page_slug = 'danh-sach-doanh-nghiep';
    $existing_page = get_page_by_path($page_slug);
    
    // Also check old slug for migration
    if (!$existing_page) {
        $old_page = get_page_by_path('page-doanh-nghiep');
        if ($old_page) {
            // Update existing page slug
            wp_update_post(array(
                'ID' => $old_page->ID,
                'post_name' => $page_slug
            ));
            $existing_page = $old_page;
        }
    }
    
    if (!$existing_page) {
        // Create the page
        $page_data = array(
            'post_title'    => 'Danh sách Doanh nghiệp',
            'post_name'     => $page_slug,
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
            'page_template' => 'page-doanh-nghiep.php',
        );
        
        $page_id = wp_insert_post($page_data);
        
        // Set page template
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-doanh-nghiep.php');
        }
    } else {
        // Update existing page to use correct template and slug
        wp_update_post(array(
            'ID' => $existing_page->ID,
            'post_name' => $page_slug
        ));
        update_post_meta($existing_page->ID, '_wp_page_template', 'page-doanh-nghiep.php');
    }
}
add_action('after_switch_theme', 'dnttvn_create_doanh_nghiep_page');

/**
 * Permalink trang form Đăng ký doanh nghiệp (slug dang-ky-doanh-nghiep).
 */
function dnttvn_get_dn_registration_page_url() {
    $p = get_page_by_path('dang-ky-doanh-nghiep');
    if ($p && isset($p->post_status) && $p->post_status === 'publish') {
        return get_permalink($p);
    }
    return home_url('/dang-ky-doanh-nghiep/');
}

/**
 * Thêm mục "Đăng ký doanh nghiệp" vào cuối menu Giao diện → Menu (vị trí primary).
 */
function dnttvn_append_dn_register_nav_link($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }
    if (strpos($items, 'menu-item-dang-ky-dn') !== false) {
        return $items;
    }
    $url         = dnttvn_get_dn_registration_page_url();
    $label       = 'Đăng ký doanh nghiệp';
    $reg_page    = get_page_by_path('dang-ky-doanh-nghiep');
    $is_current  = ($reg_page && !empty($reg_page->ID) && (int) $reg_page->ID > 0 && is_page((int) $reg_page->ID));
    $li_classes  = 'menu-item menu-item-type-custom menu-item-object-custom menu-item-dang-ky-dn';
    if ($is_current) {
        $li_classes .= ' current-menu-item current_page_item';
    }
    $aria = $is_current ? ' aria-current="page"' : '';
    $items .= '<li class="' . esc_attr($li_classes) . '"><a href="' . esc_url($url) . '"' . $aria . '>' . esc_html($label) . '</a></li>';
    return $items;
}
add_filter('wp_nav_menu_items', 'dnttvn_append_dn_register_nav_link', 10, 2);

/**
 * Gắn class body để CSS phân biệt trang đăng ký (menu CTA).
 */
function dnttvn_body_class_dn_reg_page($classes) {
    if (is_page('dang-ky-doanh-nghiep')) {
        $classes[] = 'page-dang-ky-doanh-nghiep';
    }
    return $classes;
}
add_filter('body_class', 'dnttvn_body_class_dn_reg_page');

// Default menu fallback
function dnttvn_default_menu() {
    echo '<ul class="menu" id="mainMenu">';
    echo '<li><a href="' . esc_url(home_url()) . '">Trang chủ</a></li>';
    $page_doanh_nghiep = get_page_by_path('danh-sach-doanh-nghiep');
    if (!$page_doanh_nghiep) {
        // Fallback to old slug
        $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
    }
    if ($page_doanh_nghiep) {
        echo '<li><a href="' . esc_url(get_permalink($page_doanh_nghiep->ID)) . '">Doanh nghiệp</a></li>';
    }
    $reg_url    = dnttvn_get_dn_registration_page_url();
    $reg_page   = get_page_by_path('dang-ky-doanh-nghiep');
    $is_current = ($reg_page && !empty($reg_page->ID) && is_page((int) $reg_page->ID));
    $li_cls     = 'menu-item menu-item-dang-ky-dn' . ($is_current ? ' current-menu-item current_page_item' : '');
    $aria       = $is_current ? ' aria-current="page"' : '';
    echo '<li class="' . esc_attr($li_cls) . '"><a href="' . esc_url($reg_url) . '"' . $aria . '>Đăng ký doanh nghiệp</a></li>';
    echo '<li><a href="#">Liên hệ</a></li>';
    echo '</ul>';
}

// ============================================
// ADMIN MANAGEMENT FEATURES
// ============================================

// Add Admin Columns for Doanh nghiệp
function dnttvn_add_doanh_nghiep_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['menu_order'] = 'Thứ tự';
    $new_columns['nganh_hang'] = 'Ngành hàng';
    $new_columns['khu_vuc'] = 'Khu vực';
    $new_columns['nganh_hang_tax'] = 'Ngành hàng (phân loại)';
    $new_columns['khu_vuc_tax'] = 'Khu vực (phân loại)';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_doanh_nghiep_posts_columns', 'dnttvn_add_doanh_nghiep_admin_columns');

// Populate Admin Columns for Doanh nghiệp
function dnttvn_populate_doanh_nghiep_admin_columns($column, $post_id) {
    switch ($column) {
        case 'menu_order':
            $menu_order = get_post($post_id)->menu_order;
            echo '<strong>' . esc_html($menu_order) . '</strong>';
            break;
        case 'nganh_hang':
            $nganh_hang = get_post_meta($post_id, '_nganh_hang', true);
            echo $nganh_hang ? esc_html($nganh_hang) : '—';
            break;
        case 'khu_vuc':
            $khu_vuc = get_post_meta($post_id, '_khu_vuc', true);
            echo $khu_vuc ? esc_html($khu_vuc) : '—';
            break;
        case 'nganh_hang_tax':
            $terms = get_the_terms($post_id, 'nganh_hang');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                echo implode(', ', $term_names);
            } else {
                echo '—';
            }
            break;
        case 'khu_vuc_tax':
            $terms = get_the_terms($post_id, 'khu_vuc');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                echo implode(', ', $term_names);
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_doanh_nghiep_posts_custom_column', 'dnttvn_populate_doanh_nghiep_admin_columns', 10, 2);

// Make Admin Columns Sortable
function dnttvn_make_doanh_nghiep_columns_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    $columns['nganh_hang'] = 'nganh_hang';
    $columns['khu_vuc'] = 'khu_vuc';
    return $columns;
}
add_filter('manage_edit-doanh_nghiep_sortable_columns', 'dnttvn_make_doanh_nghiep_columns_sortable');

// Handle Sorting
function dnttvn_handle_doanh_nghiep_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby == 'menu_order') {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    } elseif ($orderby == 'nganh_hang' || $orderby == 'khu_vuc') {
        $query->set('meta_key', '_' . $orderby);
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'dnttvn_handle_doanh_nghiep_sorting');

// Add Admin Columns for Tin tức
function dnttvn_add_tin_tuc_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['menu_order'] = 'Thứ tự';
    $new_columns['noi_bat'] = 'Nổi bật';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_tin_tuc_posts_columns', 'dnttvn_add_tin_tuc_admin_columns');

// Populate Admin Columns for Tin tức
function dnttvn_populate_tin_tuc_admin_columns($column, $post_id) {
    switch ($column) {
        case 'menu_order':
            $menu_order = get_post($post_id)->menu_order;
            echo '<strong>' . esc_html($menu_order) . '</strong>';
            break;
        case 'noi_bat':
            $noi_bat = get_post_meta($post_id, '_tin_tuc_noi_bat', true);
            if ($noi_bat == '1') {
                echo '<span style="color: #ff9800; font-weight: bold;">⭐ Nổi bật</span>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_tin_tuc_posts_custom_column', 'dnttvn_populate_tin_tuc_admin_columns', 10, 2);

// Make Tin tức Columns Sortable
function dnttvn_make_tin_tuc_columns_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    $columns['noi_bat'] = 'noi_bat';
    return $columns;
}
add_filter('manage_edit-tin_tuc_sortable_columns', 'dnttvn_make_tin_tuc_columns_sortable');

// Handle Tin tức Sorting
function dnttvn_handle_tin_tuc_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby == 'menu_order') {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    } elseif ($orderby == 'noi_bat') {
        $query->set('meta_key', '_tin_tuc_noi_bat');
        $query->set('orderby', 'meta_value date');
        $query->set('order', 'DESC');
    }
}
add_action('pre_get_posts', 'dnttvn_handle_tin_tuc_sorting');

// Add Admin Filters (Dropdown filters)
function dnttvn_add_doanh_nghiep_admin_filters() {
    global $typenow;
    
    if ($typenow == 'doanh_nghiep') {
        // Filter by Ngành hàng taxonomy
        $selected_nganh_hang = isset($_GET['nganh_hang_filter']) ? $_GET['nganh_hang_filter'] : '';
        $nganh_hang_terms = get_terms(array(
            'taxonomy' => 'nganh_hang',
            'hide_empty' => false,
        ));
        
        if (!empty($nganh_hang_terms) && !is_wp_error($nganh_hang_terms)) {
            echo '<select name="nganh_hang_filter">';
            echo '<option value="">Tất cả Ngành hàng</option>';
            foreach ($nganh_hang_terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected_nganh_hang, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
        
        // Filter by Khu vực taxonomy
        $selected_khu_vuc = isset($_GET['khu_vuc_filter']) ? $_GET['khu_vuc_filter'] : '';
        $khu_vuc_terms = get_terms(array(
            'taxonomy' => 'khu_vuc',
            'hide_empty' => false,
        ));
        
        if (!empty($khu_vuc_terms) && !is_wp_error($khu_vuc_terms)) {
            echo '<select name="khu_vuc_filter">';
            echo '<option value="">Tất cả Khu vực</option>';
            foreach ($khu_vuc_terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected_khu_vuc, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
    }
}
add_action('restrict_manage_posts', 'dnttvn_add_doanh_nghiep_admin_filters');

// Apply Admin Filters
function dnttvn_apply_doanh_nghiep_admin_filters($query) {
    global $pagenow, $typenow;
    
    if ($pagenow == 'edit.php' && $typenow == 'doanh_nghiep') {
        $tax_query = array();
        
        if (isset($_GET['nganh_hang_filter']) && $_GET['nganh_hang_filter'] != '') {
            $tax_query[] = array(
                'taxonomy' => 'nganh_hang',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['nganh_hang_filter']),
            );
        }
        
        if (isset($_GET['khu_vuc_filter']) && $_GET['khu_vuc_filter'] != '') {
            $tax_query[] = array(
                'taxonomy' => 'khu_vuc',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['khu_vuc_filter']),
            );
        }
        
        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }
    }
}
add_action('parse_query', 'dnttvn_apply_doanh_nghiep_admin_filters');

// Add Quick Edit Fields
function dnttvn_add_quick_edit_fields($column_name, $post_type) {
    if ($post_type != 'doanh_nghiep') {
        return;
    }
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <div class="inline-edit-group wp-clearfix">
                <label class="inline-edit-status alignleft">
                    <span class="title">Ngành hàng</span>
                    <input type="text" name="nganh_hang" value="" />
                </label>
            </div>
            <div class="inline-edit-group wp-clearfix">
                <label class="inline-edit-status alignleft">
                    <span class="title">Khu vực</span>
                    <input type="text" name="khu_vuc" value="" />
                </label>
            </div>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'dnttvn_add_quick_edit_fields', 10, 2);

// Save Quick Edit Data
function dnttvn_save_quick_edit_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (get_post_type($post_id) != 'doanh_nghiep') {
        return;
    }
    
    if (isset($_POST['nganh_hang'])) {
        update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_POST['nganh_hang']));
    }
    
    if (isset($_POST['khu_vuc'])) {
        update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_POST['khu_vuc']));
    }
}
add_action('save_post', 'dnttvn_save_quick_edit_data');

// Add Bulk Edit Fields
function dnttvn_add_bulk_edit_fields() {
    global $post_type;
    
    if ($post_type == 'doanh_nghiep') {
        ?>
        <div class="inline-edit-group">
            <label class="alignleft">
                <span class="title">Ngành hàng</span>
                <input type="text" name="_nganh_hang" value="" />
            </label>
        </div>
        <div class="inline-edit-group">
            <label class="alignleft">
                <span class="title">Khu vực</span>
                <input type="text" name="_khu_vuc" value="" />
            </label>
        </div>
        <?php
    }
}
add_action('bulk_edit_custom_box', 'dnttvn_add_bulk_edit_fields', 10, 2);

// Save Bulk Edit Data
function dnttvn_save_bulk_edit_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (get_post_type($post_id) != 'doanh_nghiep') {
        return;
    }
    
    // Check if this is bulk edit
    if (isset($_REQUEST['_inline_edit']) || isset($_REQUEST['bulk_edit'])) {
        if (isset($_REQUEST['_nganh_hang']) && $_REQUEST['_nganh_hang'] != '') {
            update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_REQUEST['_nganh_hang']));
        }
        
        if (isset($_REQUEST['_khu_vuc']) && $_REQUEST['_khu_vuc'] != '') {
            update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_REQUEST['_khu_vuc']));
        }
    }
}
add_action('save_post', 'dnttvn_save_bulk_edit_data');

// Enqueue Admin Scripts for Media Uploader & Structured Content
function dnttvn_enqueue_admin_scripts($hook) {
    global $post_type;
    
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        // Doanh nghiệp: cần media uploader + script quản lý hình ảnh / gallery + Structured Content
        if ($post_type == 'doanh_nghiep') {
            wp_enqueue_media();
            wp_enqueue_script(
                'dnttvn-admin-script',
                get_template_directory_uri() . '/assets/admin-script.js',
                array('jquery', 'jquery-ui-sortable'),
                time(),
                true
            );
        }

        // Tin tức & Cộng đồng: dùng chung script để hỗ trợ Structured Content (repeater)
        if (in_array($post_type, array('tin_tuc', 'cong_dong'))) {
            // Cả tin tức và cộng đồng đều cần media uploader cho hình phụ và structured content
            wp_enqueue_media();

            wp_enqueue_script(
                'dnttvn-admin-script',
                get_template_directory_uri() . '/assets/admin-script.js',
                array('jquery', 'jquery-ui-sortable'),
                time(),
                true
            );

            // Inline script cho upload gallery hình phụ
            $inline_script = "
            jQuery(document).ready(function($) {
                // Function to update hidden input with gallery data
                function updateGalleryInput(containerId, inputId) {
                    var ids = [];
                    $('#' + containerId + ' .gallery-item').each(function() {
                        ids.push($(this).data('id'));
                    });
                    $('#' + inputId).val(JSON.stringify(ids));
                }

                // Function to make gallery sortable
                function makeGallerySortable(containerId) {
                    $('#' + containerId).sortable({
                        items: '.gallery-item',
                        cursor: 'move',
                        update: function() {
                            updateGalleryInput(containerId.replace('_gallery', ''), containerId.replace('_gallery', ''));
                        }
                    });
                }

                // Initialize sortable for existing galleries
                makeGallerySortable('tin_tuc_hinh_phu_gallery');
                makeGallerySortable('cong_dong_hinh_phu_gallery');

                // Upload hình phụ cho tin tức (chọn nhiều ảnh/video: bấm từng mục hoặc giữ Ctrl/Cmd)
                $('#upload_tin_tuc_hinh_phu').on('click', function(e) {
                    e.preventDefault();
                    var mediaUploader = wp.media({
                        title: 'Chọn ảnh/video phụ cho tin tức (có thể chọn nhiều)',
                        button: { text: 'Thêm vào gallery' },
                        multiple: 'add',
                        library: {}
                    });

                    mediaUploader.on('select', function() {
                        var selection = mediaUploader.state().get('selection');
                        var attachments = selection.map(function(attachment) { return attachment.toJSON(); });
                        var gallery = $('#tin_tuc_hinh_phu_gallery');

                        attachments.forEach(function(attachment) {
                            var mime_type = attachment.mime || '';
                            var is_video = mime_type.indexOf('video') === 0;
                            var itemHtml = '';
                            var thumbUrl = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ? attachment.sizes.medium.url : (attachment.url || '');

                            if (is_video) {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<video style=\"width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;\" controls>' +
                                    '<source src=\"' + (attachment.url || '') + '\" type=\"' + mime_type + '\">' +
                                    '</video>' +
                                    '<button type=\"button\" class=\"remove-gallery-item button\" style=\"position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;\">×</button>' +
                                    '</div>';
                            } else {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<img src=\"' + thumbUrl + '\" alt=\"Ảnh phụ\" style=\"width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;\" />' +
                                    '<button type=\"button\" class=\"remove-gallery-item button\" style=\"position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;\">×</button>' +
                                    '</div>';
                            }

                            gallery.append(itemHtml);
                        });

                        updateGalleryInput('tin_tuc_hinh_phu_gallery', 'tin_tuc_hinh_phu');
                        $('#clear_tin_tuc_hinh_phu').show();
                        makeGallerySortable('tin_tuc_hinh_phu_gallery');
                    });

                    mediaUploader.open();
                });

                // Clear tất cả hình phụ tin tức
                $('#clear_tin_tuc_hinh_phu').on('click', function(e) {
                    e.preventDefault();
                    if (confirm('Bạn có chắc muốn xóa tất cả ảnh/video phụ?')) {
                        $('#tin_tuc_hinh_phu_gallery').empty();
                        $('#tin_tuc_hinh_phu').val('[]');
                        $('#clear_tin_tuc_hinh_phu').hide();
                    }
                });

                // Upload hình phụ cho cộng đồng (chọn nhiều ảnh/video)
                $('#upload_cong_dong_hinh_phu').on('click', function(e) {
                    e.preventDefault();
                    var mediaUploader = wp.media({
                        title: 'Chọn ảnh/video phụ cho cộng đồng (có thể chọn nhiều)',
                        button: { text: 'Thêm vào gallery' },
                        multiple: 'add',
                        library: {}
                    });

                    mediaUploader.on('select', function() {
                        var selection = mediaUploader.state().get('selection');
                        var attachments = selection.map(function(attachment) { return attachment.toJSON(); });
                        var gallery = $('#cong_dong_hinh_phu_gallery');

                        attachments.forEach(function(attachment) {
                            var mime_type = attachment.mime || '';
                            var is_video = mime_type.indexOf('video') === 0;
                            var itemHtml = '';
                            var thumbUrl = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ? attachment.sizes.medium.url : (attachment.url || '');

                            if (is_video) {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<video style=\"width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;\" controls>' +
                                    '<source src=\"' + (attachment.url || '') + '\" type=\"' + mime_type + '\">' +
                                    '</video>' +
                                    '<button type=\"button\" class=\"remove-gallery-item button\" style=\"position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;\">×</button>' +
                                    '</div>';
                            } else {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<img src=\"' + thumbUrl + '\" alt=\"Ảnh phụ\" style=\"width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;\" />' +
                                    '<button type=\"button\" class=\"remove-gallery-item button\" style=\"position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;\">×</button>' +
                                    '</div>';
                            }

                            gallery.append(itemHtml);
                        });

                        updateGalleryInput('cong_dong_hinh_phu_gallery', 'cong_dong_hinh_phu');
                        $('#clear_cong_dong_hinh_phu').show();
                        makeGallerySortable('cong_dong_hinh_phu_gallery');
                    });

                    mediaUploader.open();
                });

                // Clear tất cả hình phụ cộng đồng
                $('#clear_cong_dong_hinh_phu').on('click', function(e) {
                    e.preventDefault();
                    if (confirm('Bạn có chắc muốn xóa tất cả ảnh/video phụ?')) {
                        $('#cong_dong_hinh_phu_gallery').empty();
                        $('#cong_dong_hinh_phu').val('[]');
                        $('#clear_cong_dong_hinh_phu').hide();
                    }
                });

                // Remove individual gallery item
                $(document).on('click', '.remove-gallery-item', function(e) {
                    e.preventDefault();
                    $(this).closest('.gallery-item').remove();

                    // Update hidden inputs (container = gallery div id, second = input id)
                    updateGalleryInput('tin_tuc_hinh_phu_gallery', 'tin_tuc_hinh_phu');
                    updateGalleryInput('cong_dong_hinh_phu_gallery', 'cong_dong_hinh_phu');

                    // Hide clear buttons if no items left
                    if ($('#tin_tuc_hinh_phu_gallery .gallery-item').length === 0) {
                        $('#clear_tin_tuc_hinh_phu').hide();
                    }
                    if ($('#cong_dong_hinh_phu_gallery .gallery-item').length === 0) {
                        $('#clear_cong_dong_hinh_phu').hide();
                    }
                });
            });
            ";
            wp_add_inline_script('dnttvn-admin-script', $inline_script);
        }
    }
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_admin_scripts');

// Add Dashboard Widgets
function dnttvn_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'dnttvn_stats_widget',
        'Thống kê Cộng đồng',
        'dnttvn_dashboard_stats_widget'
    );
}
add_action('wp_dashboard_setup', 'dnttvn_add_dashboard_widgets');

// ============================================
// SEO & META HELPERS
// ============================================

// Lấy meta description cho trang hiện tại
function dnttvn_get_meta_description() {
    if (is_singular()) {
        global $post;
        if (!$post) {
            return get_bloginfo('description');
        }

        // Ưu tiên Excerpt
        if (has_excerpt($post)) {
            return wp_strip_all_tags(get_the_excerpt($post), true);
        }

        // Với Tin tức / Doanh nghiệp / Cộng đồng, cắt từ nội dung
        $content = $post->post_content;
        if (!empty($content)) {
            $text = wp_strip_all_tags(strip_shortcodes($content), true);
            return mb_substr($text, 0, 155);
        }
    }

    // Trang khác: dùng mô tả site
    $desc = get_bloginfo('description');
    return $desc ? $desc : get_bloginfo('name');
}

// Lấy OG image cho chia sẻ mạng xã hội
function dnttvn_get_og_image() {
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        if (!empty($img[0])) {
            return $img[0];
        }
    }

    // Fallback: logo custom
    if (function_exists('get_custom_logo')) {
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $img = wp_get_attachment_image_src($logo_id, 'full');
            if (!empty($img[0])) {
                return $img[0];
            }
        }
    }

    // Fallback cuối: không trả về gì
    return '';
}

// Dashboard Stats Widget
function dnttvn_dashboard_stats_widget() {
    $tin_tuc_count = wp_count_posts('tin_tuc');
    $doanh_nghiep_count = wp_count_posts('doanh_nghiep');
    
    $nganh_hang_count = wp_count_terms(array('taxonomy' => 'nganh_hang', 'hide_empty' => false));
    $khu_vuc_count = wp_count_terms(array('taxonomy' => 'khu_vuc', 'hide_empty' => false));
    
    if (is_wp_error($nganh_hang_count)) {
        $nganh_hang_count = 0;
    }
    if (is_wp_error($khu_vuc_count)) {
        $khu_vuc_count = 0;
    }
    
    ?>
    <div style="padding: 10px;">
        <h3>Tin tức</h3>
        <ul>
            <li>Đã xuất bản: <strong><?php echo number_format_i18n($tin_tuc_count->publish); ?></strong></li>
            <li>Bản nháp: <strong><?php echo number_format_i18n($tin_tuc_count->draft); ?></strong></li>
            <li>Trong thùng rác: <strong><?php echo number_format_i18n($tin_tuc_count->trash); ?></strong></li>
        </ul>
        
        <h3>Doanh nghiệp</h3>
        <ul>
            <li>Đã xuất bản: <strong><?php echo number_format_i18n($doanh_nghiep_count->publish); ?></strong></li>
            <li>Bản nháp: <strong><?php echo number_format_i18n($doanh_nghiep_count->draft); ?></strong></li>
            <li>Trong thùng rác: <strong><?php echo number_format_i18n($doanh_nghiep_count->trash); ?></strong></li>
        </ul>
        
        <h3>Phân loại</h3>
        <ul>
            <li>Ngành hàng: <strong><?php echo number_format_i18n($nganh_hang_count); ?></strong></li>
            <li>Khu vực: <strong><?php echo number_format_i18n($khu_vuc_count); ?></strong></li>
        </ul>
        
        <p style="margin-top: 15px;">
            <a href="<?php echo admin_url('edit.php?post_type=tin_tuc'); ?>" class="button">Quản lý Tin tức</a>
            <a href="<?php echo admin_url('edit.php?post_type=doanh_nghiep'); ?>" class="button">Quản lý Doanh nghiệp</a>
        </p>
    </div>
    <?php
}

// Custom Pagination Function for Doanh nghiệp page
function dnttvn_custom_pagination($query = null) {
    global $wp_query;
    
    if (!$query) {
        $query = $wp_query;
    }
    
    $big = 999999999; // Need an unlikely integer
    $total_pages = $query->max_num_pages;
    
    if ($total_pages <= 1) {
        return '';
    }
    
    // Get current page
    // For custom page templates, check multiple sources
    $current_page = 1;
    if (get_query_var('paged')) {
        $current_page = get_query_var('paged');
    } elseif (get_query_var('page')) {
        $current_page = get_query_var('page');
    } elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
        $current_page = absint($_GET['paged']);
    }
    $current_page = max(1, $current_page);
    
    // Build query string to preserve search, filter, and sort parameters
    $query_args = array();
    if (isset($_GET['ten_doanh_nghiep']) && !empty($_GET['ten_doanh_nghiep'])) {
        $query_args['ten_doanh_nghiep'] = sanitize_text_field($_GET['ten_doanh_nghiep']);
    }
    if (isset($_GET['khu_vuc']) && !empty($_GET['khu_vuc'])) {
        $query_args['khu_vuc'] = sanitize_text_field($_GET['khu_vuc']);
    }
    if (isset($_GET['nganh_hang']) && !empty($_GET['nganh_hang'])) {
        $query_args['nganh_hang'] = sanitize_text_field($_GET['nganh_hang']);
    }
    if (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) {
        $query_args['sort_by'] = sanitize_text_field($_GET['sort_by']);
    }
    
    // For custom page templates, use get_permalink() as base
    global $post;
    $page_permalink = '';
    if (is_page() && isset($post)) {
        $page_permalink = get_permalink($post->ID);
    } else {
        // Fallback to get_pagenum_link for archive pages
        $page_permalink = get_pagenum_link($big);
    }
    
    // Build base URL for pagination (query string ?paged=)
    $base = add_query_arg(array_merge($query_args, array('paged' => '%#%')), $page_permalink) . '#doanh-nghiep-list';
    
    $pagination = paginate_links(array(
        'base'      => $base,
        'format'    => '',
        'current'   => $current_page,
        'total'     => $total_pages,
        'prev_text' => '&laquo; Trước',
        'next_text' => 'Sau &raquo;',
        'type'      => 'list',
        'end_size'  => 1,
        'mid_size'  => 2, // cửa sổ 5 trang quanh trang hiện tại
    ));
    
    return '<div class="pagination">' . $pagination . '</div>';
}

/**
 * Hiển thị danh sách doanh nghiệp ở cột phải: tên + mô tả + link tới trang chi tiết.
 *
 * @param int $exclude_id ID bài doanh nghiệp cần loại trừ (trang đang xem).
 * @param int $limit Số lượng tối đa (mặc định 5).
 * @return string HTML.
 */
function dnttvn_render_doanh_nghiep_sidebar($exclude_id = 0, $limit = 5) {
    $args = array(
        'post_type'      => 'doanh_nghiep',
        'posts_per_page' => (int) $limit,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    );
    if ($exclude_id > 0) {
        $args['post__not_in'] = array($exclude_id);
    }
    $query = new WP_Query($args);
    ob_start();
    if ($query->have_posts()) {
        echo '<ul class="doanh-nghiep-sidebar-list" style="list-style: none; padding: 0; margin: 0;">';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $title   = get_the_title();
            $url     = get_permalink($post_id);
            $mo_ta   = get_post_meta($post_id, '_doanh_nghiep_mo_ta_ngan', true);
            if (empty($mo_ta)) {
                $mo_ta = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 15);
            }
            ?>
            <li class="doanh-nghiep-sidebar-item" style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #eee;">
                <a href="<?php echo esc_url($url); ?>" style="text-decoration: none; color: #333; display: block;">
                    <strong style="font-size: 14px; display: block; margin-bottom: 4px;"><?php echo esc_html($title); ?></strong>
                    <?php if (!empty($mo_ta)) : ?>
                        <span style="font-size: 13px; color: #666; line-height: 1.4;"><?php echo esc_html(wp_trim_words($mo_ta, 20)); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p style="font-size: 13px; color: #666;">Chưa có doanh nghiệp nào.</p>';
    }
    return ob_get_clean();
}

/**
 * Thu thập banner/video hợp lệ (trong khung thời gian, có URL) cho một loại VVIP/VIP/Standard.
 *
 * @param string $banner_type vvip|vip|standard
 * @param array  $data        Phần tử từ mảng banner_data
 * @param int    $now         Timestamp site
 * @return array<int, array<string, mixed>>
 */
function dnttvn_collect_valid_sidebar_banner_items($banner_type, $data, $now) {
    $banners          = isset($data['banners']) ? $data['banners'] : array();
    $links            = isset($data['links']) ? $data['links'] : array();
    $doanh_nghiep_ids = isset($data['doanh_nghiep']) ? $data['doanh_nghiep'] : array();
    $start_times      = get_option('dnttvn_' . $banner_type . '_start', array());
    $end_times        = get_option('dnttvn_' . $banner_type . '_end', array());
    $items            = array();

    if (empty($banners)) {
        return $items;
    }

    foreach ($banners as $idx => $banner_id) {
        if (! $banner_id) {
            continue;
        }
        $start_raw = isset($start_times[$idx]) ? $start_times[$idx] : '';
        $end_raw   = isset($end_times[$idx]) ? $end_times[$idx] : '';
        $start_ts  = $start_raw ? strtotime($start_raw) : false;
        $end_ts    = $end_raw ? strtotime($end_raw) : false;

        if (($start_ts && $now < $start_ts) || ($end_ts && $now > $end_ts)) {
            continue;
        }

        $mime_type = get_post_mime_type($banner_id);
        if (strpos((string) $mime_type, 'video') !== false) {
            $banner_url = wp_get_attachment_url($banner_id);
            $banner_alt = '';
        } else {
            $banner_url = wp_get_attachment_image_url($banner_id, 'full');
            $banner_alt = get_post_meta($banner_id, '_wp_attachment_image_alt', true);
        }

        if (! $banner_url) {
            continue;
        }

        $link_url = isset($links[$idx]) ? $links[$idx] : '';
        $dn_id    = isset($doanh_nghiep_ids[$idx]) ? (int) $doanh_nghiep_ids[$idx] : 0;
        $dn_post  = $dn_id ? get_post($dn_id) : null;
        $detail_url = '';
        if ($dn_post && $dn_post->post_type === 'doanh_nghiep' && $dn_post->post_status === 'publish') {
            $detail_url = get_permalink($dn_post);
        }
        $banner_link_url = $detail_url ? $detail_url : $link_url;
        $banner_title    = $dn_post ? $dn_post->post_title : $data['title'];
        $is_video        = (strpos((string) $mime_type, 'video') !== false);

        $items[] = array(
            'banner_id'       => (int) $banner_id,
            'banner_url'      => $banner_url,
            'banner_alt'      => (string) $banner_alt,
            'is_video'        => $is_video,
            'banner_link_url' => $banner_link_url,
            'banner_title'    => $banner_title,
            'detail_url'      => $detail_url,
            'dn_post'         => $dn_post,
        );
    }

    return $items;
}

/**
 * Nội dung một khối quảng cáo (tiêu đề + nhãn + media), không bọc .ad-block ngoài cùng.
 *
 * @param array  $data        Phần tử banner_data
 * @param array  $item        Một phần tử từ dnttvn_collect_valid_sidebar_banner_items
 * @param string $video_attrs Thuộc tính thẻ video (autoplay, loop…)
 */
function dnttvn_render_ad_block_inner_html($data, $item, $video_attrs = ' autoplay muted loop playsinline') {
    $out = '';
    $out .= '<h4>';
    if (! empty($item['dn_post']) && ! empty($item['detail_url'])) {
        $out .= '<a href="' . esc_url($item['detail_url']) . '" style="color: inherit; text-decoration: none;">' . esc_html($item['banner_title']) . '</a>';
    } else {
        $out .= esc_html($item['banner_title']);
    }
    $out .= '</h4>';
    $out .= '<div class="ad-type">' . esc_html($data['label']) . '</div>';
    if (! empty($item['banner_link_url'])) {
        $out .= '<a href="' . esc_url($item['banner_link_url']) . '" style="display: block;">';
    }
    if (! empty($item['is_video'])) {
        $out .= '<video src="' . esc_url($item['banner_url']) . '"' . $video_attrs . ' style="width: 100%; max-width: 100%;"></video>';
    } else {
        $out .= '<img src="' . esc_url($item['banner_url']) . '" alt="' . esc_attr($item['banner_alt']) . '" style="width: 100%; max-width: 100%;">';
    }
    if (! empty($item['banner_link_url'])) {
        $out .= '</a>';
    }
    return $out;
}

/**
 * Đếm số ô banner đã gán media (ID > 0) từ POST khi lưu form quản trị.
 *
 * @param array  $post   Thường là $_POST
 * @param string $tier  vvip|vip|standard
 */
function dnttvn_count_filled_banner_slots_from_post($post, $tier) {
    $key = $tier . '_banners';
    if (empty($post[ $key ]) || ! is_array($post[ $key ])) {
        return 0;
    }
    $n = 0;
    foreach ($post[ $key ] as $id) {
        if (absint($id) > 0) {
            $n++;
        }
    }
    return $n;
}

/**
 * Giây mỗi slide xoay sidebar theo hạng (3–300), có migrate từ option cũ một giá trị chung.
 *
 * @param string $tier vvip|vip|standard
 */
function dnttvn_get_sidebar_rotate_interval_for_tier($tier) {
    $tier = sanitize_key($tier);
    if (! in_array($tier, array('vvip', 'vip', 'standard'), true)) {
        return 8;
    }
    $opt_key = 'dnttvn_banner_sidebar_rotate_interval_' . $tier;
    $v       = absint(get_option($opt_key, 0));
    if ($v >= 3 && $v <= 300) {
        return $v;
    }
    $legacy = absint(get_option('dnttvn_banner_sidebar_rotate_interval', 0));
    if ($legacy >= 3 && $legacy <= 300) {
        return $legacy;
    }
    $defaults = array(
        'vvip'     => 30,
        'vip'      => 20,
        'standard' => 15,
    );
    return isset($defaults[ $tier ]) ? $defaults[ $tier ] : 8;
}

// Helper function to render banner blocks (for reuse in sidebar and mobile)
// $offset, $limit dùng để phục vụ view mobile (lấy theo chỉ số để xen kẽ với thẻ doanh nghiệp)
function dnttvn_render_banner_blocks($class_prefix = 'ad-block', $offset = 0, $limit = null) {
    $now   = current_time('timestamp');
    $index = 0; // Đếm số banner thực sự hiển thị (sau khi lọc thời gian)
    // Get banner order
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    $order_array         = array_map('trim', explode(',', $banner_column_order));

    // Prepare banner data
    $banner_data = array(
        'vvip' => array(
            'banners'        => get_option('dnttvn_vvip_banners', array()),
            'links'          => get_option('dnttvn_vvip_links', array()),
            'doanh_nghiep'   => get_option('dnttvn_vvip_doanh_nghiep', array()),
            'type'           => 'vvip',
            'title'          => 'Video quảng cáo hoặc banner: VVIP',
            'label'          => 'VVIP',
        ),
        'vip' => array(
            'banners'        => get_option('dnttvn_vip_banners', array()),
            'links'          => get_option('dnttvn_vip_links', array()),
            'doanh_nghiep'   => get_option('dnttvn_vip_doanh_nghiep', array()),
            'type'           => 'vip',
            'title'          => 'Video quảng cáo hoặc banner: VIP',
            'label'          => 'VIP',
        ),
        'standard' => array(
            'banners'        => get_option('dnttvn_standard_banners', array()),
            'links'          => get_option('dnttvn_standard_links', array()),
            'doanh_nghiep'   => get_option('dnttvn_standard_doanh_nghiep', array()),
            'type'           => 'standard',
            'title'          => 'Video quảng cáo hoặc banner: Standard',
            'label'          => 'Standard',
        ),
    );

    $rotate_enabled = (get_option('dnttvn_banner_sidebar_rotate', '') === '1');

    // Cột phải desktop: đúng 1 ô / hạng (VVIP, VIP, Standard), bên trong xoay hoặc xếp chồng theo cấu hình
    if ($limit === null) {
        static $dnttvn_sidebar_rot_num = 0;
        $output = '';
        foreach ($order_array as $banner_type) {
            if (! isset($banner_data[ $banner_type ])) {
                continue;
            }
            $data  = $banner_data[ $banner_type ];
            $items = dnttvn_collect_valid_sidebar_banner_items($banner_type, $data, $now);
            if (empty($items)) {
                continue;
            }

            $output .= '<div class="' . esc_attr($class_prefix) . ' ' . esc_attr($data['type']) . ' ad-block-tier-slot">';

            if ($rotate_enabled && count($items) >= 2) {
                $rotate_interval = dnttvn_get_sidebar_rotate_interval_for_tier($banner_type);
                $dnttvn_sidebar_rot_num++;
                $rid = 'dnttvn-sidebar-rot-' . $dnttvn_sidebar_rot_num;
                $output .= '<div id="' . esc_attr($rid) . '" class="ad-block-rotator" data-rotate-interval="' . (int) $rotate_interval . '" role="region" aria-roledescription="carousel" aria-label="' . esc_attr($data['label'] . ' — quảng cáo') . '">';
                $output .= '<div class="ad-block-rotator__slides">';
                foreach ($items as $si => $item) {
                    $active  = ($si === 0);
                    $vattr   = $active ? ' autoplay muted playsinline' : ' muted playsinline preload="metadata"';
                    $hidden  = $active ? '' : ' hidden';
                    $classes = 'ad-block-rotator__slide' . ($active ? ' is-active' : '');
                    $output .= '<div class="' . esc_attr($classes) . '"' . $hidden . ' data-slide-index="' . (int) $si . '">';
                    $output .= dnttvn_render_ad_block_inner_html($data, $item, $vattr);
                    $output .= '</div>';
                }
                $output .= '</div></div>';
            } elseif (count($items) === 1) {
                $output .= dnttvn_render_ad_block_inner_html($data, $items[0], ' autoplay muted loop playsinline');
            } else {
                $output .= '<div class="ad-block-stack">';
                foreach ($items as $item) {
                    $output .= '<div class="ad-block-stack__item">';
                    $output .= dnttvn_render_ad_block_inner_html($data, $item, ' autoplay muted loop playsinline');
                    $output .= '</div>';
                }
                $output .= '</div>';
            }

            $output .= '</div>';
        }
        return $output;
    }

    $output = '';

    // Luồng mobile xen kẽ (ad-block-mobile + offset/limit): danh sách phẳng như cũ
    foreach ($order_array as $banner_type) {
        if (! isset($banner_data[ $banner_type ])) {
            continue;
        }

        $data             = $banner_data[ $banner_type ];
        $banners          = $data['banners'];
        $links            = $data['links'];
        $doanh_nghiep_ids = isset($data['doanh_nghiep']) ? $data['doanh_nghiep'] : array();

        $start_times = get_option('dnttvn_' . $banner_type . '_start', array());
        $end_times   = get_option('dnttvn_' . $banner_type . '_end', array());

        if (! empty($banners)) {
            foreach ($banners as $idx => $banner_id) {
                if (! $banner_id) {
                    continue;
                }
                $start_raw = isset($start_times[ $idx ]) ? $start_times[ $idx ] : '';
                $end_raw   = isset($end_times[ $idx ]) ? $end_times[ $idx ] : '';
                $start_ts  = $start_raw ? strtotime($start_raw) : false;
                $end_ts    = $end_raw ? strtotime($end_raw) : false;

                if (($start_ts && $now < $start_ts) || ($end_ts && $now > $end_ts)) {
                    continue;
                }

                $mime_type = get_post_mime_type($banner_id);
                if (strpos((string) $mime_type, 'video') !== false) {
                    $banner_url = wp_get_attachment_url($banner_id);
                    $banner_alt = '';
                } else {
                    $banner_url = wp_get_attachment_image_url($banner_id, 'full');
                    $banner_alt = get_post_meta($banner_id, '_wp_attachment_image_alt', true);
                }

                $link_url = isset($links[ $idx ]) ? $links[ $idx ] : '';
                $dn_id    = isset($doanh_nghiep_ids[ $idx ]) ? (int) $doanh_nghiep_ids[ $idx ] : 0;
                $dn_post  = $dn_id ? get_post($dn_id) : null;
                $detail_url = '';
                if ($dn_post && $dn_post->post_type === 'doanh_nghiep' && $dn_post->post_status === 'publish') {
                    $detail_url = get_permalink($dn_post);
                }
                $banner_link_url = $detail_url ? $detail_url : $link_url;

                if ($banner_url) {
                    if ($index < $offset) {
                        $index++;
                        continue;
                    }
                    if ($limit !== null && $index >= $offset + $limit) {
                        return $output;
                    }

                    $output .= '<div class="' . esc_attr($class_prefix) . ' ' . esc_attr($data['type']) . '">';
                    $banner_title = $dn_post ? $dn_post->post_title : $data['title'];
                    $output .= '<h4>';
                    if ($dn_post && $detail_url) {
                        $output .= '<a href="' . esc_url($detail_url) . '" style="color: inherit; text-decoration: none;">' . esc_html($banner_title) . '</a>';
                    } else {
                        $output .= esc_html($banner_title);
                    }
                    $output .= '</h4>';
                    $output .= '<div class="ad-type">' . esc_html($data['label']) . '</div>';
                    if ($banner_link_url) {
                        $output .= '<a href="' . esc_url($banner_link_url) . '" style="display: block;">';
                    }
                    $mime_type = get_post_mime_type($banner_id);
                    if (strpos((string) $mime_type, 'video') !== false) {
                        $output .= '<video src="' . esc_url($banner_url) . '" autoplay muted loop playsinline style="width: 100%; max-width: 100%;"></video>';
                    } else {
                        $output .= '<img src="' . esc_url($banner_url) . '" alt="' . esc_attr($banner_alt) . '" style="width: 100%; max-width: 100%;">';
                    }
                    if ($banner_link_url) {
                        $output .= '</a>';
                    }
                    $output .= '</div>';
                    $index++;
                }
            }
        }
    }

    return $output;
}

// Add Custom Fields to Search
function dnttvn_extend_admin_search($search, $wp_query) {
    global $wpdb;
    
    if (!is_admin() || !$wp_query->is_search() || !isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'doanh_nghiep') {
        return $search;
    }
    
    $search_term = $wp_query->query_vars['s'];
    $search = " AND (
        {$wpdb->posts}.post_title LIKE '%" . esc_sql($wpdb->esc_like($search_term)) . "%'
        OR {$wpdb->posts}.post_content LIKE '%" . esc_sql($wpdb->esc_like($search_term)) . "%'
        OR EXISTS (
            SELECT 1 FROM {$wpdb->postmeta}
            WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            AND (
                {$wpdb->postmeta}.meta_key = '_nganh_hang'
                OR {$wpdb->postmeta}.meta_key = '_khu_vuc'
            )
            AND {$wpdb->postmeta}.meta_value LIKE '%" . esc_sql($wpdb->esc_like($search_term)) . "%'
        )
    )";
    
    return $search;
}
add_filter('posts_search', 'dnttvn_extend_admin_search', 10, 2);

// ============================================
// BANNER MANAGEMENT
// ============================================

// Register Customizer for Header Banners
function dnttvn_customize_register($wp_customize) {
    // Add Section for Header Banners
    $wp_customize->add_section('dnttvn_header_banners', array(
        'title'    => 'Banner Header',
        'priority' => 30,
    ));
    
    // Banner 1
    $wp_customize->add_setting('dnttvn_banner_1', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_1', array(
        'label'     => 'Banner 1',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 2
    $wp_customize->add_setting('dnttvn_banner_2', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_2', array(
        'label'     => 'Banner 2',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 3
    $wp_customize->add_setting('dnttvn_banner_3', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_3', array(
        'label'     => 'Banner 3',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 4
    $wp_customize->add_setting('dnttvn_banner_4', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_4', array(
        'label'     => 'Banner 4',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    // Header Logo (Specific logo for header)
    $wp_customize->add_setting('dnttvn_header_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_header_logo', array(
        'label'       => 'Logo Header (Riêng)',
        'section'     => 'dnttvn_header_banners',
        'description' => 'Chọn logo hiển thị trên Header (thường là logo màu sáng/trong suốt). Nếu trống sẽ dùng Logo mặc định.',
    )));

    // Banner 5
    $wp_customize->add_setting('dnttvn_banner_5', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_5', array(
        'label'     => 'Banner 5',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner Mobile (riêng cho mobile)
    $wp_customize->add_setting('dnttvn_banner_mobile', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_mobile', array(
        'label'       => 'Banner Mobile',
        'section'     => 'dnttvn_header_banners',
        'description' => 'Banner riêng cho giao diện điện thoại (360x230). Nếu trống sẽ dùng Banner Desktop.',
    )));

    // Banner Order (Thứ tự hiển thị)
    $wp_customize->add_setting('dnttvn_banner_order', array(
        'default'           => '1,2,3,4,5',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('dnttvn_banner_order', array(
        'label'       => 'Thứ tự hiển thị Banner',
        'section'     => 'dnttvn_header_banners',
        'type'        => 'text',
        'description' => 'Nhập thứ tự banner (ví dụ: 1,2,3,4,5 hoặc 5,4,3,2,1). Phân cách bằng dấu phẩy.',
    ));

    // ================================
    // Social Links (Header)
    // ================================
    $wp_customize->add_section('dnttvn_social_links', array(
        'title'       => 'Liên kết mạng xã hội (Header)',
        'priority'    => 40,
        'description' => 'Cấu hình link Facebook, TikTok, Zalo, YouTube hiển thị ở phần KÊNH LIÊN KẾT trên header.',
    ));

    $social_networks = array(
        'facebook' => 'Facebook',
        'tiktok'   => 'TikTok',
        'zalo'     => 'Zalo',
        'youtube'  => 'YouTube',
    );

    foreach ($social_networks as $key => $label) {
        $setting_id = 'dnttvn_social_' . $key . '_url';
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $label . ' URL',
            'section'     => 'dnttvn_social_links',
            'type'        => 'url',
            'input_attrs' => array(
                'placeholder' => 'https://...',
            ),
        ));
    }

    // ================================
    // Sidebar Links (Website liên kết)
    // ================================
    // Community links now managed via admin page (Quản lý Link Cộng đồng)
    // Old Customizer section commented out for reference
    /*
    $wp_customize->add_section('dnttvn_sidebar_links', array(
        'title'       => 'Liên kết Website (Sidebar)',
        'priority'    => 50,
        'description' => 'Cấu hình link cho các mục "Cộng đồng" ở cột phải.',
    ));

    $sidebar_links = array(
        'cong_dong_main' => 'Trang Cộng đồng',
        'cong_dong_tre'  => 'Cộng đồng Doanh nhân Trẻ',
        'khoi_nghiep'    => 'Cộng đồng Khởi nghiệp',
        'dau_tu'         => 'Cộng đồng Đầu tư',
    );

    foreach ($sidebar_links as $key => $label) {
        $setting_id = 'dnttvn_sidebar_link_' . $key;
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $label . ' – URL',
            'section'     => 'dnttvn_sidebar_links',
            'type'        => 'url',
            'input_attrs' => array(
                'placeholder' => 'https://...',
            ),
        ));
    }
    */
}
add_action('customize_register', 'dnttvn_customize_register');

// Header Banner Settings Page
function dnttvn_header_banner_settings_page() {
    // Xử lý lưu dữ liệu logo header
    if (isset($_POST['dnttvn_save_header_logo']) && check_admin_referer('dnttvn_save_header_logo_action', 'dnttvn_save_header_logo_nonce')) {
        if (isset($_POST['header_logo'])) {
            set_theme_mod('dnttvn_header_logo', absint($_POST['header_logo']));
        }
        echo '<div class="notice notice-success"><p>Đã lưu Logo Header thành công!</p></div>';
    }

    // Xử lý lưu dữ liệu header banner
    if (isset($_POST['dnttvn_save_banner_header']) && check_admin_referer('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce')) {
        if (isset($_POST['header_banner_1'])) {
            set_theme_mod('dnttvn_banner_1', absint($_POST['header_banner_1']));
            set_theme_mod('dnttvn_banner_2', 0);
            set_theme_mod('dnttvn_banner_3', 0);
            set_theme_mod('dnttvn_banner_4', 0);
            set_theme_mod('dnttvn_banner_5', 0);
            set_theme_mod('dnttvn_banner_order', '1');
        }
        if (isset($_POST['header_banner_mobile'])) {
            set_theme_mod('dnttvn_banner_mobile', absint($_POST['header_banner_mobile']));
        }
        echo '<div class="notice notice-success"><p>Đã lưu Banner Header thành công!</p></div>';
    }

    // Header banners (for header slider)
    $header_banners = array();
    for ($i = 1; $i <= 5; $i++) {
        $header_banners[$i] = get_theme_mod('dnttvn_banner_' . $i, '');
    }
    $header_banner_order = get_theme_mod('dnttvn_banner_order', '1,2,3,4,5');
    $header_start        = get_option('dnttvn_header_start', array());
    $header_end          = get_option('dnttvn_header_end', array());

    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Header (Logo & Banner)</h1>
        <p class="description">Cấu hình Logo và Banner hiển thị ở phần đầu trang.</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
            <!-- Cột 1: Quản lý Logo -->
            <div class="header-logo-management" style="padding: 20px; border: 1px solid #ccd0d4; border-radius: 8px; background: #fff;">
                <h2 style="margin-top:0;">1. Logo Header</h2>
                <p class="description">Logo này hiển thị bên trái của thanh Header.</p>
                <form method="post" action="">
                    <?php wp_nonce_field('dnttvn_save_header_logo_action', 'dnttvn_save_header_logo_nonce'); ?>
                    <?php $header_logo_id = get_theme_mod('dnttvn_header_logo', ''); ?>
                    <p>
                        <input type="hidden" name="header_logo" id="header_logo_id" value="<?php echo esc_attr($header_logo_id); ?>">
                        <button type="button" class="button button-secondary" id="upload_header_logo_btn">📁 Chọn Logo</button>
                        <button type="button" class="button" id="remove_header_logo_btn" style="<?php echo $header_logo_id ? '' : 'display:none;'; ?>">🗑️ Xóa</button>
                    </p>
                    <div id="header_logo_preview" style="margin: 15px 0; min-height: 100px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                        <?php if ($header_logo_id) echo wp_get_attachment_image($header_logo_id, 'full', false, array('style' => 'max-height: 95px; width: auto;')); else echo '<span style="color:#999;">Chưa có logo</span>'; ?>
                    </div>
                    <p>
                        <button type="submit" name="dnttvn_save_header_logo" class="button button-primary" style="width:100%;">Lưu Logo</button>
                    </p>
                </form>
            </div>

            <!-- Cột 2: Quản lý Banner Desktop + Mobile -->
            <div class="header-banner-management" style="padding: 20px; border: 1px solid #ccd0d4; border-radius: 8px; background: #fff;">
                <h2 style="margin-top:0;">2. Banner Header</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce'); ?>

                    <!-- Banner Desktop -->
                    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 6px; background: #fafafa;">
                        <h3 style="margin-top:0; font-size: 15px;">🖥️ Banner Desktop <span style="color:#999; font-weight:normal; font-size:12px;">(1920 x 400px)</span></h3>
                        <p class="description" style="margin-bottom:10px;">Banner hiển thị trên máy tính, tablet lớn.</p>
                        <?php $banner_id = get_theme_mod('dnttvn_banner_1', ''); ?>
                        <p>
                            <input type="hidden" name="header_banner_1" id="header_banner_1_id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-secondary" id="upload_banner_btn">📁 Chọn Banner Desktop</button>
                            <button type="button" class="button" id="remove_banner_btn" style="<?php echo $banner_id ? '' : 'display:none;'; ?>">🗑️ Xóa</button>
                        </p>
                        <div id="banner_preview" style="margin: 10px 0 0; min-height: 80px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                            <?php if ($banner_id) echo wp_get_attachment_image($banner_id, 'medium'); else echo '<span style="color:#999;">Chưa có banner desktop</span>'; ?>
                        </div>
                    </div>

                    <!-- Banner Mobile -->
                    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #d4edda; border-radius: 6px; background: #f0fff4;">
                        <h3 style="margin-top:0; font-size: 15px;">📱 Banner Mobile <span style="color:#999; font-weight:normal; font-size:12px;">(360 x 230px)</span></h3>
                        <p class="description" style="margin-bottom:10px;">Banner riêng cho điện thoại. Nếu trống sẽ dùng hình mặc định hoặc banner desktop thu nhỏ.</p>
                        <?php $banner_mobile_id = get_theme_mod('dnttvn_banner_mobile', ''); ?>
                        <p>
                            <input type="hidden" name="header_banner_mobile" id="header_banner_mobile_id" value="<?php echo esc_attr($banner_mobile_id); ?>">
                            <button type="button" class="button button-secondary" id="upload_banner_mobile_btn">📁 Chọn Banner Mobile</button>
                            <button type="button" class="button" id="remove_banner_mobile_btn" style="<?php echo $banner_mobile_id ? '' : 'display:none;'; ?>">🗑️ Xóa</button>
                        </p>
                        <div id="banner_mobile_preview" style="margin: 10px 0 0; min-height: 80px; max-width: 360px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                            <?php if ($banner_mobile_id) echo wp_get_attachment_image($banner_mobile_id, 'medium', false, array('style' => 'max-width:360px; height:auto;')); else echo '<span style="color:#999;">Chưa có banner mobile</span>'; ?>
                        </div>
                    </div>

                    <p>
                        <button type="submit" name="dnttvn_save_banner_header" class="button button-primary" style="width:100%;">Lưu Banner (Desktop + Mobile)</button>
                    </p>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($){
            // Logo upload
            $('#upload_header_logo_btn').click(function(e) {
                e.preventDefault();
                var uploader = wp.media({
                    title: 'Chọn Logo Header',
                    button: { text: 'Sử dụng Logo này' },
                    multiple: false
                }).on('select', function() {
                    var attachment = uploader.state().get('selection').first().toJSON();
                    $('#header_logo_id').val(attachment.id);
                    $('#header_logo_preview').html('<img src="'+attachment.url+'" style="max-height:95px; width:auto; display:block;">');
                    $('#remove_header_logo_btn').show();
                }).open();
            });
            $('#remove_header_logo_btn').click(function() {
                $('#header_logo_id').val('');
                $('#header_logo_preview').html('<span style="color:#999;">Chưa có logo</span>');
                $(this).hide();
            });

            // Banner Desktop upload
            $('#upload_banner_btn').click(function(e) {
                e.preventDefault();
                var uploader = wp.media({
                    title: 'Chọn Banner Desktop',
                    button: { text: 'Sử dụng Banner này' },
                    multiple: false
                }).on('select', function() {
                    var attachment = uploader.state().get('selection').first().toJSON();
                    $('#header_banner_1_id').val(attachment.id);
                    $('#banner_preview').html('<img src="'+attachment.url+'" style="max-width:100%; height:auto; display:block;">');
                    $('#remove_banner_btn').show();
                }).open();
            });
            $('#remove_banner_btn').click(function() {
                $('#header_banner_1_id').val('');
                $('#banner_preview').html('<span style="color:#999;">Chưa có banner desktop</span>');
                $(this).hide();
            });

            // Banner Mobile upload
            $('#upload_banner_mobile_btn').click(function(e) {
                e.preventDefault();
                var uploader = wp.media({
                    title: 'Chọn Banner Mobile (360x230)',
                    button: { text: 'Sử dụng Banner này' },
                    multiple: false
                }).on('select', function() {
                    var attachment = uploader.state().get('selection').first().toJSON();
                    $('#header_banner_mobile_id').val(attachment.id);
                    $('#banner_mobile_preview').html('<img src="'+attachment.url+'" style="max-width:360px; height:auto; display:block;">');
                    $('#remove_banner_mobile_btn').show();
                }).open();
            });
            $('#remove_banner_mobile_btn').click(function() {
                $('#header_banner_mobile_id').val('');
                $('#banner_mobile_preview').html('<span style="color:#999;">Chưa có banner mobile</span>');
                $(this).hide();
            });
        });
        </script>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        $('.upload-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var type = button.data('type');
            var index = button.data('index');
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Chọn Banner Header',
                button: {
                    text: 'Sử dụng banner này'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                preview.html('<img src="' + attachment.url + '" style="max-width: 300px; max-height: 200px;">');
                if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px;"></video>');
                }
            });

            mediaUploader.open();
        });

        $('.remove-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');

            input.val('');
            preview.html('');
        });
    });
    </script>
    <?php
}

// Ad Banner Settings Page (VVIP, VIP, Standard)
function dnttvn_ad_banner_settings_page() {
    // Xử lý lưu dữ liệu banner quảng cáo
    $has_save_action = isset($_POST['dnttvn_save_banner_vvip']) ||
                      isset($_POST['dnttvn_save_banner_vip']) ||
                      isset($_POST['dnttvn_save_banner_standard']) ||
                      isset($_POST['dnttvn_save_banners']);

    $dnttvn_ad_rotate_rule_error = false;

    if ($has_save_action && check_admin_referer('dnttvn_save_ad_banners_action', 'dnttvn_save_ad_banners_nonce')) {
        $is_global      = isset($_POST['dnttvn_save_banners']);
        $save_vvip      = $is_global || isset($_POST['dnttvn_save_banner_vvip']);
        $save_vip       = $is_global || isset($_POST['dnttvn_save_banner_vip']);
        $save_standard  = $is_global || isset($_POST['dnttvn_save_banner_standard']);

        // Save VVIP Ad Blocks
        if ($save_vvip) {
            if (isset($_POST['vvip_banners'])) {
                update_option('dnttvn_vvip_banners', array_map('absint', $_POST['vvip_banners']));
            }
            if (isset($_POST['vvip_links'])) {
                update_option('dnttvn_vvip_links', array_map('esc_url_raw', $_POST['vvip_links']));
            }
            if (isset($_POST['vvip_doanh_nghiep']) && is_array($_POST['vvip_doanh_nghiep'])) {
                update_option('dnttvn_vvip_doanh_nghiep', array_map('absint', $_POST['vvip_doanh_nghiep']));
            }
            // Lưu thời gian hiển thị VVIP
            if (isset($_POST['vvip_start']) && is_array($_POST['vvip_start'])) {
                update_option('dnttvn_vvip_start', array_map('sanitize_text_field', $_POST['vvip_start']));
            }
            if (isset($_POST['vvip_end']) && is_array($_POST['vvip_end'])) {
                update_option('dnttvn_vvip_end', array_map('sanitize_text_field', $_POST['vvip_end']));
            }
        }

        // Save VIP Ad Blocks
        if ($save_vip) {
            if (isset($_POST['vip_banners'])) {
                update_option('dnttvn_vip_banners', array_map('absint', $_POST['vip_banners']));
            }
            if (isset($_POST['vip_links'])) {
                update_option('dnttvn_vip_links', array_map('esc_url_raw', $_POST['vip_links']));
            }
            // Lưu thời gian hiển thị VIP
            if (isset($_POST['vip_start']) && is_array($_POST['vip_start'])) {
                update_option('dnttvn_vip_start', array_map('sanitize_text_field', $_POST['vip_start']));
            }
            if (isset($_POST['vip_end']) && is_array($_POST['vip_end'])) {
                update_option('dnttvn_vip_end', array_map('sanitize_text_field', $_POST['vip_end']));
            }
            if (isset($_POST['vip_doanh_nghiep']) && is_array($_POST['vip_doanh_nghiep'])) {
                update_option('dnttvn_vip_doanh_nghiep', array_map('absint', $_POST['vip_doanh_nghiep']));
            }
        }

        // Save Standard Ad Blocks
        if ($save_standard) {
            if (isset($_POST['standard_banners'])) {
                update_option('dnttvn_standard_banners', array_map('absint', $_POST['standard_banners']));
            }
            if (isset($_POST['standard_links'])) {
                update_option('dnttvn_standard_links', array_map('esc_url_raw', $_POST['standard_links']));
            }
            // Lưu thời gian hiển thị Standard
            if (isset($_POST['standard_start']) && is_array($_POST['standard_start'])) {
                update_option('dnttvn_standard_start', array_map('sanitize_text_field', $_POST['standard_start']));
            }
            if (isset($_POST['standard_end']) && is_array($_POST['standard_end'])) {
                update_option('dnttvn_standard_end', array_map('sanitize_text_field', $_POST['standard_end']));
            }
            if (isset($_POST['standard_doanh_nghiep']) && is_array($_POST['standard_doanh_nghiep'])) {
                update_option('dnttvn_standard_doanh_nghiep', array_map('absint', $_POST['standard_doanh_nghiep']));
            }
        }

        // Save Banner Order (Thứ tự hiển thị cột phải)
        if ($is_global && isset($_POST['banner_column_order'])) {
            update_option('dnttvn_banner_column_order', sanitize_text_field($_POST['banner_column_order']));
        }

        if ($is_global) {
            $sec_vvip     = isset($_POST['banner_rotate_sec_vvip']) ? absint($_POST['banner_rotate_sec_vvip']) : 30;
            $sec_vip      = isset($_POST['banner_rotate_sec_vip']) ? absint($_POST['banner_rotate_sec_vip']) : 20;
            $sec_standard = isset($_POST['banner_rotate_sec_standard']) ? absint($_POST['banner_rotate_sec_standard']) : 15;
            $sec_vvip     = max(3, min(300, $sec_vvip));
            $sec_vip      = max(3, min(300, $sec_vip));
            $sec_standard = max(3, min(300, $sec_standard));

            update_option('dnttvn_banner_sidebar_rotate_interval_vvip', $sec_vvip);
            update_option('dnttvn_banner_sidebar_rotate_interval_vip', $sec_vip);
            update_option('dnttvn_banner_sidebar_rotate_interval_standard', $sec_standard);

            $want_rotate = isset($_POST['banner_sidebar_rotate']);
            if ($want_rotate) {
                $cycle_parts = array();
                foreach (array('vvip' => 'VVIP', 'vip' => 'VIP', 'standard' => 'Standard') as $slug => $lab) {
                    $n = dnttvn_count_filled_banner_slots_from_post($_POST, $slug);
                    if ($n < 2) {
                        continue;
                    }
                    $sec = ('vvip' === $slug) ? $sec_vvip : (('vip' === $slug) ? $sec_vip : $sec_standard);
                    $cycle_parts[] = array(
                        'label' => $lab,
                        'n'     => $n,
                        'sec'   => $sec,
                        'prod'  => $n * $sec,
                    );
                }
                $products = array_unique(array_column($cycle_parts, 'prod'));
                if (count($cycle_parts) >= 2 && count($products) > 1) {
                    $dnttvn_ad_rotate_rule_error = true;
                    $msg_bits                    = array();
                    foreach ($cycle_parts as $p) {
                        $msg_bits[] = $p['label'] . ': ' . $p['n'] . ' mục × ' . $p['sec'] . 's = ' . $p['prod'] . 's';
                    }
                    echo '<div class="notice notice-error"><p><strong>Chưa bật xoay.</strong> Khi tick bật xoay, <strong>tổng thời gian một vòng</strong> (số mục có media × giây mỗi slide) phải <strong>bằng nhau</strong> giữa các hạng có từ 2 mục trở lên. Ví dụ chuẩn: VVIP 4×30s = VIP 6×20s = Standard 8×15s = 120s. Hiện tại: ' . esc_html(implode('; ', $msg_bits)) . '. Đã lưu số giây từng hạng; chỉnh lại cho khớp rồi lưu lại.</p></div>';
                } else {
                    update_option('dnttvn_banner_sidebar_rotate', '1');
                }
            } else {
                update_option('dnttvn_banner_sidebar_rotate', '0');
            }
        }

        echo '<div class="notice notice-success"><p>Đã lưu banner quảng cáo thành công!';
        if ($dnttvn_ad_rotate_rule_error) {
            echo ' <strong>Cài đặt xoay giữ nguyên</strong> — chỉnh số mục hoặc số giây cho khớp tổng một vòng.';
        }
        echo '</p></div>';
    }

    // Migrate: một giây chung cũ → ba option theo hạng (một lần)
    if (! get_option('dnttvn_sidebar_rotate_intervals_split_done')) {
        $old = get_option('dnttvn_banner_sidebar_rotate_interval');
        if (false !== $old && '' !== $old) {
            $m = max(3, min(300, absint($old)));
            update_option('dnttvn_banner_sidebar_rotate_interval_vvip', $m);
            update_option('dnttvn_banner_sidebar_rotate_interval_vip', $m);
            update_option('dnttvn_banner_sidebar_rotate_interval_standard', $m);
        }
        update_option('dnttvn_sidebar_rotate_intervals_split_done', '1');
    }

    // Load data
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    $banner_sidebar_rotate = (get_option('dnttvn_banner_sidebar_rotate', '') === '1');
    $banner_rotate_sec_vvip     = dnttvn_get_sidebar_rotate_interval_for_tier('vvip');
    $banner_rotate_sec_vip      = dnttvn_get_sidebar_rotate_interval_for_tier('vip');
    $banner_rotate_sec_standard = dnttvn_get_sidebar_rotate_interval_for_tier('standard');
    $vvip_banners  = get_option('dnttvn_vvip_banners', array());
    $vvip_links    = get_option('dnttvn_vvip_links', array());
    $vvip_doanh_nghiep = get_option('dnttvn_vvip_doanh_nghiep', array());
    $vvip_start    = get_option('dnttvn_vvip_start', array());
    $vvip_end      = get_option('dnttvn_vvip_end', array());
    $vip_banners   = get_option('dnttvn_vip_banners', array());
    $vip_links     = get_option('dnttvn_vip_links', array());
    $vip_doanh_nghiep = get_option('dnttvn_vip_doanh_nghiep', array());
    $vip_start     = get_option('dnttvn_vip_start', array());
    $vip_end       = get_option('dnttvn_vip_end', array());
    $standard_banners = get_option('dnttvn_standard_banners', array());
    $standard_links   = get_option('dnttvn_standard_links', array());
    $standard_doanh_nghiep = get_option('dnttvn_standard_doanh_nghiep', array());
    $standard_start   = get_option('dnttvn_standard_start', array());
    $standard_end     = get_option('dnttvn_standard_end', array());

    $doanh_nghiep_posts = get_posts(array('post_type' => 'doanh_nghiep', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC'));

    $dnttvn_ad_dn_options = '';
    foreach ($doanh_nghiep_posts as $dn_post) {
        $dnttvn_ad_dn_options .= '<option value="' . esc_attr((string) $dn_post->ID) . '">' . esc_html($dn_post->post_title) . '</option>';
    }

    wp_enqueue_media();
    ?>
    <style>
        .dnttvn-ad-wrap .dnttvn-ad-intro { max-width: 720px; margin-bottom: 16px; font-size: 14px; }
        .dnttvn-ad-wrap .dnttvn-ad-nav { margin: 0 0 -1px; padding-top: 9px; }
        .dnttvn-ad-wrap .dnttvn-ad-nav .nav-tab { font-size: 14px; }
        .dnttvn-ad-tab-panel {
            display: none;
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 20px 24px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .dnttvn-ad-tab-panel.is-active { display: block; }
        .dnttvn-tier-intro {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 18px;
            border-left: 4px solid #646970;
            background: #f6f7f7;
            font-size: 13px;
        }
        .dnttvn-tier-vvip .dnttvn-tier-intro { border-left-color: #b8860b; background: #fffbeb; }
        .dnttvn-tier-vip .dnttvn-tier-intro { border-left-color: #6b7280; background: #f9fafb; }
        .dnttvn-tier-standard .dnttvn-tier-intro { border-left-color: #a16207; background: #fffbf5; }
        .dnttvn-banner-card {
            border: 1px solid #dcdcde;
            border-radius: 6px;
            margin-bottom: 16px;
            background: #fcfcfc;
            overflow: hidden;
        }
        .dnttvn-banner-card__head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: #f6f7f7;
            border-bottom: 1px solid #dcdcde;
        }
        .dnttvn-tier-vvip .dnttvn-banner-card__head { background: linear-gradient(90deg, #fff9e6 0%, #f6f7f7 100%); border-left: 4px solid #b8860b; }
        .dnttvn-tier-vip .dnttvn-banner-card__head { background: linear-gradient(90deg, #f3f4f6 0%, #f6f7f7 100%); border-left: 4px solid #6b7280; }
        .dnttvn-tier-standard .dnttvn-banner-card__head { background: linear-gradient(90deg, #faf5f0 0%, #f6f7f7 100%); border-left: 4px solid #a16207; }
        .dnttvn-banner-tier-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 4px;
            background: #1d2327;
            color: #fff;
        }
        .dnttvn-tier-vvip .dnttvn-banner-tier-badge { background: #b8860b; }
        .dnttvn-tier-vip .dnttvn-banner-tier-badge { background: #4b5563; }
        .dnttvn-tier-standard .dnttvn-banner-tier-badge { background: #92400e; }
        .dnttvn-banner-card__title { margin: 0; font-size: 15px; line-height: 1.3; }
        .dnttvn-banner-card__body { padding: 8px 16px 4px; }
        .dnttvn-banner-card__body .form-table th { width: 160px; padding-left: 0; vertical-align: top; padding-top: 14px; }
        .dnttvn-banner-card__body .form-table td .description { margin-top: 6px; }
        .dnttvn-banner-preview-zone {
            min-height: 72px;
            padding: 10px;
            background: #fff;
            border: 1px dashed #c3c4c7;
            border-radius: 4px;
            margin-top: 6px;
        }
        .dnttvn-ad-submit-bar {
            position: sticky;
            bottom: 0;
            z-index: 50;
            background: #f0f0f1;
            margin: 24px -20px -10px;
            padding: 16px 20px;
            border-top: 1px solid #c3c4c7;
            box-shadow: 0 -2px 8px rgba(0,0,0,.06);
        }
        .dnttvn-ad-submit-bar .button-primary { min-height: 36px; padding: 0 18px; font-size: 14px; }
        .dnttvn-ad-general-box { max-width: 640px; }
    </style>
    <div class="wrap dnttvn-ad-wrap">
        <h1>Quản lý Banner Quảng cáo</h1>
        <p class="dnttvn-ad-intro">Banner và video hiển thị ở <strong>cột phải</strong> trang danh sách doanh nghiệp. Chọn từng <strong>hạng</strong> bên dưới để thêm hoặc sửa từng mục. Nhấn <strong>Lưu tất cả</strong> ở cuối trang sau khi chỉnh.</p>

        <form method="post" action="" class="dnttvn-ad-form">
            <?php wp_nonce_field('dnttvn_save_ad_banners_action', 'dnttvn_save_ad_banners_nonce'); ?>
            <div id="dnttvn-dn-options-store" style="display:none" aria-hidden="true"><?php echo $dnttvn_ad_dn_options; ?></div>

            <h2 class="nav-tab-wrapper dnttvn-ad-nav wp-clearfix">
                <a href="#" class="nav-tab nav-tab-active" data-dnttvn-tab="general">Cài đặt chung</a>
                <a href="#" class="nav-tab" data-dnttvn-tab="vvip">Hạng VVIP</a>
                <a href="#" class="nav-tab" data-dnttvn-tab="vip">Hạng VIP</a>
                <a href="#" class="nav-tab" data-dnttvn-tab="standard">Hạng Standard</a>
            </h2>

            <div class="dnttvn-ad-panels">
            <div id="dnttvn-tab-general" class="dnttvn-ad-tab-panel is-active dnttvn-ad-general-box">
                <h3 style="margin-top:0;">Thứ tự &amp; hiệu ứng cột phải</h3>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="banner_column_order">Thứ tự các hạng trên website</label></th>
                        <td>
                            <select name="banner_column_order" id="banner_column_order" class="regular-text">
                                <option value="vvip,vip,standard" <?php selected($banner_column_order, 'vvip,vip,standard'); ?>>VVIP → VIP → Standard</option>
                                <option value="vvip,standard,vip" <?php selected($banner_column_order, 'vvip,standard,vip'); ?>>VVIP → Standard → VIP</option>
                                <option value="vip,vvip,standard" <?php selected($banner_column_order, 'vip,vvip,standard'); ?>>VIP → VVIP → Standard</option>
                                <option value="vip,standard,vvip" <?php selected($banner_column_order, 'vip,standard,vvip'); ?>>VIP → Standard → VVIP</option>
                                <option value="standard,vvip,vip" <?php selected($banner_column_order, 'standard,vvip,vip'); ?>>Standard → VVIP → VIP</option>
                                <option value="standard,vip,vvip" <?php selected($banner_column_order, 'standard,vip,vvip'); ?>>Standard → VIP → VVIP</option>
                            </select>
                            <p class="description">Thứ tự các khối VVIP / VIP / Standard xếp dọc ở sidebar.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Xoay tự động (desktop)</th>
                        <td>
                            <label>
                                <input type="checkbox" name="banner_sidebar_rotate" id="banner_sidebar_rotate" value="1" <?php checked($banner_sidebar_rotate); ?>>
                                Bật xoay — mỗi hạng chỉ hiện <strong>một</strong> mục tại một thời điểm; sau <em>đúng số giây của hạng đó</em> chuyển mục kế.
                            </label>
                            <p class="description" style="margin-top:12px;">Mỗi hạng có <strong>thời gian mỗi slide</strong> riêng (3–300 giây). Số mục có ảnh/video do bạn thêm ở tab VVIP / VIP / Standard. Khi bật xoay: các hạng nào có <strong>từ 2 mục trở lên</strong> thì <strong>tổng một vòng</strong> (số mục × giây) phải <strong>bằng nhau</strong>. Ví dụ chuẩn: <strong>4×30 = 6×20 = 8×15 = 120</strong> giây.</p>
                            <table class="form-table" style="margin-top:8px; max-width:520px;" role="presentation">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="padding:8px 12px 8px 0;"><label for="banner_rotate_sec_vvip">VVIP — giây / slide</label></th>
                                        <td style="padding:8px 0;"><input type="number" name="banner_rotate_sec_vvip" id="banner_rotate_sec_vvip" value="<?php echo esc_attr((string) $banner_rotate_sec_vvip); ?>" min="3" max="300" step="1" class="small-text dnttvn-rot-sec" data-tier="vvip"> <span class="description dnttvn-rot-hint" data-tier="vvip"></span></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="padding:8px 12px 8px 0;"><label for="banner_rotate_sec_vip">VIP — giây / slide</label></th>
                                        <td style="padding:8px 0;"><input type="number" name="banner_rotate_sec_vip" id="banner_rotate_sec_vip" value="<?php echo esc_attr((string) $banner_rotate_sec_vip); ?>" min="3" max="300" step="1" class="small-text dnttvn-rot-sec" data-tier="vip"> <span class="description dnttvn-rot-hint" data-tier="vip"></span></td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="padding:8px 12px 8px 0;"><label for="banner_rotate_sec_standard">Standard — giây / slide</label></th>
                                        <td style="padding:8px 0;"><input type="number" name="banner_rotate_sec_standard" id="banner_rotate_sec_standard" value="<?php echo esc_attr((string) $banner_rotate_sec_standard); ?>" min="3" max="300" step="1" class="small-text dnttvn-rot-sec" data-tier="standard"> <span class="description dnttvn-rot-hint" data-tier="standard"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="description" id="dnttvn-rot-cycle-summary" style="margin-top:10px;"></p>
                            <p class="description">Trên mobile (cột phải): hiện đủ mục xếp dọc, không tự chuyển.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="dnttvn-tab-vvip" class="dnttvn-ad-tab-panel dnttvn-tier-vvip">
                <div class="dnttvn-tier-intro">
                    <strong>Hạng VVIP</strong> — gói cao nhất. Thêm nhiều mục (ảnh hoặc video); thứ tự từ trên xuống là thứ tự trong nhóm. Có thể lên lịch hiển thị theo ngày giờ cho từng mục.
                </div>
                <p class="dnttvn-tier-toolbar">
                    <button type="button" id="add-vvip-banner" class="button button-secondary">+ Thêm mục VVIP</button>
                </p>
                <div id="vvip-banners-container">
                <?php
                $vvip_count = max(2, count($vvip_banners));
                for ($i = 0; $i < $vvip_count; $i++) {
                    $banner_id   = isset($vvip_banners[$i]) ? $vvip_banners[$i] : '';
                    $banner_url  = isset($vvip_links[$i]) ? $vvip_links[$i] : '';
                    $start_value = isset($vvip_start[$i]) ? $vvip_start[$i] : '';
                    $end_value   = isset($vvip_end[$i]) ? $vvip_end[$i] : '';
                    ?>
                    <div class="dnttvn-banner-card banner-item">
                        <div class="dnttvn-banner-card__head">
                            <span class="dnttvn-banner-tier-badge">VVIP</span>
                            <h3 class="dnttvn-banner-card__title">Mục <?php echo (int) ($i + 1); ?></h3>
                        </div>
                        <div class="dnttvn-banner-card__body">
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row">Ảnh / video</th>
                                    <td>
                                        <input type="hidden" name="vvip_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                                        <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">Chọn từ thư viện</button>
                                        <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">Gỡ media</button>
                                        <p class="description">JPG, PNG hoặc MP4; nên tỉ lệ dọc cho cột phải.</p>
                                        <div class="banner-preview dnttvn-banner-preview-zone">
                                            <?php if ($banner_id) :
                                                $mime_type = get_post_mime_type($banner_id);
                                                if (strpos($mime_type, 'video') !== false) :
                                                    $video_url = wp_get_attachment_url($banner_id);
                                                    ?>
                                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 320px; max-height: 200px; display: block;"></video>
                                                    <p class="description" style="margin-top:6px;">ID file: <?php echo esc_html((string) $banner_id); ?></p>
                                                <?php else :
                                                    echo wp_get_attachment_image($banner_id, 'medium');
                                                endif;
                                            endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="vvip_links_<?php echo (int) $i; ?>">Link khi bấm</label></th>
                                    <td>
                                        <input type="url" name="vvip_links[<?php echo $i; ?>]" id="vvip_links_<?php echo (int) $i; ?>" value="<?php echo esc_attr($banner_url); ?>" class="large-text" placeholder="https://...">
                                        <p class="description">Nếu đã chọn doanh nghiệp bên dưới, ưu tiên link tới trang chi tiết DN.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="vvip_dn_<?php echo (int) $i; ?>">Doanh nghiệp</label></th>
                                    <td>
                                        <select name="vvip_doanh_nghiep[<?php echo $i; ?>]" id="vvip_dn_<?php echo (int) $i; ?>" class="regular-text">
                                            <option value="0">— Không gắn bài DN —</option>
                                            <?php foreach ($doanh_nghiep_posts as $dn_post) : $sel = (isset($vvip_doanh_nghiep[$i]) && (int) $vvip_doanh_nghiep[$i] === (int) $dn_post->ID) ? ' selected' : ''; ?>
                                                <option value="<?php echo esc_attr($dn_post->ID); ?>"<?php echo $sel; ?>><?php echo esc_html($dn_post->post_title); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="description">Hiển thị tên bài làm tiêu đề khối; link tới chi tiết doanh nghiệp.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Lịch hiển thị</th>
                                    <td>
                                        <input type="datetime-local" name="vvip_start[<?php echo $i; ?>]" value="<?php echo esc_attr($start_value); ?>" class="regular-text" style="max-width:220px;">
                                        <span aria-hidden="true"> → </span>
                                        <input type="datetime-local" name="vvip_end[<?php echo $i; ?>]" value="<?php echo esc_attr($end_value); ?>" class="regular-text" style="max-width:220px;">
                                        <p class="description">Để trống cả hai = luôn hiển thị (nếu đủ điều kiện khác).</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            </div>

            <div id="dnttvn-tab-vip" class="dnttvn-ad-tab-panel dnttvn-tier-vip">
                <div class="dnttvn-tier-intro">
                    <strong>Hạng VIP</strong> — thêm nhiều mục quảng cáo; cấu hình giống VVIP (media, link, doanh nghiệp, lịch).
                </div>
                <p class="dnttvn-tier-toolbar">
                    <button type="button" id="add-vip-banner" class="button button-secondary">+ Thêm mục VIP</button>
                </p>
                <div id="vip-banners-container">
                <?php
                $vip_count = max(2, count($vip_banners));
                for ($i = 0; $i < $vip_count; $i++) {
                    $banner_id   = isset($vip_banners[$i]) ? $vip_banners[$i] : '';
                    $banner_url  = isset($vip_links[$i]) ? $vip_links[$i] : '';
                    $start_value = isset($vip_start[$i]) ? $vip_start[$i] : '';
                    $end_value   = isset($vip_end[$i]) ? $vip_end[$i] : '';
                    ?>
                    <div class="dnttvn-banner-card banner-item">
                        <div class="dnttvn-banner-card__head">
                            <span class="dnttvn-banner-tier-badge">VIP</span>
                            <h3 class="dnttvn-banner-card__title">Mục <?php echo (int) ($i + 1); ?></h3>
                        </div>
                        <div class="dnttvn-banner-card__body">
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row">Ảnh / video</th>
                                    <td>
                                        <input type="hidden" name="vip_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                                        <button type="button" class="button button-primary upload-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">Chọn từ thư viện</button>
                                        <button type="button" class="button remove-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">Gỡ media</button>
                                        <p class="description">JPG, PNG hoặc MP4.</p>
                                        <div class="banner-preview dnttvn-banner-preview-zone">
                                            <?php if ($banner_id) :
                                                $mime_type = get_post_mime_type($banner_id);
                                                if (strpos($mime_type, 'video') !== false) :
                                                    $video_url = wp_get_attachment_url($banner_id);
                                                    ?>
                                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 320px; max-height: 200px; display: block;"></video>
                                                    <p class="description" style="margin-top:6px;">ID file: <?php echo esc_html((string) $banner_id); ?></p>
                                                <?php else :
                                                    echo wp_get_attachment_image($banner_id, 'medium');
                                                endif;
                                            endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="vip_links_<?php echo (int) $i; ?>">Link khi bấm</label></th>
                                    <td>
                                        <input type="url" name="vip_links[<?php echo $i; ?>]" id="vip_links_<?php echo (int) $i; ?>" value="<?php echo esc_attr($banner_url); ?>" class="large-text" placeholder="https://...">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="vip_dn_<?php echo (int) $i; ?>">Doanh nghiệp</label></th>
                                    <td>
                                        <select name="vip_doanh_nghiep[<?php echo $i; ?>]" id="vip_dn_<?php echo (int) $i; ?>" class="regular-text">
                                            <option value="0">— Không gắn bài DN —</option>
                                            <?php foreach ($doanh_nghiep_posts as $dn_post) : $sel = (isset($vip_doanh_nghiep[$i]) && (int) $vip_doanh_nghiep[$i] === (int) $dn_post->ID) ? ' selected' : ''; ?>
                                                <option value="<?php echo esc_attr($dn_post->ID); ?>"<?php echo $sel; ?>><?php echo esc_html($dn_post->post_title); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Lịch hiển thị</th>
                                    <td>
                                        <input type="datetime-local" name="vip_start[<?php echo $i; ?>]" value="<?php echo esc_attr($start_value); ?>" class="regular-text" style="max-width:220px;">
                                        <span aria-hidden="true"> → </span>
                                        <input type="datetime-local" name="vip_end[<?php echo $i; ?>]" value="<?php echo esc_attr($end_value); ?>" class="regular-text" style="max-width:220px;">
                                        <p class="description">Để trống = không giới hạn.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            </div>

            <div id="dnttvn-tab-standard" class="dnttvn-ad-tab-panel dnttvn-tier-standard">
                <div class="dnttvn-tier-intro">
                    <strong>Hạng Standard</strong> — mục quảng cáo tiêu chuẩn; cấu hình tương tự các hạng trên.
                </div>
                <p class="dnttvn-tier-toolbar">
                    <button type="button" id="add-standard-banner" class="button button-secondary">+ Thêm mục Standard</button>
                </p>
                <div id="standard-banners-container">
                <?php
                $standard_count = max(2, count($standard_banners));
                for ($i = 0; $i < $standard_count; $i++) {
                    $banner_id   = isset($standard_banners[$i]) ? $standard_banners[$i] : '';
                    $banner_url  = isset($standard_links[$i]) ? $standard_links[$i] : '';
                    $start_value = isset($standard_start[$i]) ? $standard_start[$i] : '';
                    $end_value   = isset($standard_end[$i]) ? $standard_end[$i] : '';
                    ?>
                    <div class="dnttvn-banner-card banner-item">
                        <div class="dnttvn-banner-card__head">
                            <span class="dnttvn-banner-tier-badge">Standard</span>
                            <h3 class="dnttvn-banner-card__title">Mục <?php echo (int) ($i + 1); ?></h3>
                        </div>
                        <div class="dnttvn-banner-card__body">
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row">Ảnh / video</th>
                                    <td>
                                        <input type="hidden" name="standard_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                                        <button type="button" class="button button-primary upload-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">Chọn từ thư viện</button>
                                        <button type="button" class="button remove-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">Gỡ media</button>
                                        <p class="description">JPG, PNG hoặc MP4.</p>
                                        <div class="banner-preview dnttvn-banner-preview-zone">
                                            <?php if ($banner_id) :
                                                $mime_type = get_post_mime_type($banner_id);
                                                if (strpos($mime_type, 'video') !== false) :
                                                    $video_url = wp_get_attachment_url($banner_id);
                                                    ?>
                                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 320px; max-height: 200px; display: block;"></video>
                                                    <p class="description" style="margin-top:6px;">ID file: <?php echo esc_html((string) $banner_id); ?></p>
                                                <?php else :
                                                    echo wp_get_attachment_image($banner_id, 'medium');
                                                endif;
                                            endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="standard_links_<?php echo (int) $i; ?>">Link khi bấm</label></th>
                                    <td>
                                        <input type="url" name="standard_links[<?php echo $i; ?>]" id="standard_links_<?php echo (int) $i; ?>" value="<?php echo esc_attr($banner_url); ?>" class="large-text" placeholder="https://...">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="standard_dn_<?php echo (int) $i; ?>">Doanh nghiệp</label></th>
                                    <td>
                                        <select name="standard_doanh_nghiep[<?php echo $i; ?>]" id="standard_dn_<?php echo (int) $i; ?>" class="regular-text">
                                            <option value="0">— Không gắn bài DN —</option>
                                            <?php foreach ($doanh_nghiep_posts as $dn_post) : $sel = (isset($standard_doanh_nghiep[$i]) && (int) $standard_doanh_nghiep[$i] === (int) $dn_post->ID) ? ' selected' : ''; ?>
                                                <option value="<?php echo esc_attr($dn_post->ID); ?>"<?php echo $sel; ?>><?php echo esc_html($dn_post->post_title); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Lịch hiển thị</th>
                                    <td>
                                        <input type="datetime-local" name="standard_start[<?php echo $i; ?>]" value="<?php echo esc_attr($start_value); ?>" class="regular-text" style="max-width:220px;">
                                        <span aria-hidden="true"> → </span>
                                        <input type="datetime-local" name="standard_end[<?php echo $i; ?>]" value="<?php echo esc_attr($end_value); ?>" class="regular-text" style="max-width:220px;">
                                        <p class="description">Để trống = không giới hạn.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            </div>
            </div>

            <p class="submit dnttvn-ad-submit-bar">
                <input type="submit" name="dnttvn_save_banners" class="button button-primary button-large" value="Lưu tất cả thay đổi">
                <span class="description" style="margin-left:12px; vertical-align:middle;">Áp dụng cho cả VVIP, VIP, Standard và cài đặt chung.</span>
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var $nav = $('.dnttvn-ad-nav');
        $nav.on('click', 'a.nav-tab', function(e) {
            e.preventDefault();
            var tab = $(this).data('dnttvn-tab');
            if (!tab) {
                return;
            }
            $nav.find('a.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.dnttvn-ad-tab-panel').removeClass('is-active');
            $('#dnttvn-tab-' + tab).addClass('is-active');
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', window.location.pathname + window.location.search + '#' + tab);
            }
        });

        var tabHash = window.location.hash.replace(/^#/, '');
        if (tabHash && $.inArray(tabHash, ['general', 'vvip', 'vip', 'standard']) !== -1) {
            $nav.find('a.nav-tab[data-dnttvn-tab="' + tabHash + '"]').trigger('click');
        }

        $(document).on('click', '.dnttvn-ad-wrap .upload-banner-btn', function(e) {
            e.preventDefault();
            var container = $(this).closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');
            var frame = wp.media({
                title: 'Chọn hình hoặc video quảng cáo',
                button: { text: 'Dùng file này' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.id);
                if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width:320px;max-height:200px;display:block;"></video><p class="description" style="margin-top:6px;">ID file: ' + attachment.id + '</p>');
                } else {
                    preview.html('<img src="' + attachment.url + '" alt="" style="max-width:320px;height:auto;">');
                }
            });
            frame.open();
        });

        $(document).on('click', '.dnttvn-ad-wrap .remove-banner-btn', function(e) {
            e.preventDefault();
            var container = $(this).closest('.banner-item');
            container.find('.banner-image-id').val('');
            container.find('.banner-preview').html('');
        });

        var dnOpts = $('#dnttvn-dn-options-store').html() || '';

        function newBannerCard(tierKey, tierLabel, count) {
            var idBase = tierKey + '_' + count;
            return (
                '<div class="dnttvn-banner-card banner-item">' +
                '<div class="dnttvn-banner-card__head">' +
                '<span class="dnttvn-banner-tier-badge">' + tierLabel + '</span>' +
                '<h3 class="dnttvn-banner-card__title">Mục ' + (count + 1) + '</h3>' +
                '</div>' +
                '<div class="dnttvn-banner-card__body">' +
                '<table class="form-table" role="presentation">' +
                '<tr><th scope="row">Ảnh / video</th><td>' +
                '<input type="hidden" name="' + tierKey + '_banners[' + count + ']" class="banner-image-id" value="">' +
                '<button type="button" class="button button-primary upload-banner-btn" data-type="' + tierKey + '" data-index="' + count + '">Chọn từ thư viện</button> ' +
                '<button type="button" class="button remove-banner-btn" data-type="' + tierKey + '" data-index="' + count + '">Gỡ media</button>' +
                '<p class="description">JPG, PNG hoặc MP4.</p>' +
                '<div class="banner-preview dnttvn-banner-preview-zone"></div></td></tr>' +
                '<tr><th scope="row"><label for="' + idBase + '_link">Link khi bấm</label></th><td>' +
                '<input type="url" name="' + tierKey + '_links[' + count + ']" id="' + idBase + '_link" class="large-text" placeholder="https://..."></td></tr>' +
                '<tr><th scope="row"><label for="' + idBase + '_dn">Doanh nghiệp</label></th><td>' +
                '<select name="' + tierKey + '_doanh_nghiep[' + count + ']" id="' + idBase + '_dn" class="regular-text">' +
                '<option value="0">— Không gắn bài DN —</option>' + dnOpts + '</select></td></tr>' +
                '<tr><th scope="row">Lịch hiển thị</th><td>' +
                '<input type="datetime-local" name="' + tierKey + '_start[' + count + ']" class="regular-text" style="max-width:220px;">' +
                ' <span aria-hidden="true">→</span> ' +
                '<input type="datetime-local" name="' + tierKey + '_end[' + count + ']" class="regular-text" style="max-width:220px;">' +
                '<p class="description">Để trống = không giới hạn.</p></td></tr>' +
                '</table></div></div>'
            );
        }

        $('#add-vvip-banner').on('click', function() {
            var $c = $('#vvip-banners-container');
            $c.append(newBannerCard('vvip', 'VVIP', $c.children('.banner-item').length));
        });
        $('#add-vip-banner').on('click', function() {
            var $c = $('#vip-banners-container');
            $c.append(newBannerCard('vip', 'VIP', $c.children('.banner-item').length));
        });
        $('#add-standard-banner').on('click', function() {
            var $c = $('#standard-banners-container');
            $c.append(newBannerCard('standard', 'Standard', $c.children('.banner-item').length));
        });

        function dnttvnRefreshRotateCycleHints() {
            var tiers = [
                { key: 'vvip', wrap: '#vvip-banners-container', sec: '#banner_rotate_sec_vvip', lab: 'VVIP' },
                { key: 'vip', wrap: '#vip-banners-container', sec: '#banner_rotate_sec_vip', lab: 'VIP' },
                { key: 'standard', wrap: '#standard-banners-container', sec: '#banner_rotate_sec_standard', lab: 'Standard' }
            ];
            var products = [];
            var parts = [];
            tiers.forEach(function(t) {
                var n = 0;
                $(t.wrap).find('.banner-image-id').each(function() {
                    if (parseInt($(this).val(), 10) > 0) {
                        n++;
                    }
                });
                var sec = Math.max(0, parseInt($(t.sec).val(), 10) || 0);
                $('.dnttvn-rot-hint[data-tier="' + t.key + '"]').text(n ? '(' + n + ' mục có media)' : '');
                if (n >= 2) {
                    var p = n * sec;
                    products.push(p);
                    parts.push(t.lab + ': ' + n + ' × ' + sec + 's = <strong>' + p + 's</strong>/vòng');
                } else {
                    parts.push(t.lab + ': ' + (n < 1 ? 'chưa có đủ media' : '1 mục (không xoay trong nhóm)') + (n ? ' · ' + sec + 's/slide' : ''));
                }
            });
            var uniq = products.filter(function(v, i, a) { return a.indexOf(v) === i; });
            var clash = products.length >= 2 && uniq.length > 1;
            var msg = '<strong>Ước tính:</strong> ' + parts.join(' · ');
            if ($('#banner_sidebar_rotate').is(':checked') && clash) {
                msg += ' <span style="color:#b32d2e;font-weight:600;">→ Tổng một vòng chưa bằng nhau; khi Lưu, xoay sẽ không được bật.</span>';
            }
            $('#dnttvn-rot-cycle-summary').html(msg);
        }
        dnttvnRefreshRotateCycleHints();
        $(document).on('input change', '.dnttvn-ad-form .banner-image-id, .dnttvn-rot-sec, #banner_sidebar_rotate', dnttvnRefreshRotateCycleHints);
        $('#add-vvip-banner, #add-vip-banner, #add-standard-banner').on('click', function() {
            setTimeout(dnttvnRefreshRotateCycleHints, 80);
        });
    });
    </script>
    <?php
}

// Create Admin Settings Pages
function dnttvn_add_admin_menu() {
    // Menu chính: Quản lý Banner Header
    add_menu_page(
        'Quản lý Banner Header',
        'Banner Header',
        'manage_options',
        'dnttvn-banner-header',
        'dnttvn_header_banner_settings_page',
        'dashicons-images-alt2',
        30
    );

    // Submenu: Quản lý Banner Quảng cáo (VVIP, VIP, Standard)
    add_submenu_page(
        'dnttvn-banner-header',
        'Quản lý Banner Quảng cáo',
        'Banner Quảng cáo',
        'manage_options',
        'dnttvn-banner-ads',
        'dnttvn_ad_banner_settings_page'
    );

    // Submenu: Quản lý Link Cộng đồng
    add_submenu_page(
        'dnttvn-banner-header',
        'Quản lý Link Cộng đồng',
        'Link Cộng đồng',
        'manage_options',
        'dnttvn-community-links',
        'dnttvn_community_links_page'
    );

    // Submenu: Thông tin chân trang (tên hiển thị, địa chỉ, email, hotline)
    add_submenu_page(
        'dnttvn-banner-header',
        'Thông tin chân trang',
        'Chân trang',
        'manage_options',
        'dnttvn-footer-info',
        'dnttvn_footer_info_settings_page'
    );
}
add_action('admin_menu', 'dnttvn_add_admin_menu');

// Ẩn menu admin cho Tin tức và Cộng đồng trong theme Doanh nghiệp
function dnttvn_hide_tin_tuc_cong_dong_menus() {
    // Ẩn menu Tin tức
    remove_menu_page('edit.php?post_type=tin_tuc');

    // Ẩn menu Cộng đồng
    remove_menu_page('edit.php?post_type=cong_dong');

    // Giữ menu Doanh nghiệp hiển thị
}
add_action('admin_menu', 'dnttvn_hide_tin_tuc_cong_dong_menus', 999);

// AJAX handlers for community links
add_action('wp_ajax_save_individual_community_link', 'dnttvn_save_individual_community_link');
add_action('wp_ajax_delete_community_link', 'dnttvn_delete_community_link');

// Banner Settings Page - REMOVED (Split into separate pages)
function dnttvn_banner_settings_page_removed() {
    // Xử lý lưu dữ liệu khi bấm các nút Lưu (tổng hoặc từng ô)
    $has_save_action =
        isset($_POST['dnttvn_save_banners']) ||
        isset($_POST['dnttvn_save_banner_header']) ||
        isset($_POST['dnttvn_save_banner_vvip']) ||
        isset($_POST['dnttvn_save_banner_vip']) ||
        isset($_POST['dnttvn_save_banner_standard']);

    if ($has_save_action && check_admin_referer('dnttvn_save_banners_action', 'dnttvn_save_banners_nonce')) {
        $is_global      = isset($_POST['dnttvn_save_banners']);
        $save_header    = $is_global || isset($_POST['dnttvn_save_banner_header']);
        $save_vvip      = $is_global || isset($_POST['dnttvn_save_banner_vvip']);
        $save_vip       = $is_global || isset($_POST['dnttvn_save_banner_vip']);
        $save_standard  = $is_global || isset($_POST['dnttvn_save_banner_standard']);

        // Save Header Banners (for header slider)
        if ($save_header) {
            if (isset($_POST['header_banners']) && is_array($_POST['header_banners'])) {
                // Lưu vào theme_mod cũ để tương thích (1–5)
                for ($i = 1; $i <= 5; $i++) {
                    $banner_id = isset($_POST['header_banners'][$i]) ? absint($_POST['header_banners'][$i]) : 0;
                    set_theme_mod('dnttvn_banner_' . $i, $banner_id);
                }
            }
            // Lưu thời gian hiển thị cho từng Banner Header
            $header_start = isset($_POST['header_start']) && is_array($_POST['header_start'])
                ? array_map('sanitize_text_field', $_POST['header_start'])
                : array();
            $header_end   = isset($_POST['header_end']) && is_array($_POST['header_end'])
                ? array_map('sanitize_text_field', $_POST['header_end'])
                : array();
            update_option('dnttvn_header_start', $header_start);
            update_option('dnttvn_header_end', $header_end);

            if (isset($_POST['header_banner_order'])) {
                set_theme_mod('dnttvn_banner_order', sanitize_text_field($_POST['header_banner_order']));
            }
        }
        
        // Save VVIP Ad Blocks
        if ($save_vvip) {
            if (isset($_POST['vvip_banners'])) {
                update_option('dnttvn_vvip_banners', array_map('absint', $_POST['vvip_banners']));
            }
            if (isset($_POST['vvip_links'])) {
                update_option('dnttvn_vvip_links', array_map('esc_url_raw', $_POST['vvip_links']));
            }
            if (isset($_POST['vvip_doanh_nghiep']) && is_array($_POST['vvip_doanh_nghiep'])) {
                update_option('dnttvn_vvip_doanh_nghiep', array_map('absint', $_POST['vvip_doanh_nghiep']));
            }
            // Lưu thời gian hiển thị VVIP
            if (isset($_POST['vvip_start']) && is_array($_POST['vvip_start'])) {
                update_option('dnttvn_vvip_start', array_map('sanitize_text_field', $_POST['vvip_start']));
            }
            if (isset($_POST['vvip_end']) && is_array($_POST['vvip_end'])) {
                update_option('dnttvn_vvip_end', array_map('sanitize_text_field', $_POST['vvip_end']));
            }
        }
        
        // Save VIP Ad Blocks
        if ($save_vip) {
            if (isset($_POST['vip_banners'])) {
                update_option('dnttvn_vip_banners', array_map('absint', $_POST['vip_banners']));
            }
            if (isset($_POST['vip_links'])) {
                update_option('dnttvn_vip_links', array_map('esc_url_raw', $_POST['vip_links']));
            }
            if (isset($_POST['vip_doanh_nghiep']) && is_array($_POST['vip_doanh_nghiep'])) {
                update_option('dnttvn_vip_doanh_nghiep', array_map('absint', $_POST['vip_doanh_nghiep']));
            }
            // Lưu thời gian hiển thị VIP
            if (isset($_POST['vip_start']) && is_array($_POST['vip_start'])) {
                update_option('dnttvn_vip_start', array_map('sanitize_text_field', $_POST['vip_start']));
            }
            if (isset($_POST['vip_end']) && is_array($_POST['vip_end'])) {
                update_option('dnttvn_vip_end', array_map('sanitize_text_field', $_POST['vip_end']));
            }
        }
        
        // Save Standard Ad Blocks
        if ($save_standard) {
            if (isset($_POST['standard_banners'])) {
                update_option('dnttvn_standard_banners', array_map('absint', $_POST['standard_banners']));
            }
            if (isset($_POST['standard_links'])) {
                update_option('dnttvn_standard_links', array_map('esc_url_raw', $_POST['standard_links']));
            }
            if (isset($_POST['standard_doanh_nghiep']) && is_array($_POST['standard_doanh_nghiep'])) {
                update_option('dnttvn_standard_doanh_nghiep', array_map('absint', $_POST['standard_doanh_nghiep']));
            }
            // Lưu thời gian hiển thị Standard
            if (isset($_POST['standard_start']) && is_array($_POST['standard_start'])) {
                update_option('dnttvn_standard_start', array_map('sanitize_text_field', $_POST['standard_start']));
            }
            if (isset($_POST['standard_end']) && is_array($_POST['standard_end'])) {
                update_option('dnttvn_standard_end', array_map('sanitize_text_field', $_POST['standard_end']));
            }
        }
        
        // Save Banner Order (Thứ tự hiển thị cột phải)
        if ($is_global && isset($_POST['banner_column_order'])) {
            update_option('dnttvn_banner_column_order', sanitize_text_field($_POST['banner_column_order']));
        }
        
        echo '<div class="notice notice-success"><p>Đã lưu banner thành công!</p></div>';
    }
    
    // Header banners (for header slider)
    $header_banners = array();
    for ($i = 1; $i <= 5; $i++) {
        $header_banners[$i] = get_theme_mod('dnttvn_banner_' . $i, '');
    }
    $header_banner_order = get_theme_mod('dnttvn_banner_order', '1,2,3,4,5');
    $header_start        = get_option('dnttvn_header_start', array());
    $header_end          = get_option('dnttvn_header_end', array());
    
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    
    $vvip_banners  = get_option('dnttvn_vvip_banners', array());
    $vvip_links    = get_option('dnttvn_vvip_links', array());
    $vvip_start    = get_option('dnttvn_vvip_start', array());
    $vvip_end      = get_option('dnttvn_vvip_end', array());
    $vip_banners   = get_option('dnttvn_vip_banners', array());
    $vip_links     = get_option('dnttvn_vip_links', array());
    $vip_start     = get_option('dnttvn_vip_start', array());
    $vip_end       = get_option('dnttvn_vip_end', array());
    $standard_banners = get_option('dnttvn_standard_banners', array());
    $standard_links   = get_option('dnttvn_standard_links', array());
    $standard_start   = get_option('dnttvn_standard_start', array());
    $standard_end     = get_option('dnttvn_standard_end', array());
    
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Banner & Video Quảng cáo</h1>
        <p class="description">Quản lý banner và video quảng cáo hiển thị ở cột bên phải trang "Danh sách Doanh nghiệp". Hỗ trợ cả hình ảnh và video.</p>
        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_banners_action', 'dnttvn_save_banners_nonce'); ?>
            
            <h2>Banner Header (Slider trên đầu trang)</h2>
            <p class="description">Chọn tối đa 5 banner để hiển thị trong slider ở phần đầu trang (Header). Thứ tự hiển thị có thể thay đổi bên dưới.</p>
            <div id="header-banners-container">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    $banner_id    = isset($header_banners[$i]) ? $header_banners[$i] : '';
                    $start_value  = isset($header_start[$i]) ? $header_start[$i] : '';
                    $end_value    = isset($header_end[$i]) ? $header_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner Header <?php echo $i; ?></h3>
                        <p>
                            <label><strong>Hình ảnh Banner Header <?php echo $i; ?>:</strong></label><br>
                            <input type="hidden" name="header_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="header" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh</button>
                            <button type="button" class="button remove-banner-btn" data-type="header" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước ngang, định dạng JPG/PNG.</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php
                            if ($banner_id) {
                                echo wp_get_attachment_image($banner_id, 'medium');
                            }
                            ?>
                        </div>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="header_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="header_end[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_header" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Header <?php echo $i; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            
            <h3>Thứ tự hiển thị Banner Header</h3>
            <p>
                <label for="header_banner_order"><strong>Thứ tự banner:</strong></label><br>
                <input type="text" id="header_banner_order" name="header_banner_order" value="<?php echo esc_attr($header_banner_order); ?>" class="regular-text" />
            </p>
            <p class="description">Nhập thứ tự banner theo số (1–5), phân cách bằng dấu phẩy. Ví dụ: <code>1,2,3,4,5</code> hoặc <code>5,4,3,2,1</code>.</p>
            
            <h2>Banner VVIP</h2>
            <p class="description">Có thể thêm nhiều banner VVIP, hệ thống sẽ hiển thị theo thứ tự bạn cấu hình.</p>
            <div id="vvip-banners-container">
                <?php
                $vvip_count = max(2, count($vvip_banners));
                for ($i = 0; $i < $vvip_count; $i++) {
                    $banner_id   = isset($vvip_banners[$i]) ? $vvip_banners[$i] : '';
                    $banner_url  = isset($vvip_links[$i]) ? $vvip_links[$i] : '';
                    $start_value = isset($vvip_start[$i]) ? $vvip_start[$i] : '';
                    $end_value   = isset($vvip_end[$i]) ? $vvip_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VVIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label><strong>Hình ảnh/Video Quảng cáo:</strong></label><br>
                            <input type="hidden" name="vvip_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Hỗ trợ: JPG, PNG, GIF, MP4, WebM</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : 
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vvip_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vvip_start[]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vvip_end[]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_vvip" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner VVIP <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-vvip-banner" class="button">+ Thêm Banner VVIP</button>
            </p>
            
            <h2>Banner VIP</h2>
            <p class="description">Có thể thêm nhiều banner VIP.</p>
            <div id="vip-banners-container">
                <?php
                $vip_count = max(2, count($vip_banners));
                for ($i = 0; $i < $vip_count; $i++) {
                    $banner_id   = isset($vip_banners[$i]) ? $vip_banners[$i] : '';
                    $banner_url  = isset($vip_links[$i]) ? $vip_links[$i] : '';
                    $start_value = isset($vip_start[$i]) ? $vip_start[$i] : '';
                    $end_value   = isset($vip_end[$i]) ? $vip_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label>Hình ảnh/Video:</label><br>
                            <input type="hidden" name="vip_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button upload-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">Xóa</button>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : 
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vip_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vip_start[]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vip_end[]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_vip" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner VIP <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-vip-banner" class="button">+ Thêm Banner VIP</button>
            </p>
            
            <h2>Banner Standard</h2>
            <p class="description">Có thể thêm nhiều banner Standard.</p>
            <div id="standard-banners-container">
                <?php
                $standard_count = max(2, count($standard_banners));
                for ($i = 0; $i < $standard_count; $i++) {
                    $banner_id   = isset($standard_banners[$i]) ? $standard_banners[$i] : '';
                    $banner_url  = isset($standard_links[$i]) ? $standard_links[$i] : '';
                    $start_value = isset($standard_start[$i]) ? $standard_start[$i] : '';
                    $end_value   = isset($standard_end[$i]) ? $standard_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner Standard <?php echo $i + 1; ?></h3>
                        <p>
                            <label>Hình ảnh/Video:</label><br>
                            <input type="hidden" name="standard_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button upload-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">Xóa</button>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : 
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="standard_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="standard_start[]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="standard_end[]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_standard" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Standard <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-standard-banner" class="button">+ Thêm Banner Standard</button>
            </p>
            
            <h2 style="margin-top: 40px;">Thứ tự hiển thị Banner</h2>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                <p>
                    <label for="banner_column_order"><strong>Thứ tự hiển thị các loại banner:</strong></label><br>
                    <select name="banner_column_order" id="banner_column_order" style="min-width: 300px; padding: 8px;">
                        <option value="vvip,vip,standard" <?php selected($banner_column_order, 'vvip,vip,standard'); ?>>VVIP → VIP → Standard</option>
                        <option value="vvip,standard,vip" <?php selected($banner_column_order, 'vvip,standard,vip'); ?>>VVIP → Standard → VIP</option>
                        <option value="vip,vvip,standard" <?php selected($banner_column_order, 'vip,vvip,standard'); ?>>VIP → VVIP → Standard</option>
                        <option value="vip,standard,vvip" <?php selected($banner_column_order, 'vip,standard,vvip'); ?>>VIP → Standard → VVIP</option>
                        <option value="standard,vvip,vip" <?php selected($banner_column_order, 'standard,vvip,vip'); ?>>Standard → VVIP → VIP</option>
                        <option value="standard,vip,vvip" <?php selected($banner_column_order, 'standard,vip,vvip'); ?>>Standard → VIP → VVIP</option>
                    </select>
                </p>
                <p class="description">Chọn thứ tự hiển thị các loại banner ở cột bên phải trang doanh nghiệp.</p>
            </div>
            
            <p class="submit">
                <input type="submit" name="dnttvn_save_banners" class="button button-primary" value="Lưu Banner">
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('.upload-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var type = button.data('type');
            var index = button.data('index');
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Chọn Banner',
                button: {
                    text: 'Sử dụng hình ảnh này'
                },
                multiple: false,
                library: {
                    type: ['image', 'video']
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                var previewHtml = '';
                if (attachment.type === 'image') {
                    previewHtml = '<img src="' + attachment.url + '" style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;">';
                    previewHtml += '<p style="margin-top: 5px; color: #666; font-size: 12px;">Hình ảnh ID: ' + attachment.id + '</p>';
                } else if (attachment.type === 'video') {
                    previewHtml = '<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>';
                    previewHtml += '<p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: ' + attachment.id + ' (' + attachment.mime + ')</p>';
                }
                preview.html(previewHtml);
            });
            
            mediaUploader.open();
        });
        
        $('.remove-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var container = button.closest('.banner-item');
            container.find('.banner-image-id').val('');
            container.find('.banner-preview').html('');
            container.find('input[type="url"]').val('');
        });
    });
    </script>
    <?php
}

/**
 * Giá trị mặc định cấu hình chân trang (theme Doanh nghiệp).
 *
 * @return array{site_title:string,address:string,email:string,phone:string}
 */
function dnttvn_get_footer_info_defaults() {
    return array(
        'site_title' => 'My websites',
        'address'    => '',
        'email'      => '',
        'phone'      => '',
    );
}

/**
 * Cấu hình chân trang đã lưu (gộp option dnttvn_footer_info).
 *
 * @return array{site_title:string,address:string,email:string,phone:string}
 */
function dnttvn_get_footer_info() {
    $raw = get_option('dnttvn_footer_info', array());
    $raw = is_array($raw) ? $raw : array();
    return wp_parse_args($raw, dnttvn_get_footer_info_defaults());
}

/**
 * Trang quản trị: Thông tin chân trang.
 */
function dnttvn_footer_info_settings_page() {
    if (! current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['dnttvn_save_footer_info']) && check_admin_referer('dnttvn_footer_info_nonce', 'dnttvn_footer_info_nonce_field')) {
        $site_title = isset($_POST['dnttvn_footer_site_title']) ? sanitize_text_field(wp_unslash($_POST['dnttvn_footer_site_title'])) : '';
        $address     = isset($_POST['dnttvn_footer_address']) ? sanitize_textarea_field(wp_unslash($_POST['dnttvn_footer_address'])) : '';
        $email       = isset($_POST['dnttvn_footer_email']) ? sanitize_email(wp_unslash($_POST['dnttvn_footer_email'])) : '';
        $phone       = isset($_POST['dnttvn_footer_phone']) ? sanitize_text_field(wp_unslash($_POST['dnttvn_footer_phone'])) : '';

        if ($site_title === '') {
            $site_title = dnttvn_get_footer_info_defaults()['site_title'];
        }

        update_option(
            'dnttvn_footer_info',
            array(
                'site_title' => $site_title,
                'address'    => $address,
                'email'      => $email,
                'phone'      => $phone,
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>Đã lưu thông tin chân trang.</p></div>';
    }

    $f = dnttvn_get_footer_info();
    ?>
    <div class="wrap">
        <h1>Thông tin chân trang</h1>
        <p class="description">Nội dung hiển thị ở cuối trang (footer). Dòng bản quyền dùng năm hiện tại trên máy chủ và tên hiển thị bên dưới.</p>

        <form method="post" action="" style="max-width: 720px; margin-top: 16px;">
            <?php wp_nonce_field('dnttvn_footer_info_nonce', 'dnttvn_footer_info_nonce_field'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="dnttvn_footer_site_title">Tên hiển thị (dòng thứ hai &amp; bản quyền)</label></th>
                    <td>
                        <input name="dnttvn_footer_site_title" id="dnttvn_footer_site_title" type="text" class="regular-text"
                               value="<?php echo esc_attr($f['site_title']); ?>" required>
                        <p class="description">Ví dụ: <code>My websites</code></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dnttvn_footer_address">Địa chỉ liên hệ</label></th>
                    <td>
                        <textarea name="dnttvn_footer_address" id="dnttvn_footer_address" class="large-text" rows="3"><?php echo esc_textarea($f['address']); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dnttvn_footer_email">Email liên hệ</label></th>
                    <td>
                        <input name="dnttvn_footer_email" id="dnttvn_footer_email" type="email" class="regular-text"
                               value="<?php echo esc_attr($f['email']); ?>" autocomplete="email">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dnttvn_footer_phone">Hotline</label></th>
                    <td>
                        <input name="dnttvn_footer_phone" id="dnttvn_footer_phone" type="text" class="regular-text"
                               value="<?php echo esc_attr($f['phone']); ?>" autocomplete="tel">
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="dnttvn_save_footer_info" class="button button-primary" value="Lưu thông tin chân trang">
            </p>
        </form>

        <hr style="margin: 28px 0;">

        <h2>Xem trước</h2>
        <div style="background:#f6f7f7;border:1px solid #c3c4c7;padding:16px 20px;border-radius:6px;max-width:720px;">
            <p style="margin:0 0 8px;"><strong>Thông tin chân trang</strong></p>
            <p style="margin:0 0 8px;"><?php echo esc_html($f['site_title']); ?></p>
            <p style="margin:0 0 8px;font-size:14px;color:#444;">
                <?php
                $prev = array();
                if ($f['address'] !== '') {
                    $prev[] = 'Địa chỉ: ' . esc_html($f['address']);
                }
                if ($f['email'] !== '') {
                    $prev[] = 'Email: ' . esc_html($f['email']);
                }
                if ($f['phone'] !== '') {
                    $prev[] = 'Hotline: ' . esc_html($f['phone']);
                }
                echo ! empty($prev) ? implode(' | ', $prev) : '<em>Chưa nhập địa chỉ / email / hotline — các mục để trống sẽ không hiện trên website.</em>';
                ?>
            </p>
            <p style="margin:0;font-size:14px;color:#444;">&copy; <?php echo esc_html((string) (int) current_time('Y')); ?> - Bản quyền thuộc về <?php echo esc_html($f['site_title']); ?></p>
        </div>
    </div>
    <?php
}

// Get community links for sidebar display
function dnttvn_get_community_links() {
    $links = get_option('dnttvn_community_links', array());

    // Fallback to default if empty
    if (empty($links)) {
        $links = array(
            array('name' => 'Trang Cộng đồng', 'url' => ''),
            array('name' => 'Cộng đồng Doanh nhân Trẻ', 'url' => ''),
            array('name' => 'Cộng đồng Khởi nghiệp', 'url' => ''),
            array('name' => 'Cộng đồng Đầu tư', 'url' => '')
        );
    }

    return $links;
}

// Community Links Settings Page - Enhanced
function dnttvn_community_links_page() {
    // Xử lý lưu dữ liệu khi submit form (fallback)
    if (isset($_POST['dnttvn_save_all_community_links']) && check_admin_referer('dnttvn_community_links_nonce')) {
        $links = array();

        if (isset($_POST['community_link_names']) && isset($_POST['community_link_urls'])) {
            $names = $_POST['community_link_names'];
            $urls = $_POST['community_link_urls'];

            foreach ($names as $index => $name) {
                $name = sanitize_text_field($name);
                $url = isset($urls[$index]) ? esc_url_raw($urls[$index]) : '';

                if (!empty($name)) {
                    $links[] = array(
                        'name' => $name,
                        'url' => $url
                    );
                }
            }
        }

        update_option('dnttvn_community_links', $links);
        echo '<div class="notice notice-success"><p>Đã lưu tất cả các liên kết cộng đồng!</p></div>';
    }

    // Lấy dữ liệu hiện tại
    $community_links = get_option('dnttvn_community_links', array());

    // Mặc định nếu chưa có dữ liệu
    if (empty($community_links)) {
        $community_links = array(
            array('name' => 'Trang Cộng đồng', 'url' => ''),
            array('name' => 'Cộng đồng Doanh nhân Trẻ', 'url' => ''),
            array('name' => 'Cộng đồng Khởi nghiệp', 'url' => ''),
            array('name' => 'Cộng đồng Đầu tư', 'url' => '')
        );
    }

    ?>
    <div class="wrap">
        <h1>Quản lý Link Cộng đồng</h1>
        <p class="description">Quản lý các liên kết hiển thị ở cột phải của website. Bạn có thể thêm không giới hạn liên kết, lưu từng liên kết riêng biệt hoặc lưu tất cả cùng lúc.</p>

        <div id="messages-container"></div>

        <div id="community-links-container">
            <?php foreach ($community_links as $index => $link) : ?>
            <div class="community-link-item" data-index="<?php echo $index; ?>" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #23282d;">Liên kết <?php echo $index + 1; ?></h3>
                    <button type="button" class="button button-small remove-community-link" style="background: #dc3232; color: white; border-color: #dc3232;" title="Xóa liên kết này">🗑️ Xóa</button>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>Tên hiển thị:</strong></label>
                        <input type="text"
                               class="link-name regular-text"
                               value="<?php echo esc_attr($link['name']); ?>"
                               placeholder="Ví dụ: Cộng đồng ABC"
                               style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>URL:</strong></label>
                        <input type="url"
                               class="link-url regular-text"
                               value="<?php echo esc_attr($link['url']); ?>"
                               placeholder="https://..."
                               style="width: 100%;">
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="button button-primary save-individual-link" data-index="<?php echo $index; ?>">
                        💾 Lưu liên kết này
                    </button>
                    <span class="save-status" style="line-height: 30px; color: #666;"></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="margin: 30px 0; padding: 20px; background: #fff; border: 2px dashed #007cba; border-radius: 6px; text-align: center;">
            <button type="button" id="add-community-link" class="button button-primary button-hero" style="font-size: 16px; padding: 12px 24px;">
                ➕ Thêm liên kết cộng đồng mới
            </button>
            <p style="margin: 10px 0 0 0; color: #666;">Không giới hạn số lượng liên kết</p>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #f1f1f1; border-radius: 6px;">
            <h3 style="margin-top: 0; color: #23282d;">💡 Mẹo sử dụng:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Lưu từng liên kết:</strong> Nhấn "Lưu liên kết này" để lưu ngay lập tức mà không ảnh hưởng đến các liên kết khác</li>
                <li><strong>Thêm liên kết:</strong> Nhấn nút "Thêm liên kết cộng đồng mới" để tạo liên kết mới</li>
                <li><strong>Xóa liên kết:</strong> Nhấn nút "Xóa" ở góc phải để xóa liên kết</li>
                <li><strong>Lưu tất cả:</strong> Sử dụng nút "Lưu tất cả" ở cuối trang nếu muốn cập nhật đồng thời</li>
            </ul>
        </div>

        <form method="post" style="margin-top: 30px;">
            <?php wp_nonce_field('dnttvn_community_links_nonce'); ?>

            <!-- Hidden fields for fallback form submission -->
            <div id="hidden-form-fields"></div>

            <p class="submit">
                <input type="submit" name="dnttvn_save_all_community_links" class="button button-primary button-large" value="💾 Lưu tất cả liên kết" style="font-size: 16px; padding: 12px 24px;">
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var linkIndex = <?php echo count($community_links); ?>;
        var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var nonce = '<?php echo wp_create_nonce('dnttvn_community_links_nonce'); ?>';

        // Hàm hiển thị thông báo
        function showMessage(message, type = 'success') {
            var $container = $('#messages-container');
            var bgColor = type === 'success' ? '#d4edda' : '#f8d7da';
            var textColor = type === 'success' ? '#155724' : '#721c24';
            var borderColor = type === 'success' ? '#c3e6cb' : '#f5c6cb';

            var $notice = $('<div class="notice notice-' + type + ' is-dismissible" style="margin: 0 0 20px 0; padding: 12px 15px; background: ' + bgColor + '; border: 1px solid ' + borderColor + '; color: ' + textColor + '; border-radius: 4px;">' +
                '<p>' + message + '</p>' +
                '<button type="button" class="notice-dismiss" style="color: ' + textColor + ';"><span class="screen-reader-text">Bỏ qua thông báo này.</span></button>' +
                '</div>');

            $container.html($notice);

            // Auto hide after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() { $(this).remove(); });
            }, 5000);

            // Dismissible
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut(function() { $(this).remove(); });
            });
        }

        // Lưu từng liên kết riêng biệt (AJAX)
        $(document).on('click', '.save-individual-link', function() {
            var $button = $(this);
            var $item = $button.closest('.community-link-item');
            var $status = $item.find('.save-status');
            var index = $button.data('index');
            var name = $item.find('.link-name').val().trim();
            var url = $item.find('.link-url').val().trim();

            if (!name) {
                showMessage('Vui lòng nhập tên liên kết!', 'error');
                return;
            }

            $button.prop('disabled', true).text('⏳ Đang lưu...');
            $status.html('<span style="color: #666;">Đang lưu...</span>');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'save_individual_community_link',
                    link_index: index,
                    link_name: name,
                    link_url: url,
                    _wpnonce: nonce
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        showMessage('✅ ' + data.message);
                        $status.html('<span style="color: #28a745;">✓ Đã lưu</span>');
                        $button.text('💾 Lưu liên kết này');
                    } else {
                        showMessage('❌ Lỗi khi lưu liên kết!', 'error');
                        $status.html('<span style="color: #dc3545;">✗ Lỗi</span>');
                        $button.text('💾 Lưu liên kết này');
                    }
                    $button.prop('disabled', false);
                },
                error: function() {
                    showMessage('❌ Lỗi kết nối! Vui lòng thử lại.', 'error');
                    $status.html('<span style="color: #dc3545;">✗ Lỗi</span>');
                    $button.prop('disabled', false).text('💾 Lưu liên kết này');
                }
            });
        });

        // Thêm liên kết mới
        $('#add-community-link').on('click', function() {
            linkIndex++;
            var newLink = `
                <div class="community-link-item" data-index="${linkIndex}" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0; color: #23282d;">Liên kết ${linkIndex + 1}</h3>
                        <button type="button" class="button button-small remove-community-link" style="background: #dc3232; color: white; border-color: #dc3232;" title="Xóa liên kết này">🗑️ Xóa</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>Tên hiển thị:</strong></label>
                            <input type="text" class="link-name regular-text" placeholder="Ví dụ: Cộng đồng ABC" style="width: 100%;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>URL:</strong></label>
                            <input type="url" class="link-url regular-text" placeholder="https://..." style="width: 100%;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="button button-primary save-individual-link" data-index="${linkIndex}">
                            💾 Lưu liên kết này
                        </button>
                        <span class="save-status" style="line-height: 30px; color: #666;"></span>
                    </div>
                </div>
            `;
            $('#community-links-container').append(newLink);
            showMessage('✨ Đã thêm liên kết mới! Hãy điền thông tin và nhấn "Lưu liên kết này".');
        });

        // Xóa liên kết (AJAX)
        $(document).on('click', '.remove-community-link', function() {
            var $item = $(this).closest('.community-link-item');
            var index = $item.data('index');

            if (!confirm('Bạn có chắc chắn muốn xóa liên kết này?')) {
                return;
            }

            $item.css('opacity', '0.5');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'delete_community_link',
                    link_index: index,
                    _wpnonce: nonce
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $item.fadeOut(function() { $(this).remove(); });
                        showMessage('🗑️ ' + data.message);
                        // Re-number remaining items
                        $('.community-link-item').each(function(i) {
                            $(this).find('h3').text('Liên kết ' + (i + 1));
                        });
                    } else {
                        $item.css('opacity', '1');
                        showMessage('❌ Lỗi khi xóa liên kết!', 'error');
                    }
                },
                error: function() {
                    $item.css('opacity', '1');
                    showMessage('❌ Lỗi kết nối! Vui lòng thử lại.', 'error');
                }
            });
        });

        // Cập nhật hidden form fields trước khi submit
        $('form').on('submit', function() {
            $('#hidden-form-fields').empty();
            $('.community-link-item').each(function(index) {
                var name = $(this).find('.link-name').val();
                var url = $(this).find('.link-url').val();
                if (name.trim()) {
                    $('#hidden-form-fields').append(
                        '<input type="hidden" name="community_link_names[]" value="' + name.replace(/"/g, '&quot;') + '">' +
                        '<input type="hidden" name="community_link_urls[]" value="' + url + '">'
                    );
                }
            });
        });
    });
    </script>
    <?php
}

// AJAX Handler: Save individual community link
function dnttvn_save_individual_community_link() {
    check_ajax_referer('dnttvn_community_links_nonce');

    $index = intval($_POST['link_index']);
    $name = sanitize_text_field($_POST['link_name']);
    $url = esc_url_raw($_POST['link_url']);

    if (empty($name)) {
        wp_die(json_encode(array('success' => false, 'message' => 'Tên liên kết không được để trống!')));
    }

    $links = get_option('dnttvn_community_links', array());

    // Nếu chưa có dữ liệu, tạo mặc định
    if (empty($links)) {
        $links = array(
            array('name' => 'Trang Cộng đồng', 'url' => ''),
            array('name' => 'Cộng đồng Doanh nhân Trẻ', 'url' => ''),
            array('name' => 'Cộng đồng Khởi nghiệp', 'url' => ''),
            array('name' => 'Cộng đồng Đầu tư', 'url' => '')
        );
    }

    // Cập nhật hoặc thêm link
    if ($index >= 0 && $index < count($links)) {
        $links[$index] = array('name' => $name, 'url' => $url);
    } else {
        $links[] = array('name' => $name, 'url' => $url);
    }

    $saved = update_option('dnttvn_community_links', $links);

    if ($saved) {
        wp_die(json_encode(array('success' => true, 'message' => 'Đã lưu liên kết thành công!')));
    } else {
        wp_die(json_encode(array('success' => false, 'message' => 'Không có thay đổi để lưu.')));
    }
}

// AJAX Handler: Delete community link
function dnttvn_delete_community_link() {
    check_ajax_referer('dnttvn_community_links_nonce');

    $index = intval($_POST['link_index']);
    $links = get_option('dnttvn_community_links', array());

    if (!isset($links[$index])) {
        wp_die(json_encode(array('success' => false, 'message' => 'Liên kết không tồn tại!')));
    }

    unset($links[$index]);
    $links = array_values($links); // Re-index array

    $saved = update_option('dnttvn_community_links', $links);

    if ($saved) {
        wp_die(json_encode(array('success' => true, 'message' => 'Đã xóa liên kết thành công!')));
    } else {
        wp_die(json_encode(array('success' => false, 'message' => 'Lỗi khi xóa liên kết!')));
    }
}

// Enqueue admin script for banner management
function dnttvn_enqueue_banner_admin_scripts($hook) {
    if ($hook != 'toplevel_page_dnttvn-banner-header' &&
        $hook != 'banner_page_dnttvn-banner-ads' &&
        $hook != 'banner_page_dnttvn-community-links') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_banner_admin_scripts');

// Enqueue jQuery UI Sortable for structured content
function dnttvn_enqueue_structured_content_scripts($hook) {
    global $post_type;
    if (in_array($post_type, array('tin_tuc', 'cong_dong', 'doanh_nghiep')) && ($hook == 'post.php' || $hook == 'post-new.php')) {
        wp_enqueue_script('jquery-ui-sortable');
    }
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_structured_content_scripts');

// Add Favicon (from admin settings or fallback)
function dnttvn_add_favicon() {
    $favicon_id = get_option('dnttvn_favicon_id');
    if ($favicon_id) {
        $favicon_url = wp_get_attachment_image_url(absint($favicon_id), 'full');
    }
    if (empty($favicon_url)) {
        $favicon_url = get_template_directory_uri() . '/Logo-nhỏ CDTTVN.png';
    }
    echo '<link rel="icon" type="image/png" href="' . esc_url($favicon_url) . '" />' . "\n";
    echo '<link rel="shortcut icon" type="image/png" href="' . esc_url($favicon_url) . '" />' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($favicon_url) . '" />' . "\n";
}
add_action('wp_head', 'dnttvn_add_favicon');
add_action('admin_head', 'dnttvn_add_favicon');

// Favicon settings page
function dnttvn_favicon_admin_menu() {
    add_theme_page('Quản lý Favicon', 'Favicon', 'manage_options', 'dnttvn-favicon', 'dnttvn_favicon_settings_page');
}
add_action('admin_menu', 'dnttvn_favicon_admin_menu');

function dnttvn_favicon_settings_page() {
    if (isset($_POST['dnttvn_favicon_save']) && check_admin_referer('dnttvn_favicon_nonce')) {
        $new_id = isset($_POST['dnttvn_favicon_id']) ? absint($_POST['dnttvn_favicon_id']) : 0;
        update_option('dnttvn_favicon_id', $new_id);
        echo '<div class="notice notice-success"><p>Đã lưu favicon thành công!</p></div>';
    }
    $favicon_id  = get_option('dnttvn_favicon_id');
    $favicon_url = $favicon_id ? wp_get_attachment_image_url(absint($favicon_id), 'thumbnail') : '';
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Favicon</h1>
        <form method="post">
            <?php wp_nonce_field('dnttvn_favicon_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th>Favicon hiện tại</th>
                    <td>
                        <div id="favicon-preview" style="margin-bottom:10px;">
                            <?php if ($favicon_url) : ?>
                                <img src="<?php echo esc_url($favicon_url); ?>" style="width:64px; height:64px; object-fit:contain; border:1px solid #ddd; border-radius:6px; padding:4px;" />
                            <?php else : ?>
                                <p style="color:#888;">Chưa chọn — đang dùng ảnh mặc định (Logo-nhỏ CDTTVN.png)</p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" id="dnttvn_favicon_id" name="dnttvn_favicon_id" value="<?php echo esc_attr($favicon_id); ?>" />
                        <button type="button" class="button button-primary" id="btn-choose-favicon">Chọn ảnh Favicon</button>
                        <button type="button" class="button" id="btn-remove-favicon" style="color:#a00;<?php echo empty($favicon_id) ? 'display:none;' : ''; ?>">Xóa</button>
                        <p class="description">Khuyến nghị: ảnh vuông PNG, kích thước 32x32px hoặc 64x64px.</p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="dnttvn_favicon_save" class="button button-primary" value="Lưu thay đổi" />
            </p>
        </form>
    </div>
    <script>
    jQuery(function($){
        var frame;
        $('#btn-choose-favicon').on('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'Chọn ảnh Favicon', library: { type: 'image' }, button: { text: 'Chọn' }, multiple: false });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                var thumb = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
                $('#dnttvn_favicon_id').val(att.id);
                $('#favicon-preview').html('<img src="'+thumb+'" style="width:64px;height:64px;object-fit:contain;border:1px solid #ddd;border-radius:6px;padding:4px;" />');
                $('#btn-remove-favicon').show();
            });
            frame.open();
        });
        $('#btn-remove-favicon').on('click', function(){
            $('#dnttvn_favicon_id').val('');
            $('#favicon-preview').html('<p style="color:#888;">Chưa chọn — đang dùng ảnh mặc định (Logo-nhỏ CDTTVN.png)</p>');
            $(this).hide();
        });
    });
    </script>
    <?php
}
