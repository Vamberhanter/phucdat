<?php
/**
 * Functions and definitions for Cộng đồng Doanh nhân Trí tuệ Việt Nam Theme
 */

// Enqueue styles and scripts
function dnttvn_enqueue_styles() {
    $css_file = get_template_directory() . '/assets/style-gioi-thieu.css';
    $js_file  = get_template_directory() . '/assets/script.js';

    wp_enqueue_style(
        'dnttvn-main-style',
        get_template_directory_uri() . '/assets/style-gioi-thieu.css',
        array(),
        file_exists($css_file) ? filemtime($css_file) : '1.0.10'
    );
    wp_enqueue_script(
        'dnttvn-main-script',
        get_template_directory_uri() . '/assets/script.js',
        array(),
        file_exists($js_file) ? filemtime($js_file) : '1.0.10',
        true
    );
}
add_action('wp_enqueue_scripts', 'dnttvn_enqueue_styles');

// Giảm Unused CSS: gỡ block-library trên front-end nếu trang không dùng block (PageSpeed)
function dnttvn_dequeue_block_library_on_front() {
    if (is_admin() || (function_exists('has_blocks') && has_blocks())) {
        return;
    }
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
}
add_action('wp_enqueue_scripts', 'dnttvn_dequeue_block_library_on_front', 100);

// Thêm thuộc tính defer cho script chính để không chặn render đầu trang
function dnttvn_defer_main_script($tag, $handle, $src) {
    if ('dnttvn-main-script' === $handle && !is_admin()) {
        // Nếu tag đã có defer/async thì giữ nguyên
        if (strpos($tag, 'defer') === false && strpos($tag, 'async') === false) {
            $tag = '<script src="' . esc_url($src) . '" defer></script>' . "\n";
        }
    }
    return $tag;
}
add_filter('script_loader_tag', 'dnttvn_defer_main_script', 10, 3);

// Load main CSS async (non-render-blocking) for PageSpeed LCP
function dnttvn_async_main_style($html, $handle, $href, $media) {
    if ('dnttvn-main-style' === $handle && !is_admin()) {
        $html = '<link rel="preload" href="' . esc_url($href) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">'
            . '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
    }
    return $html;
}
add_filter('style_loader_tag', 'dnttvn_async_main_style', 10, 4);

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
        'show_in_menu'          => true,
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
    // Redirect tin_tuc/ID to trang-tin-tuc-chi-tiet?post_id=ID
    $tin_tuc_page = get_page_by_path('trang-tin-tuc-chi-tiet');
    if ($tin_tuc_page) {
        add_rewrite_rule(
            '^tin_tuc/([0-9]+)/?$',
            'index.php?page_id=' . $tin_tuc_page->ID . '&post_id=$matches[1]',
            'top'
        );
    }
}
add_action('init', 'dnttvn_add_tin_tuc_rewrite_rules');

// Modify the permalink for tin_tuc posts to use page template
function dnttvn_tin_tuc_permalink($permalink, $post) {
    if ($post->post_type === 'tin_tuc') {
        // Tìm page có template "Chi Tiết Tin Tức"
        $tin_tuc_page = get_page_by_path('trang-tin-tuc-chi-tiet');
        if ($tin_tuc_page) {
            return get_permalink($tin_tuc_page) . '?post_id=' . $post->ID;
        }
        // Fallback nếu không tìm thấy page
        return home_url('/trang-tin-tuc-chi-tiet/?post_id=' . $post->ID);
    }
    return $permalink;
}
add_filter('post_type_link', 'dnttvn_tin_tuc_permalink', 10, 2);

// Manual function to flush rewrite rules (call this if needed)
function dnttvn_manual_flush_rewrite_rules() {
    flush_rewrite_rules();
    update_option('dnttvn_theme_activated', 'yes');
}
// Auto flush rewrite rules when theme is activated
add_action('after_switch_theme', 'dnttvn_manual_flush_rewrite_rules');

// Force create necessary pages on admin init (for production deployment)
function dnttvn_force_create_pages_on_admin() {
    if (is_admin() && current_user_can('manage_options')) {
        // Force create pages if they don't exist
        dnttvn_create_necessary_pages();

        // Flush rewrite rules to ensure they work
        flush_rewrite_rules();

        // Force flush cong_dong rewrite rules specifically
        dnttvn_add_cong_dong_rewrite_rules();
    }
}
// Override "View" link in admin for cong_dong posts
function dnttvn_cong_dong_admin_view_link($actions, $post) {
    if ($post->post_type === 'cong_dong' && isset($actions['view'])) {
        $cong_dong_page = get_page_by_path('cong-dong');
        if ($cong_dong_page) {
            $actions['view'] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                esc_url(get_permalink($cong_dong_page) . '?post_id=' . $post->ID),
                __('View')
            );
        }
    }
    return $actions;
}
add_filter('post_row_actions', 'dnttvn_cong_dong_admin_view_link', 10, 2);

// Redirect old cong_dong URLs to new format
function dnttvn_cong_dong_redirect_old_urls() {
    if (isset($_GET['post_type']) && $_GET['post_type'] === 'cong_dong' && isset($_GET['p'])) {
        $post_id = intval($_GET['p']);
        $cong_dong_page = get_page_by_path('cong-dong');
        if ($cong_dong_page && $post_id > 0) {
            wp_redirect(get_permalink($cong_dong_page) . '?post_id=' . $post_id, 301);
            exit;
        }
    }
}
add_action('template_redirect', 'dnttvn_cong_dong_redirect_old_urls');

add_action('admin_init', 'dnttvn_force_create_pages_on_admin');

// Add debug link to admin bar
function dnttvn_add_debug_link_to_admin_bar($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        $wp_admin_bar->add_node(array(
            'id'    => 'dnttvn-debug',
            'title' => '🔧 Debug Pages',
            'href'  => admin_url('admin.php?page=dnttvn-debug-pages'),
            'meta'  => array(
                'target' => '_blank'
            )
        ));
    }
}
add_action('admin_bar_menu', 'dnttvn_add_debug_link_to_admin_bar', 999);

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

// --------------------------------------------------------------------------
// URL Helper Functions
// --------------------------------------------------------------------------

/**
 * Lấy URL trang chi tiết Tin tức
 */
function dnttvn_get_tin_tuc_detail_url($post_id) {
    $page = get_page_by_path('trang-tin-tuc-chi-tiet');
    if ($page) {
        return add_query_arg('post_id', $post_id, get_permalink($page));
    }
    return get_permalink($post_id);
}

/**
 * Lấy URL trang chi tiết Cộng đồng
 */
function dnttvn_get_cong_dong_detail_url($post_id) {
    $page = get_page_by_path('cong-dong');
    if ($page) {
        return add_query_arg('post_id', $post_id, get_permalink($page));
    }
    return get_permalink($post_id);
}

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
    } else {
        // Đảm bảo template luôn đúng
        update_post_meta($existing_tin_tuc_page->ID, '_wp_page_template', 'page-tin-tuc-chi-tiet.php');
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
    } else {
        update_post_meta($existing_archive_page->ID, '_wp_page_template', 'page-tin-tuc.php');
    }

    // Create "Trang Về cộng đồng" (chi tiết Cộng đồng theo post_id)
    $ve_cong_dong_title = 'Trang Về cộng đồng';
    $ve_cong_dong_slug = 'cong-dong';
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
    } else {
        update_post_meta($existing_ve_cong_dong->ID, '_wp_page_template', 'page-ve-cong-dong.php');
    }

    // Trang Danh sách Cộng đồng
    $cong_dong_slug = 'cong-dong';
    $existing_cong_dong = get_page_by_path($cong_dong_slug);
    if (!$existing_cong_dong) {
        $cd_page_id = wp_insert_post(array(
            'post_title'   => 'Cộng đồng',
            'post_name'    => $cong_dong_slug,
            'post_content' => 'Danh sách bài viết cộng đồng.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        if ($cd_page_id) {
            update_post_meta($cd_page_id, '_wp_page_template', 'page-cong-dong.php');
        }
    } else {
        update_post_meta($existing_cong_dong->ID, '_wp_page_template', 'page-cong-dong.php');
    }

    // Trang Đăng ký gia nhập
    $dang_ky_slug = 'dang-ky';
    $existing_dang_ky = get_page_by_path($dang_ky_slug);
    if (!$existing_dang_ky) {
        $dk_page_id = wp_insert_post(array(
            'post_title'   => 'Đăng ký',
            'post_name'    => $dang_ky_slug,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        if ($dk_page_id && !is_wp_error($dk_page_id)) {
            update_post_meta($dk_page_id, '_wp_page_template', 'page-dang-ky.php');
        }
    } else {
        update_post_meta($existing_dang_ky->ID, '_wp_page_template', 'page-dang-ky.php');
    }

    // Trang Danh sách Doanh nhân
    $doanh_nhan_slug = 'doanh-nhan';
    $existing_doanh_nhan = get_page_by_path($doanh_nhan_slug);
    if (!$existing_doanh_nhan) {
        $dn_page_id = wp_insert_post(array(
            'post_title'   => 'Doanh nhân',
            'post_name'    => $doanh_nhan_slug,
            'post_content' => 'Danh sách Doanh nhân Cộng đồng.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        if ($dn_page_id && !is_wp_error($dn_page_id)) {
            update_post_meta($dn_page_id, '_wp_page_template', 'page-doanh-nhan.php');
        }
    } else {
        update_post_meta($existing_doanh_nhan->ID, '_wp_page_template', 'page-doanh-nhan.php');
    }

    // Trang Đăng ký Hướng nghiệp & Khai mở Trí tuệ (gắn với Phụng sự Con Doanh nhân)
    $dang_ky_hn_slug = 'dang-ky-huong-nghiep';
    $existing_dang_ky_hn = get_page_by_path($dang_ky_hn_slug);
    if (!$existing_dang_ky_hn) {
        $dk_hn_page_id = wp_insert_post(array(
            'post_title'   => 'Đăng ký chương trình Hướng nghiệp & Khai mở Trí tuệ',
            'post_name'    => $dang_ky_hn_slug,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        ));
        if ($dk_hn_page_id && !is_wp_error($dk_hn_page_id)) {
            update_post_meta($dk_hn_page_id, '_wp_page_template', 'page-dang-ky-huong-nghiep.php');
        }
    } else {
        update_post_meta($existing_dang_ky_hn->ID, '_wp_page_template', 'page-dang-ky-huong-nghiep.php');
    }

    // Trang thành viên mới: Giá trị / Quy trình / Phụng Doanh nhân / Phụng sự Con Doanh nhân / Nghĩa vụ thành viên / Hỏi đáp
    $thanh_vien_pages = array(
        'gia-tri-thanh-vien'       => array('title' => 'Giá trị nhận được của thành viên', 'template' => 'page-thanh-vien-moi.php'),
        'quy-trinh-gia-nhap'       => array('title' => 'Quy trình gia nhập Cộng đồng', 'template' => 'page-quy-trinh-gia-nhap.php'),
        'phung-doanh-nhan'         => array('title' => 'Phụng Doanh nhân', 'template' => 'page-phung-doanh-nhan.php'),
        'phung-su-con-doanh-nhan'  => array('title' => 'Phụng sự Con Doanh nhân', 'template' => 'page-phung-su-con-doanh-nhan.php'),
        'nghia-vu-thanh-vien-cong-dong' => array('title' => 'Nghĩa vụ thành viên Cộng Đồng', 'template' => 'page-nghia-vu-thanh-vien-cong-dong.php'),
        'hoi-dap-cong-dong'        => array('title' => 'Hỏi đáp về Cộng đồng', 'template' => 'page-hoi-dap-cong-dong.php'),
    );
    foreach ($thanh_vien_pages as $slug => $data) {
        $title = $data['title'];
        $template = $data['template'];
        $existing = get_page_by_path($slug);
        if (!$existing) {
            $pid = wp_insert_post(array(
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
            ));
            if ($pid && !is_wp_error($pid)) {
                update_post_meta($pid, '_wp_page_template', $template);
            }
        } else {
            update_post_meta($existing->ID, '_wp_page_template', $template);
        }
    }
}
add_action('init', 'dnttvn_create_necessary_pages');

// Khối cột trái: Giá trị / Quy trình / Phụng Doanh nhân / Phụng sự Con Doanh nhân / Nghĩa vụ thành viên / Hỏi đáp
function dnttvn_render_left_sidebar_thanh_vien_block() {
    $pages = array(
        'gia-tri-thanh-vien'        => 'Giá trị nhận được của thành viên',
        'quy-trinh-gia-nhap'        => 'Quy trình gia nhập Cộng đồng',
        'phung-doanh-nhan'          => 'Phụng sự Doanh nhân',
        'phung-su-con-doanh-nhan'   => 'Phụng sự Con Doanh nhân',
        'nghia-vu-thanh-vien-cong-dong' => 'Nghĩa vụ thành viên Cộng Đồng',
        'hoi-dap-cong-dong'         => 'Hỏi đáp về Cộng đồng',
    );
    echo '<div class="sidebar-block-thanh-vien">';
    echo '<div class="column-header mobile-toggle collapsed">Thành viên mới</div>';
    echo '<div class="column-content mobile-collapsed">';
    echo '<ul class="thanh-vien-moi-list">';
    foreach ($pages as $slug => $label) {
        $page = get_page_by_path($slug);
        $url = $page ? get_permalink($page) : home_url('/' . $slug . '/');
        echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul></div></div>';
} // Chạy ở init để đảm bảo khi lên host mới nó tự check và tạo

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

    add_meta_box(
        'tin_tuc_excel_upload',
        '📊 Nội dung Excel (Bảng dữ liệu)',
        'dnttvn_excel_upload_meta_box_callback',
        'tin_tuc',
        'normal',
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
    $items = array();
    if (is_array($raw)) {
        $items = $raw;
    } elseif (!empty($raw) && is_string($raw)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $items = $decoded;
        } else {
            $unserialized = @unserialize($raw);
            $items = is_array($unserialized) ? $unserialized : array();
        }
    }
    // Chuẩn hóa: mỗi item có 'content' từ content_items[0]['text'] nếu chưa có (để template dùng $item['content'] hoặc content_items đều đúng)
    foreach ($items as $key => $item) {
        if (!isset($item['content']) || $item['content'] === '') {
            if (!empty($item['content_items']) && is_array($item['content_items'])) {
                $first = reset($item['content_items']);
                $text = is_array($first) ? (isset($first['text']) ? $first['text'] : '') : (string) $first;
                if ($text !== '') {
                    $items[$key]['content'] = $text;
                }
            }
        }
    }
    return $items;
}

// Meta box callback for Structured Content (Tin tức & Cộng đồng)
function dnttvn_structured_content_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_structured_content', 'dnttvn_structured_content_nonce');
    
    $items = dnttvn_get_structured_content_array($post->ID);
    
    ?>
    <div class="dnttvn-structured-content-wrapper">
        <p class="description" style="margin-bottom: 15px;">
            <strong>Hướng dẫn:</strong> Mỗi mục gồm tiêu đề, hình/video, caption và nội dung. <?php if (in_array(get_post_type($post->ID), array('cong_dong', 'tin_tuc', 'doanh_nhan'), true)) : ?>Ô nội dung có thanh công cụ chỉnh sửa chữ (in đậm, in nghiêng, gạch chân, link, danh sách...); nội dung đã chỉnh sửa sẽ hiển thị đúng trên bài viết chi tiết. <?php endif; ?>Có thể thêm nhiều mục và kéo thả để sắp xếp.
        </p>
        
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

                            $images = isset($item['images']) ? $item['images'] : array();
                            $media_id = !empty($item['media_id']) ? absint($item['media_id']) : 0;
                            if ($media_id && empty($images)) {
                                $images = array($media_id);
                            }
                            
                            $media_caption = $item['media_caption'] ?? '';
                            ?>
                            <p>
                                <label><strong>Tiêu đề mục:</strong></label><br>
                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][heading]"
                                       value="<?php echo esc_attr($heading); ?>"
                                       class="large-text structured-heading" placeholder="Nhập tiêu đề mục...">
                            </p>

                            <div class="structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">
                                <label style="font-weight: bold; color: #007cba;">🖼️ Hình/Video (có thể chọn nhiều hình):</label>
                                <?php
                                $item_layout = $item['image_layout'] ?? 'slideshow';
                                ?>
                                <div style="margin-bottom: 10px;">
                                    <label>Cách trình bày:</label>
                                    <select name="structured_content[<?php echo esc_attr($index); ?>][image_layout]">
                                        <option value="slideshow" <?php selected($item_layout, 'slideshow'); ?>>🎞️ Slideshow</option>
                                        <option value="photo_grid" <?php selected($item_layout, 'photo_grid'); ?>>🔲 Photo Grid</option>
                                        <option value="multi_photo" <?php selected($item_layout, 'multi_photo'); ?>>🖼️ Multi-photo</option>
                                    </select>
                                </div>
                                <div class="structured-media-preview-gallery sortable-initialized" id="structured-media-gallery-<?php echo esc_attr($index); ?>" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; min-height: 50px; background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                    <?php 
                                    foreach ($images as $img_id) :
                                        $img_id = absint($img_id);
                                        if (!$img_id) continue;
                                        $mime = get_post_mime_type($img_id);
                                        $is_vid = strpos((string) $mime, 'video') === 0;
                                        $m_url = $is_vid ? wp_get_attachment_url($img_id) : wp_get_attachment_image_url($img_id, 'medium');
                                        if (!$m_url) $m_url = wp_get_attachment_url($img_id);
                                    ?>
                                        <div class="gallery-item" data-id="<?php echo $img_id; ?>" style="position: relative; width: 120px;">
                                            <?php if ($is_vid) : ?>
                                                <video style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" controls>
                                                    <source src="<?php echo esc_url($m_url); ?>" type="<?php echo esc_attr($mime); ?>">
                                                </video>
                                            <?php else : ?>
                                                <img src="<?php echo esc_url($m_url); ?>" alt="" style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                            <?php endif; ?>
                                            <input type="hidden" name="structured_content[<?php echo esc_attr($index); ?>][images][]" value="<?php echo $img_id; ?>">
                                            <button type="button" class="remove-gallery-item-v2 button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; line-height: 1; padding: 0; font-size: 14px;">×</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <p style="margin: 0;">
                                    <button type="button" class="button upload-structured-media-btn" data-item-index="<?php echo esc_attr($index); ?>">📁 Thêm hình/video</button>
                                </p>
                            </div>

                            <p>
                                <label><strong>Caption:</strong></label><br>
                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][media_caption]"
                                       value="<?php echo esc_attr($media_caption); ?>"
                                       class="large-text structured-media-caption" placeholder="Nhập caption cho hình/video...">
                            </p>

                            <p>
                                <label><strong>Nội dung mục (đoạn văn / nhiều dòng):</strong></label><br>
                                <?php
                                $post_type = get_post_type($post->ID);
                                $use_editor = ($post_type === 'cong_dong' || $post_type === 'tin_tuc' || $post_type === 'doanh_nhan' || $post_type === 'su_kien');
                                if ($use_editor) {
                                    $editor_id = 'structured_content_editor_' . $post->ID . '_' . $index;
                                    wp_editor($content, $editor_id, array_merge(
                                        dnttvn_get_content_section_editor_settings('structured_content[' . $index . '][content]', $editor_id),
                                        array('textarea_rows' => 10, 'editor_class' => 'structured-content-item')
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
                                        $content = apply_filters('dnttvn_display_content', $content);
                                        $pt = get_post_type($post->ID);
                                        if (($pt === 'cong_dong' || $pt === 'tin_tuc' || $pt === 'doanh_nhan' || $pt === 'su_kien') && preg_match('/\s*</', $content)) {
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
        /* Ô Nội dung nhỏ: font hỗ trợ dấu tiếng Việt, cho phép xuống dòng */
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

        $(document).on('click', '#add-structured-item', function(e) {
            e.preventDefault();
            var itemCount = $('.structured-item').length;
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
                    '<div class="structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                        '<label style="font-weight: bold; color: #007cba;">🖼️ Hình/Video:</label>' +
                        '<input type="hidden" name="structured_content[' + itemCount + '][media_id]" value="" class="structured-media-id">' +
                        '<div class="structured-media-preview" style="margin: 10px 0; min-height: 60px;"></div>' +
                        '<p style="margin: 0;">' +
                            '<button type="button" class="button upload-structured-media-btn" data-item-index="' + itemCount + '">📁 Chọn hình/video</button>' +
                            '<button type="button" class="button remove-structured-media-btn" data-item-index="' + itemCount + '" style="display:none; margin-left: 6px;">🗑️ Xóa</button>' +
                        '</p>' +
                    '</div>' +
                    '<p>' +
                        '<label><strong>Caption:</strong></label><br>' +
                        '<input type="text" name="structured_content[' + itemCount + '][media_caption]" class="large-text structured-media-caption" placeholder="Nhập caption cho hình/video...">' +
                    '</p>' +
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
            var button = $(this);
            var itemIndex = button.data('item-index');
            var container = $('#structured-media-gallery-' + itemIndex);

            var mediaUploader = wp.media({
                title: 'Chọn ảnh/video cho mục này',
                button: { text: 'Thêm vào mục' },
                multiple: true,
                library: { type: ['image', 'video'] }
            });

            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toJSON();

                attachments.forEach(function(attachment) {
                    var mime_type = attachment.mime;
                    var is_video = mime_type.indexOf('video') === 0;
                    var thumb = is_video ? attachment.url : (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url);
                    
                    var itemHtml = '<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative; width: 120px;">' +
                        (is_video ? 
                            '<video style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" controls><source src="' + attachment.url + '" type="' + mime_type + '"></video>' :
                            '<img src="' + thumb + '" style="width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">'
                        ) +
                        '<input type="hidden" name="structured_content[' + itemIndex + '][images][]" value="' + attachment.id + '">' +
                        '<button type="button" class="remove-gallery-item-v2 button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; line-height: 1; padding: 0; font-size: 14px;">×</button>' +
                    '</div>';

                    container.append(itemHtml);
                });

                if (!container.hasClass('ui-sortable')) {
                    container.sortable({ items: '.gallery-item', cursor: 'move' });
                }
            });

            mediaUploader.open();
        });

        // Remove gallery item
        $(document).on('click', '.remove-gallery-item-v2', function(e) {
            e.preventDefault();
            $(this).closest('.gallery-item').remove();
        });

        $(document).on('click', '.remove-structured-media-btn', function(e) {
            e.preventDefault();
            clearMedia($(this).closest('.structured-item'));
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

        $('.structured-media-preview-gallery').each(function() {
            if (!$(this).hasClass('ui-sortable')) {
                $(this).sortable({ items: '.gallery-item', cursor: 'move' });
            }
        });

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
                    '<div class="structured-media-section" style="background: #f6fbff; border: 1px dashed #007cba; padding: 10px; margin: 10px 0; border-radius: 4px;">' +
                        '<label style="font-weight: bold; color: #007cba;">🖼️ Hình/Video (có thể chọn nhiều hình):</label>' +
                        '<div style="margin: 10px 0;">' +
                            '<label>Cách trình bày:</label>' +
                            '<select name="structured_content[' + itemCount + '][image_layout]">' +
                                '<option value="slideshow">🎞️ Slideshow</option>' +
                                '<option value="photo_grid">🔲 Photo Grid</option>' +
                                '<option value="multi_photo">🖼️ Multi-photo</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="structured-media-preview-gallery sortable-initialized" id="structured-media-gallery-' + itemCount + '" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; min-height: 50px; background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></div>' +
                        '<p style="margin: 0;">' +
                            '<button type="button" class="button upload-structured-media-btn" data-item-index="' + itemCount + '">📁 Thêm hình/video</button>' +
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
            <th><label for="tin_tuc_hinh_noi_dung_layout">Cách trình bày hình ảnh nội dung</label></th>
            <td>
                <?php
                $image_layout = get_post_meta($post->ID, '_tin_tuc_hinh_noi_dung_layout', true);
                if (!$image_layout) $image_layout = 'slideshow';
                $layout_options = array(
                    'slideshow'    => '🎞️ Slideshow (xem từng ảnh, mặc định)',
                    'photo_grid'   => '🔲 Photo Grid (lưới ảnh đều nhau)',
                    'multi_photo'  => '🖼️ Multi-photo (ảnh đầu lớn + ảnh nhỏ bên phải)',
                );
                ?>
                <select id="tin_tuc_hinh_noi_dung_layout" name="tin_tuc_hinh_noi_dung_layout" class="regular-text">
                    <?php foreach ($layout_options as $val => $label) : ?>
                        <option value="<?php echo esc_attr($val); ?>" <?php selected($image_layout, $val); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($hinh_phu_array)) : ?>
                <p class="description" style="margin-top: 8px;">
                    📊 <strong>Thống kê:</strong>
                    Tổng số ảnh: <strong><?php echo count($hinh_phu_array); ?></strong> &nbsp;|&nbsp;
                    <?php
                    $ngang = 0; $doc = 0; $vuong = 0;
                    foreach ($hinh_phu_array as $aid) {
                        $meta = wp_get_attachment_metadata(intval($aid));
                        if ($meta && isset($meta['width']) && isset($meta['height']) && $meta['height'] > 0) {
                            $ratio = $meta['width'] / $meta['height'];
                            if ($ratio > 1.2) $ngang++;
                            elseif ($ratio < 0.85) $doc++;
                            else $vuong++;
                        }
                    }
                    $parts = array();
                    if ($ngang) $parts[] = "📐 Ngang: {$ngang}";
                    if ($doc)   $parts[] = "📱 Dọc: {$doc}";
                    if ($vuong) $parts[] = "⬜ Vuông: {$vuong}";
                    echo implode(' &nbsp;|&nbsp; ', $parts);
                    ?>
                </p>
                <p class="description" style="color: #666;">
                    💡 <em>Ảnh đầu tiên luôn được ưu tiên hiển thị lớn nhất (Multi-photo). Photo Grid tự điều chỉnh kích thước đều nhau.</em>
                </p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_hinh_phu">Ảnh/Video/PDF phụ</label></th>
            <td>
                <input type="hidden" id="tin_tuc_hinh_phu" name="tin_tuc_hinh_phu" value="<?php echo esc_attr(json_encode($hinh_phu_array)); ?>" />
                <div id="tin_tuc_hinh_phu_gallery" class="hinh-phu-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; min-height: 60px; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9;">
                    <?php foreach ($hinh_phu_array as $index => $attachment_id) :
                        $attachment_id = intval($attachment_id);
                        if ($attachment_id > 0) :
                            $mime_type = get_post_mime_type($attachment_id);
                            $is_video = is_string($mime_type) && strpos($mime_type, 'video') === 0;
                            $is_pdf = ((string) $mime_type) === 'application/pdf';
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
                            <?php elseif ($is_pdf) : ?>
                                <div style="width:150px; height:100px; display:flex; align-items:center; justify-content:center; border:1px solid #ddd; border-radius:4px; background:#f2f2f2; font-weight:800; color:#b30000;">
                                    PDF
                                </div>
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
                <p class="description">Ảnh/video/PDF phụ sẽ được hiển thị như gallery ở đầu bài viết. Bấm &quot;Thêm&quot; rồi chọn nhiều file (có thể giữ Ctrl/Cmd). Có thể kéo thả để sắp xếp thứ tự.</p>
            </td>
        </tr>
        <tr>
            <th><label>Link catalog</label></th>
            <td>
                <?php
                $catalog_raw = get_post_meta($post->ID, '_tin_tuc_catalog_links', true);
                $catalog_links = is_string($catalog_raw) ? json_decode($catalog_raw, true) : $catalog_raw;
                $catalog_links = is_array($catalog_links) ? $catalog_links : array();
                ?>
                <div id="tin_tuc_catalog_links_wrap">
                    <?php foreach ($catalog_links as $item) :
                        $label = isset($item['label']) ? $item['label'] : '';
                        $url = isset($item['url']) ? $item['url'] : '';
                        ?>
                        <div class="catalog-link-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
                            <input type="text" name="tin_tuc_catalog_label[]" value="<?php echo esc_attr($label); ?>" placeholder="Tên link" class="regular-text" style="flex:1;" />
                            <input type="url" name="tin_tuc_catalog_url[]" value="<?php echo esc_url($url); ?>" placeholder="https://..." class="large-text" style="flex:2;" />
                            <button type="button" class="button remove-catalog-link">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add_tin_tuc_catalog_link" class="button">+ Thêm link catalog</button>
                <p class="description">Thêm các link catalog/tài liệu liên quan. Hiển thị ở bài chi tiết tin tức.</p>
            </td>
        </tr>
    </table>
    <script>
    jQuery(function($) {
        $('#add_tin_tuc_catalog_link').on('click', function() {
            var row = '<div class="catalog-link-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">' +
                '<input type="text" name="tin_tuc_catalog_label[]" value="" placeholder="Tên link" class="regular-text" style="flex:1;">' +
                '<input type="url" name="tin_tuc_catalog_url[]" value="" placeholder="https://..." class="large-text" style="flex:2;">' +
                '<button type="button" class="button remove-catalog-link">×</button></div>';
            $('#tin_tuc_catalog_links_wrap').append(row);
        });
        $(document).on('click', '#tin_tuc_catalog_links_wrap .remove-catalog-link', function() { $(this).closest('.catalog-link-row').remove(); });
    });
    </script>
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
    $post_types = array('tin_tuc', 'cong_dong', 'doanh_nhan', 'su_kien');
    
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
                    $heading = sanitize_text_field(isset($item['heading']) ? wp_unslash($item['heading']) : '');
                    $raw_content = isset($item['content']) ? $item['content'] : '';
                    $content = is_string($raw_content) ? dnttvn_sanitize_content_with_tables($raw_content) : '';
                    $media_id = isset($item['media_id']) ? absint($item['media_id']) : 0;
                    if ($media_id && !get_post($media_id)) {
                        $media_id = 0;
                    }
                    $media_caption = sanitize_text_field(isset($item['media_caption']) ? wp_unslash($item['media_caption']) : '');

                    $images = array();
                    $image_captions = array();
                    $content_items = array();

                    if (isset($item['images']) && is_array($item['images'])) {
                        foreach ($item['images'] as $image_id) {
                            $image_id = absint($image_id);
                            if ($image_id > 0 && get_post($image_id)) {
                                $images[] = $image_id;
                            }
                        }
                    } elseif ($media_id) {
                        $images[] = $media_id;
                    }

                    if (isset($item['image_captions']) && is_array($item['image_captions'])) {
                        foreach ($item['image_captions'] as $caption) {
                            $image_captions[] = sanitize_text_field(wp_unslash($caption));
                        }
                    } elseif ($media_caption !== '') {
                        $image_captions[] = $media_caption;
                    }

                    // Process content items (nested)
                    if (isset($item['content_items']) && is_array($item['content_items'])) {
                        foreach ($item['content_items'] as $content_data) {
                            $content_obj = array(
                                'text' => '',
                                'images' => array(),
                                'image_captions' => array()
                            );

                            if (is_string($content_data)) {
                                $content_obj['text'] = dnttvn_sanitize_content_with_tables($content_data);
                            } else {
                                $content_obj['text'] = dnttvn_sanitize_content_with_tables($content_data['text'] ?? '');
                                if (isset($content_data['images']) && is_array($content_data['images'])) {
                                    foreach ($content_data['images'] as $img_id) {
                                        $img_id = absint($img_id);
                                        if ($img_id > 0 && get_post($img_id)) {
                                            $content_obj['images'][] = $img_id;
                                        }
                                    }
                                }
                                if (isset($content_data['image_captions']) && is_array($content_data['image_captions'])) {
                                    foreach ($content_data['image_captions'] as $cap) {
                                        $content_obj['image_captions'][] = sanitize_text_field(wp_unslash($cap));
                                    }
                                }
                            }

                            if (!empty(trim($content_obj['text'])) || !empty($content_obj['images'])) {
                                $content_items[] = $content_obj;
                            }
                        }
                    }

                    // Fallback: If old 'content' field is set, convert it to a content item
                    if ($content !== '' && empty($content_items)) {
                         $content_items[] = array(
                            'text' => $content,
                            'images' => array(),
                            'image_captions' => array()
                        );
                    }

                    $image_layout = sanitize_text_field(isset($item['image_layout']) ? $item['image_layout'] : 'slideshow');
                    if (!in_array($image_layout, array('slideshow', 'photo_grid', 'multi_photo'))) {
                        $image_layout = 'slideshow';
                    }

                    if ($heading !== '' || $content !== '' || $media_id > 0 || !empty($content_items) || !empty($images)) {
                        $items[] = array(
                            'heading' => $heading,
                            'content_items' => $content_items,
                            'images' => $images,
                            'image_captions' => $image_captions,
                            'image_layout' => $image_layout
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
        <label for="cong_dong_hinh_phu"><strong>Ảnh/Video/PDF phụ</strong></label><br>
        <input type="hidden" id="cong_dong_hinh_phu" name="cong_dong_hinh_phu" value="<?php echo esc_attr(json_encode($hinh_phu_array)); ?>" />
        <div id="cong_dong_hinh_phu_gallery" class="hinh-phu-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; min-height: 60px; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9;">
            <?php foreach ($hinh_phu_array as $index => $attachment_id) :
                $attachment_id = intval($attachment_id);
                if ($attachment_id > 0) :
                    $mime_type = get_post_mime_type($attachment_id);
                    $is_video = is_string($mime_type) && strpos($mime_type, 'video') === 0;
                    $is_pdf = ((string) $mime_type) === 'application/pdf';
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
                    <?php elseif ($is_pdf) : ?>
                        <div style="width:150px; height:100px; display:flex; align-items:center; justify-content:center; border:1px solid #ddd; border-radius:4px; background:#f2f2f2; font-weight:800; color:#b30000;">
                            PDF
                        </div>
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
        <span class="description">Ảnh/video/PDF phụ sẽ được hiển thị như gallery ở đầu bài viết. Có thể kéo thả để sắp xếp thứ tự.</span>
    </p>

    <p>
        <label for="cong_dong_flipbook_url"><strong>Flipbook (Heyzine) URL</strong></label><br>
        <input type="url" id="cong_dong_flipbook_url" name="cong_dong_flipbook_url" value="<?php echo esc_url(get_post_meta($post->ID, '_cong_dong_flipbook_url', true)); ?>" class="large-text" placeholder="https://heyzine.com/flip-book/..." />
        <span class="description">Dán link Heyzine flipbook để nhúng vào bài chi tiết.</span>
    </p>

    <table class="form-table">
        <tr>
            <th><label>Link catalog</label></th>
            <td>
                <?php
                $catalog_raw = get_post_meta($post->ID, '_cong_dong_catalog_links', true);
                $catalog_links = is_string($catalog_raw) ? json_decode($catalog_raw, true) : $catalog_raw;
                $catalog_links = is_array($catalog_links) ? $catalog_links : array();
                ?>
                <div id="cong_dong_catalog_links_wrap">
                    <?php foreach ($catalog_links as $item) :
                        $label = isset($item['label']) ? $item['label'] : '';
                        $url = isset($item['url']) ? $item['url'] : '';
                        ?>
                        <div class="catalog-link-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
                            <input type="text" name="cong_dong_catalog_label[]" value="<?php echo esc_attr($label); ?>" placeholder="Tên link" class="regular-text" style="flex:1;" />
                            <input type="url" name="cong_dong_catalog_url[]" value="<?php echo esc_url($url); ?>" placeholder="https://..." class="large-text" style="flex:2;" />
                            <button type="button" class="button remove-catalog-link">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add_cong_dong_catalog_link" class="button">+ Thêm link catalog</button>
                <p class="description">Thêm các link catalog/tài liệu liên quan. Hiển thị ở bài chi tiết Cộng đồng.</p>
            </td>
        </tr>
    </table>
    <script>
    jQuery(function($) {
        $('#add_cong_dong_catalog_link').on('click', function() {
            var row = '<div class="catalog-link-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">' +
                '<input type="text" name="cong_dong_catalog_label[]" value="" placeholder="Tên link" class="regular-text" style="flex:1;">' +
                '<input type="url" name="cong_dong_catalog_url[]" value="" placeholder="https://..." class="large-text" style="flex:2;">' +
                '<button type="button" class="button remove-catalog-link">×</button></div>';
            $('#cong_dong_catalog_links_wrap').append(row);
        });
        $(document).on('click', '#cong_dong_catalog_links_wrap .remove-catalog-link', function() { $(this).closest('.catalog-link-row').remove(); });
    });
    </script>

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

    if (isset($_POST['cong_dong_flipbook_url'])) {
        $url = esc_url_raw($_POST['cong_dong_flipbook_url']);
        if ($url) {
            update_post_meta($post_id, '_cong_dong_flipbook_url', $url);
        } else {
            delete_post_meta($post_id, '_cong_dong_flipbook_url');
        }
    }

    // Save catalog links
    if (isset($_POST['cong_dong_catalog_label']) && is_array($_POST['cong_dong_catalog_label']) && isset($_POST['cong_dong_catalog_url']) && is_array($_POST['cong_dong_catalog_url'])) {
        $labels = array_map('sanitize_text_field', $_POST['cong_dong_catalog_label']);
        $urls = array_map('esc_url_raw', $_POST['cong_dong_catalog_url']);
        $catalog_links = array();
        foreach ($labels as $i => $label) {
            $url = isset($urls[$i]) ? $urls[$i] : '';
            if ($label !== '' || $url !== '') {
                $catalog_links[] = array('label' => $label, 'url' => $url);
            }
        }
        if (!empty($catalog_links)) {
            update_post_meta($post_id, '_cong_dong_catalog_links', wp_json_encode($catalog_links));
        } else {
            delete_post_meta($post_id, '_cong_dong_catalog_links');
        }
    }
}
add_action('save_post', 'dnttvn_save_cong_dong_meta');

// Meta box: Hình chính (Ảnh/Video/PDF) cho Quy trình & Phụng Doanh nhân & Phụng sự Con Doanh nhân
function dnttvn_quy_trinh_main_media_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_quy_trinh_main_media', 'dnttvn_quy_trinh_main_media_nonce');

    $raw = get_post_meta($post->ID, '_quy_trinh_main_media', true);
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        $ids = is_array($decoded) ? $decoded : array();
    } else {
        $ids = is_array($raw) ? $raw : (!empty($raw) ? array($raw) : array());
    }
    $ids = array_values(array_filter(array_map('absint', (array) $ids)));
    ?>
    <p>
        <label for="quy_trinh_main_media"><strong>Hình chính (Ảnh/Video/PDF)</strong></label><br>
        <input type="hidden" id="quy_trinh_main_media" name="quy_trinh_main_media" value="<?php echo esc_attr(json_encode($ids)); ?>" />
        <div id="quy_trinh_main_media_gallery" class="hinh-phu-gallery" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; min-height: 60px; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9;">
            <?php foreach ($ids as $attachment_id) :
                $attachment_id = intval($attachment_id);
                if ($attachment_id > 0) :
                    $mime_type = get_post_mime_type($attachment_id);
                    $is_video = is_string($mime_type) && strpos($mime_type, 'video') === 0;
                    $is_pdf = ((string) $mime_type) === 'application/pdf';
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
                    <?php elseif ($is_pdf) : ?>
                        <div style="width:150px; height:100px; display:flex; align-items:center; justify-content:center; border:1px solid #ddd; border-radius:4px; background:#f2f2f2; font-weight:800; color:#b30000;">
                            PDF
                        </div>
                    <?php else : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="Hình chính" style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" />
                    <?php endif; ?>
                    <button type="button" class="remove-gallery-item button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>
                </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div>
            <button type="button" id="upload_quy_trinh_main_media" class="button button-primary">
                📁 <?php echo empty($ids) ? 'Thêm hình chính' : 'Thêm hình/video/PDF nữa'; ?>
            </button>
            <button type="button" id="clear_quy_trinh_main_media" class="button" style="display: <?php echo empty($ids) ? 'none' : 'inline-block'; ?>; margin-left: 5px;">
                🗑️ Xóa tất cả
            </button>
        </div>
        <p class="description">Hình chính sẽ hiển thị như gallery lớn ở đầu nội dung, giống Tin tức/Cộng đồng.</p>
    </p>
    <script>
    jQuery(document).ready(function($) {
        var frame;
        function updateMainMediaInput() {
            var ids = [];
            $('#quy_trinh_main_media_gallery .gallery-item').each(function() {
                ids.push($(this).data('id'));
            });
            $('#quy_trinh_main_media').val(JSON.stringify(ids));
            $('#clear_quy_trinh_main_media').toggle(ids.length > 0);
        }
        $('#upload_quy_trinh_main_media').on('click', function(e) {
            e.preventDefault();
            if (!frame) {
                frame = wp.media({
                    title: 'Chọn hình chính (Ảnh/Video/PDF)',
                    button: { text: 'Dùng làm hình chính' },
                    multiple: true
                });
                frame.on('select', function() {
                    var selection = frame.state().get('selection');
                    selection.each(function(attachment) {
                        attachment = attachment.toJSON();
                        if (!attachment.id) return;
                        var mime = attachment.mime || '';
                        var isVideo = mime.indexOf('video') === 0;
                        var isPdf = mime === 'application/pdf';
                        var url = isVideo || isPdf ? attachment.url : (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url);
                        if (!url) url = attachment.url;
                        var html = '<div class="gallery-item" data-id="' + attachment.id + '" style="position: relative; display: inline-block;">';
                        if (isVideo) {
                            html += '<video style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" controls>';
                            html += '<source src="' + url + '" type="' + mime + '"></video>';
                        } else if (isPdf) {
                            html += '<div style="width:150px; height:100px; display:flex; align-items:center; justify-content:center; border:1px solid #ddd; border-radius:4px; background:#f2f2f2; font-weight:800; color:#b30000;">PDF</div>';
                        } else {
                            html += '<img src="' + url + '" alt="Hình chính" style="width: 150px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;" />';
                        }
                        html += '<button type="button" class="remove-gallery-item button" style="position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px;">×</button>';
                        html += '</div>';
                        $('#quy_trinh_main_media_gallery').append(html);
                    });
                    if ($.fn.sortable) {
                        $('#quy_trinh_main_media_gallery').sortable({ items: '.gallery-item', update: updateMainMediaInput });
                    }
                    updateMainMediaInput();
                });
            }
            frame.open();
        });
        $('#quy_trinh_main_media_gallery').on('click', '.remove-gallery-item', function(e) {
            e.preventDefault();
            $(this).closest('.gallery-item').remove();
            updateMainMediaInput();
        });
        $('#clear_quy_trinh_main_media').on('click', function(e) {
            e.preventDefault();
            $('#quy_trinh_main_media_gallery').empty();
            updateMainMediaInput();
        });
        if ($.fn.sortable) {
            $('#quy_trinh_main_media_gallery').sortable({ items: '.gallery-item', update: updateMainMediaInput });
        }
    });
    </script>
    <?php
}

function dnttvn_add_quy_trinh_main_media_meta_box() {
    foreach (array('quy_trinh', 'gia_tri_thanh_vien', 'phung_doanh_nhan', 'phung_su_con_dn') as $pt) {
        add_meta_box(
            'quy_trinh_main_media',
            'Hình chính (Ảnh/Video/PDF)',
            'dnttvn_quy_trinh_main_media_meta_box_callback',
            $pt,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'dnttvn_add_quy_trinh_main_media_meta_box');

function dnttvn_save_quy_trinh_main_media($post_id) {
    if (!isset($_POST['dnttvn_quy_trinh_main_media_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['dnttvn_quy_trinh_main_media_nonce'], 'dnttvn_save_quy_trinh_main_media')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $pt = get_post_type($post_id);
    if (!in_array($pt, array('quy_trinh', 'gia_tri_thanh_vien', 'phung_doanh_nhan', 'phung_su_con_dn', 'nghia_vu_thanh_vien'), true)) {
        return;
    }
    if (isset($_POST['quy_trinh_main_media'])) {
        $json = sanitize_text_field(wp_unslash($_POST['quy_trinh_main_media']));
        $arr = json_decode($json, true);
        if (is_array($arr) && !empty($arr)) {
            $valid_ids = array();
            foreach ($arr as $id) {
                $id = absint($id);
                if ($id > 0 && get_post($id)) {
                    $valid_ids[] = $id;
                }
            }
            update_post_meta($post_id, '_quy_trinh_main_media', $valid_ids);
        } else {
            delete_post_meta($post_id, '_quy_trinh_main_media');
        }
    }
}
add_action('save_post', 'dnttvn_save_quy_trinh_main_media');

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

    // Save image layout preference
    $allowed_layouts = array('slideshow', 'photo_grid', 'multi_photo');
    if (isset($_POST['tin_tuc_hinh_noi_dung_layout']) && in_array($_POST['tin_tuc_hinh_noi_dung_layout'], $allowed_layouts)) {
        update_post_meta($post_id, '_tin_tuc_hinh_noi_dung_layout', sanitize_text_field($_POST['tin_tuc_hinh_noi_dung_layout']));
    } else {
        update_post_meta($post_id, '_tin_tuc_hinh_noi_dung_layout', 'slideshow');
    }

    if (isset($_POST['tin_tuc_flipbook_url'])) {
        $url = esc_url_raw($_POST['tin_tuc_flipbook_url']);
        if ($url) {
            update_post_meta($post_id, '_tin_tuc_flipbook_url', $url);
        } else {
            delete_post_meta($post_id, '_tin_tuc_flipbook_url');
        }
    }

    // Save catalog links
    if (isset($_POST['tin_tuc_catalog_label']) && is_array($_POST['tin_tuc_catalog_label']) && isset($_POST['tin_tuc_catalog_url']) && is_array($_POST['tin_tuc_catalog_url'])) {
        $labels = array_map('sanitize_text_field', $_POST['tin_tuc_catalog_label']);
        $urls = array_map('esc_url_raw', $_POST['tin_tuc_catalog_url']);
        $catalog_links = array();
        foreach ($labels as $i => $label) {
            $url = isset($urls[$i]) ? $urls[$i] : '';
            if ($label !== '' || $url !== '') {
                $catalog_links[] = array('label' => $label, 'url' => $url);
            }
        }
        if (!empty($catalog_links)) {
            update_post_meta($post_id, '_tin_tuc_catalog_links', wp_json_encode($catalog_links));
        } else {
            delete_post_meta($post_id, '_tin_tuc_catalog_links');
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

// Enhance main editor (nội dung chính) cho các post type: Tin tức, Cộng đồng, Quy trình, Giá trị thành viên, Phụng Doanh nhân, Phụng sự Con Doanh nhân, Hỏi đáp
function dnttvn_enhance_tin_tuc_editor($settings, $editor_id) {
    if ($editor_id === 'content') {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if ($screen && in_array($screen->post_type, array('tin_tuc', 'cong_dong', 'quy_trinh', 'gia_tri_thanh_vien', 'phung_doanh_nhan', 'phung_su_con_dn', 'nghia_vu_thanh_vien'), true)) {
            // Bật full toolbar: giống (hoặc hơn) Cộng đồng
            $settings['toolbar1'] = 'bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv';
            $settings['toolbar2'] = 'formatselect,fontselect,fontsizeselect,forecolor,backcolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help';

            // Thêm các thiết lập hữu ích
            $settings['wordpress_adv_hidden'] = false;
            $settings['paste_as_text'] = false;
            $settings['wpautop'] = true;
            $settings['media_buttons'] = true;
        }
    }
    return $settings;
}
add_filter('tiny_mce_before_init', 'dnttvn_enhance_tin_tuc_editor', 10, 2);

// Luôn bật trình soạn thảo trực quan (Visual Editor) cho admin,
// để tất cả các ô "Nội dung" (bao gồm Mục nội dung Quy trình / Phụng / Hỏi đáp)
// đều có thanh công cụ chỉnh sửa chữ (B, I, gạch chân, màu, link, danh sách...).
if (!is_admin() || !function_exists('add_filter')) {
    // no-op on frontend
} else {
    add_filter('user_can_richedit', '__return_true');
}

/**
 * Xóa các thẻ bookmark của TinyMCE (khi chuyển Visual/Code) để không lưu/hiển thị trong nội dung.
 * Giúp chỉ hiển thị chữ đã định dạng, không lộ code điều chỉnh (mce_SELRES_start, data-mce-type="bookmark").
 */
function dnttvn_strip_tinymce_bookmark_spans($content) {
    if (!is_string($content) || $content === '') {
        return $content;
    }
    // Nhiều pattern để bắt mọi biến thể: thứ tự thuộc tính, dấu ngoặc, khoảng trắng
    $patterns = array(
        '/<span[^>]*\s+data-mce-type\s*=\s*["\']bookmark["\'][^>]*>[\s\S]*?<\/span>/iu',
        '/<span[^>]*\s+class\s*=\s*["\'][^"\']*mce_SELRES_(?:start|end)[^"\']*["\'][^>]*>[\s\S]*?<\/span>/iu',
        '/<span[^>]*\s+class\s*=\s*["\'][^"\']*mce_SELRES[^"\']*["\'][^>]*>[\s\S]*?<\/span>/iu',
        '/<span[^>]*data-mce-type=["\']bookmark["\'][^>]*>[\s\S]*?<\/span>/iu',
        '/<span[^>]*class=["\'][^"\']*mce_SELRES[^"\']*["\'][^>]*>[\s\S]*?<\/span>/iu',
    );
    foreach ($patterns as $p) {
        $content = preg_replace($p, '', $content);
    }
    return $content;
}

/**
 * Filter nội dung trước khi hiển thị: xóa bookmark TinyMCE (dùng cho meta section content).
 */
function dnttvn_display_content_strip_bookmarks($content) {
    return dnttvn_strip_tinymce_bookmark_spans($content);
}
add_filter('the_content', 'dnttvn_display_content_strip_bookmarks', 1);
add_filter('dnttvn_display_content', 'dnttvn_display_content_strip_bookmarks');

/** Cho phép style màu nền + khung bảng (border) trên table, td, th khi lưu/hiển thị. */
function dnttvn_safe_style_css_table_cell($styles) {
    $styles = (array) $styles;
    $add = array('background-color', 'background', 'color', 'border', 'border-width', 'border-style', 'border-color', 'border-collapse', 'border-spacing');
    foreach ($add as $prop) {
        if (!in_array($prop, $styles, true)) {
            $styles[] = $prop;
        }
    }
    return $styles;
}
add_filter('safe_style_css', 'dnttvn_safe_style_css_table_cell', 99);

/**
 * Cho phép đầy đủ thẻ và thuộc tính bảng khi lưu/hiển thị (wp_kses_post) để bảng đã tạo hiển thị đúng ở view.
 */
function dnttvn_kses_allowed_html_post_tables($allowed, $context) {
    if ($context !== 'post') {
        return $allowed;
    }
    $table_attrs = array('style' => true, 'class' => true, 'id' => true, 'align' => true, 'valign' => true, 'width' => true, 'border' => true, 'cellpadding' => true, 'cellspacing' => true);
    $cell_attrs  = array('style' => true, 'class' => true, 'id' => true, 'colspan' => true, 'rowspan' => true, 'align' => true, 'valign' => true);
    $merge = function ($key, $attrs) use (&$allowed) {
        $allowed[$key] = array_merge(isset($allowed[$key]) && is_array($allowed[$key]) ? $allowed[$key] : array(), $attrs);
    };
    $merge('table', $table_attrs);
    $merge('thead', $table_attrs);
    $merge('tbody', $table_attrs);
    $merge('tfoot', $table_attrs);
    $merge('tr', $table_attrs);
    $merge('td', array_merge($cell_attrs, array('headers' => true)));
    $merge('th', array_merge($cell_attrs, array('scope' => true, 'headers' => true)));
    $merge('caption', array('style' => true, 'class' => true, 'align' => true));
    return $allowed;
}
add_filter('wp_kses_allowed_html', 'dnttvn_kses_allowed_html_post_tables', 99, 2);

/**
 * Chỉ xóa thẻ/thuộc tính nguy hiểm, giữ nguyên bảng (số dòng, số cột, style ô).
 * Dùng khi wp_kses làm mất bảng để vẫn lưu được đúng cấu trúc và màu.
 */
function dnttvn_sanitize_content_strip_dangerous_only($content) {
    if (!is_string($content) || $content === '') {
        return '';
    }
    $content = dnttvn_strip_tinymce_bookmark_spans(wp_unslash($content));
    $content = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $content);
    $content = preg_replace('/<iframe\b[^>]*>[\s\S]*?<\/iframe>/i', '', $content);
    $content = preg_replace('/<object\b[^>]*>[\s\S]*?<\/object>/i', '', $content);
    $content = preg_replace('/<embed\b[^>]*>/i', '', $content);
    $content = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $content);
    $content = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/i', '', $content);
    return $content;
}

/**
 * Sanitize nội dung khi lưu, đảm bảo giữ bảng (số dòng, số cột) và style màu nền ô (td/th).
 * Dùng cho Tin tức, Cộng đồng. Không liên quan CFDB7 (Contact Form 7 Database) — bảng do theme xử lý.
 */
function dnttvn_sanitize_content_with_tables($content) {
    if (!is_string($content)) {
        return '';
    }
    $content = dnttvn_strip_tinymce_bookmark_spans(wp_unslash($content));
    if ($content === '') {
        return '';
    }
    $allowed = wp_kses_allowed_html('post');
    if (!is_array($allowed) || empty($allowed)) {
        return wp_kses_post($content);
    }
    $allowed['table']   = array('style' => true, 'class' => true, 'id' => true, 'border' => true, 'cellpadding' => true, 'cellspacing' => true, 'width' => true);
    $allowed['thead']   = array('style' => true, 'class' => true);
    $allowed['tbody']   = array('style' => true, 'class' => true);
    $allowed['tfoot']   = array('style' => true, 'class' => true);
    $allowed['tr']      = array('style' => true, 'class' => true);
    $allowed['td']      = array('style' => true, 'class' => true, 'id' => true, 'colspan' => true, 'rowspan' => true, 'align' => true, 'valign' => true);
    $allowed['th']      = array('style' => true, 'class' => true, 'id' => true, 'colspan' => true, 'rowspan' => true, 'align' => true, 'valign' => true);
    $allowed['caption'] = array('style' => true, 'class' => true);
    $protocols = function_exists('wp_kses_allowed_protocols') ? wp_kses_allowed_protocols() : array('http', 'https', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'tel');
    $out = wp_kses($content, $allowed, $protocols);
    if ($out === '' && strlen(trim($content)) > 20) {
        return wp_kses_post($content);
    }
    if (stripos($content, '<table') !== false && stripos($out, '<table') === false) {
        return dnttvn_sanitize_content_strip_dangerous_only($content);
    }
    return $out;
}

/**
 * Hiển thị nội dung Tin tức / Cộng đồng (structured content) cho phép bảng và màu nền ô.
 * View chi tiết dùng hàm này để vẽ bảng đúng số dòng, số cột, màu ô.
 */
function dnttvn_kses_structured_content($content) {
    if (!is_string($content) || $content === '') {
        return '';
    }
    $allowed = wp_kses_allowed_html('post');
    if (!is_array($allowed)) {
        $allowed = array();
    }
    $table_attrs = array('style' => true, 'class' => true, 'id' => true, 'border' => true, 'cellpadding' => true, 'cellspacing' => true, 'width' => true);
    $cell_attrs = array('style' => true, 'class' => true, 'id' => true, 'colspan' => true, 'rowspan' => true, 'align' => true, 'valign' => true);
    $allowed['table']   = $table_attrs;
    $allowed['thead']  = array('style' => true, 'class' => true);
    $allowed['tbody']  = array('style' => true, 'class' => true);
    $allowed['tfoot']  = array('style' => true, 'class' => true);
    $allowed['tr']     = array('style' => true, 'class' => true);
    $allowed['td']     = $cell_attrs;
    $allowed['th']     = array_merge($cell_attrs, array('scope' => true));
    $allowed['caption'] = array('style' => true, 'class' => true);
    $protocols = function_exists('wp_kses_allowed_protocols') ? wp_kses_allowed_protocols() : array('http', 'https', 'mailto');
    $out = wp_kses($content, $allowed, $protocols);
    if (stripos($content, '<table') !== false && stripos($out, '<table') === false) {
        return dnttvn_sanitize_content_strip_dangerous_only($content);
    }
    return $out;
}

/** Xóa bookmark TinyMCE trước khi lưu nội dung bài (chuyển Visual/Code). */
function dnttvn_content_save_pre_strip_bookmarks($content) {
    return dnttvn_strip_tinymce_bookmark_spans($content);
}
add_filter('content_save_pre', 'dnttvn_content_save_pre_strip_bookmarks', 10);

/**
 * TinyMCE: xóa bookmark ngay khi lấy nội dung (GetContent) — trước khi ghi vào textarea khi chuyển Code/Visual hoặc submit.
 * Nhờ đó bookmark không bao giờ được lưu vào DB.
 */
function dnttvn_tinymce_before_init_strip_bookmarks($init) {
    if (!empty($init['setup']) && is_string($init['setup'])) {
        return $init;
    }
    $init['setup'] = 'function(editor){editor.on("GetContent",function(e){if(e.format==="html"&&e.content){e.content=e.content.replace(/<span[^>]*\\s+data-mce-type\\s*=\\s*["\']bookmark["\'][^>]*>[\\s\\S]*?<\\/span>/gi,"");e.content=e.content.replace(/<span[^>]*\\s+class\\s*=\\s*["\'][^"\']*mce_SELRES[^"\']*["\'][^>]*>[\\s\\S]*?<\\/span>/gi,"");}}});if(editor.addButton){editor.addButton("dnttvn_table",{title:"Chèn bảng",onclick:function(){var r=parseInt(prompt("Số dòng","3"),10)||3;var c=parseInt(prompt("Số cột","3"),10)||3;r=Math.min(20,Math.max(1,r));c=Math.min(10,Math.max(1,c));var h="<table class=\\"dnttvn-editor-table\\"><tbody>";for(var i=0;i<r;i++){h+="<tr>";for(var j=0;j<c;j++)h+="<td>&nbsp;</td>";h+="</tr>";}h+="</tbody></table>";editor.insertContent(h);}});editor.addButton("dnttvn_addrow",{title:"Thêm dòng",onclick:function(){var ed=editor,start=ed.selection.getStart(),t=ed.dom.getParent(start,"table");if(!t){ed.windowManager.alert("Đặt con trỏ vào bảng rồi bấm Thêm dòng.");return;}var tbody=t.querySelector("tbody")||t,ref=ed.dom.getParent(start,"tr")||tbody.querySelector("tr");if(!ref)return;var newTr=ref.cloneNode(true),cels=newTr.querySelectorAll("td,th");for(var i=0;i<cels.length;i++)cels[i].innerHTML="&nbsp;";tbody.insertBefore(newTr,ref.nextSibling);}});editor.addButton("dnttvn_deletetable",{title:"Xóa bảng",onclick:function(){var t=editor.dom.getParent(editor.selection.getStart(),"table");if(!t){editor.windowManager.alert("Đặt con trỏ vào bảng cần xóa rồi bấm Xóa bảng.");return;}editor.dom.remove(t);}});editor.addButton("dnttvn_addcol",{title:"Thêm cột",onclick:function(){var ed=editor,start=ed.selection.getStart(),t=ed.dom.getParent(start,"table");if(!t){ed.windowManager.alert("Đặt con trỏ vào bảng rồi bấm Thêm cột.");return;}var cell=ed.dom.getParent(start,"td")||ed.dom.getParent(start,"th");if(!cell)return;var row=cell.parentNode,cells=row.querySelectorAll("td,th");var colIndex=-1;for(var i=0;i<cells.length;i++)if(cells[i]===cell){colIndex=i;break;}if(colIndex<0)return;var tbody=t.querySelector("tbody")||t,allRows=tbody.querySelectorAll("tr");for(var r=0;r<allRows.length;r++){var rowCells=allRows[r].querySelectorAll("td,th"),newCell=cell.tagName==="TH"?document.createElement("th"):document.createElement("td");newCell.innerHTML="&nbsp;";var ref=rowCells[colIndex];allRows[r].insertBefore(newCell,ref);}});editor.addButton("dnttvn_deleterow",{title:"Xóa dòng",onclick:function(){var ed=editor,start=ed.selection.getStart(),t=ed.dom.getParent(start,"table");if(!t){ed.windowManager.alert("Đặt con trỏ vào bảng rồi bấm Xóa dòng.");return;}var row=ed.dom.getParent(start,"tr");if(!row){ed.windowManager.alert("Đặt con trỏ vào ô trong dòng cần xóa.");return;}var tbody=t.querySelector("tbody")||t,rows=tbody.querySelectorAll("tr");if(rows.length<=1){ed.windowManager.alert("Bảng chỉ còn một dòng.");return;}ed.dom.remove(row);}});editor.addButton("dnttvn_deletecol",{title:"Xóa cột",onclick:function(){var ed=editor,start=ed.selection.getStart(),t=ed.dom.getParent(start,"table");if(!t){ed.windowManager.alert("Đặt con trỏ vào bảng rồi bấm Xóa cột.");return;}var cell=ed.dom.getParent(start,"td")||ed.dom.getParent(start,"th");if(!cell)return;var row=cell.parentNode,cells=row.querySelectorAll("td,th");var colIndex=-1;for(var i=0;i<cells.length;i++)if(cells[i]===cell){colIndex=i;break;}if(colIndex<0)return;var tbody=t.querySelector("tbody")||t,allRows=tbody.querySelectorAll("tr");for(var r=0;r<allRows.length;r++){var rowCells=allRows[r].querySelectorAll("td,th");if(rowCells[colIndex])ed.dom.remove(rowCells[colIndex]);}}});}}';
    return $init;
}
add_filter('tiny_mce_before_init', 'dnttvn_tinymce_before_init_strip_bookmarks', 50);

/**
 * Script admin: gắn GetContent cho mọi editor (kể cả editor khởi tạo sau qua wp.editor.initialize).
 */
function dnttvn_admin_footer_tinymce_strip_bookmarks() {
    ?>
<script>
(function() {
    function stripBookmarks(html) {
        if (!html || typeof html !== 'string') return html;
        return html
            .replace(/<span[^>]*\s+data-mce-type\s*=\s*["']bookmark["'][^>]*>[\s\S]*?<\/span>/gi, '')
            .replace(/<span[^>]*\s+class\s*=\s*["'][^"']*mce_SELRES[^"']*["'][^>]*>[\s\S]*?<\/span>/gi, '');
    }
    function attachStrip(editor) {
        if (editor.getParam('dnttvn_strip_done')) return;
        editor.on('GetContent', function(e) {
            if (e.format === 'html' && e.content) {
                e.content = stripBookmarks(e.content);
            }
        });
        editor.settings.dnttvn_strip_done = true;
    }
    if (typeof tinymce !== 'undefined') {
        tinymce.on('AddEditor', function(e) {
            attachStrip(e.editor);
        });
        if (tinymce.editors) {
            for (var i = 0; i < tinymce.editors.length; i++) {
                attachStrip(tinymce.editors[i]);
            }
        }
    }
    /* Luôn hiển thị Visual (WYSIWYG) cho ô nội dung — ẩn mã HTML, chỉ thấy chữ đã định dạng */
    function forceVisualMode() {
        var ids = ['tin_tuc_structured_content', 'cong_dong_structured_content', 'quy_trinh_sections', 'postdivrich', 'wp-dnttvn_luat_choi_content-wrap', 'wp-dnttvn_content_gia_tri-wrap'];
        ids.forEach(function(id) {
            var box = document.getElementById(id);
            if (!box) return;
            var wraps = box.classList && box.classList.contains('wp-editor-wrap') ? [box] : box.querySelectorAll('.wp-editor-wrap');
            for (var i = 0; i < wraps.length; i++) {
                var wrap = wraps[i];
                wrap.classList.remove('html-active');
                wrap.classList.add('tmce-active');
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', forceVisualMode);
    } else {
        forceVisualMode();
    }
    if (typeof jQuery !== 'undefined') {
        jQuery(function() { setTimeout(forceVisualMode, 500); });
        // Trước khi submit form bài viết: đồng bộ TinyMCE xuống textarea để nội dung (kể cả bảng) được gửi đúng
        jQuery('form#post').on('submit', function() {
            if (typeof wp !== 'undefined' && wp.editor && typeof wp.editor.triggerSave === 'function') {
                wp.editor.triggerSave();
            }
        });
    }
})();
</script>
    <?php
}
add_action('admin_footer', 'dnttvn_admin_footer_tinymce_strip_bookmarks');

/**
 * Ẩn tab Text/Visual cho ô mục nội dung trên mọi màn quản lý có editor (meta box + Luật chơi, Trang thành viên).
 */
function dnttvn_admin_hide_editor_tabs_for_content_sections() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    $is_post_edit = $screen && !empty($screen->post_type);
    $post_type = $is_post_edit ? $screen->post_type : '';
    $with_sections = array('tin_tuc', 'cong_dong', 'quy_trinh', 'gia_tri_thanh_vien', 'phung_doanh_nhan', 'phung_su_con_dn', 'nghia_vu_thanh_vien');
    $is_options_editor = $screen && in_array($screen->id, array('dnttvn-banner-header_page_dnttvn-luat-choi', 'dnttvn-banner-header_page_dnttvn-trang-thanh-vien-moi'), true);
    if (!in_array($post_type, $with_sections, true) && !$is_options_editor) {
        return;
    }
    echo '<style id="dnttvn-hide-editor-tabs">';
    echo '#tin_tuc_structured_content .wp-editor-tabs,#cong_dong_structured_content .wp-editor-tabs,#quy_trinh_sections .wp-editor-tabs{display:none !important;}';
    echo '#postdivrich .wp-editor-tabs{display:none !important;}';
    if ($is_options_editor) {
        echo '#wp-dnttvn_luat_choi_content-wrap .wp-editor-tabs,#wp-dnttvn_content_gia_tri-wrap .wp-editor-tabs{display:none !important;}';
    }
    echo '</style>';
}
add_action('admin_head', 'dnttvn_admin_hide_editor_tabs_for_content_sections');

// Add admin styles for Tin tức and Cộng đồng editor
function dnttvn_tin_tuc_admin_styles() {
    global $post_type;
    if (in_array($post_type, array('tin_tuc', 'cong_dong'))) {
        ?>
        <style>
            /* Improve editor area */
            #tin_tuc_details .inside,
            #cong_dong_structured_content .inside,
            #doanh_nhan_structured_content .inside {
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
            .postbox#doanh_nhan_structured_content {
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

            /* Thanh công cụ chỉnh sửa nội dung (B, I, link, danh sách) cho mục nội dung nhỏ */
            .dnttvn-content-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                padding: 6px 8px;
                margin-bottom: 6px;
                background: #f6f7f7;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .dnttvn-content-toolbar .dnttvn-toolbar-btn {
                min-width: 28px;
                height: 28px;
                padding: 0 6px;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                background: #fff;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                color: #1d2327;
            }
            .dnttvn-content-toolbar .dnttvn-toolbar-btn:hover {
                background: #f0f0f1;
                border-color: #8c8f94;
            }
            .dnttvn-content-toolbar .dnttvn-toolbar-btn.dnttvn-toolbar-link { font-weight: normal; font-size: 12px; }
            .dnttvn-content-toolbar .dnttvn-toolbar-sep { color: #8c8f94; margin: 0 4px; user-select: none; }
            .dnttvn-content-toolbar-wrap { margin-bottom: 10px; position: relative; }
            .dnttvn-color-palette {
                position: fixed;
                background: #fff;
                border: 1px solid #ccc;
                border-radius: 6px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.15);
                padding: 10px;
                z-index: 100100;
            }
            .dnttvn-color-palette-grid {
                display: grid;
                grid-template-columns: repeat(8, 20px);
                gap: 4px;
                margin-bottom: 8px;
            }
            .dnttvn-color-swatch {
                width: 20px;
                height: 20px;
                border: 1px solid rgba(0,0,0,0.2);
                border-radius: 4px;
                padding: 0;
                cursor: pointer;
            }
            .dnttvn-color-swatch:hover {
                transform: scale(1.1);
                box-shadow: 0 1px 4px rgba(0,0,0,0.2);
            }
            .dnttvn-color-custom {
                display: block;
                width: 100%;
                margin-top: 4px;
            }
            .dnttvn-rich-editor-wrap { position: relative; }
            .dnttvn-rich-editor {
                min-height: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;
                background: #fff; font-family: "Segoe UI", Arial, sans-serif; font-size: 14px; line-height: 1.6;
                outline: none; white-space: pre-wrap; word-wrap: break-word;
            }
            .dnttvn-rich-editor:empty:before { content: attr(data-placeholder); color: #999; }
            .dnttvn-rich-editor p { margin: 0 0 0.75em 0; }
            .dnttvn-rich-editor:focus { border-color: #007cba; box-shadow: 0 0 0 1px #007cba; }
            .dnttvn-rich-editor table,
            .dnttvn-editor-table {
                border-collapse: collapse; width: 100%; max-width: 100%;
            }
            .dnttvn-rich-editor td,
            .dnttvn-rich-editor th,
            .dnttvn-editor-table td,
            .dnttvn-editor-table th {
                border: 1px solid #ccc; padding: 6px 10px; text-align: left; vertical-align: top;
            }
            .dnttvn-rich-editor th,
            .dnttvn-editor-table th { background: #f5f5f5; font-weight: 600; }
            
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
        'show_in_menu'          => true,
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

// Taxonomy: Khu vực (Dùng cho Đăng ký / Doanh nhân)
function dnttvn_register_khu_vuc_taxonomy() {
    register_taxonomy('khu_vuc', array('dang_ky', 'doanh_nhan'), array(
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
add_action('init', 'dnttvn_register_khu_vuc_taxonomy', 0);

function dnttvn_get_all_chuc_vu() {
    global $wpdb;
    $results = $wpdb->get_col(
        "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = '_doanh_nhan_chuc_vu'
         AND pm.meta_value != ''
         AND p.post_type = 'doanh_nhan'
         AND p.post_status = 'publish'
         ORDER BY pm.meta_value ASC"
    );
    return $results ? $results : array();
}

// Taxonomy: Ngành (Doanh nhân — quản lý ngành nghề hiển thị)
function dnttvn_register_nganh_nghe_doanh_nhan_taxonomy() {
    register_taxonomy('nganh_nghe', array('doanh_nhan'), array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => 'Ngành',
            'singular_name' => 'Ngành',
            'search_items'  => 'Tìm ngành',
            'all_items'     => 'Tất cả ngành',
            'edit_item'     => 'Sửa ngành',
            'update_item'   => 'Cập nhật',
            'add_new_item'  => 'Thêm ngành',
            'new_item_name' => 'Tên ngành',
            'menu_name'     => 'Ngành',
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'nganh-nghe-dn'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'dnttvn_register_nganh_nghe_doanh_nhan_taxonomy', 0);

// Seed default Khu vực terms
function dnttvn_seed_khu_vuc_terms() {
    if (get_option('dnttvn_seeded_khu_vuc_congdong')) {
        return;
    }
    $regions = array(
        'Hà Nội', 'TP. Hồ Chí Minh', 'Hải Phòng', 'Đà Nẵng', 'Cần Thơ',
        'Bắc Ninh', 'Hưng Yên', 'Hải Dương', 'Nam Định', 'Ninh Bình',
        'Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế',
        'Khánh Hòa', 'Lâm Đồng', 'Đắk Lắk', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu',
        'Long An', 'Tiền Giang', 'Vĩnh Long', 'Quảng Ngãi', 'Bình Định', 'Phú Yên'
    );
    foreach ($regions as $name) {
        if (!term_exists($name, 'khu_vuc')) {
            wp_insert_term($name, 'khu_vuc');
        }
    }
    update_option('dnttvn_seeded_khu_vuc_congdong', 1);
}
add_action('init', 'dnttvn_seed_khu_vuc_terms', 20);

// Custom Post Type: Đăng ký (lưu đơn đăng ký gia nhập)
function dnttvn_register_dang_ky_post_type() {
    $labels = array(
        'name'               => 'Đăng ký',
        'singular_name'      => 'Đơn đăng ký',
        'menu_name'          => 'Đơn đăng ký',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm đơn đăng ký',
        'edit_item'          => 'Xem đơn đăng ký',
        'new_item'           => 'Đơn mới',
        'view_item'          => 'Xem đơn',
        'search_items'       => 'Tìm đơn đăng ký',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Đơn đăng ký',
        'labels'              => $labels,
        'supports'            => array('title', 'custom-fields', 'thumbnail'),
        'taxonomies'          => array('khu_vuc'),
        'public'               => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-clipboard',
        'capability_type'     => 'post',
    );
    register_post_type('dang_ky', $args);
}
add_action('init', 'dnttvn_register_dang_ky_post_type', 0);

// CPT: Đăng ký Chương trình Hướng nghiệp & Khai mở Trí tuệ (con em Thành viên)
function dnttvn_register_dang_ky_huong_nghiep_post_type() {
    $labels = array(
        'name'               => 'Đăng ký Hướng nghiệp',
        'singular_name'      => 'Phiếu đăng ký Hướng nghiệp',
        'menu_name'          => 'Đăng ký Hướng nghiệp',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm phiếu đăng ký',
        'edit_item'          => 'Xem phiếu đăng ký',
        'new_item'           => 'Phiếu mới',
        'view_item'          => 'Xem phiếu',
        'search_items'       => 'Tìm phiếu đăng ký',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Đăng ký Hướng nghiệp',
        'labels'              => $labels,
        'supports'            => array('title', 'custom-fields'),
        'public'               => false,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=phung_su_con_dn',
        'menu_position'       => 8,
        'menu_icon'           => 'dashicons-welcome-learn-more',
        'capability_type'     => 'post',
    );
    register_post_type('dang_ky_huong_nghiep', $args);
}
add_action('init', 'dnttvn_register_dang_ky_huong_nghiep_post_type', 0);

// CPT: Quy trình gia nhập Cộng đồng (tiêu đề + hình chính + mô tả + tiêu đề nhỏ 1 - nội dung 1 ...)
function dnttvn_register_quy_trinh_post_type() {
    $labels = array(
        'name'               => 'Quy trình gia nhập',
        'singular_name'      => 'Quy trình',
        'menu_name'          => 'Quy trình gia nhập',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm quy trình',
        'edit_item'          => 'Chỉnh sửa quy trình',
        'new_item'           => 'Quy trình mới',
        'view_item'          => 'Xem quy trình',
        'search_items'       => 'Tìm quy trình',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Quy trình gia nhập',
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 8,
        'menu_icon'           => 'dashicons-list-view',
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('quy_trinh', $args);
}
add_action('init', 'dnttvn_register_quy_trinh_post_type', 0);

// CPT: Giá trị nhận được của thành viên (nhiều bài, mỗi bài có Mục nội dung giống Quy trình)
function dnttvn_register_gia_tri_thanh_vien_post_type() {
    $labels = array(
        'name'               => 'Giá trị thành viên',
        'singular_name'      => 'Giá trị',
        'menu_name'          => 'Giá trị thành viên',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm bài Giá trị',
        'edit_item'          => 'Chỉnh sửa Giá trị',
        'new_item'           => 'Giá trị mới',
        'view_item'          => 'Xem Giá trị',
        'search_items'       => 'Tìm Giá trị',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Giá trị thành viên',
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 10,
        'menu_icon'           => 'dashicons-awards',
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('gia_tri_thanh_vien', $args);
}
add_action('init', 'dnttvn_register_gia_tri_thanh_vien_post_type', 0);

// CPT: Hỏi đáp về Cộng đồng (cùng cấu trúc: tiêu đề + hình + mô tả + tiêu đề nhỏ - nội dung)
function dnttvn_register_hoi_dap_post_type() {
    $labels = array(
        'name'               => 'Hỏi đáp Cộng đồng',
        'singular_name'      => 'Hỏi đáp',
        'menu_name'          => 'Hỏi đáp Cộng đồng',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm hỏi đáp',
        'edit_item'          => 'Chỉnh sửa hỏi đáp',
        'new_item'           => 'Hỏi đáp mới',
        'view_item'          => 'Xem hỏi đáp',
        'search_items'       => 'Tìm hỏi đáp',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Hỏi đáp Cộng đồng',
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 9,
        'menu_icon'           => 'dashicons-format-chat',
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('hoi_dap', $args);
}
add_action('init', 'dnttvn_register_hoi_dap_post_type', 0);

// CPT: Phụng Doanh nhân
function dnttvn_register_phung_doanh_nhan_post_type() {
    $labels = array(
        'name'               => 'Phụng Doanh nhân',
        'singular_name'      => 'Phụng Doanh nhân',
        'menu_name'          => 'Phụng Doanh nhân',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm bài Phụng Doanh nhân',
        'edit_item'          => 'Chỉnh sửa Phụng Doanh nhân',
        'new_item'           => 'Phụng Doanh nhân mới',
        'view_item'          => 'Xem Phụng Doanh nhân',
        'search_items'       => 'Tìm Phụng Doanh nhân',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Phụng Doanh nhân',
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 11,
        'menu_icon'           => 'dashicons-groups',
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('phung_doanh_nhan', $args);
}
add_action('init', 'dnttvn_register_phung_doanh_nhan_post_type', 0);

// CPT: Phụng sự Con Doanh nhân
function dnttvn_register_phung_su_con_dn_post_type() {
    $labels = array(
        'name'               => 'Phụng sự Con Doanh nhân',
        'singular_name'      => 'Phụng sự Con Doanh nhân',
        'menu_name'          => 'Phụng sự Con Doanh nhân',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm bài Phụng sự Con Doanh nhân',
        'edit_item'          => 'Chỉnh sửa Phụng sự Con Doanh nhân',
        'new_item'           => 'Bài mới',
        'view_item'          => 'Xem bài',
        'search_items'       => 'Tìm kiếm',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Phụng sự Con Doanh nhân',
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 12,
        'menu_icon'           => 'dashicons-heart',
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('phung_su_con_dn', $args);
}
add_action('init', 'dnttvn_register_phung_su_con_dn_post_type', 0);

// Register Custom Post Type: Doanh nhân (Quản lý hồ sơ doanh nhân sau khi duyệt)
function dnttvn_register_doanh_nhan_post_type() {
    $labels = array(
        'name'                  => 'Doanh nhân',
        'singular_name'         => 'Doanh nhân',
        'menu_name'             => 'Doanh nhân',
        'name_admin_bar'        => 'Doanh nhân',
        'add_new'               => 'Thêm Doanh nhân',
        'add_new_item'          => 'Thêm Doanh nhân mới',
        'new_item'              => 'Doanh nhân mới',
        'edit_item'             => 'Chỉnh sửa Doanh nhân',
        'view_item'             => 'Xem Doanh nhân',
        'all_items'             => 'Toàn bộ Doanh nhân',
        'search_items'          => 'Tìm Doanh nhân',
        'not_found'             => 'Không tìm thấy Doanh nhân nào',
    );
    $args = array(
        'label'                 => 'Doanh nhân',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('khu_vuc', 'nganh_nghe'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6.1, // Ngay sau Cộng đồng/Đăng ký
        'menu_icon'             => 'dashicons-businessperson',
        'show_in_rest'          => true,
        'has_archive'           => true,
    );
    register_post_type('doanh_nhan', $args);
}
add_action('init', 'dnttvn_register_doanh_nhan_post_type', 0);

/**
 * Liên kết doanh nghiệp hiển thị: bài CPT doanh_nghiep (nếu có) + URL tùy chỉnh.
 *
 * @return array<int, array{url:string,label:string}>
 */
function dnttvn_get_doanh_nhan_doanh_nghiep_links($post_id) {
    $post_id = absint($post_id);
    $items   = array();
    $seen    = array();

    $ids = get_post_meta($post_id, '_doanh_nhan_linked_dn_ids', true);
    if (!is_array($ids)) {
        $ids = array();
    }
    $ids = array_values(array_unique(array_filter(array_map('absint', $ids))));

    if (function_exists('post_type_exists') && post_type_exists('doanh_nghiep')) {
        foreach ($ids as $pid) {
            if ($pid < 1) {
                continue;
            }
            $p = get_post($pid);
            if (!$p || $p->post_type !== 'doanh_nghiep' || $p->post_status !== 'publish') {
                continue;
            }
            $url = get_permalink($pid);
            if (!$url || isset($seen[ $url ])) {
                continue;
            }
            $seen[ $url ] = 1;
            $items[] = array(
                'url'   => $url,
                'label' => get_the_title($p),
            );
        }
    }

    $raw = get_post_meta($post_id, '_doanh_nhan_manual_dn_links', true);
    $manual = is_string($raw) ? json_decode($raw, true) : array();
    if (is_array($manual)) {
        foreach ($manual as $row) {
            if (!is_array($row) || empty($row['url'])) {
                continue;
            }
            $url = esc_url_raw(trim((string) $row['url']));
            if ($url === '' || isset($seen[ $url ])) {
                continue;
            }
            $seen[ $url ] = 1;
            $lab = isset($row['label']) ? sanitize_text_field((string) $row['label']) : '';
            if ($lab === '') {
                $lab = $url;
            }
            $items[] = array('url' => $url, 'label' => $lab);
        }
    }

    return $items;
}

function dnttvn_add_doanh_nhan_meta_boxes() {
    add_meta_box(
        'dnttvn_doanh_nhan_info',
        'Thông tin Doanh nhân',
        'dnttvn_doanh_nhan_info_meta_box_callback',
        'doanh_nhan',
        'normal',
        'high'
    );
    add_meta_box(
        'dnttvn_doanh_nhan_dn_links',
        'Liên kết Doanh nghiệp',
        'dnttvn_doanh_nhan_dn_links_meta_box_callback',
        'doanh_nhan',
        'normal',
        'default'
    );
    add_meta_box(
        'doanh_nhan_structured_content',
        'Thêm mục nội dung',
        'dnttvn_structured_content_meta_box_callback',
        'doanh_nhan',
        'normal',
        'low'
    );
}

function dnttvn_doanh_nhan_info_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_doanh_nhan_info', 'dnttvn_doanh_nhan_info_nonce');
    $chuc_vu    = get_post_meta($post->ID, '_doanh_nhan_chuc_vu', true);
    $cong_ty    = get_post_meta($post->ID, '_doanh_nhan_cong_ty', true);
    $ngay_sinh  = get_post_meta($post->ID, '_doanh_nhan_ngay_sinh', true);
    $gioi_tinh  = get_post_meta($post->ID, '_doanh_nhan_gioi_tinh', true);
    $dien_thoai = get_post_meta($post->ID, '_doanh_nhan_dien_thoai', true);
    $email      = get_post_meta($post->ID, '_doanh_nhan_email', true);
    $dia_chi    = get_post_meta($post->ID, '_doanh_nhan_dia_chi', true);
    ?>
    <p class="description" style="margin-bottom:10px; color:#666;"><em>Tiêu đề bài viết = Họ và tên doanh nhân</em></p>
    <table class="form-table">
        <tr>
            <th><label for="doanh_nhan_ngay_sinh">Ngày sinh</label></th>
            <td>
                <input type="date" id="doanh_nhan_ngay_sinh" name="doanh_nhan_ngay_sinh" value="<?php echo esc_attr($ngay_sinh); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_gioi_tinh">Giới tính</label></th>
            <td>
                <select id="doanh_nhan_gioi_tinh" name="doanh_nhan_gioi_tinh" class="regular-text">
                    <option value="">-- Chọn --</option>
                    <option value="Nam" <?php selected($gioi_tinh, 'Nam'); ?>>Nam</option>
                    <option value="Nữ" <?php selected($gioi_tinh, 'Nữ'); ?>>Nữ</option>
                    <option value="Khác" <?php selected($gioi_tinh, 'Khác'); ?>>Khác</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_dien_thoai">Số điện thoại</label></th>
            <td>
                <input type="text" id="doanh_nhan_dien_thoai" name="doanh_nhan_dien_thoai" value="<?php echo esc_attr($dien_thoai); ?>" class="regular-text" placeholder="VD: 0901234567" />
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_email">Email</label></th>
            <td>
                <input type="email" id="doanh_nhan_email" name="doanh_nhan_email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="VD: ten@email.com" />
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_dia_chi">Địa chỉ liên hệ</label></th>
            <td>
                <input type="text" id="doanh_nhan_dia_chi" name="doanh_nhan_dia_chi" value="<?php echo esc_attr($dia_chi); ?>" class="large-text" placeholder="VD: 123 Nguyễn Huệ, Q.1, TP.HCM" />
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_chuc_vu">Chức vụ hiện nay</label></th>
            <td>
                <select id="doanh_nhan_chuc_vu" name="doanh_nhan_chuc_vu" class="regular-text">
                    <option value="">-- Chọn chức vụ --</option>
                    <?php
                    $cv_options = dnttvn_get_all_chuc_vu();
                    foreach ($cv_options as $cv_opt) {
                        echo '<option value="' . esc_attr($cv_opt) . '"' . selected($chuc_vu, $cv_opt, false) . '>' . esc_html($cv_opt) . '</option>';
                    }
                    if ($chuc_vu !== '' && !in_array($chuc_vu, $cv_options)) {
                        echo '<option value="' . esc_attr($chuc_vu) . '" selected>' . esc_html($chuc_vu) . '</option>';
                    }
                    ?>
                    <option value="__new__">+ Thêm chức vụ mới...</option>
                </select>
                <input type="text" id="doanh_nhan_chuc_vu_new" style="display:none; margin-top:5px;" class="large-text" placeholder="Nhập chức vụ mới..." />
                <script>
                jQuery(function($){
                    $('#doanh_nhan_chuc_vu').on('change', function(){
                        if ($(this).val() === '__new__') {
                            $('#doanh_nhan_chuc_vu_new').show().focus();
                        } else {
                            $('#doanh_nhan_chuc_vu_new').hide().val('');
                        }
                    });
                    $('form#post').on('submit', function(){
                        if ($('#doanh_nhan_chuc_vu').val() === '__new__') {
                            var nv = $('#doanh_nhan_chuc_vu_new').val().trim();
                            if (nv) {
                                $('#doanh_nhan_chuc_vu').append('<option value="'+nv+'" selected>'+nv+'</option>');
                            } else {
                                $('#doanh_nhan_chuc_vu').val('');
                            }
                        }
                    });
                });
                </script>
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_cong_ty">Tên doanh nghiệp / đơn vị công tác</label></th>
            <td>
                <input type="text" id="doanh_nhan_cong_ty" name="doanh_nhan_cong_ty" value="<?php echo esc_attr($cong_ty); ?>" class="large-text" placeholder="VD: Công ty TNHH ABC..." />
            </td>
        </tr>
        <tr>
            <th><label>Lĩnh vực hoạt động</label></th>
            <td>
                <?php
                $selected_terms = wp_get_post_terms($post->ID, 'nganh_nghe', array('fields' => 'ids'));
                $all_terms = get_terms(array('taxonomy' => 'nganh_nghe', 'hide_empty' => false));
                if (!is_wp_error($all_terms) && !empty($all_terms)) :
                    foreach ($all_terms as $term) :
                        $checked = in_array($term->term_id, $selected_terms) ? ' checked' : '';
                        ?>
                        <label style="display:inline-block; margin-right:15px; margin-bottom:5px;">
                            <input type="checkbox" name="doanh_nhan_nganh_nghe[]" value="<?php echo esc_attr($term->term_id); ?>"<?php echo $checked; ?>>
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach;
                else : ?>
                    <p class="description">Chưa có lĩnh vực nào. Vào <strong>Doanh nhân > Ngành</strong> để thêm.</p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label>Khu vực</label></th>
            <td>
                <?php
                $selected_kv = wp_get_post_terms($post->ID, 'khu_vuc', array('fields' => 'ids'));
                $selected_kv_id = !empty($selected_kv) ? (int) $selected_kv[0] : 0;
                $all_kv = get_terms(array('taxonomy' => 'khu_vuc', 'hide_empty' => false));
                ?>
                <select name="doanh_nhan_khu_vuc" id="doanh_nhan_khu_vuc" class="regular-text">
                    <option value="">-- Chọn khu vực --</option>
                    <?php if (!is_wp_error($all_kv)) :
                        foreach ($all_kv as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($selected_kv_id, $term->term_id); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach;
                    endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_menu_order">Thứ tự hiển thị</label></th>
            <td>
                <input type="number" id="doanh_nhan_menu_order" name="menu_order" value="<?php echo esc_attr($post->menu_order); ?>" class="small-text" min="0" step="1" />
                <p class="description">Số nhỏ hiển thị trước. Mặc định 0.</p>
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nhan_hinh_phu">Hình ảnh phụ</label></th>
            <td>
                <input type="text" id="doanh_nhan_hinh_phu" name="doanh_nhan_hinh_phu" value="<?php echo esc_attr(get_post_meta($post->ID, '_doanh_nhan_hinh_phu', true)); ?>" class="regular-text" />
                <button type="button" class="button" id="upload_dn_hinh_phu">Chọn hình ảnh</button>
                <button type="button" class="button" id="remove_dn_hinh_phu" style="color:#a00;<?php echo empty(get_post_meta($post->ID, '_doanh_nhan_hinh_phu', true)) ? 'display:none;' : ''; ?>">Xóa hình phụ</button>
                <p class="description">Ảnh bổ sung hiển thị bên dưới thông tin liên hệ. Click "Chọn hình ảnh" để upload.</p>
                <div id="dn_hinh_phu_preview" style="margin-top: 10px;">
                <?php
                $dn_hinh_phu = get_post_meta($post->ID, '_doanh_nhan_hinh_phu', true);
                if ($dn_hinh_phu) :
                    $preview_id = is_numeric($dn_hinh_phu) ? absint($dn_hinh_phu) : attachment_url_to_postid($dn_hinh_phu);
                    if ($preview_id) {
                        echo wp_get_attachment_image($preview_id, 'thumbnail');
                    } elseif (filter_var($dn_hinh_phu, FILTER_VALIDATE_URL)) {
                        echo '<img src="' . esc_url($dn_hinh_phu) . '" style="max-width: 150px; height: auto;" />';
                    }
                endif;
                ?>
                </div>
                <script>
                jQuery(function($){
                    var mediaUploader;
                    $('#upload_dn_hinh_phu').on('click', function(e){
                        e.preventDefault();
                        if (mediaUploader) { mediaUploader.open(); return; }
                        mediaUploader = wp.media({ title: 'Chọn hình ảnh phụ', button: { text: 'Chọn' }, multiple: false });
                        mediaUploader.on('select', function(){
                            var attachment = mediaUploader.state().get('selection').first().toJSON();
                            $('#doanh_nhan_hinh_phu').val(attachment.id);
                            $('#dn_hinh_phu_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" />');
                            $('#remove_dn_hinh_phu').show();
                        });
                        mediaUploader.open();
                    });
                    $('#remove_dn_hinh_phu').on('click', function(e){
                        e.preventDefault();
                        $('#doanh_nhan_hinh_phu').val('');
                        $('#dn_hinh_phu_preview').empty();
                        $(this).hide();
                    });
                });
                </script>
            </td>
        </tr>
    </table>
    <?php
}
add_action('add_meta_boxes', 'dnttvn_add_doanh_nhan_meta_boxes');

function dnttvn_doanh_nhan_custom_columns($columns) {
    $new = array();
    foreach ($columns as $key => $val) {
        if ($key === 'taxonomy-nganh_nghe') {
            continue;
        }
        $new[$key] = $val;
        if ($key === 'title') {
            $new['dn_chuc_vu']    = 'Chức vụ';
            $new['dn_menu_order'] = 'Thứ tự';
        }
    }
    return $new;
}
add_filter('manage_doanh_nhan_posts_columns', 'dnttvn_doanh_nhan_custom_columns');

function dnttvn_doanh_nhan_custom_column_content($column, $post_id) {
    if ($column === 'dn_chuc_vu') {
        $cv = get_post_meta($post_id, '_doanh_nhan_chuc_vu', true);
        echo $cv ? esc_html($cv) : '—';
    }
    if ($column === 'dn_menu_order') {
        echo esc_html(get_post_field('menu_order', $post_id));
    }
}
add_action('manage_doanh_nhan_posts_custom_column', 'dnttvn_doanh_nhan_custom_column_content', 10, 2);

function dnttvn_doanh_nhan_sortable_columns($columns) {
    $columns['dn_chuc_vu']    = 'dn_chuc_vu';
    $columns['dn_menu_order'] = 'menu_order';
    return $columns;
}
add_filter('manage_edit-doanh_nhan_sortable_columns', 'dnttvn_doanh_nhan_sortable_columns');

function dnttvn_doanh_nhan_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'doanh_nhan') {
        return;
    }
    $orderby = $query->get('orderby');
    if ($orderby === 'dn_chuc_vu') {
        $query->set('meta_key', '_doanh_nhan_chuc_vu');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'dnttvn_doanh_nhan_column_orderby');

function dnttvn_doanh_nhan_dn_links_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_doanh_nhan_links', 'dnttvn_doanh_nhan_links_nonce');

    $saved_ids = get_post_meta($post->ID, '_doanh_nhan_linked_dn_ids', true);
    if (!is_array($saved_ids)) {
        $saved_ids = array();
    }
    $saved_ids = array_flip(array_map('absint', $saved_ids));

    $raw_manual = get_post_meta($post->ID, '_doanh_nhan_manual_dn_links', true);
    $manual     = is_string($raw_manual) ? json_decode($raw_manual, true) : array();
    if (!is_array($manual)) {
        $manual = array();
    }

    echo '<p class="description">Chọn một hoặc nhiều bài <strong>Doanh nghiệp</strong> (nếu website đang bật CPT này), và/hoặc thêm liên kết URL + nhãn hiển thị.</p>';

    if (function_exists('post_type_exists') && post_type_exists('doanh_nghiep')) {
        $dn_posts = get_posts(array(
            'post_type'      => 'doanh_nghiep',
            'post_status'    => 'publish',
            'posts_per_page' => 500,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ));
        if (!empty($dn_posts)) {
            echo '<fieldset style="margin:12px 0;padding:10px;border:1px solid #ccd0d4;"><legend><strong>Chọn từ danh mục Doanh nghiệp</strong></legend>';
            echo '<div style="max-height:220px;overflow:auto;display:grid;grid-template-columns:repeat(2,minmax(200px,1fr));gap:6px;">';
            foreach ($dn_posts as $pid) {
                $pid = absint($pid);
                $cb  = isset($saved_ids[ $pid ]) ? ' checked' : '';
                echo '<label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="dnttvn_linked_dn_ids[]" value="' . esc_attr((string) $pid) . '"' . $cb . '> ';
                echo esc_html(get_the_title($pid)) . '</label>';
            }
            echo '</div></fieldset>';
        } else {
            echo '<p><em>Chưa có bài Doanh nghiệp nào để chọn.</em></p>';
        }
    } else {
        echo '<p class="description">CPT <code>doanh_nghiep</code> chưa đăng ký trên site này — chỉ dùng bảng liên kết URL bên dưới.</p>';
    }

    echo '<fieldset style="margin:12px 0;padding:10px;border:1px solid #ccd0d4;"><legend><strong>Liên kết URL (một hoặc nhiều)</strong></legend>';
    echo '<table class="widefat"><thead><tr><th>Nhãn hiển thị</th><th>URL</th></tr></thead><tbody>';
    $rows = max(5, count($manual) + 2);
    for ($i = 0; $i < $rows; $i++) {
        $lab = isset($manual[ $i ]['label']) ? $manual[ $i ]['label'] : '';
        $u   = isset($manual[ $i ]['url']) ? $manual[ $i ]['url'] : '';
        echo '<tr><td><input type="text" class="widefat" name="dnttvn_manual_dn_label[]" value="' . esc_attr($lab) . '" placeholder="Tên doanh nghiệp"></td>';
        echo '<td><input type="url" class="widefat" name="dnttvn_manual_dn_url[]" value="' . esc_attr($u) . '" placeholder="https://..."></td></tr>';
    }
    echo '</tbody></table></fieldset>';
}

function dnttvn_save_doanh_nhan_links_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (get_post_type($post_id) !== 'doanh_nhan') {
        return;
    }
    if (!isset($_POST['dnttvn_doanh_nhan_links_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dnttvn_doanh_nhan_links_nonce'])), 'dnttvn_save_doanh_nhan_links')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $ids = array();
    if (!empty($_POST['dnttvn_linked_dn_ids']) && is_array($_POST['dnttvn_linked_dn_ids'])) {
        $ids = array_values(array_unique(array_filter(array_map('absint', wp_unslash($_POST['dnttvn_linked_dn_ids'])))));
    }
    update_post_meta($post_id, '_doanh_nhan_linked_dn_ids', $ids);

    $labels = isset($_POST['dnttvn_manual_dn_label']) ? wp_unslash($_POST['dnttvn_manual_dn_label']) : array();
    $urls   = isset($_POST['dnttvn_manual_dn_url']) ? wp_unslash($_POST['dnttvn_manual_dn_url']) : array();
    $manual = array();
    $n      = max(count($labels), count($urls));
    for ($i = 0; $i < $n; $i++) {
        $u = isset($urls[ $i ]) ? esc_url_raw(trim((string) $urls[ $i ])) : '';
        if ($u === '') {
            continue;
        }
        $l = isset($labels[ $i ]) ? sanitize_text_field((string) $labels[ $i ]) : '';
        $manual[] = array('label' => $l, 'url' => $u);
    }
    if (empty($manual)) {
        delete_post_meta($post_id, '_doanh_nhan_manual_dn_links');
    } else {
        update_post_meta($post_id, '_doanh_nhan_manual_dn_links', wp_json_encode($manual, JSON_UNESCAPED_UNICODE));
    }
}
add_action('save_post_doanh_nhan', 'dnttvn_save_doanh_nhan_links_meta', 10, 1);

function dnttvn_save_doanh_nhan_info_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (get_post_type($post_id) !== 'doanh_nhan') {
        return;
    }
    if (!isset($_POST['dnttvn_doanh_nhan_info_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dnttvn_doanh_nhan_info_nonce'])), 'dnttvn_save_doanh_nhan_info')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['doanh_nhan_ngay_sinh'])) {
        update_post_meta($post_id, '_doanh_nhan_ngay_sinh', sanitize_text_field(wp_unslash($_POST['doanh_nhan_ngay_sinh'])));
    }
    if (isset($_POST['doanh_nhan_gioi_tinh'])) {
        update_post_meta($post_id, '_doanh_nhan_gioi_tinh', sanitize_text_field(wp_unslash($_POST['doanh_nhan_gioi_tinh'])));
    }
    if (isset($_POST['doanh_nhan_dien_thoai'])) {
        update_post_meta($post_id, '_doanh_nhan_dien_thoai', sanitize_text_field(wp_unslash($_POST['doanh_nhan_dien_thoai'])));
    }
    if (isset($_POST['doanh_nhan_email'])) {
        update_post_meta($post_id, '_doanh_nhan_email', sanitize_email(wp_unslash($_POST['doanh_nhan_email'])));
    }
    if (isset($_POST['doanh_nhan_dia_chi'])) {
        update_post_meta($post_id, '_doanh_nhan_dia_chi', sanitize_text_field(wp_unslash($_POST['doanh_nhan_dia_chi'])));
    }

    if (isset($_POST['doanh_nhan_chuc_vu'])) {
        update_post_meta($post_id, '_doanh_nhan_chuc_vu', sanitize_text_field(wp_unslash($_POST['doanh_nhan_chuc_vu'])));
    }
    if (isset($_POST['doanh_nhan_cong_ty'])) {
        update_post_meta($post_id, '_doanh_nhan_cong_ty', sanitize_text_field(wp_unslash($_POST['doanh_nhan_cong_ty'])));
    }

    if (isset($_POST['doanh_nhan_nganh_nghe']) && is_array($_POST['doanh_nhan_nganh_nghe'])) {
        $term_ids = array_map('absint', wp_unslash($_POST['doanh_nhan_nganh_nghe']));
        wp_set_post_terms($post_id, $term_ids, 'nganh_nghe');
    } else {
        wp_set_post_terms($post_id, array(), 'nganh_nghe');
    }

    if (isset($_POST['doanh_nhan_khu_vuc'])) {
        $kv_id = absint(wp_unslash($_POST['doanh_nhan_khu_vuc']));
        if ($kv_id > 0) {
            wp_set_post_terms($post_id, array($kv_id), 'khu_vuc');
        } else {
            wp_set_post_terms($post_id, array(), 'khu_vuc');
        }
    }

    if (isset($_POST['doanh_nhan_hinh_phu'])) {
        update_post_meta($post_id, '_doanh_nhan_hinh_phu', sanitize_text_field(wp_unslash($_POST['doanh_nhan_hinh_phu'])));
    }
}
add_action('save_post_doanh_nhan', 'dnttvn_save_doanh_nhan_info_meta', 10, 1);

// CPT: Nghĩa vụ thành viên Cộng Đồng
function dnttvn_register_nghia_vu_thanh_vien_post_type() {
    $labels = array(
        'name'               => 'Nghĩa vụ thành viên Cộng Đồng',
        'singular_name'      => 'Nghĩa vụ thành viên Cộng Đồng',
        'menu_name'          => 'Nghĩa vụ thành viên Cộng Đồng',
        'add_new'            => 'Thêm mới',
        'add_new_item'       => 'Thêm bài Nghĩa vụ thành viên',
        'edit_item'          => 'Chỉnh sửa Nghĩa vụ thành viên',
        'new_item'           => 'Bài mới',
        'view_item'          => 'Xem bài',
        'search_items'       => 'Tìm kiếm',
        'not_found'          => 'Không tìm thấy',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Nghĩa vụ thành viên Cộng Đồng',
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 13,
        'menu_icon'           => 'dashicons-clipboard',
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('nghia_vu_thanh_vien', $args);
}
add_action('init', 'dnttvn_register_nghia_vu_thanh_vien_post_type', 0);

// CPT: Sự kiện
function dnttvn_register_su_kien_post_type() {
    $labels = array(
        'name'               => 'Sự kiện',
        'singular_name'      => 'Sự kiện',
        'menu_name'          => 'Sự kiện',
        'add_new'            => 'Thêm sự kiện',
        'add_new_item'       => 'Thêm sự kiện mới',
        'edit_item'          => 'Chỉnh sửa sự kiện',
        'new_item'           => 'Sự kiện mới',
        'view_item'          => 'Xem sự kiện',
        'all_items'          => 'Toàn bộ sự kiện',
        'search_items'       => 'Tìm sự kiện',
        'not_found'          => 'Không tìm thấy sự kiện nào',
        'not_found_in_trash' => 'Không có trong thùng rác',
    );
    $args = array(
        'label'               => 'Sự kiện',
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-calendar-alt',
        'show_in_rest'        => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('su_kien', $args);
}
add_action('init', 'dnttvn_register_su_kien_post_type', 0);

function dnttvn_add_su_kien_meta_boxes() {
    add_meta_box(
        'dnttvn_su_kien_info',
        'Thông tin Sự kiện',
        'dnttvn_su_kien_info_meta_box_callback',
        'su_kien',
        'normal',
        'high'
    );
    add_meta_box(
        'su_kien_structured_content',
        'Thêm mục nội dung',
        'dnttvn_structured_content_meta_box_callback',
        'su_kien',
        'normal',
        'low'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_su_kien_meta_boxes');

function dnttvn_su_kien_info_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_su_kien_info', 'dnttvn_su_kien_info_nonce');
    $ngay_mo       = get_post_meta($post->ID, '_su_kien_ngay_mo', true);
    $ngay_ket_thuc = get_post_meta($post->ID, '_su_kien_ngay_ket_thuc', true);
    // Backward compat: migrate old _su_kien_ngay
    if (!$ngay_mo) {
        $ngay_mo = get_post_meta($post->ID, '_su_kien_ngay', true);
    }
    $dia_diem      = get_post_meta($post->ID, '_su_kien_dia_diem', true);
    $video_url     = get_post_meta($post->ID, '_su_kien_video_url', true);
    $gallery_raw   = get_post_meta($post->ID, '_su_kien_gallery', true);

    $now = current_time('Y-m-d');
    $trang_thai = 'Chưa xác định';
    if ($ngay_mo && $ngay_ket_thuc) {
        if ($now < $ngay_mo) $trang_thai = 'Sắp diễn ra';
        elseif ($now > $ngay_ket_thuc) $trang_thai = 'Đã kết thúc';
        else $trang_thai = 'Đang diễn ra';
    } elseif ($ngay_mo) {
        $trang_thai = ($now < $ngay_mo) ? 'Sắp diễn ra' : 'Đang/Đã diễn ra';
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="su_kien_ngay_mo">Ngày mở sự kiện</label></th>
            <td>
                <input type="date" id="su_kien_ngay_mo" name="su_kien_ngay_mo" value="<?php echo esc_attr($ngay_mo); ?>" class="regular-text" />
                <p class="description">Sự kiện tự động hiển thị từ ngày này.</p>
            </td>
        </tr>
        <tr>
            <th><label for="su_kien_ngay_ket_thuc">Ngày kết thúc</label></th>
            <td>
                <input type="date" id="su_kien_ngay_ket_thuc" name="su_kien_ngay_ket_thuc" value="<?php echo esc_attr($ngay_ket_thuc); ?>" class="regular-text" />
                <p class="description">Sự kiện tự động ẩn sau ngày này.</p>
            </td>
        </tr>
        <tr>
            <th>Trạng thái</th>
            <td>
                <span style="padding:4px 12px; border-radius:4px; font-weight:600; color:#fff; background:<?php
                    if ($trang_thai === 'Đang diễn ra') echo '#4caf50';
                    elseif ($trang_thai === 'Sắp diễn ra') echo '#ff9800';
                    elseif ($trang_thai === 'Đã kết thúc') echo '#9e9e9e';
                    else echo '#607d8b';
                ?>;"><?php echo esc_html($trang_thai); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="su_kien_dia_diem">Địa điểm</label></th>
            <td><input type="text" id="su_kien_dia_diem" name="su_kien_dia_diem" value="<?php echo esc_attr($dia_diem); ?>" class="large-text" placeholder="VD: Hội trường A, TP.HCM..." /></td>
        </tr>
        <tr>
            <th><label for="su_kien_video_url">Video URL</label></th>
            <td>
                <input type="text" id="su_kien_video_url" name="su_kien_video_url" value="<?php echo esc_attr($video_url); ?>" class="large-text" placeholder="YouTube/Vimeo URL hoặc upload..." />
                <button type="button" class="button" id="upload_su_kien_video">Chọn video</button>
                <p class="description">Nhập URL YouTube/Vimeo hoặc click "Chọn video" để upload video từ media library.</p>
            </td>
        </tr>
        <tr>
            <th><label for="su_kien_gallery">Thư viện hình ảnh</label></th>
            <td>
                <input type="hidden" id="su_kien_gallery" name="su_kien_gallery" value="<?php echo esc_attr($gallery_raw); ?>" />
                <button type="button" class="button" id="upload_su_kien_gallery">Chọn hình ảnh</button>
                <button type="button" class="button" id="clear_su_kien_gallery" style="color:#a00;<?php echo empty($gallery_raw) ? 'display:none;' : ''; ?>">Xóa tất cả</button>
                <p class="description">Chọn nhiều hình ảnh cho sự kiện. IDs cách nhau bằng dấu phẩy.</p>
                <div id="su_kien_gallery_preview" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:8px;">
                    <?php
                    if ($gallery_raw) {
                        $ids = array_filter(array_map('absint', explode(',', $gallery_raw)));
                        foreach ($ids as $img_id) {
                            $thumb = wp_get_attachment_image_url($img_id, 'thumbnail');
                            if ($thumb) {
                                echo '<img src="' . esc_url($thumb) . '" style="width:80px;height:80px;object-fit:cover;border-radius:4px;" />';
                            }
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="su_kien_menu_order">Thứ tự hiển thị</label></th>
            <td>
                <input type="number" id="su_kien_menu_order" name="menu_order" value="<?php echo esc_attr($post->menu_order); ?>" class="small-text" min="0" step="1" />
                <p class="description">Số nhỏ hiển thị trước. Mặc định 0.</p>
            </td>
        </tr>
    </table>
    <script>
    jQuery(function($){
        var videoUploader, galleryUploader;
        $('#upload_su_kien_video').on('click', function(e){
            e.preventDefault();
            if (videoUploader) { videoUploader.open(); return; }
            videoUploader = wp.media({ title: 'Chọn video', library: { type: 'video' }, button: { text: 'Chọn' }, multiple: false });
            videoUploader.on('select', function(){ $('#su_kien_video_url').val(videoUploader.state().get('selection').first().toJSON().url); });
            videoUploader.open();
        });
        $('#upload_su_kien_gallery').on('click', function(e){
            e.preventDefault();
            if (galleryUploader) { galleryUploader.open(); return; }
            galleryUploader = wp.media({ title: 'Chọn hình ảnh sự kiện', library: { type: 'image' }, button: { text: 'Chọn' }, multiple: true });
            galleryUploader.on('select', function(){
                var ids = [], preview = '';
                galleryUploader.state().get('selection').each(function(att){
                    var a = att.toJSON();
                    ids.push(a.id);
                    var thumb = a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url;
                    preview += '<img src="'+thumb+'" style="width:80px;height:80px;object-fit:cover;border-radius:4px;" />';
                });
                var existing = $('#su_kien_gallery').val();
                if (existing) ids = existing.split(',').concat(ids);
                $('#su_kien_gallery').val(ids.join(','));
                $('#su_kien_gallery_preview').html($('#su_kien_gallery_preview').html() + preview);
                $('#clear_su_kien_gallery').show();
            });
            galleryUploader.open();
        });
        $('#clear_su_kien_gallery').on('click', function(e){
            e.preventDefault();
            $('#su_kien_gallery').val('');
            $('#su_kien_gallery_preview').empty();
            $(this).hide();
        });
    });
    </script>
    <?php
}

function dnttvn_save_su_kien_info_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'su_kien') return;
    if (!isset($_POST['dnttvn_su_kien_info_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dnttvn_su_kien_info_nonce'])), 'dnttvn_save_su_kien_info')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['su_kien_ngay_mo'])) {
        update_post_meta($post_id, '_su_kien_ngay_mo', sanitize_text_field(wp_unslash($_POST['su_kien_ngay_mo'])));
        update_post_meta($post_id, '_su_kien_ngay', sanitize_text_field(wp_unslash($_POST['su_kien_ngay_mo'])));
    }
    if (isset($_POST['su_kien_ngay_ket_thuc'])) {
        update_post_meta($post_id, '_su_kien_ngay_ket_thuc', sanitize_text_field(wp_unslash($_POST['su_kien_ngay_ket_thuc'])));
    }
    if (isset($_POST['su_kien_dia_diem'])) {
        update_post_meta($post_id, '_su_kien_dia_diem', sanitize_text_field(wp_unslash($_POST['su_kien_dia_diem'])));
    }
    if (isset($_POST['su_kien_video_url'])) {
        update_post_meta($post_id, '_su_kien_video_url', esc_url_raw(wp_unslash($_POST['su_kien_video_url'])));
    }
    if (isset($_POST['su_kien_gallery'])) {
        $gallery = sanitize_text_field(wp_unslash($_POST['su_kien_gallery']));
        $gallery = implode(',', array_filter(array_map('absint', explode(',', $gallery))));
        update_post_meta($post_id, '_su_kien_gallery', $gallery);
    }
}
add_action('save_post_su_kien', 'dnttvn_save_su_kien_info_meta', 10, 1);

// Auto-schedule: chi hien thi su kien dang trong thoi gian dien ra
function dnttvn_su_kien_active_query_args() {
    $now = current_time('Y-m-d');
    return array(
        'post_type'      => 'su_kien',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array('key' => '_su_kien_ngay_mo', 'value' => $now, 'compare' => '<=', 'type' => 'DATE'),
                array('key' => '_su_kien_ngay_mo', 'compare' => 'NOT EXISTS'),
            ),
            array(
                'relation' => 'OR',
                array('key' => '_su_kien_ngay_ket_thuc', 'value' => $now, 'compare' => '>=', 'type' => 'DATE'),
                array('key' => '_su_kien_ngay_ket_thuc', 'compare' => 'NOT EXISTS'),
            ),
        ),
    );
}

// Lấy mục nội dung (tiêu đề nhỏ + nội dung + hình + caption) cho Quy trình / Hỏi đáp
function dnttvn_get_sections_array($post_id, $meta_key) {
    $raw = get_post_meta($post_id, $meta_key, true);
    $list = array();
    if (is_array($raw)) {
        $list = $raw;
    } elseif (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) $list = $decoded;
    }
    // Chuẩn hóa từng mục: hỗ trợ cả legacy (image_id/image_caption) và mới (media_ids/media_captions)
    $out = array();
    foreach ($list as $sec) {
        if (!is_array($sec)) continue;
        $image_id = isset($sec['image_id']) ? absint($sec['image_id']) : 0;
        $image_caption = isset($sec['image_caption']) ? (string) $sec['image_caption'] : '';

        $media_ids = array();
        $media_captions = array();
        if (isset($sec['media_ids']) && is_array($sec['media_ids'])) {
            $media_ids = array_values(array_filter(array_map('absint', $sec['media_ids'])));
        }
        if (isset($sec['media_captions']) && is_array($sec['media_captions'])) {
            $media_captions = array_values(array_map('sanitize_text_field', $sec['media_captions']));
        }
        // Nếu dữ liệu cũ chỉ có 1 ảnh, map sang mảng mới để hiển thị đồng nhất
        if (empty($media_ids) && $image_id) {
            $media_ids = array($image_id);
            $media_captions = array($image_caption);
        }

        $out[] = array(
            'heading'      => isset($sec['heading']) ? $sec['heading'] : '',
            'content'      => isset($sec['content']) ? $sec['content'] : '',
            'image_id'     => $image_id,
            'image_caption'=> $image_caption,
            'media_ids'    => $media_ids,
            'media_captions' => $media_captions,
        );
    }
    return $out;
}

/**
 * Cấu hình thanh công cụ chỉnh sửa nội dung dùng chung cho tất cả mục nội dung
 * (Quy trình, Tin tức, Cộng đồng...). Dùng cho wp_editor và wp.editor.initialize.
 */
function dnttvn_get_content_section_editor_settings($textarea_name = '', $editor_id = '') {
    $tinymce = array(
        'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,forecolor,backcolor,link,unlink,bullist,numlist,outdent,indent,blockquote,alignleft,aligncenter,alignright,alignjustify',
        'toolbar2' => 'hr,removeformat,charmap,undo,redo,pastetext,|,subscript,superscript,|,dnttvn_table,dnttvn_addrow,dnttvn_addcol,dnttvn_deleterow,dnttvn_deletecol,dnttvn_deletetable',
        'content_style' => 'body, .mce-content-body { font-family: "Segoe UI", Arial, "Helvetica Neue", sans-serif !important; line-height: 1.6 !important; font-size: 14px !important; } table { border-collapse: collapse; width: 100%; max-width: 100%; } .mce-content-body td, .mce-content-body th { border: 1px solid #ccc; padding: 6px 10px; text-align: left; vertical-align: top; } .mce-content-body th { background: #f5f5f5; font-weight: 600; }',
    );
    $base = array(
        'textarea_rows' => 8,
        'teeny'         => false,
        'quicktags'     => array('buttons' => 'strong,em,link,block,ul,ol,li,del,ins,code,close'),
        'media_buttons' => false,
        'tinymce'       => $tinymce,
        'wpautop'       => true,
        'dfw'           => false,
    );
    if ($textarea_name !== '') {
        $base['textarea_name'] = $textarea_name;
    }
    if ($editor_id !== '') {
        $base['editor_class'] = 'dnttvn-content-section-editor';
    }
    return $base;
}

/**
 * Trả về cấu hình editor dạng mảng để truyền sang JS (cho wp.editor.initialize khi thêm mục mới).
 */
function dnttvn_get_content_section_editor_settings_for_js() {
    $s = dnttvn_get_content_section_editor_settings();
    unset($s['textarea_name']);
    return $s;
}

// Meta box: Mục nội dung (thanh công cụ chỉnh sửa) cho:
// - Giá trị nhận được của thành viên | Quy trình gia nhập Cộng đồng | Phụng Doanh nhân | Phụng sự Con Doanh nhân | Nghĩa vụ thành viên Cộng đồng
// Hỏi đáp không dùng meta box này (không quản lý tạo bài).
function dnttvn_add_quy_trinh_meta_boxes() {
    $screens = array(
        'gia_tri_thanh_vien',   // Giá trị nhận được của thành viên
        'quy_trinh',            // Quy trình gia nhập Cộng đồng
        'phung_doanh_nhan',     // Phụng Doanh nhân
        'phung_su_con_dn',      // Phụng sự Con Doanh nhân
        'nghia_vu_thanh_vien',  // Nghĩa vụ thành viên Cộng đồng
    );
    foreach ($screens as $screen) {
        add_meta_box(
            'quy_trinh_sections',
            'Mục nội dung (tiêu đề nhỏ + nội dung)',
            'dnttvn_quy_trinh_sections_callback',
            $screen,
            'normal',
            'default'
        );
    }
    // Meta box riêng cho Phụng sự Con Doanh nhân: Con nhân viên đã duyệt (chọn hiển thị + ghi chú)
    add_meta_box(
        'phung_su_con_dn_con_da_duyet',
        'Con nhân viên đã duyệt (hiển thị & ghi chú)',
        'dnttvn_phung_su_con_dn_con_da_duyet_callback',
        'phung_su_con_dn',
        'normal',
        'high'
    );
}
function dnttvn_phung_su_con_dn_con_da_duyet_callback($post) {
    wp_nonce_field('dnttvn_save_phung_su_con_dn_con', 'dnttvn_phung_su_con_dn_con_nonce');
    $hien_thi_ids = get_post_meta($post->ID, '_phung_su_con_dn_hien_thi_ids', true);
    if (!is_array($hien_thi_ids)) {
        $hien_thi_ids = array();
    }
    $hien_thi_ids = array_map('absint', $hien_thi_ids);
    $ghi_chu = get_post_meta($post->ID, '_phung_su_con_dn_ghi_chu', true);
    if (!is_string($ghi_chu)) {
        $ghi_chu = '';
    }
    $approved = get_posts(array(
        'post_type'      => 'dang_ky_huong_nghiep',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'meta_key'       => '_hn_approved',
        'meta_value'     => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    echo '<p class="description">Chọn các con nhân viên đã duyệt để hiển thị trong bài này. Chỉ hiển thị phiếu đăng ký Hướng nghiệp đã được đánh dấu "Đã duyệt".</p>';
    if (empty($approved)) {
        echo '<p><em>Chưa có con nhân viên nào được duyệt. Vào <a href="' . esc_url(admin_url('edit.php?post_type=dang_ky_huong_nghiep')) . '">Đăng ký Hướng nghiệp</a> để duyệt phiếu.</em></p>';
    } else {
        echo '<ul style="list-style:none; margin:0 0 16px 0;">';
        foreach ($approved as $p) {
            $phu_huynh = get_post_meta($p->ID, '_hn_ho_ten_phu_huynh', true);
            $con = get_post_meta($p->ID, '_hn_ho_ten_con', true);
            $label = trim($phu_huynh . ' – ' . $con);
            if ($label === '–') {
                $label = $p->post_title;
            }
            $checked = in_array($p->ID, $hien_thi_ids, true) ? ' checked="checked"' : '';
            echo '<li style="margin-bottom:6px;"><label><input type="checkbox" name="phung_su_con_dn_hien_thi_ids[]" value="' . esc_attr($p->ID) . '"' . $checked . '> ' . esc_html($label) . '</label></li>';
        }
        echo '</ul>';
    }
    echo '<p><label for="phung_su_con_dn_ghi_chu"><strong>Ghi chú:</strong></label></p>';
    echo '<textarea name="phung_su_con_dn_ghi_chu" id="phung_su_con_dn_ghi_chu" class="large-text" rows="4" placeholder="Ghi chú nội bộ...">' . esc_textarea($ghi_chu) . '</textarea>';
}
function dnttvn_save_phung_su_con_dn_con_da_duyet($post_id) {
    if (!isset($_POST['dnttvn_phung_su_con_dn_con_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['dnttvn_phung_su_con_dn_con_nonce']), 'dnttvn_save_phung_su_con_dn_con')) {
        return;
    }
    if (get_post_type($post_id) !== 'phung_su_con_dn' || !current_user_can('edit_post', $post_id)) {
        return;
    }
    $ids = isset($_POST['phung_su_con_dn_hien_thi_ids']) && is_array($_POST['phung_su_con_dn_hien_thi_ids'])
        ? array_values(array_filter(array_map('absint', $_POST['phung_su_con_dn_hien_thi_ids'])))
        : array();
    update_post_meta($post_id, '_phung_su_con_dn_hien_thi_ids', $ids);
    $ghi_chu = isset($_POST['phung_su_con_dn_ghi_chu']) ? sanitize_textarea_field($_POST['phung_su_con_dn_ghi_chu']) : '';
    update_post_meta($post_id, '_phung_su_con_dn_ghi_chu', $ghi_chu);
}
add_action('save_post_phung_su_con_dn', 'dnttvn_save_phung_su_con_dn_con_da_duyet');

function dnttvn_quy_trinh_sections_callback($post) {
    dnttvn_quy_trinh_sections_callback_with_image($post);
}

// Meta box Quy trình: tiêu đề nhỏ + hình ảnh + nội dung từng mục
function dnttvn_quy_trinh_sections_callback_with_image($post) {
    $meta_key = '_quy_trinh_sections';
    wp_nonce_field('dnttvn_save_sections', 'dnttvn_sections_nonce');
    $sections = dnttvn_get_sections_array($post->ID, $meta_key);
    ?>
    <p class="description">Thêm các mục: Tiêu đề nhỏ, Hình ảnh (tùy chọn), Nội dung. Ô nội dung có thanh công cụ chỉnh sửa chữ (in đậm, in nghiêng, màu, link, danh sách...). Có thể thêm/xóa mục.</p>
    <div id="sections-container-<?php echo esc_attr($meta_key); ?>">
        <?php
        if (!empty($sections)) {
            foreach ($sections as $i => $sec) {
                $h = isset($sec['heading']) ? $sec['heading'] : '';
                $c = isset($sec['content']) ? $sec['content'] : '';
                // Legacy (single image) fields
                $img_id = isset($sec['image_id']) ? absint($sec['image_id']) : 0;
                $img_caption = isset($sec['image_caption']) ? $sec['image_caption'] : '';
                // New (multi media) fields
                $media_ids = array();
                $media_captions = array();
                if (!empty($sec['media_ids']) && is_array($sec['media_ids'])) {
                    $media_ids = array_values(array_filter(array_map('absint', $sec['media_ids'])));
                }
                if (!empty($sec['media_captions']) && is_array($sec['media_captions'])) {
                    $media_captions = array_values(array_map('sanitize_text_field', $sec['media_captions']));
                }
                if (empty($media_ids) && $img_id) {
                    $media_ids = array($img_id);
                    $media_captions = array($img_caption);
                }
                ?>
                <div class="section-row section-row-quy-trinh" style="margin-bottom:15px; padding:12px; border:1px solid #ddd; background:#fafafa;">
                    <label>Tiêu đề nhỏ <?php echo (int)($i+1); ?></label>
                    <input type="text" name="<?php echo esc_attr($meta_key); ?>[<?php echo (int)$i; ?>][heading]" value="<?php echo esc_attr($h); ?>" class="widefat" style="margin-bottom:8px;">
                    <label>Media (Ảnh / Video / PDF) <?php echo (int)($i+1); ?></label>
                    <div class="quy-trinh-media-wrap" style="margin-bottom: 10px; background:#f6fbff; border:1px dashed #007cba; padding:10px; border-radius:6px;">
                        <div class="quy-trinh-media-gallery" data-section-index="<?php echo (int) $i; ?>" style="display:flex; flex-wrap:wrap; gap:10px; min-height:60px; padding:10px; background:#fff; border:1px solid #ddd; border-radius:6px;">
                            <?php foreach ($media_ids as $m_idx => $aid) :
                                $aid = (int) $aid;
                                if ($aid <= 0) continue;
                                $mime = get_post_mime_type($aid);
                                $url = wp_get_attachment_url($aid);
                                if (!$url) continue;
                                $is_video = is_string($mime) && strpos($mime, 'video') === 0;
                                $is_pdf = (string) $mime === 'application/pdf';
                                $thumb = !$is_video && !$is_pdf ? wp_get_attachment_image_url($aid, 'thumbnail') : '';
                                $cap = isset($media_captions[$m_idx]) ? (string) $media_captions[$m_idx] : '';
                                ?>
                                <div class="qt-media-item" data-id="<?php echo (int) $aid; ?>" style="position:relative; width:150px; border:1px solid #ddd; border-radius:6px; padding:6px; background:#fff;">
                                    <input type="hidden" name="<?php echo esc_attr($meta_key); ?>[<?php echo (int)$i; ?>][media_ids][]" value="<?php echo (int) $aid; ?>">
                                    <?php if ($is_video) : ?>
                                        <video style="width: 100%; height: 90px; object-fit: cover; border-radius: 4px;" controls>
                                            <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime); ?>">
                                        </video>
                                    <?php elseif ($is_pdf) : ?>
                                        <div style="width:100%; height:90px; border-radius:4px; background:#f2f2f2; display:flex; align-items:center; justify-content:center; font-weight:700; color:#b30000;">
                                            PDF
                                        </div>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url($thumb ? $thumb : $url); ?>" alt="" style="width: 100%; height: 90px; object-fit: cover; border-radius: 4px;" />
                                    <?php endif; ?>
                                    <button type="button" class="button qt-remove-media" style="position:absolute; top:-8px; right:-8px; background:#dc3232; color:#fff; border:none; border-radius:50%; width:22px; height:22px; cursor:pointer; font-size:12px; line-height:20px; padding:0;">×</button>
                                    <input type="text" name="<?php echo esc_attr($meta_key); ?>[<?php echo (int)$i; ?>][media_captions][]" value="<?php echo esc_attr($cap); ?>" placeholder="Caption..." style="width:100%; margin-top:6px; font-size:12px;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin:10px 0 0 0;">
                            <button type="button" class="button button-primary qt-add-media" data-section-index="<?php echo (int) $i; ?>">📁 Thêm ảnh/video/PDF</button>
                            <button type="button" class="button qt-clear-media" data-section-index="<?php echo (int) $i; ?>" style="<?php echo empty($media_ids) ? 'display:none;' : ''; ?> margin-left:6px;">🗑️ Xóa tất cả</button>
                            <span class="description" style="margin-left:8px;">Có thể chọn nhiều file, kéo thả để sắp xếp thứ tự.</span>
                        </p>
                        <!-- Keep legacy fields (hidden) for backward compatibility -->
                        <input type="hidden" name="<?php echo esc_attr($meta_key); ?>[<?php echo (int)$i; ?>][image_id]" value="<?php echo (int)$img_id; ?>">
                        <input type="hidden" name="<?php echo esc_attr($meta_key); ?>[<?php echo (int)$i; ?>][image_caption]" value="<?php echo esc_attr($img_caption); ?>">
                    </div>
                    <label>Nội dung <?php echo (int)($i+1); ?></label>
                    <?php
                    $editor_id = 'quy_trinh_section_editor_' . $post->ID . '_' . (int) $i;
                    wp_editor($c, $editor_id, array_merge(
                        dnttvn_get_content_section_editor_settings($meta_key . '[' . (int) $i . '][content]', $editor_id),
                        array('editor_class' => 'quy-trinh-section-content-editor')
                    ));
                    ?>
                    <button type="button" class="button remove-section">Xóa mục</button>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <p><button type="button" class="button" id="add-section-<?php echo esc_attr($meta_key); ?>">+ Thêm mục</button></p>
    <script>
    (function(){
        var metaKey = '<?php echo esc_js($meta_key); ?>';
        var container = document.getElementById('sections-container-' + metaKey);
        var addBtn = document.getElementById('add-section-' + metaKey);
        if (!container || !addBtn) return;
        var index = container.querySelectorAll('.section-row-quy-trinh').length;
        function bindSectionRow(row) {
            var removeBtn = row.querySelector('.remove-section');
            if (removeBtn) removeBtn.onclick = function() {
                var editorId = row.getAttribute('data-editor-id') || (row.querySelector('textarea[id]') && row.querySelector('textarea[id]').id);
                if (editorId && typeof wp !== 'undefined' && wp.editor && wp.editor.remove) try { wp.editor.remove(editorId); } catch (e) {}
                row.remove();
            };
            // Bind media gallery actions
            if (typeof jQuery !== 'undefined') {
                var $row = jQuery(row);
                var $gallery = $row.find('.quy-trinh-media-gallery');
                var $btnAdd = $row.find('.qt-add-media');
                var $btnClear = $row.find('.qt-clear-media');

                function ensureSortable() {
                    if (!$gallery.length) return;
                    if (typeof $gallery.sortable === 'function') {
                        try {
                            if (!$gallery.hasClass('ui-sortable')) {
                                $gallery.sortable({ items: '.qt-media-item', cursor: 'move' });
                            }
                        } catch (e) {}
                    }
                }

                ensureSortable();

                $row.on('click', '.qt-remove-media', function(e) {
                    e.preventDefault();
                    jQuery(this).closest('.qt-media-item').remove();
                    if ($btnClear.length) {
                        $btnClear.toggle($gallery.find('.qt-media-item').length > 0);
                    }
                });

                if ($btnClear.length) {
                    $btnClear.off('click').on('click', function(e) {
                        e.preventDefault();
                        $gallery.find('.qt-media-item').remove();
                        $btnClear.hide();
                    });
                }

                if ($btnAdd.length) {
                    $btnAdd.off('click').on('click', function(e) {
                        e.preventDefault();
                        if (typeof wp === 'undefined' || !wp.media) {
                            alert('Thư viện media chưa sẵn sàng. Vui lòng tải lại trang.');
                            return;
                        }
                        var sectionIndex = parseInt($btnAdd.attr('data-section-index') || '0', 10);
                        var frame = wp.media({
                            title: 'Chọn ảnh / video / PDF (có thể chọn nhiều)',
                            button: { text: 'Thêm' },
                            multiple: 'add',
                            library: {}
                        });
                        frame.on('select', function() {
                            var selection = frame.state().get('selection');
                            var attachments = selection.map(function(att) { return att.toJSON(); });
                            attachments.forEach(function(att) {
                                var id = att.id;
                                if (!id) return;
                                var mime = att.mime || '';
                                var isVideo = mime.indexOf('video') === 0;
                                var isPdf = mime === 'application/pdf';
                                var thumbUrl = (att.sizes && att.sizes.thumbnail && att.sizes.thumbnail.url) ? att.sizes.thumbnail.url : (att.url || '');
                                var previewHtml = '';
                                if (isVideo) {
                                    previewHtml = '<video style="width:100%; height:90px; object-fit:cover; border-radius:4px;" controls><source src="' + (att.url || '') + '" type="' + mime + '"></video>';
                                } else if (isPdf) {
                                    previewHtml = '<div style="width:100%; height:90px; border-radius:4px; background:#f2f2f2; display:flex; align-items:center; justify-content:center; font-weight:700; color:#b30000;">PDF</div>';
                                } else {
                                    previewHtml = '<img src="' + thumbUrl + '" alt="" style="width:100%; height:90px; object-fit:cover; border-radius:4px;">';
                                }
                                var itemHtml = '' +
                                    '<div class="qt-media-item" data-id="' + id + '" style="position:relative; width:150px; border:1px solid #ddd; border-radius:6px; padding:6px; background:#fff;">' +
                                        '<input type="hidden" name="' + metaKey + '[' + sectionIndex + '][media_ids][]" value="' + id + '">' +
                                        previewHtml +
                                        '<button type="button" class="button qt-remove-media" style="position:absolute; top:-8px; right:-8px; background:#dc3232; color:#fff; border:none; border-radius:50%; width:22px; height:22px; cursor:pointer; font-size:12px; line-height:20px; padding:0;">×</button>' +
                                        '<input type="text" name="' + metaKey + '[' + sectionIndex + '][media_captions][]" value="" placeholder="Caption..." style="width:100%; margin-top:6px; font-size:12px;">' +
                                    '</div>';
                                $gallery.append(itemHtml);
                            });
                            ensureSortable();
                            if ($btnClear.length) {
                                $btnClear.toggle($gallery.find('.qt-media-item').length > 0);
                            }
                        });
                        frame.open();
                    });
                }
            }
        }
        container.querySelectorAll('.section-row-quy-trinh').forEach(bindSectionRow);
        var sectionEditorPostId = <?php echo json_encode($post->ID); ?>;
        var sectionEditorBaseSettings = <?php echo json_encode(dnttvn_get_content_section_editor_settings_for_js()); ?>;
        addBtn.onclick = function() {
            var editorId = 'quy_trinh_section_editor_' + sectionEditorPostId + '_' + index;
            var div = document.createElement('div');
            div.className = 'section-row section-row-quy-trinh';
            div.style.cssText = 'margin-bottom:15px; padding:12px; border:1px solid #ddd; background:#fafafa;';
            div.innerHTML = '<label>Tiêu đề nhỏ ' + (index + 1) + '</label>' +
                '<input type="text" name="' + metaKey + '[' + index + '][heading]" value="" class="widefat" style="margin-bottom:8px;">' +
                '<label>Media (Ảnh / Video / PDF) ' + (index + 1) + '</label>' +
                '<div class="quy-trinh-media-wrap" style="margin-bottom: 10px; background:#f6fbff; border:1px dashed #007cba; padding:10px; border-radius:6px;">' +
                    '<div class="quy-trinh-media-gallery" data-section-index="' + index + '" style="display:flex; flex-wrap:wrap; gap:10px; min-height:60px; padding:10px; background:#fff; border:1px solid #ddd; border-radius:6px;"></div>' +
                    '<p style="margin:10px 0 0 0;">' +
                        '<button type="button" class="button button-primary qt-add-media" data-section-index="' + index + '">📁 Thêm ảnh/video/PDF</button>' +
                        '<button type="button" class="button qt-clear-media" data-section-index="' + index + '" style="display:none; margin-left:6px;">🗑️ Xóa tất cả</button>' +
                        '<span class="description" style="margin-left:8px;">Có thể chọn nhiều file, kéo thả để sắp xếp thứ tự.</span>' +
                    '</p>' +
                    '<input type="hidden" name="' + metaKey + '[' + index + '][image_id]" value="0">' +
                    '<input type="hidden" name="' + metaKey + '[' + index + '][image_caption]" value="">' +
                '</div>' +
                '<label>Nội dung ' + (index + 1) + '</label>' +
                '<textarea id="' + editorId + '" name="' + metaKey + '[' + index + '][content]" class="widefat" rows="8"></textarea>' +
                '<button type="button" class="button remove-section">Xóa mục</button>';
            div.setAttribute('data-editor-id', editorId);
            container.appendChild(div);
            bindSectionRow(div);
            if (typeof wp !== 'undefined' && wp.editor && wp.editor.initialize) {
                var settings = Object.assign({}, sectionEditorBaseSettings, { textarea_name: metaKey + '[' + index + '][content]' });
                wp.editor.initialize(editorId, settings);
            }
            index++;
        };
    })();
    </script>
    <?php
}

function dnttvn_sections_meta_box_callback($post, $meta_key) {
    wp_nonce_field('dnttvn_save_sections', 'dnttvn_sections_nonce');
    $sections = dnttvn_get_sections_array($post->ID, $meta_key);
    ?>
    <p class="description">
        Thêm các mục: Tiêu đề nhỏ 1, Nội dung 1, Tiêu đề nhỏ 2, Nội dung 2... (có thể thêm/xóa mục).<br>
        Ô <strong>Nội dung</strong> dùng trình soạn thảo có thể in đậm, in nghiêng, gạch chân, đổi màu chữ, chèn link, danh sách... (giống phần Cộng đồng).
    </p>
    <div id="sections-container-<?php echo esc_attr($meta_key); ?>">
        <?php
        if (!empty($sections)) {
            foreach ($sections as $i => $sec) {
                $h = isset($sec['heading']) ? $sec['heading'] : '';
                $c = isset($sec['content']) ? $sec['content'] : '';
                ?>
                <div class="section-row" style="margin-bottom:15px; padding:12px; border:1px solid #ddd; background:#fafafa;">
                    <label>Tiêu đề nhỏ <?php echo (int)($i+1); ?></label>
                    <input type="text" name="<?php echo esc_attr($meta_key); ?>[<?php echo (int)$i; ?>][heading]" value="<?php echo esc_attr($h); ?>" class="widefat" style="margin-bottom:8px;">
                    <label>Nội dung <?php echo (int)($i+1); ?></label>
                    <?php
                    $editor_id = 'sections_editor_' . $post->ID . '_' . (int) $i;
                    wp_editor($c, $editor_id, array_merge(
                        dnttvn_get_content_section_editor_settings($meta_key . '[' . (int) $i . '][content]', $editor_id),
                        array('textarea_rows' => 6, 'editor_class' => 'sections-content-editor')
                    ));
                    ?>
                    <button type="button" class="button remove-section" style="margin-top:8px;">Xóa mục</button>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <p><button type="button" class="button" id="add-section-<?php echo esc_attr($meta_key); ?>">+ Thêm mục</button></p>
    <script>
    (function(){
        var metaKey = <?php echo json_encode($meta_key); ?>;
        var container = document.getElementById('sections-container-' + metaKey);
        var addBtn = document.getElementById('add-section-' + metaKey);
        if (!container || !addBtn) return;
        var index = container.querySelectorAll('.section-row').length;
        var sectionEditorPostId = <?php echo json_encode($post->ID); ?>;
        var sectionEditorBaseSettings = <?php echo json_encode(dnttvn_get_content_section_editor_settings_for_js()); ?>;
        addBtn.onclick = function() {
            var editorId = 'sections_editor_' + sectionEditorPostId + '_' + index;
            var div = document.createElement('div');
            div.className = 'section-row';
            div.style.cssText = 'margin-bottom:15px; padding:12px; border:1px solid #ddd; background:#fafafa;';
            div.innerHTML = '<label>Tiêu đề nhỏ ' + (index + 1) + '</label>' +
                '<input type="text" name="' + metaKey + '[' + index + '][heading]" value="" class="widefat" style="margin-bottom:8px;">' +
                '<label>Nội dung ' + (index + 1) + '</label>' +
                '<textarea id="' + editorId + '" name="' + metaKey + '[' + index + '][content]" class="widefat sections-content-textarea" rows="6"></textarea>' +
                '<button type="button" class="button remove-section" style="margin-top:8px;">Xóa mục</button>';
            div.querySelector('.remove-section').onclick = function() {
                if (typeof wp !== 'undefined' && wp.editor && wp.editor.remove) wp.editor.remove(editorId);
                div.remove();
            };
            container.appendChild(div);
            if (typeof wp !== 'undefined' && wp.editor && wp.editor.initialize) {
                var settings = Object.assign({}, sectionEditorBaseSettings, { textarea_name: metaKey + '[' + index + '][content]' });
                wp.editor.initialize(editorId, settings);
            }
            index++;
        };
        container.querySelectorAll('.remove-section').forEach(function(btn) {
            btn.onclick = function() { this.closest('.section-row').remove(); };
        });
    })();
    </script>
    <?php
}
function dnttvn_save_sections_meta($post_id, $meta_key) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['dnttvn_sections_nonce']) || !wp_verify_nonce($_POST['dnttvn_sections_nonce'], 'dnttvn_save_sections')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST[$meta_key]) || !is_array($_POST[$meta_key])) return;
    $sections = array();
    foreach ($_POST[$meta_key] as $row) {
        $h = isset($row['heading']) ? sanitize_text_field($row['heading']) : '';
        $c = isset($row['content']) ? dnttvn_sanitize_content_with_tables($row['content']) : '';
        $sections[] = array('heading' => $h, 'content' => $c);
    }
    update_post_meta($post_id, $meta_key, $sections);
}
function dnttvn_save_quy_trinh_sections($post_id) {
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, array('quy_trinh', 'gia_tri_thanh_vien', 'phung_doanh_nhan', 'phung_su_con_dn', 'nghia_vu_thanh_vien'), true)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['dnttvn_sections_nonce']) || !wp_verify_nonce($_POST['dnttvn_sections_nonce'], 'dnttvn_save_sections')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['_quy_trinh_sections']) || !is_array($_POST['_quy_trinh_sections'])) return;
    $sections = array();
    foreach ($_POST['_quy_trinh_sections'] as $row) {
        $h = isset($row['heading']) ? sanitize_text_field($row['heading']) : '';
        $c = isset($row['content']) ? dnttvn_sanitize_content_with_tables($row['content']) : '';
        $img_id = isset($row['image_id']) ? absint($row['image_id']) : 0; // legacy
        $img_caption = isset($row['image_caption']) ? sanitize_text_field($row['image_caption']) : ''; // legacy

        $media_ids = array();
        $media_captions = array();
        if (isset($row['media_ids']) && is_array($row['media_ids'])) {
            foreach ($row['media_ids'] as $mid) {
                $mid = absint($mid);
                if ($mid <= 0 || !get_post($mid)) continue;
                $mime = get_post_mime_type($mid);
                $ok = (is_string($mime) && (strpos($mime, 'image') === 0 || strpos($mime, 'video') === 0 || $mime === 'application/pdf'));
                if (!$ok) continue;
                $media_ids[] = $mid;
            }
        }
        if (isset($row['media_captions']) && is_array($row['media_captions'])) {
            foreach ($row['media_captions'] as $cap) {
                $media_captions[] = sanitize_text_field($cap);
            }
        }
        // If only legacy image present, map into new arrays (for consistency)
        if (empty($media_ids) && $img_id > 0 && get_post($img_id)) {
            $mime = get_post_mime_type($img_id);
            $ok = (is_string($mime) && (strpos($mime, 'image') === 0 || strpos($mime, 'video') === 0 || $mime === 'application/pdf'));
            if ($ok) {
                $media_ids = array($img_id);
                $media_captions = array($img_caption);
            }
        }

        $sections[] = array(
            'heading' => $h,
            'content' => $c,
            'media_ids' => $media_ids,
            'media_captions' => $media_captions,
            'image_id' => $img_id,
            'image_caption' => $img_caption,
        );
    }
    update_post_meta($post_id, '_quy_trinh_sections', $sections);
}
add_action('add_meta_boxes', 'dnttvn_add_quy_trinh_meta_boxes');
add_action('save_post_quy_trinh', 'dnttvn_save_quy_trinh_sections');
add_action('save_post_gia_tri_thanh_vien', 'dnttvn_save_quy_trinh_sections');
add_action('save_post_phung_doanh_nhan', 'dnttvn_save_quy_trinh_sections');
add_action('save_post_phung_su_con_dn', 'dnttvn_save_quy_trinh_sections');
add_action('save_post_nghia_vu_thanh_vien', 'dnttvn_save_quy_trinh_sections');

// Xử lý gửi form đăng ký
function dnttvn_handle_dang_ky_form() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['dnttvn_dang_ky_nonce'])) {
        return;
    }
    if (!wp_verify_nonce(sanitize_text_field($_POST['dnttvn_dang_ky_nonce']), 'dnttvn_dang_ky_submit')) {
        return;
    }
    $ho_ten = isset($_POST['dang_ky_ho_ten']) ? sanitize_text_field($_POST['dang_ky_ho_ten']) : '';
    $khu_vuc = isset($_POST['dang_ky_khu_vuc']) ? absint($_POST['dang_ky_khu_vuc']) : 0;
    $ngay_sinh = isset($_POST['dang_ky_ngay_sinh']) ? sanitize_text_field($_POST['dang_ky_ngay_sinh']) : '';
    $ten_dn = isset($_POST['dang_ky_ten_dn']) ? sanitize_text_field($_POST['dang_ky_ten_dn']) : '';
    $nganh_nghe = isset($_POST['dang_ky_nganh_nghe']) ? sanitize_text_field($_POST['dang_ky_nganh_nghe']) : '';
    $nganh_nghe_khac = isset($_POST['dang_ky_nganh_nghe_khac']) ? sanitize_text_field($_POST['dang_ky_nganh_nghe_khac']) : '';
    $chuc_vu = isset($_POST['dang_ky_chuc_vu']) ? sanitize_text_field($_POST['dang_ky_chuc_vu']) : '';
    $sdt = isset($_POST['dang_ky_sdt']) ? sanitize_text_field($_POST['dang_ky_sdt']) : '';
    $dia_chi = isset($_POST['dang_ky_dia_chi']) ? sanitize_text_field($_POST['dang_ky_dia_chi']) : '';
    $email = isset($_POST['dang_ky_email']) ? sanitize_email($_POST['dang_ky_email']) : '';
    $hon_nhan = isset($_POST['dang_ky_hon_nhan']) ? sanitize_text_field($_POST['dang_ky_hon_nhan']) : '';
    $so_con = isset($_POST['dang_ky_so_con']) ? sanitize_text_field($_POST['dang_ky_so_con']) : '';
    $do_tuoi_con = isset($_POST['dang_ky_do_tuoi_con']) ? $_POST['dang_ky_do_tuoi_con'] : array();
    $muc_tieu = isset($_POST['dang_ky_muc_tieu']) ? $_POST['dang_ky_muc_tieu'] : array();
    $khoa_hoc = isset($_POST['dang_ky_khoa_hoc']) ? sanitize_text_field($_POST['dang_ky_khoa_hoc']) : '';
    $khoa_hoc_ten = isset($_POST['dang_ky_khoa_hoc_ten']) ? sanitize_text_field($_POST['dang_ky_khoa_hoc_ten']) : '';
    $nguon_tin = isset($_POST['dang_ky_nguon_tin']) ? $_POST['dang_ky_nguon_tin'] : array();
    $nguon_tin_khac = isset($_POST['dang_ky_nguon_tin_khac']) ? sanitize_text_field($_POST['dang_ky_nguon_tin_khac']) : '';
    $xac_nhan = isset($_POST['dang_ky_xac_nhan']) ? sanitize_text_field($_POST['dang_ky_xac_nhan']) : '';

    // Validate fields on the backend
    if (empty($ho_ten) || $khu_vuc <= 0 || empty($ngay_sinh) || empty($ten_dn) || empty($nganh_nghe) || empty($chuc_vu) || empty($sdt) || empty($dia_chi) || empty($email) || empty($hon_nhan) || $so_con === '' || empty($muc_tieu) || empty($khoa_hoc) || empty($nguon_tin) || empty($xac_nhan)) {
        return;
    }
    
    // Check email format
    if (!is_email($email)) {
        return;
    }

    // Check conditional require for "Other" industry
    if ($nganh_nghe === '__other__' && empty($nganh_nghe_khac)) {
        return;
    }
    
    // Check conditional require for children ages if so_con > 0
    if (intval($so_con) > 0 && empty($do_tuoi_con)) {
        return;
    }

    // Check conditional require for courses
    if ($khoa_hoc === 'Tôi đã từng tham gia' && empty($khoa_hoc_ten)) {
        return;
    }

    // Check conditional require for referral source
    if (is_array($nguon_tin) && in_array('Khác', $nguon_tin, true) && empty($nguon_tin_khac)) {
        return;
    }

    // Check if portrait file was uploaded
    if (empty($_FILES['dang_ky_portrait']['name']) || $_FILES['dang_ky_portrait']['error'] !== UPLOAD_ERR_OK) {
        return;
    }

    $title = $ho_ten . ' - ' . $sdt . ' - ' . current_time('Y-m-d H:i');
    $post_id = wp_insert_post(array(
        'post_type'   => 'dang_ky',
        'post_title'  => $title,
        'post_status' => 'publish',
        'post_author' => 1,
    ));
    if (is_wp_error($post_id)) {
        return;
    }
    update_post_meta($post_id, '_dang_ky_status', 'pending');
    // Ngành nghề: nếu chọn "Khác (ghi rõ)" thì lấy giá trị ô nhập và lưu vào danh sách tùy chọn
    $nganh_nghe_value = '';
    if (isset($_POST['dang_ky_nganh_nghe'])) {
        $nganh_raw = sanitize_text_field($_POST['dang_ky_nganh_nghe']);
        if ($nganh_raw === '__other__' && isset($_POST['dang_ky_nganh_nghe_khac'])) {
            $nganh_nghe_value = sanitize_text_field($_POST['dang_ky_nganh_nghe_khac']);
            if ($nganh_nghe_value !== '') {
                $opts = function_exists('dnttvn_get_nganh_nghe_options') ? dnttvn_get_nganh_nghe_options() : array('Sản xuất', 'Dịch vụ', 'Thương mại', 'Công nghệ', 'Bất động sản', 'Tài chính', 'Giáo dục - Đào tạo', 'F&B - Ẩm thực', 'Y tế - Dược', 'Xây dựng', 'Nông nghiệp', 'Logistics');
                if (!in_array($nganh_nghe_value, $opts, true)) {
                    $opts[] = $nganh_nghe_value;
                    update_option('dnttvn_nganh_nghe_options', array_values(array_unique(array_filter(array_map('trim', $opts)))));
                }
            }
        } else {
            $nganh_nghe_value = $nganh_raw;
        }
        update_post_meta($post_id, '_dang_ky_nganh_nghe', $nganh_nghe_value);
    }
    $fields = array(
        'dang_ky_ho_ten', 'dang_ky_ngay_sinh', 'dang_ky_ten_dn',
        'dang_ky_chuc_vu', 'dang_ky_sdt', 'dang_ky_hon_nhan', 'dang_ky_so_con',
        'dang_ky_do_tuoi_con', 'dang_ky_muc_tieu', 'dang_ky_xac_nhan',
        'dang_ky_dia_chi', 'dang_ky_email', 'dang_ky_khoa_hoc', 'dang_ky_khoa_hoc_ten',
        'dang_ky_nguon_tin', 'dang_ky_nguon_tin_khac',
    );
    foreach ($fields as $key) {
        if (isset($_POST[$key])) {
            if (is_array($_POST[$key])) {
                update_post_meta($post_id, '_' . $key, array_map('sanitize_text_field', $_POST[$key]));
            } else {
                update_post_meta($post_id, '_' . $key, sanitize_text_field($_POST[$key]));
            }
        }
    }
    // Khu vực (Taxonomy)
    if (isset($_POST['dang_ky_khu_vuc'])) {
        $kv_id = absint($_POST['dang_ky_khu_vuc']);
        if ($kv_id > 0) {
            wp_set_object_terms($post_id, $kv_id, 'khu_vuc');
        }
    }

    // Ảnh chân dung (Portrait)
    if (!empty($_FILES['dang_ky_portrait']['name']) && $_FILES['dang_ky_portrait']['error'] === UPLOAD_ERR_OK) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        
        $file = $_FILES['dang_ky_portrait'];
        $allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
        if (in_array($file['type'], $allowed, true)) {
            $overrides = array('test_form' => false);
            $move = wp_handle_upload($file, $overrides);
            if (!empty($move['file'])) {
                $attachment = array(
                    'post_mime_type' => $move['type'],
                    'post_title'     => 'Chân dung - ' . $ho_ten,
                    'post_status'   => 'inherit',
                );
                $attach_id = wp_insert_attachment($attachment, $move['file'], $post_id);
                if (!is_wp_error($attach_id)) {
                    wp_generate_attachment_metadata($attach_id, $move['file']);
                    set_post_thumbnail($post_id, $attach_id);
                    update_post_meta($post_id, '_dang_ky_portrait_id', $attach_id);
                }
            }
        }
    }

    wp_safe_redirect(add_query_arg('submitted', '1', wp_get_referer() ? wp_get_referer() : home_url('/dang-ky/')));
    exit;
}
add_action('template_redirect', 'dnttvn_handle_dang_ky_form');

// Tự động đồng bộ sang CPT 'doanh_nhan' khi Duyệt đơn đăng ký
function dnttvn_sync_dang_ky_to_doanh_nhan($post_id) {
    if (get_post_type($post_id) !== 'dang_ky') return;
    
    $status = get_post_meta($post_id, '_dang_ky_status', true);
    if ($status !== 'approved') return;

    $ho_ten = get_post_meta($post_id, '_dang_ky_ho_ten', true);
    $chuc_vu = get_post_meta($post_id, '_dang_ky_chuc_vu', true);
    $ten_dn = get_post_meta($post_id, '_dang_ky_ten_dn', true);
    $portrait_id = get_post_meta($post_id, '_dang_ky_portrait_id', true);
    $muc_tieu = get_post_meta($post_id, '_dang_ky_muc_tieu', true);
    $muc_tieu = is_string($muc_tieu) ? $muc_tieu : '';
    $nganh_text = get_post_meta($post_id, '_dang_ky_nganh_nghe', true);
    $nganh_text = is_string($nganh_text) ? sanitize_text_field($nganh_text) : '';

    // Kiểm tra xem đã có bài doanh nhân tương ứng chưa (dựa trên meta '_dang_ky_id')
    $existing = get_posts(array(
        'post_type' => 'doanh_nhan',
        'meta_key' => '_dang_ky_id',
        'meta_value' => $post_id,
        'posts_per_page' => 1
    ));

    $dn_data = array(
        'post_type' => 'doanh_nhan',
        'post_title' => $ho_ten,
        'post_status' => 'publish',
    );
    if (!$existing) {
        $dn_data['post_excerpt'] = wp_trim_words(strip_tags($muc_tieu), 55, '…');
        $dn_data['post_content'] = $muc_tieu;
    }

    if ($existing) {
        $dn_id = $existing[0]->ID;
        $dn_data['ID'] = $dn_id;
        wp_update_post($dn_data);
    } else {
        $dn_id = wp_insert_post($dn_data);
        update_post_meta($dn_id, '_dang_ky_id', $post_id);
    }

    if (!is_wp_error($dn_id)) {
        update_post_meta($dn_id, '_doanh_nhan_chuc_vu', $chuc_vu);
        update_post_meta($dn_id, '_doanh_nhan_cong_ty', $ten_dn);
        if ($portrait_id) {
            set_post_thumbnail($dn_id, $portrait_id);
        }

        // Co-sync taxonomy Khu vực
        $kv_terms = wp_get_object_terms($post_id, 'khu_vuc', array('fields' => 'ids'));
        if (!empty($kv_terms) && !is_wp_error($kv_terms)) {
            wp_set_object_terms($dn_id, $kv_terms, 'khu_vuc');
        }

        // Ngành: từ đơn đăng ký → taxonomy nganh_nghe (tạo term nếu chưa có)
        if ($nganh_text !== '' && taxonomy_exists('nganh_nghe')) {
            $t = term_exists($nganh_text, 'nganh_nghe');
            if ($t) {
                $tid = is_array($t) ? (int) $t['term_id'] : (int) $t;
            } else {
                $ins = wp_insert_term($nganh_text, 'nganh_nghe');
                $tid = is_wp_error($ins) ? 0 : (int) $ins['term_id'];
            }
            if ($tid > 0) {
                wp_set_object_terms($dn_id, array($tid), 'nganh_nghe');
            }
        }
    }
}
add_action('save_post_dang_ky', 'dnttvn_sync_dang_ky_to_doanh_nhan', 20);

// Xử lý form Đăng ký Chương trình Hướng nghiệp & Khai mở Trí tuệ
function dnttvn_handle_dang_ky_huong_nghiep_form() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['dnttvn_hn_nonce'])) {
        return;
    }
    if (!wp_verify_nonce(sanitize_text_field($_POST['dnttvn_hn_nonce']), 'dnttvn_dang_ky_huong_nghiep_submit')) {
        return;
    }
    $ho_ten_phu_huynh = isset($_POST['hn_ho_ten_phu_huynh']) ? sanitize_text_field($_POST['hn_ho_ten_phu_huynh']) : '';
    $ho_ten_con = isset($_POST['hn_ho_ten_con']) ? sanitize_text_field($_POST['hn_ho_ten_con']) : '';
    if (empty($ho_ten_phu_huynh) || empty($ho_ten_con)) {
        return;
    }
    $title = $ho_ten_phu_huynh . ' - ' . $ho_ten_con . ' - ' . current_time('Y-m-d H:i');
    $post_id = wp_insert_post(array(
        'post_type'   => 'dang_ky_huong_nghiep',
        'post_title'  => $title,
        'post_status' => 'publish',
        'post_author' => 1,
    ));
    if (is_wp_error($post_id)) {
        return;
    }
    $text_fields = array(
        'hn_ho_ten_phu_huynh', 'hn_ma_so_thanh_vien', 'hn_chi_hoi', 'hn_ten_doanh_nghiep', 'hn_sdt_zalo',
        'hn_ho_ten_con', 'hn_ngay_sinh_con', 'hn_gioi_tinh_con', 'hn_lop', 'hn_truong', 'hn_chuong_trinh',
    );
    $textarea_fields = array('hn_tinh_cach_con', 'hn_so_thich_nang_khieu', 'hn_dinh_huong_gia_dinh', 'hn_van_de_lon_nhat', 'hn_mong_muon_sau_khoa_hoc');
    foreach ($text_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, '_' . $key, sanitize_text_field($_POST[$key]));
        }
    }
    foreach ($textarea_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, '_' . $key, sanitize_textarea_field($_POST[$key]));
        }
    }
    update_post_meta($post_id, '_hn_cam_ket_dong_hanh', !empty($_POST['hn_cam_ket_dong_hanh']) ? '1' : '');
    update_post_meta($post_id, '_hn_cam_ket_ton_trong', !empty($_POST['hn_cam_ket_ton_trong']) ? '1' : '');
    if (!empty($_FILES['hn_chu_ky']['name']) && $_FILES['hn_chu_ky']['error'] === UPLOAD_ERR_OK) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $file = $_FILES['hn_chu_ky'];
        $allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
        if (in_array($file['type'], $allowed, true)) {
            $overrides = array('test_form' => false);
            $move = wp_handle_upload($file, $overrides);
            if (!empty($move['file'])) {
                $attachment = array(
                    'post_mime_type' => $move['type'],
                    'post_title'     => 'Chữ ký - ' . $ho_ten_phu_huynh,
                    'post_status'   => 'inherit',
                );
                $attach_id = wp_insert_attachment($attachment, $move['file'], $post_id);
                if (!is_wp_error($attach_id)) {
                    wp_generate_attachment_metadata($attach_id, $move['file']);
                    update_post_meta($post_id, '_hn_chu_ky_id', $attach_id);
                }
            }
        }
    }
    $redirect_url = wp_get_referer() ? wp_get_referer() : home_url('/dang-ky-huong-nghiep/');
    wp_safe_redirect(add_query_arg('submitted', '1', $redirect_url));
    exit;
}
add_action('template_redirect', 'dnttvn_handle_dang_ky_huong_nghiep_form');

// Meta box hiển thị phiếu đăng ký Hướng nghiệp trong admin
function dnttvn_add_dang_ky_huong_nghiep_meta_box() {
    add_meta_box(
        'dang_ky_huong_nghiep_data',
        'Nội dung phiếu đăng ký',
        'dnttvn_dang_ky_huong_nghiep_meta_box_callback',
        'dang_ky_huong_nghiep',
        'normal',
        'high'
    );
}
function dnttvn_dang_ky_huong_nghiep_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_hn_approved', 'dnttvn_hn_approved_nonce');
    $approved = get_post_meta($post->ID, '_hn_approved', true);
    echo '<p style="margin-bottom:12px;"><label><input type="checkbox" name="hn_approved" value="1" ' . ($approved === '1' ? ' checked="checked"' : '') . '> <strong>Đã duyệt</strong> (con nhân viên đã duyệt)</label></p>';
    echo '<hr style="margin:12px 0;">';
    $labels = array(
        'hn_ho_ten_phu_huynh' => 'Họ tên Cha/Mẹ',
        'hn_ma_so_thanh_vien' => 'Mã số Thành viên',
        'hn_chi_hoi' => 'Chi hội',
        'hn_ten_doanh_nghiep' => 'Tên doanh nghiệp',
        'hn_sdt_zalo' => 'SĐT (Zalo)',
        'hn_ho_ten_con' => 'Họ tên con',
        'hn_ngay_sinh_con' => 'Ngày sinh con',
        'hn_gioi_tinh_con' => 'Giới tính',
        'hn_lop' => 'Lớp',
        'hn_truong' => 'Trường',
        'hn_tinh_cach_con' => 'Tính cách đặc trưng',
        'hn_so_thich_nang_khieu' => 'Sở thích / năng khiếu',
        'hn_chuong_trinh' => 'Chương trình đăng ký',
        'hn_dinh_huong_gia_dinh' => 'Định hướng gia đình',
        'hn_van_de_lon_nhat' => 'Vấn đề lớn nhất',
        'hn_mong_muon_sau_khoa_hoc' => 'Mong muốn sau khóa học',
        'hn_cam_ket_dong_hanh' => 'Cam kết đồng hành',
        'hn_cam_ket_ton_trong' => 'Cam kết tôn trọng lộ trình',
    );
    $chuong_trinh_opts = function_exists('dnttvn_get_huong_nghiep_chuong_trinh_options') ? dnttvn_get_huong_nghiep_chuong_trinh_options() : array();
    echo '<table class="widefat striped" style="margin-top:8px;">';
    foreach ($labels as $key => $label) {
        $val = get_post_meta($post->ID, '_' . $key, true);
        if ($val === '' && !in_array($key, array('hn_cam_ket_dong_hanh', 'hn_cam_ket_ton_trong'), true)) continue;
        if (in_array($key, array('hn_cam_ket_dong_hanh', 'hn_cam_ket_ton_trong'), true)) {
            $val = ($val === '1') ? 'Có' : '';
            if ($val === '') continue;
        }
        if ($key === 'hn_chuong_trinh' && $val !== '' && !empty($chuong_trinh_opts)) {
            $idx = (int) $val - 1;
            if (isset($chuong_trinh_opts[$idx])) {
                $item = $chuong_trinh_opts[$idx];
                $val = $item['title'];
                if (!empty($item['subtitle'])) {
                    $val .= ' (' . $item['subtitle'] . ')';
                }
            }
        }
        echo '<tr><th style="width:200px;">' . esc_html($label) . '</th><td>' . nl2br(esc_html($val)) . '</td></tr>';
    }
    $chu_ky_id = get_post_meta($post->ID, '_hn_chu_ky_id', true);
    if ($chu_ky_id && get_post($chu_ky_id)) {
        $url = wp_get_attachment_image_url($chu_ky_id, 'medium');
        if ($url) {
            echo '<tr><th>Chữ ký phụ huynh</th><td><img src="' . esc_url($url) . '" alt="Chữ ký" style="max-width:200px; height:auto;"></td></tr>';
        }
    }
    echo '</table>';
}
add_action('add_meta_boxes', 'dnttvn_add_dang_ky_huong_nghiep_meta_box');

// Lưu trạng thái duyệt phiếu đăng ký Hướng nghiệp
function dnttvn_save_dang_ky_huong_nghiep_approved($post_id) {
    if (!isset($_POST['dnttvn_hn_approved_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['dnttvn_hn_approved_nonce']), 'dnttvn_save_hn_approved')) {
        return;
    }
    if (get_post_type($post_id) !== 'dang_ky_huong_nghiep' || !current_user_can('edit_post', $post_id)) {
        return;
    }
    update_post_meta($post_id, '_hn_approved', !empty($_POST['hn_approved']) ? '1' : '');
}
add_action('save_post_dang_ky_huong_nghiep', 'dnttvn_save_dang_ky_huong_nghiep_approved');

// Cột và hành động cho danh sách Đăng ký Hướng nghiệp
function dnttvn_dang_ky_huong_nghiep_columns($columns) {
    $new = array();
    foreach ($columns as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['hn_approved'] = 'Duyệt';
        }
    }
    if (!isset($new['hn_approved'])) {
        $new['hn_approved'] = 'Duyệt';
    }
    return $new;
}
function dnttvn_dang_ky_huong_nghiep_column_content($column, $post_id) {
    if ($column !== 'hn_approved') {
        return;
    }
    $approved = get_post_meta($post_id, '_hn_approved', true);
    if ($approved === '1') {
        echo '<span style="color:green;">Đã duyệt</span>';
    } else {
        echo '<span style="color:#999;">Chưa duyệt</span>';
    }
}
function dnttvn_dang_ky_huong_nghiep_row_actions($actions, $post) {
    if ($post->post_type !== 'dang_ky_huong_nghiep') {
        return $actions;
    }
    $approved = get_post_meta($post->ID, '_hn_approved', true);
    $toggle = $approved === '1' ? 'bỏ_duyet' : 'duyet';
    $label  = $approved === '1' ? 'Bỏ duyệt' : 'Duyệt';
    $url = wp_nonce_url(admin_url('admin-post.php?action=dnttvn_toggle_hn_approved&post_id=' . $post->ID . '&toggle=' . $toggle), 'dnttvn_toggle_hn_' . $post->ID);
    $actions['hn_approve'] = '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    return $actions;
}
function dnttvn_handle_toggle_hn_approved() {
    if (!current_user_can('edit_posts')) {
        wp_die('Không có quyền.');
    }
    $post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
    $toggle = isset($_GET['toggle']) ? sanitize_text_field($_GET['toggle']) : '';
    if (!$post_id || get_post_type($post_id) !== 'dang_ky_huong_nghiep') {
        wp_safe_redirect(admin_url('edit.php?post_type=dang_ky_huong_nghiep'));
        exit;
    }
    check_admin_referer('dnttvn_toggle_hn_' . $post_id);
    if ($toggle === 'duyet') {
        update_post_meta($post_id, '_hn_approved', '1');
    } else {
        update_post_meta($post_id, '_hn_approved', '');
    }
    wp_safe_redirect(admin_url('edit.php?post_type=dang_ky_huong_nghiep'));
    exit;
}
add_filter('manage_dang_ky_huong_nghiep_posts_columns', 'dnttvn_dang_ky_huong_nghiep_columns');
add_action('manage_dang_ky_huong_nghiep_posts_custom_column', 'dnttvn_dang_ky_huong_nghiep_column_content', 10, 2);
add_filter('post_row_actions', 'dnttvn_dang_ky_huong_nghiep_row_actions', 10, 2);
add_action('admin_post_dnttvn_toggle_hn_approved', 'dnttvn_handle_toggle_hn_approved');

/**
 * Nội dung đăng ký Hướng nghiệp: Chọn 01 chương trình phù hợp với độ tuổi.
 * Lưu tại option `dnttvn_huong_nghiep_chuong_trinh` (array of title, subtitle, description).
 */
function dnttvn_get_huong_nghiep_chuong_trinh_options() {
    $default = array(
        array(
            'title'       => 'Chương trình 1: ĐÁNH THỨC ƯỚC MƠ',
            'subtitle'    => 'Dành cho THCS',
            'description' => 'Mục tiêu: Giúp con xác định đam mê, xây dựng mục tiêu cá nhân và thói quen chủ động.',
        ),
        array(
            'title'       => 'Chương trình 2: CÔNG TY CỦA EM',
            'subtitle'    => 'Dành cho THPT',
            'description' => 'Mục tiêu: Nhận thức về giá trị đồng tiền, lòng biết ơn gia đình và tư duy quản lý tài chính sớm.',
        ),
        array(
            'title'       => 'Chương trình 3: DỰ ÁN KHỞI NGHIỆP - BABY SHARK',
            'subtitle'    => 'Sau THPT',
            'description' => 'Mục tiêu: Khai mở tư duy kinh doanh thực chiến, kỹ năng gọi vốn và quản trị dự án.',
        ),
    );
    $opts = get_option('dnttvn_huong_nghiep_chuong_trinh', array());
    if (!is_array($opts) || empty($opts)) {
        return $default;
    }
    return array_values(array_filter(array_map(function ($item) {
        if (!is_array($item)) {
            return null;
        }
        return array(
            'title'       => isset($item['title']) ? trim((string) $item['title']) : '',
            'subtitle'    => isset($item['subtitle']) ? trim((string) $item['subtitle']) : '',
            'description' => isset($item['description']) ? trim((string) $item['description']) : '',
        );
    }, $opts)));
}

/**
 * Danh sách ngành nghề cho form Đăng ký.
 * Lưu tại option `dnttvn_nganh_nghe_options` (array), có migrate từ `dnttvn_nganh_nghe_custom` nếu còn.
 */
function dnttvn_get_nganh_nghe_options() {
    $default = array('Sản xuất', 'Dịch vụ', 'Thương mại', 'Công nghệ', 'Bất động sản', 'Tài chính', 'Giáo dục - Đào tạo', 'F&B - Ẩm thực', 'Y tế - Dược', 'Xây dựng', 'Nông nghiệp', 'Logistics');
    $opts = get_option('dnttvn_nganh_nghe_options', array());
    if (!is_array($opts)) $opts = array();
    $opts = array_values(array_filter(array_map('trim', $opts)));

    // Migrate legacy custom list once (keep compatibility)
    $legacy = get_option('dnttvn_nganh_nghe_custom', array());
    if (is_array($legacy) && !empty($legacy)) {
        $legacy = array_values(array_filter(array_map('trim', $legacy)));
        $merged = array_values(array_unique(array_filter(array_merge($default, $opts, $legacy))));
        // Save merged into new option if changed
        if ($merged !== $opts) {
            update_option('dnttvn_nganh_nghe_options', $merged);
        }
        return $merged;
    }

    if (empty($opts)) return $default;
    return array_values(array_unique(array_filter(array_merge($default, $opts))));
}

// Meta box hiển thị dữ liệu đơn đăng ký trong admin
function dnttvn_add_dang_ky_meta_box() {
    add_meta_box(
        'dang_ky_data',
        'Thông tin đăng ký',
        'dnttvn_dang_ky_meta_box_callback',
        'dang_ky',
        'normal',
        'high'
    );
    add_meta_box(
        'dang_ky_duyet',
        'Duyệt đơn',
        'dnttvn_dang_ky_duyet_meta_box_callback',
        'dang_ky',
        'side',
        'high'
    );
}
function dnttvn_dang_ky_meta_box_callback($post) {
    $labels = array(
        'dang_ky_ho_ten'          => 'Họ và tên',
        'dang_ky_ngay_sinh'       => 'Ngày sinh',
        'dang_ky_ten_dn'          => 'Tên doanh nghiệp',
        'dang_ky_nganh_nghe'      => 'Ngành nghề',
        'dang_ky_chuc_vu'         => 'Chức vụ',
        'dang_ky_sdt'             => 'SĐT / Zalo',
        'dang_ky_dia_chi'         => 'Địa chỉ',
        'dang_ky_email'           => 'Email',
        'dang_ky_hon_nhan'        => 'Tình trạng hôn nhân',
        'dang_ky_so_con'          => 'Số lượng con',
        'dang_ky_do_tuoi_con'     => 'Độ tuổi các con',
        'dang_ky_muc_tieu'        => 'Mục tiêu gia nhập',
        'dang_ky_khoa_hoc'        => 'Khóa học thầy Ngô Minh Tuấn',
        'dang_ky_khoa_hoc_ten'    => 'Chi tiết khóa học',
        'dang_ky_nguon_tin'       => 'Biết đến cộng đồng qua đâu',
        'dang_ky_nguon_tin_khac'  => 'Chi tiết nguồn khác',
        'dang_ky_xac_nhan'        => 'Xác nhận cam kết',
    );
    echo '<table class="widefat striped" style="margin-top:8px;">';
    foreach ($labels as $key => $label) {
        $val = get_post_meta($post->ID, '_' . $key, true);
        if ($val === '') continue;
        if (is_array($val)) $val = implode(', ', $val);
        echo '<tr><th style="width:180px;">' . esc_html($label) . '</th><td>' . esc_html($val) . '</td></tr>';
    }
    // Hiển thị Khu vực (Taxonomy)
    $kv_terms = get_the_terms($post->ID, 'khu_vuc');
    if ($kv_terms && !is_wp_error($kv_terms)) {
        $kv_names = wp_list_pluck($kv_terms, 'name');
        echo '<tr><th>Khu vực</th><td>' . esc_html(implode(', ', $kv_names)) . '</td></tr>';
    }
    // Hiển thị Ảnh chân dung
    if (has_post_thumbnail($post->ID)) {
        echo '<tr><th>Ảnh chân dung</th><td>' . get_the_post_thumbnail($post->ID, 'thumbnail') . '</td></tr>';
    }
    echo '</table>';
}
function dnttvn_dang_ky_duyet_meta_box_callback($post) {
    $status = get_post_meta($post->ID, '_dang_ky_status', true);
    if ($status === '') $status = 'pending';
    $ly_do = get_post_meta($post->ID, '_dang_ky_ly_do', true);
    wp_nonce_field('dnttvn_dang_ky_duyet_save', 'dnttvn_dang_ky_duyet_nonce');
    echo '<p><strong>Trạng thái hiện tại:</strong> ';
    if ($status === 'approved') echo '<span style="color:green;">Đã duyệt</span>';
    elseif ($status === 'rejected') echo '<span style="color:red;">Từ chối</span>';
    else echo '<span style="color:#d63638;">Chờ duyệt</span>';
    echo '</p>';
    echo '<p><label for="dang_ky_status">Cập nhật trạng thái:</label></p>';
    echo '<select name="dang_ky_status" id="dang_ky_status" style="width:100%;">';
    echo '<option value="pending"' . selected($status, 'pending', false) . '>Chờ duyệt</option>';
    echo '<option value="approved"' . selected($status, 'approved', false) . '>Đã duyệt</option>';
    echo '<option value="rejected"' . selected($status, 'rejected', false) . '>Từ chối</option>';
    echo '</select>';
    echo '<p><label for="dang_ky_ly_do">Lý do (bắt buộc khi Từ chối, có thể ghi khi Duyệt):</label></p>';
    echo '<textarea name="dang_ky_ly_do" id="dang_ky_ly_do" rows="4" style="width:100%;">' . esc_textarea($ly_do) . '</textarea>';
    echo '<p class="description">Sau khi chọn trạng thái và nhập lý do (nếu có), bấm <strong>Cập nhật</strong> hoặc <strong>Đăng</strong> để lưu.</p>';
}
function dnttvn_save_dang_ky_duyet($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['dnttvn_dang_ky_duyet_nonce']) || !wp_verify_nonce($_POST['dnttvn_dang_ky_duyet_nonce'], 'dnttvn_dang_ky_duyet_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'dang_ky') return;
    if (isset($_POST['dang_ky_status'])) {
        $status = sanitize_text_field($_POST['dang_ky_status']);
        if (in_array($status, array('pending', 'approved', 'rejected'), true)) {
            update_post_meta($post_id, '_dang_ky_status', $status);
        }
    }
    if (isset($_POST['dang_ky_ly_do'])) {
        update_post_meta($post_id, '_dang_ky_ly_do', sanitize_textarea_field($_POST['dang_ky_ly_do']));
    }
}
add_action('save_post_dang_ky', 'dnttvn_save_dang_ky_duyet');
add_action('add_meta_boxes', 'dnttvn_add_dang_ky_meta_box');

// Cột danh sách Đơn đăng ký: Trạng thái
function dnttvn_dang_ky_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['dang_ky_status'] = 'Trạng thái';
    $new_columns['dang_ky_ly_do'] = 'Lý do';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
function dnttvn_dang_ky_column_content($column, $post_id) {
    if ($column === 'dang_ky_status') {
        $status = get_post_meta($post_id, '_dang_ky_status', true);
        if ($status === '') $status = 'pending';
        if ($status === 'approved') echo '<span style="color:green;">Đã duyệt</span>';
        elseif ($status === 'rejected') echo '<span style="color:red;">Từ chối</span>';
        else echo '<span style="color:#d63638;">Chờ duyệt</span>';
    }
    if ($column === 'dang_ky_ly_do') {
        $ly_do = get_post_meta($post_id, '_dang_ky_ly_do', true);
        echo $ly_do ? esc_html(wp_trim_words($ly_do, 12)) : '—';
    }
}
add_filter('manage_dang_ky_posts_columns', 'dnttvn_dang_ky_columns');
add_action('manage_dang_ky_posts_custom_column', 'dnttvn_dang_ky_column_content', 10, 2);

// Nút xuất danh sách đăng ký ra Excel (CSV) trên màn hình danh sách Đăng ký
function dnttvn_dang_ky_export_button($which) {
    if ($which !== 'top') return;
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    $post_type = $screen && isset($screen->post_type) ? $screen->post_type : (isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'post');
    if ($post_type !== 'dang_ky') return;
    $url = wp_nonce_url(admin_url('edit.php?post_type=dang_ky&action=dnttvn_export_dang_ky_csv'), 'dnttvn_export_dang_ky_csv');
    echo '<a href="' . esc_url($url) . '" class="page-title-action" style="margin-left:8px;">Xuất Excel (CSV)</a>';
}
add_action('manage_posts_extra_tablenav', 'dnttvn_dang_ky_export_button', 10, 1);

// Xử lý xuất CSV khi bấm nút
function dnttvn_export_dang_ky_csv() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'dnttvn_export_dang_ky_csv') return;
    if (!current_user_can('edit_posts')) wp_die('Bạn không có quyền.');
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'dnttvn_export_dang_ky_csv')) wp_die('Nonce không hợp lệ.');
    $posts = get_posts(array(
        'post_type'      => 'dang_ky',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    $labels = array(
        'Ngày đăng ký',
        'Họ và tên',
        'Khu vực',
        'Ngày sinh',
        'Tên doanh nghiệp',
        'Ngành nghề',
        'Chức vụ',
        'SĐT / Zalo',
        'Địa chỉ',
        'Email',
        'Ảnh chân dung (URL)',
        'Tình trạng hôn nhân',
        'Số lượng con',
        'Độ tuổi các con',
        'Mục tiêu gia nhập',
        'Khóa học thầy Ngô Minh Tuấn',
        'Chi tiết khóa học',
        'Biết đến cộng đồng qua đâu',
        'Chi tiết nguồn khác',
        'Xác nhận cam kết',
        'Trạng thái',
        'Lý do',
    );
    $filename = 'danh-sach-dang-ky-' . date('Y-m-d-His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($out, $labels);
    foreach ($posts as $post) {
        $row = array(get_the_date('d/m/Y H:i', $post));
        
        // Họ và tên
        $row[] = get_post_meta($post->ID, '_dang_ky_ho_ten', true);
        
        // Khu vực (Taxonomy)
        $kv_terms = get_the_terms($post->ID, 'khu_vuc');
        if ($kv_terms && !is_wp_error($kv_terms)) {
            $kv_names = wp_list_pluck($kv_terms, 'name');
            $row[] = implode(', ', $kv_names);
        } else {
            $row[] = '';
        }
        
        // Ngày sinh
        $row[] = get_post_meta($post->ID, '_dang_ky_ngay_sinh', true);
        
        // Tên doanh nghiệp
        $row[] = get_post_meta($post->ID, '_dang_ky_ten_dn', true);
        
        // Ngành nghề
        $row[] = get_post_meta($post->ID, '_dang_ky_nganh_nghe', true);
        
        // Chức vụ
        $row[] = get_post_meta($post->ID, '_dang_ky_chuc_vu', true);
        
        // SĐT / Zalo
        $row[] = get_post_meta($post->ID, '_dang_ky_sdt', true);

        // Địa chỉ
        $row[] = get_post_meta($post->ID, '_dang_ky_dia_chi', true);

        // Email
        $row[] = get_post_meta($post->ID, '_dang_ky_email', true);
        
        // Ảnh chân dung (URL)
        $portrait_id = get_post_meta($post->ID, '_dang_ky_portrait_id', true);
        $portrait_url = '';
        if ($portrait_id) {
            $portrait_url = wp_get_attachment_url($portrait_id);
        } else {
            $portrait_url = get_the_post_thumbnail_url($post->ID, 'full');
        }
        $row[] = $portrait_url ? $portrait_url : '';
        
        // Tình trạng hôn nhân
        $row[] = get_post_meta($post->ID, '_dang_ky_hon_nhan', true);
        
        // Số lượng con
        $row[] = get_post_meta($post->ID, '_dang_ky_so_con', true);
        
        // Độ tuổi các con
        $do_tuoi = get_post_meta($post->ID, '_dang_ky_do_tuoi_con', true);
        if (is_array($do_tuoi)) {
            $row[] = implode(', ', $do_tuoi);
        } else {
            $row[] = $do_tuoi === '' ? '' : $do_tuoi;
        }
        
        // Mục tiêu gia nhập
        $muc_tieu = get_post_meta($post->ID, '_dang_ky_muc_tieu', true);
        if (is_array($muc_tieu)) {
            $row[] = implode(', ', $muc_tieu);
        } else {
            $row[] = $muc_tieu === '' ? '' : $muc_tieu;
        }

        // Khóa học thầy Ngô Minh Tuấn
        $row[] = get_post_meta($post->ID, '_dang_ky_khoa_hoc', true);

        // Chi tiết khóa học
        $row[] = get_post_meta($post->ID, '_dang_ky_khoa_hoc_ten', true);

        // Biết đến cộng đồng qua đâu
        $nguon_tin = get_post_meta($post->ID, '_dang_ky_nguon_tin', true);
        if (is_array($nguon_tin)) {
            $row[] = implode(', ', $nguon_tin);
        } else {
            $row[] = $nguon_tin === '' ? '' : $nguon_tin;
        }

        // Chi tiết nguồn khác
        $row[] = get_post_meta($post->ID, '_dang_ky_nguon_tin_khac', true);
        
        // Xác nhận cam kết
        $row[] = get_post_meta($post->ID, '_dang_ky_xac_nhan', true);
        
        // Trạng thái (Việt hóa)
        $status = get_post_meta($post->ID, '_dang_ky_status', true);
        if ($status === 'approved') {
            $row[] = 'Đã duyệt';
        } elseif ($status === 'rejected') {
            $row[] = 'Từ chối';
        } else {
            $row[] = 'Chờ duyệt';
        }
        
        // Lý do
        $row[] = get_post_meta($post->ID, '_dang_ky_ly_do', true);
        
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}
add_action('admin_init', 'dnttvn_export_dang_ky_csv');

// Add custom rewrite rules for cong_dong to use ID instead of slug
function dnttvn_add_cong_dong_rewrite_rules() {
    // Redirect cong_dong/ID to cong-dong?post_id=ID
    $cong_dong_page = get_page_by_path('cong-dong');
    if ($cong_dong_page) {
        add_rewrite_rule(
            '^cong_dong/([0-9]+)/?$',
            'index.php?page_id=' . $cong_dong_page->ID . '&post_id=$matches[1]',
            'top'
        );
    }
}
add_action('init', 'dnttvn_add_cong_dong_rewrite_rules');

// Modify the permalink for cong_dong: dẫn tới trang cong-dong/?post_id=ID
function dnttvn_cong_dong_permalink($permalink, $post) {
    if ($post->post_type === 'cong_dong') {
        // Always use page template URL
        $cong_dong_page = get_page_by_path('cong-dong');
        if ($cong_dong_page) {
            return get_permalink($cong_dong_page) . '?post_id=' . $post->ID;
        }
        // Fallback
        return home_url('/cong-dong/?post_id=' . $post->ID);
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

    add_meta_box(
        'cong_dong_excel_upload',
        '📊 Nội dung Excel (Bảng dữ liệu)',
        'dnttvn_excel_upload_meta_box_callback',
        'cong_dong',
        'normal',
        'default'
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
 * Thêm mục "Đăng ký doanh nghiệp" vào cuối menu vị trí primary.
 */
function dnttvn_append_dn_register_nav_link($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }
    if (strpos($items, 'menu-item-dang-ky-dn') !== false) {
        return $items;
    }
    $url   = dnttvn_get_dn_registration_page_url();
    $label = 'Đăng ký doanh nghiệp';
    $items .= '<li class="menu-item menu-item-dang-ky-dn"><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
    return $items;
}
add_filter('wp_nav_menu_items', 'dnttvn_append_dn_register_nav_link', 10, 2);

// Default menu fallback
function dnttvn_default_menu() {
    echo '<ul class="menu" id="mainMenu">';
    echo '<li><a href="' . esc_url(home_url()) . '">Trang chủ</a></li>';
    $tin_tuc_page = get_page_by_path('tin-tuc');
    if ($tin_tuc_page) {
        $tin_tuc_link = get_permalink($tin_tuc_page);
        echo '<li><a href="' . esc_url($tin_tuc_link) . '">Tin tức</a></li>';
    }
    $cong_dong_page = get_page_by_path('cong-dong');
    if ($cong_dong_page) {
        $cong_dong_link = get_permalink($cong_dong_page);
        echo '<li><a href="' . esc_url($cong_dong_link) . '">Cộng đồng</a></li>';
    }
    $dang_ky_url = home_url('/dang-ky/');
    $dang_ky_page = get_page_by_path('dang-ky');
    if ($dang_ky_page) {
        $dang_ky_url = get_permalink($dang_ky_page);
    }
    echo '<li><a href="' . esc_url($dang_ky_url) . '">Đăng ký</a></li>';
    echo '<li class="menu-item menu-item-dang-ky-dn"><a href="' . esc_url(dnttvn_get_dn_registration_page_url()) . '">Đăng ký doanh nghiệp</a></li>';
    echo '<li><a href="#">Liên hệ</a></li>';
    echo '</ul>';
}

// ============================================
// ADMIN MANAGEMENT FEATURES
// ============================================

// Add Admin Columns for Doanh nghiệp




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






// Enqueue Admin Scripts for Media Uploader & Structured Content
function dnttvn_enqueue_admin_scripts($hook) {
    global $post_type;
    
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        // Màn hình có mục nội dung (Giá trị nhận được, Quy trình, Phụng Doanh nhân, Phụng sự Con Doanh nhân, Nghĩa vụ thành viên): cần editor cho hàng thêm mới. Hỏi đáp không dùng.
        $sections_post_types = array('gia_tri_thanh_vien', 'quy_trinh', 'phung_doanh_nhan', 'phung_su_con_dn', 'nghia_vu_thanh_vien');
        if (in_array($post_type, $sections_post_types, true)) {
            wp_enqueue_editor();
            wp_enqueue_media();
        }
        // Quy trình gia nhập: cần media uploader cho hình ảnh từng mục
        if (in_array($post_type, array('quy_trinh', 'gia_tri_thanh_vien', 'phung_doanh_nhan', 'phung_su_con_dn', 'nghia_vu_thanh_vien'), true)) {
            wp_enqueue_media();
            wp_enqueue_script('jquery-ui-sortable');
        }
        // Sự kiện: cần media uploader cho gallery + video
        if ($post_type === 'su_kien') {
            wp_enqueue_media();
            wp_enqueue_editor();
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

                // Upload hình phụ cho tin tức (chọn nhiều ảnh/video/PDF)
                $('#upload_tin_tuc_hinh_phu').on('click', function(e) {
                    e.preventDefault();
                    var mediaUploader = wp.media({
                        title: 'Chọn ảnh/video/PDF phụ cho tin tức (có thể chọn nhiều)',
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
                            var is_pdf = mime_type === 'application/pdf';
                            var itemHtml = '';
                            var thumbUrl = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ? attachment.sizes.medium.url : (attachment.url || '');

                            if (is_video) {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<video style=\"width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;\" controls>' +
                                    '<source src=\"' + (attachment.url || '') + '\" type=\"' + mime_type + '\">' +
                                    '</video>' +
                                    '<button type=\"button\" class=\"remove-gallery-item button\" style=\"position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;\">×</button>' +
                                    '</div>';
                            } else if (is_pdf) {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<div style=\"width: 120px; height: 80px; display:flex; align-items:center; justify-content:center; border: 1px solid #ddd; border-radius: 4px; background:#f2f2f2; font-weight:800; color:#b30000;\">PDF</div>' +
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
                    if (confirm('Bạn có chắc muốn xóa tất cả ảnh/video/PDF phụ?')) {
                        $('#tin_tuc_hinh_phu_gallery').empty();
                        $('#tin_tuc_hinh_phu').val('[]');
                        $('#clear_tin_tuc_hinh_phu').hide();
                    }
                });

                // Upload hình phụ cho cộng đồng (chọn nhiều ảnh/video/PDF)
                $('#upload_cong_dong_hinh_phu').on('click', function(e) {
                    e.preventDefault();
                    var mediaUploader = wp.media({
                        title: 'Chọn ảnh/video/PDF phụ cho cộng đồng (có thể chọn nhiều)',
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
                            var is_pdf = mime_type === 'application/pdf';
                            var itemHtml = '';
                            var thumbUrl = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) ? attachment.sizes.medium.url : (attachment.url || '');

                            if (is_video) {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<video style=\"width: 120px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;\" controls>' +
                                    '<source src=\"' + (attachment.url || '') + '\" type=\"' + mime_type + '\">' +
                                    '</video>' +
                                    '<button type=\"button\" class=\"remove-gallery-item button\" style=\"position: absolute; top: -5px; right: -5px; background: #dc3232; color: white; border: none; border-radius: 50%; width: 18px; height: 18px; cursor: pointer; font-size: 10px;\">×</button>' +
                                    '</div>';
                            } else if (is_pdf) {
                                itemHtml = '<div class=\"gallery-item\" data-id=\"' + attachment.id + '\" style=\"position: relative; display: inline-block; margin: 5px;\">' +
                                    '<div style=\"width: 120px; height: 80px; display:flex; align-items:center; justify-content:center; border: 1px solid #ddd; border-radius: 4px; background:#f2f2f2; font-weight:800; color:#b30000;\">PDF</div>' +
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
                    if (confirm('Bạn có chắc muốn xóa tất cả ảnh/video/PDF phụ?')) {
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

    ?>
    <div style="padding: 10px;">
        <h3>Tin tức</h3>
        <ul>
            <li>Đã xuất bản: <strong><?php echo number_format_i18n($tin_tuc_count->publish); ?></strong></li>
            <li>Bản nháp: <strong><?php echo number_format_i18n($tin_tuc_count->draft); ?></strong></li>
            <li>Trong thùng rác: <strong><?php echo number_format_i18n($tin_tuc_count->trash); ?></strong></li>
        </ul>

        <p style="margin-top: 15px;">
            <a href="<?php echo admin_url('edit.php?post_type=tin_tuc'); ?>" class="button">Quản lý Tin tức</a>
        </p>
    </div>
    <?php
}

// Custom Pagination Function for Doanh nghiệp page
// $preserve_get_keys: thêm tên tham số GET cần giữ khi chuyển trang (vd. tìm kiếm Doanh nhân).
function dnttvn_custom_pagination($query = null, $preserve_get_keys = null) {
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
    if (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) {
        $query_args['sort_by'] = sanitize_text_field(wp_unslash($_GET['sort_by']));
    }
    if (is_array($preserve_get_keys)) {
        foreach ($preserve_get_keys as $gkey) {
            $gkey = sanitize_key($gkey);
            if ($gkey === '' || $gkey === 'paged') {
                continue;
            }
            if (isset($_GET[$gkey]) && (string) wp_unslash($_GET[$gkey]) !== '') {
                $query_args[$gkey] = sanitize_text_field(wp_unslash($_GET[$gkey]));
            }
        }
    }
    
    // For custom page templates, use get_permalink() as base
    global $post;
    $page_permalink = '';
    if (is_page() && isset($post)) {
        $page_permalink = get_permalink($post->ID);
    } elseif (is_home()) {
        $page_permalink = home_url('/');
    } else {
        // Fallback to get_pagenum_link for archive pages
        $page_permalink = get_pagenum_link($big);
    }
    
    // Build base URL for pagination (query string ?paged=)
    $base = add_query_arg(array_merge($query_args, array('paged' => '%#%')), $page_permalink);
    
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

// Helper function to render banner blocks (for reuse in sidebar and mobile)
// $offset, $limit dùng để phục vụ view mobile (lấy theo chỉ số để xen kẽ với thẻ doanh nghiệp)
function dnttvn_render_banner_blocks($class_prefix = 'ad-block', $offset = 0, $limit = null) {
    $now   = current_time('timestamp');
    $index = 0; // Đếm số banner thực sự hiển thị (sau khi lọc thời gian)
    // Get banner order
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    $order_array = array_map('trim', explode(',', $banner_column_order));
    
    // Prepare banner data
    $banner_data = array(
        'vvip' => array(
            'banners' => get_option('dnttvn_vvip_banners', array()),
            'links' => get_option('dnttvn_vvip_links', array()),
            'type' => 'vvip',
            'title' => 'Video quảng cáo hoặc banner: VVIP',
            'label' => 'VVIP'
        ),
        'vip' => array(
            'banners' => get_option('dnttvn_vip_banners', array()),
            'links' => get_option('dnttvn_vip_links', array()),
            'type' => 'vip',
            'title' => 'Video quảng cáo hoặc banner: VIP',
            'label' => 'VIP'
        ),
        'standard' => array(
            'banners' => get_option('dnttvn_standard_banners', array()),
            'links' => get_option('dnttvn_standard_links', array()),
            'type' => 'standard',
            'title' => 'Video quảng cáo hoặc banner: Standard',
            'label' => 'Standard'
        )
    );
    
    $output = '';
    
    // Display banners according to order
    foreach ($order_array as $banner_type) {
        if (!isset($banner_data[$banner_type])) continue;
        
        $data    = $banner_data[$banner_type];
        $banners = $data['banners'];
        $links   = $data['links'];

        // Lấy thời gian hiển thị tương ứng với từng loại banner
        $start_times = get_option('dnttvn_' . $banner_type . '_start', array());
        $end_times   = get_option('dnttvn_' . $banner_type . '_end', array());
        
        if (!empty($banners)) {
            foreach ($banners as $idx => $banner_id) {
                if ($banner_id) {
                    // Kiểm tra thời gian hiển thị (nếu có cấu hình)
                    $start_raw = isset($start_times[$idx]) ? $start_times[$idx] : '';
                    $end_raw   = isset($end_times[$idx]) ? $end_times[$idx] : '';
                    $start_ts  = $start_raw ? strtotime($start_raw) : false;
                    $end_ts    = $end_raw ? strtotime($end_raw) : false;

                    if (($start_ts && $now < $start_ts) || ($end_ts && $now > $end_ts)) {
                        continue;
                    }

                    // Lấy đúng URL theo loại file (image/video)
                    $mime_type = get_post_mime_type($banner_id);
                    if (strpos($mime_type, 'video') !== false) {
                        // Video: dùng URL gốc của file video
                        $banner_url = wp_get_attachment_url($banner_id);
                        $banner_alt = ''; // video không dùng alt
                    } else {
                        // Ảnh: dùng URL ảnh theo kích thước full
                    $banner_url = wp_get_attachment_image_url($banner_id, 'full');
                    $banner_alt = get_post_meta($banner_id, '_wp_attachment_image_alt', true);
                    }

                    $link_url = isset($links[$index]) ? $links[$index] : '';
                    if ($banner_url) {
                        // Áp dụng offset/limit nếu có (phục vụ view mobile)
                        if ($index < $offset) {
                            $index++;
                            continue;
                        }
                        if ($limit !== null && $index >= $offset + $limit) {
                            // Đã đủ số lượng cần lấy
                            return $output;
                        }

                        $output .= '<div class="' . esc_attr($class_prefix) . ' ' . esc_attr($data['type']) . '">';
                        $output .= '<h4>' . esc_html($data['title']) . '</h4>';
                        $output .= '<div class="ad-type">' . esc_html($data['label']) . '</div>';
                        $output .= '<div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>';
                        if ($link_url) {
                            $output .= '<a href="' . esc_url($link_url) . '" target="_blank">';
                        }
                        // Hiển thị video hoặc ảnh tùy theo mime type
                        $mime_type = get_post_mime_type($banner_id);
                        if (strpos($mime_type, 'video') !== false) {
                // Tự động chạy, lặp lại, không hiển thị thanh điều khiển
                $output .= '<video src="' . esc_url($banner_url) . '" autoplay muted loop playsinline style="width: 100%; max-width: 100%;"></video>';
                        } else {
                            $output .= '<img src="' . esc_url($banner_url) . '" alt="' . esc_attr($banner_alt) . '" style="width: 100%; max-width: 100%;">';
                        }
                        if ($link_url) {
                            $output .= '</a>';
                        }
                        $output .= '</div>';
                        $index++;
                    }
                }
            }
        }
    }
    
    return $output;
}


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
    
    // Banner Header (Desktop)
    $wp_customize->add_setting('dnttvn_banner_1', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_1', array(
        'label'       => 'Banner Header (Desktop)',
        'section'     => 'dnttvn_header_banners',
        'description' => 'Chọn hình ảnh hoặc video cho banner header trên màn hình lớn.',
    )));

    // Banner Header (Mobile)
    $wp_customize->add_setting('dnttvn_banner_mobile', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_mobile', array(
        'label'       => 'Banner Header (Mobile)',
        'section'     => 'dnttvn_header_banners',
        'description' => 'Chọn hình ảnh/video riêng cho banner trên mobile. Để trống thì dùng banner desktop (tự co kích thước).',
    )));

    // Logo Header (cùng khu vực Banner Header)
    $wp_customize->add_setting('dnttvn_header_logo', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_header_logo', array(
        'label'       => 'Logo Header',
        'section'     => 'dnttvn_header_banners',
        'description' => 'Chọn logo hiển thị trên header. Để trống thì dùng Logo trong Nhận diện trang (Site Identity).',
    )));

    // Favicon (icon tab trình duyệt)
    $wp_customize->add_setting('dnttvn_favicon', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_favicon', array(
        'label'       => 'Favicon',
        'section'     => 'dnttvn_header_banners',
        'description' => 'Chọn ảnh favicon (icon tab trình duyệt). Khuyến nghị: 32×32 hoặc 64×64 px, PNG/ICO.',
    )));

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
    // Xử lý lưu dữ liệu header banner
    if (isset($_POST['dnttvn_save_favicon']) && check_admin_referer('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce')) {
        if (isset($_POST['header_favicon'])) {
            set_theme_mod('dnttvn_favicon', absint($_POST['header_favicon']));
        }
        echo '<div class="notice notice-success"><p>Đã lưu favicon thành công!</p></div>';
    } elseif (isset($_POST['dnttvn_save_logo']) && check_admin_referer('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce')) {
        if (isset($_POST['header_logo'])) {
            set_theme_mod('dnttvn_header_logo', absint($_POST['header_logo']));
        }
        echo '<div class="notice notice-success"><p>Đã lưu logo thành công!</p></div>';
    } elseif (isset($_POST['dnttvn_save_banner_mobile']) && check_admin_referer('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce')) {
        if (isset($_POST['header_banner_mobile'])) {
            set_theme_mod('dnttvn_banner_mobile', absint($_POST['header_banner_mobile']));
        }
        echo '<div class="notice notice-success"><p>Đã lưu banner mobile thành công!</p></div>';
    } elseif (isset($_POST['dnttvn_save_banner_header']) && check_admin_referer('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce')) {
        if (isset($_POST['header_banners']) && is_array($_POST['header_banners'])) {
            $banner_id = isset($_POST['header_banners'][1]) ? absint($_POST['header_banners'][1]) : 0;
            set_theme_mod('dnttvn_banner_1', $banner_id);
        }

        // Lưu thời gian hiển thị cho Banner Header
        $header_start = isset($_POST['header_start']) && is_array($_POST['header_start'])
            ? array_map('sanitize_text_field', $_POST['header_start'])
            : array();
        $header_end   = isset($_POST['header_end']) && is_array($_POST['header_end'])
            ? array_map('sanitize_text_field', $_POST['header_end'])
            : array();
        update_option('dnttvn_header_start', $header_start);
        update_option('dnttvn_header_end', $header_end);

        echo '<div class="notice notice-success"><p>Đã lưu banner header thành công!</p></div>';
    }

    // Header banner desktop + mobile + logo + favicon
    $header_banners        = array();
    $header_banners[1]     = get_theme_mod('dnttvn_banner_1', '');
    $header_banner_mobile  = get_theme_mod('dnttvn_banner_mobile', '');
    $header_logo           = get_theme_mod('dnttvn_header_logo', '');
    $header_favicon        = get_theme_mod('dnttvn_favicon', '');
    $header_start          = get_option('dnttvn_header_start', array());
    $header_end            = get_option('dnttvn_header_end', array());

    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Banner Header</h1>
        <p class="description">Quản lý banner hiển thị trong slider ở phần đầu trang (Header). Hỗ trợ cả hình ảnh và video.</p>

        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce'); ?>

            <h2>Logo Header</h2>
            <p class="description">Chọn logo hiển thị trên header. Để trống thì dùng Logo trong Giao diện → Nhận diện trang.</p>
            <div class="banner-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd;">
                <p>
                    <input type="hidden" name="header_logo" id="header-logo-id" value="<?php echo esc_attr($header_logo); ?>">
                    <button type="button" class="button button-primary upload-banner-btn" data-type="header-logo" data-index="logo">📁 Chọn logo</button>
                    <button type="button" class="button remove-banner-btn" data-type="header-logo" data-index="logo">🗑️ Xóa</button>
                </p>
                <div class="banner-preview" id="header-logo-preview" style="margin-top: 10px;">
                    <?php if ($header_logo) { echo wp_get_attachment_image($header_logo, 'medium'); } ?>
                </div>
                <p style="margin-top: 10px;">
                    <button type="submit" name="dnttvn_save_logo" value="1" class="button button-secondary">Lưu Logo</button>
                </p>
            </div>

            <h2>Favicon</h2>
            <p class="description">Chọn ảnh favicon (icon hiển thị trên tab trình duyệt). Khuyến nghị: 32×32 hoặc 64×64 px, PNG/ICO.</p>
            <div class="banner-item" style="margin-bottom: 25px; padding: 15px; border: 1px solid #ddd;">
                <p>
                    <input type="hidden" name="header_favicon" id="header-favicon-id" value="<?php echo esc_attr($header_favicon); ?>">
                    <button type="button" class="button button-primary upload-banner-btn" data-type="header-favicon" data-index="favicon">📁 Chọn favicon</button>
                    <button type="button" class="button remove-banner-btn" data-type="header-favicon" data-index="favicon">🗑️ Xóa</button>
                </p>
                <div class="banner-preview" id="header-favicon-preview" style="margin-top: 10px;">
                    <?php if ($header_favicon) { echo wp_get_attachment_image($header_favicon, 'thumbnail'); } ?>
                </div>
                <p style="margin-top: 10px;">
                    <button type="submit" name="dnttvn_save_favicon" value="1" class="button button-secondary">Lưu Favicon</button>
                </p>
            </div>

            <h2>Banner Header</h2>
            <p class="description">Chọn banner hiển thị trong phần đầu trang (Header). Hỗ trợ cả hình ảnh và video.</p>
            <div id="header-banners-container">
                <?php
                $i = 1; // Chỉ có 1 banner
                $banner_id    = isset($header_banners[$i]) ? $header_banners[$i] : '';
                $start_value  = isset($header_start[$i]) ? $header_start[$i] : '';
                $end_value    = isset($header_end[$i]) ? $header_end[$i] : '';
                ?>
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner Header</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner Header:</strong></label><br>
                        <input type="hidden" name="header_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="header" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="header" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước ngang, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;">
                        <?php
                        if ($banner_id) {
                            $mime_type = get_post_mime_type($banner_id);
                            if (strpos($mime_type, 'video') !== false) :
                                $video_url = wp_get_attachment_url($banner_id);
                                ?>
                                <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                            <?php else :
                                echo wp_get_attachment_image($banner_id, 'medium');
                            endif;
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
                        <button type="submit" name="dnttvn_save_banner_header" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Header</button>
                    </p>
                </div>

                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner Header (Mobile)</h3>
                    <p class="description">Chọn banner riêng cho mobile. Để trống thì dùng banner desktop (tự co). Khuyến nghị: 360×230 px.</p>
                    <p>
                        <input type="hidden" name="header_banner_mobile" id="header-banner-mobile-id" value="<?php echo esc_attr($header_banner_mobile); ?>">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="header-mobile" data-index="mobile">📁 Chọn hình ảnh/video Mobile</button>
                        <button type="button" class="button remove-banner-btn" data-type="header-mobile" data-index="mobile">🗑️ Xóa</button>
                    </p>
                    <div class="banner-preview" id="header-banner-mobile-preview" style="margin-top: 10px;">
                        <?php
                        if ($header_banner_mobile) {
                            $mime_type = get_post_mime_type($header_banner_mobile);
                            if (strpos($mime_type, 'video') !== false) :
                                $video_url = wp_get_attachment_url($header_banner_mobile);
                                ?>
                                <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($header_banner_mobile); ?></p>
                            <?php else :
                                echo wp_get_attachment_image($header_banner_mobile, 'medium');
                            endif;
                        }
                        ?>
                    </div>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_mobile" value="1" class="button button-secondary">Lưu Banner Mobile</button>
                    </p>
                </div>
            </div>
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
            var input = (type === 'header-mobile') ? $('#header-banner-mobile-id') : (type === 'header-logo') ? $('#header-logo-id') : (type === 'header-favicon') ? $('#header-favicon-id') : container.find('.banner-image-id');
            var preview = (type === 'header-mobile') ? $('#header-banner-mobile-preview') : (type === 'header-logo') ? $('#header-logo-preview') : (type === 'header-favicon') ? $('#header-favicon-preview') : container.find('.banner-preview');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: (type === 'header-mobile') ? 'Chọn Banner Mobile' : (type === 'header-logo') ? 'Chọn Logo' : (type === 'header-favicon') ? 'Chọn Favicon' : 'Chọn Banner Header',
                button: {
                    text: 'Sử dụng banner này'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px;"></video>');
                } else {
                    preview.html('<img src="' + (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url) + '" style="max-width: 300px; max-height: 200px;">');
                }
            });

            mediaUploader.open();
        });

        $('.remove-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var type = button.data('type');
            var container = button.closest('.banner-item');
            var input = (type === 'header-mobile') ? $('#header-banner-mobile-id') : (type === 'header-logo') ? $('#header-logo-id') : (type === 'header-favicon') ? $('#header-favicon-id') : container.find('.banner-image-id');
            var preview = (type === 'header-mobile') ? $('#header-banner-mobile-preview') : (type === 'header-logo') ? $('#header-logo-preview') : (type === 'header-favicon') ? $('#header-favicon-preview') : container.find('.banner-preview');
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
        }

        // Save Banner Order (Thứ tự hiển thị cột phải)
        if ($is_global && isset($_POST['banner_column_order'])) {
            update_option('dnttvn_banner_column_order', sanitize_text_field($_POST['banner_column_order']));
        }

        echo '<div class="notice notice-success"><p>Đã lưu banner quảng cáo thành công!</p></div>';
    }

    // Load data
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
        <h1>Quản lý Banner Quảng cáo</h1>
        <p class="description">Quản lý banner và video quảng cáo hiển thị ở cột bên phải trang "Danh sách Doanh nghiệp". Hỗ trợ cả hình ảnh và video.</p>

        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_ad_banners_action', 'dnttvn_save_ad_banners_nonce'); ?>

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
                            <label><strong>Hình ảnh/Video Banner VVIP <?php echo $i + 1; ?>:</strong></label><br>
                            <input type="hidden" name="vvip_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
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
                            <input type="url" name="vvip_links[<?php echo $i; ?>]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vvip_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vvip_end[<?php echo $i; ?>]"
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
                            <label><strong>Hình ảnh/Video Banner VIP <?php echo $i + 1; ?>:</strong></label><br>
                            <input type="hidden" name="vip_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
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
                            <input type="url" name="vip_links[<?php echo $i; ?>]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vip_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vip_end[<?php echo $i; ?>]"
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
                            <label><strong>Hình ảnh/Video Banner Standard <?php echo $i + 1; ?>:</strong></label><br>
                            <input type="hidden" name="standard_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
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
                            <input type="url" name="standard_links[<?php echo $i; ?>]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="standard_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="standard_end[<?php echo $i; ?>]"
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

            <h2>Thứ tự hiển thị Banner</h2>
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
                <input type="submit" name="dnttvn_save_banners" class="button button-primary" value="Lưu Banner Quảng cáo">
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
                title: 'Chọn Banner Quảng cáo',
                button: {
                    text: 'Sử dụng banner này'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px;"></video><p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: ' + attachment.id + '</p>');
                } else {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 300px; max-height: 200px;">');
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

        // Add VVIP Banner
        $('#add-vvip-banner').on('click', function() {
            var container = $('#vvip-banners-container');
            var count = container.children('.banner-item').length;
            var newBanner = `
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner VVIP ${count + 1}</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner VVIP ${count + 1}:</strong></label><br>
                        <input type="hidden" name="vvip_banners[${count}]" class="banner-image-id" value="">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="${count}">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="${count}">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;"></div>
                    <p>
                        <label>Link (nếu có):</label><br>
                        <input type="url" name="vvip_links[${count}]" class="regular-text" placeholder="https://...">
                    </p>
                    <p>
                        <label><strong>Thời gian hiển thị:</strong></label><br>
                        <input type="datetime-local" name="vvip_start[${count}]" style="max-width: 220px;">
                        đến
                        <input type="datetime-local" name="vvip_end[${count}]" style="max-width: 220px;">
                        <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                    </p>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_vvip" value="${count}" class="button button-secondary">Lưu Banner VVIP ${count + 1}</button>
                    </p>
                </div>
            `;
            container.append(newBanner);
        });

        // Add VIP Banner
        $('#add-vip-banner').on('click', function() {
            var container = $('#vip-banners-container');
            var count = container.children('.banner-item').length;
            var newBanner = `
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner VIP ${count + 1}</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner VIP ${count + 1}:</strong></label><br>
                        <input type="hidden" name="vip_banners[${count}]" class="banner-image-id" value="">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="vip" data-index="${count}">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="vip" data-index="${count}">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;"></div>
                    <p>
                        <label>Link (nếu có):</label><br>
                        <input type="url" name="vip_links[${count}]" class="regular-text" placeholder="https://...">
                    </p>
                    <p>
                        <label><strong>Thời gian hiển thị:</strong></label><br>
                        <input type="datetime-local" name="vip_start[${count}]" style="max-width: 220px;">
                        đến
                        <input type="datetime-local" name="vip_end[${count}]" style="max-width: 220px;">
                        <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                    </p>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_vip" value="${count}" class="button button-secondary">Lưu Banner VIP ${count + 1}</button>
                    </p>
                </div>
            `;
            container.append(newBanner);
        });

        // Add Standard Banner
        $('#add-standard-banner').on('click', function() {
            var container = $('#standard-banners-container');
            var count = container.children('.banner-item').length;
            var newBanner = `
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner Standard ${count + 1}</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner Standard ${count + 1}:</strong></label><br>
                        <input type="hidden" name="standard_banners[${count}]" class="banner-image-id" value="">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="standard" data-index="${count}">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="standard" data-index="${count}">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;"></div>
                    <p>
                        <label>Link (nếu có):</label><br>
                        <input type="url" name="standard_links[${count}]" class="regular-text" placeholder="https://...">
                    </p>
                    <p>
                        <label><strong>Thời gian hiển thị:</strong></label><br>
                        <input type="datetime-local" name="standard_start[${count}]" style="max-width: 220px;">
                        đến
                        <input type="datetime-local" name="standard_end[${count}]" style="max-width: 220px;">
                        <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                    </p>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_standard" value="${count}" class="button button-secondary">Lưu Banner Standard ${count + 1}</button>
                    </p>
                </div>
            `;
            container.append(newBanner);
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

    // Submenu: Luật chơi (hiển thị ở form Đăng ký - Phần 5)
    add_submenu_page(
        'dnttvn-banner-header',
        'Quản lý Luật chơi',
        'Luật chơi',
        'manage_options',
        'dnttvn-luat-choi',
        'dnttvn_luat_choi_page'
    );

    // Submenu: Trang thành viên mới (3 trang nội dung + trang đầu hiển thị thành viên đã duyệt)
    add_submenu_page(
        'dnttvn-banner-header',
        'Trang thành viên mới',
        'Trang thành viên mới',
        'manage_options',
        'dnttvn-trang-thanh-vien-moi',
        'dnttvn_trang_thanh_vien_moi_page'
    );

    // Submenu: Quản lý Ngành nghề (Form Đăng ký)
    add_submenu_page(
        'dnttvn-banner-header',
        'Quản lý Ngành nghề đăng ký',
        'Ngành nghề đăng ký',
        'manage_options',
        'dnttvn-nganh-nghe-dang-ky',
        'dnttvn_nganh_nghe_dang_ky_page'
    );

    // Submenu dưới Phụng sự Con Doanh nhân: Nội dung đăng ký (Chọn 01 chương trình phù hợp với độ tuổi)
    add_submenu_page(
        'edit.php?post_type=phung_su_con_dn',
        'Chương trình đăng ký Hướng nghiệp',
        'Chương trình đăng ký',
        'manage_options',
        'dnttvn-chuong-trinh-huong-nghiep',
        'dnttvn_chuong_trinh_huong_nghiep_page'
    );
}

function dnttvn_chuong_trinh_huong_nghiep_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập.');
    }
    if (isset($_POST['dnttvn_save_chuong_trinh_hn']) && check_admin_referer('dnttvn_save_chuong_trinh_hn', 'dnttvn_chuong_trinh_hn_nonce')) {
        $items = array();
        if (isset($_POST['chuong_trinh_hn']) && is_array($_POST['chuong_trinh_hn'])) {
            foreach ($_POST['chuong_trinh_hn'] as $idx => $row) {
                $title = isset($row['title']) ? sanitize_text_field(wp_unslash($row['title'])) : '';
                $subtitle = isset($row['subtitle']) ? sanitize_text_field(wp_unslash($row['subtitle'])) : '';
                $desc = isset($row['description']) ? sanitize_textarea_field(wp_unslash($row['description'])) : '';
                if ($title === '' && $subtitle === '' && $desc === '') {
                    continue;
                }
                $items[] = array('title' => $title, 'subtitle' => $subtitle, 'description' => $desc);
            }
        }
        if (isset($_POST['chuong_trinh_hn_new_title']) && trim($_POST['chuong_trinh_hn_new_title']) !== '') {
            $items[] = array(
                'title'       => sanitize_text_field(wp_unslash($_POST['chuong_trinh_hn_new_title'])),
                'subtitle'    => isset($_POST['chuong_trinh_hn_new_subtitle']) ? sanitize_text_field(wp_unslash($_POST['chuong_trinh_hn_new_subtitle'])) : '',
                'description' => isset($_POST['chuong_trinh_hn_new_description']) ? sanitize_textarea_field(wp_unslash($_POST['chuong_trinh_hn_new_description'])) : '',
            );
        }
        update_option('dnttvn_huong_nghiep_chuong_trinh', $items);
        echo '<div class="notice notice-success"><p>Đã lưu nội dung đăng ký (Chương trình Hướng nghiệp).</p></div>';
    }
    $opts = function_exists('dnttvn_get_huong_nghiep_chuong_trinh_options') ? dnttvn_get_huong_nghiep_chuong_trinh_options() : array();
    ?>
    <div class="wrap">
        <h1>Nội dung đăng ký – Chọn 01 chương trình phù hợp với độ tuổi</h1>
        <p class="description">Quản lý danh sách chương trình hiển thị tại mục <strong>III. NỘI DUNG ĐĂNG KÝ</strong> trên phiếu đăng ký Hướng nghiệp &amp; Khai mở Trí tuệ. Thứ tự hiển thị theo thứ tự bên dưới (Chương trình 1, 2, 3...).</p>
        <form method="post">
            <?php wp_nonce_field('dnttvn_save_chuong_trinh_hn', 'dnttvn_chuong_trinh_hn_nonce'); ?>
            <table class="widefat striped" style="max-width: 900px;">
                <thead>
                    <tr>
                        <th style="width:80px;">#</th>
                        <th>Tên chương trình</th>
                        <th style="width:180px;">Phụ đề (vd: Dành cho THCS)</th>
                        <th>Mô tả / Mục tiêu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opts as $i => $item) : ?>
                        <tr>
                            <td><strong><?php echo (int) ($i + 1); ?></strong></td>
                            <td><input type="text" name="chuong_trinh_hn[<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="large-text" placeholder="Chương trình 1: ĐÁNH THỨC ƯỚC MƠ"></td>
                            <td><input type="text" name="chuong_trinh_hn[<?php echo (int) $i; ?>][subtitle]" value="<?php echo esc_attr($item['subtitle']); ?>" class="regular-text" placeholder="Dành cho THCS"></td>
                            <td><textarea name="chuong_trinh_hn[<?php echo (int) $i; ?>][description]" rows="2" class="large-text" placeholder="Mục tiêu: ..."><?php echo esc_textarea($item['description']); ?></textarea></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td>+</td>
                        <td><input type="text" name="chuong_trinh_hn_new_title" value="" class="large-text" placeholder="Thêm chương trình mới"></td>
                        <td><input type="text" name="chuong_trinh_hn_new_subtitle" value="" class="regular-text" placeholder="Phụ đề"></td>
                        <td><textarea name="chuong_trinh_hn_new_description" rows="2" class="large-text" placeholder="Mô tả / Mục tiêu"></textarea></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit" style="margin-top: 15px;">
                <button type="submit" name="dnttvn_save_chuong_trinh_hn" class="button button-primary">Lưu thay đổi</button>
            </p>
        </form>
    </div>
    <?php
}

function dnttvn_nganh_nghe_dang_ky_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập.');
    }

    // Xử lý lưu: cập nhật, xóa, thêm mới
    if (isset($_POST['dnttvn_save_nganh_nghe']) && check_admin_referer('dnttvn_save_nganh_nghe', 'dnttvn_nganh_nghe_nonce')) {
        $existing = isset($_POST['nganh_name']) && is_array($_POST['nganh_name']) ? $_POST['nganh_name'] : array();
        $delete   = isset($_POST['nganh_delete']) && is_array($_POST['nganh_delete']) ? array_map('intval', $_POST['nganh_delete']) : array();
        $new_list = isset($_POST['nganh_new']) && is_array($_POST['nganh_new']) ? $_POST['nganh_new'] : array();

        $result = array();

        foreach ($existing as $idx => $name) {
            $idx  = (int) $idx;
            $name = trim(wp_strip_all_tags((string) wp_unslash($name)));
            if ($name === '') {
                continue;
            }
            if (in_array($idx, $delete, true)) {
                continue;
            }
            $result[] = $name;
        }

        foreach ($new_list as $name) {
            $name = trim(wp_strip_all_tags((string) wp_unslash($name)));
            if ($name === '') {
                continue;
            }
            $result[] = $name;
        }

        $result = array_values(array_unique($result));
        update_option('dnttvn_nganh_nghe_options', $result);
        echo '<div class="notice notice-success"><p>Đã lưu danh sách ngành nghề.</p></div>';
    }

    $opts_full = function_exists('dnttvn_get_nganh_nghe_options') ? dnttvn_get_nganh_nghe_options() : array();
    $default   = array('Sản xuất', 'Dịch vụ', 'Thương mại', 'Công nghệ', 'Bất động sản', 'Tài chính', 'Giáo dục - Đào tạo', 'F&B - Ẩm thực', 'Y tế - Dược', 'Xây dựng', 'Nông nghiệp', 'Logistics');
    $custom_only = array_values(array_diff($opts_full, $default));
    ?>
    <div class="wrap">
        <h1>Ngành nghề đăng ký</h1>
        <p class="description">Danh sách này dùng cho dropdown <strong>Ngành nghề kinh doanh</strong> ở trang Đăng ký. Dưới đây là danh sách ngành nghề hiển thị dạng list, có thể sửa / xóa / thêm như quản lý bài viết.</p>
        <form method="post">
            <?php wp_nonce_field('dnttvn_save_nganh_nghe', 'dnttvn_nganh_nghe_nonce'); ?>
            <h2>Ngành nghề mặc định</h2>
            <ul style="margin-left: 18px; list-style: disc; max-width: 600px;">
                <?php foreach ($default as $name) : ?>
                    <li><strong><?php echo esc_html($name); ?></strong> <em>(mặc định, không chỉnh sửa tại đây)</em></li>
                <?php endforeach; ?>
            </ul>

            <h2 style="margin-top: 25px;">Ngành nghề tùy chỉnh</h2>
            <table class="widefat striped" style="max-width: 800px;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Ngành nghề</th>
                        <th style="width:80px;">Xóa?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($custom_only)) : ?>
                        <?php foreach ($custom_only as $i => $name) : ?>
                            <tr>
                                <td><?php echo (int) ($i + 1); ?></td>
                                <td>
                                    <input type="text" name="nganh_name[<?php echo (int) $i; ?>]" value="<?php echo esc_attr($name); ?>" class="regular-text" style="width: 100%;" />
                                </td>
                                <td style="text-align:center;">
                                    <label><input type="checkbox" name="nganh_delete[]" value="<?php echo (int) $i; ?>"> Xóa</label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3"><em>Chưa có ngành nghề tùy chỉnh.</em></td>
                        </tr>
                    <?php endif; ?>
                    <?php for ($j = 0; $j < 3; $j++) : ?>
                        <tr>
                            <td>+</td>
                            <td colspan="2">
                                <input type="text" name="nganh_new[]" value="" class="regular-text" placeholder="Thêm ngành nghề mới..." style="width: 100%;" />
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <p class="submit" style="margin-top: 15px;">
                <button type="submit" name="dnttvn_save_nganh_nghe" class="button button-primary">Lưu thay đổi</button>
            </p>

            <h2>Danh sách ngành nghề đang dùng (Dropdown)</h2>
            <?php if (!empty($opts_full)) : ?>
                <ol style="max-width: 600px; padding-left: 20px; list-style: decimal;">
                    <?php foreach ($opts_full as $name) : ?>
                        <li><?php echo esc_html($name); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php else : ?>
                <p>Chưa có ngành nghề nào.</p>
            <?php endif; ?>
        </form>
    </div>
    <?php
}
// Add debug menu for checking pages
function dnttvn_add_debug_menu() {
    add_submenu_page(
        null, // Hidden menu
        'Debug Pages',
        'Debug Pages',
        'manage_options',
        'dnttvn-debug-pages',
        'dnttvn_debug_pages_callback'
    );
}
add_action('admin_menu', 'dnttvn_add_debug_menu');

// Debug callback to check and create pages
function dnttvn_debug_pages_callback() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }

    echo '<div class="wrap">';
    echo '<h1>Kiểm tra và tạo lại Pages</h1>';

    // Force create pages
    dnttvn_create_necessary_pages();

    // Check pages
    $pages_to_check = array(
        'trang-tin-tuc-chi-tiet' => 'page-tin-tuc-chi-tiet.php',
        'cong-dong' => 'page-ve-cong-dong.php',
        'tin-tuc' => 'page-tin-tuc.php',
        'cong-dong' => 'page-cong-dong.php'
    );

    echo '<h2>Kết quả kiểm tra:</h2>';
    echo '<ul>';

    foreach ($pages_to_check as $slug => $template) {
        $page = get_page_by_path($slug);
        if ($page) {
            $current_template = get_post_meta($page->ID, '_wp_page_template', true);
            echo '<li><strong>' . esc_html($slug) . ':</strong> ✅ Tồn tại (ID: ' . $page->ID . ', Template: ' . esc_html($current_template) . ')</li>';
        } else {
            echo '<li><strong>' . esc_html($slug) . ':</strong> ❌ Không tồn tại</li>';
        }
    }

    echo '</ul>';

    echo '<p><a href="' . admin_url() . '" class="button">← Quay lại Dashboard</a></p>';
    echo '</div>';
}

add_action('admin_menu', 'dnttvn_add_admin_menu');

// Trang quản lý Luật chơi (hiển thị ở form Đăng ký - Phần 5)
function dnttvn_luat_choi_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập.');
    }
    $option_key = 'dnttvn_luat_choi';
    if (isset($_POST['dnttvn_save_luat_choi']) && check_admin_referer('dnttvn_luat_choi_save', 'dnttvn_luat_choi_nonce')) {
        $content = isset($_POST['dnttvn_luat_choi_content']) ? dnttvn_sanitize_content_with_tables($_POST['dnttvn_luat_choi_content']) : '';
        update_option($option_key, $content);
        echo '<div class="notice notice-success"><p>Đã lưu Luật chơi.</p></div>';
    }
    $content = get_option($option_key, '');
    echo '<div class="wrap">';
    echo '<h1>Quản lý Luật chơi</h1>';
    echo '<p class="description">Nội dung sẽ hiển thị ở form Đăng ký gia nhập, Phần 5: Xác nhận cam kết (mục 11. Xác nhận "Luật chơi"). Người đăng ký đọc nội dung này trước khi chọn "Đã rõ và Cam kết".</p>';
    echo '<form method="post" action="">';
    wp_nonce_field('dnttvn_luat_choi_save', 'dnttvn_luat_choi_nonce');
    wp_editor($content, 'dnttvn_luat_choi_content', array_merge(
        dnttvn_get_content_section_editor_settings('dnttvn_luat_choi_content', 'dnttvn_luat_choi_content'),
        array(
            'textarea_rows' => 16,
            'media_buttons' => true,
        )
    ));
    echo '<p class="submit"><input type="submit" name="dnttvn_save_luat_choi" class="button button-primary" value="Lưu Luật chơi"></p>';
    echo '</form>';
    echo '</div>';
}

// Trang quản lý 3 (4) trang thành viên mới: nội dung từng trang
function dnttvn_trang_thanh_vien_moi_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập.');
    }
    $sections = array(
        'gia_tri'    => array('key' => 'dnttvn_page_gia_tri_content',    'title' => 'Giá trị nhận được của thành viên', 'note' => 'Trang này hiển thị danh sách người đăng ký đã được duyệt và nội dung bên dưới.'),
    );
    foreach ($sections as $slug => $cfg) {
        if (isset($_POST['dnttvn_save_thanh_vien_' . $slug]) && check_admin_referer('dnttvn_thanh_vien_' . $slug, 'dnttvn_thanh_vien_nonce_' . $slug)) {
            $content = isset($_POST['dnttvn_content_' . $slug]) ? dnttvn_sanitize_content_with_tables($_POST['dnttvn_content_' . $slug]) : '';
            update_option($cfg['key'], $content);
            echo '<div class="notice notice-success"><p>Đã lưu: ' . esc_html($cfg['title']) . '.</p></div>';
        }
    }
    echo '<div class="wrap">';
    echo '<h1>Quản lý Trang thành viên mới</h1>';
    echo '<p class="description">Nội dung trang <strong>Giá trị nhận được của thành viên</strong> (trang này cũng hiển thị danh sách người đăng ký đã được duyệt). <strong>Quy trình gia nhập</strong> và <strong>Hỏi đáp về Cộng đồng</strong> quản lý tại menu <em>Quy trình gia nhập</em> và <em>Hỏi đáp Cộng đồng</em>.</p>';
    foreach ($sections as $slug => $cfg) {
        $content = get_option($cfg['key'], '');
        echo '<div style="margin-bottom:30px; padding:20px; background:#fff; border:1px solid #ccd0d4; box-shadow:0 1px 1px rgba(0,0,0,.04);">';
        echo '<h2 style="margin-top:0;">' . esc_html($cfg['title']) . '</h2>';
        if (!empty($cfg['note'])) echo '<p class="description">' . esc_html($cfg['note']) . '</p>';
        echo '<form method="post" action="">';
        wp_nonce_field('dnttvn_thanh_vien_' . $slug, 'dnttvn_thanh_vien_nonce_' . $slug);
        wp_editor($content, 'dnttvn_content_' . $slug, array_merge(
            dnttvn_get_content_section_editor_settings('dnttvn_content_' . $slug, 'dnttvn_content_' . $slug),
            array(
                'textarea_rows' => 12,
                'media_buttons' => true,
            )
        ));
        echo '<p class="submit"><input type="submit" name="dnttvn_save_thanh_vien_' . esc_attr($slug) . '" class="button button-primary" value="Lưu ' . esc_attr($cfg['title']) . '"></p>';
        echo '</form></div>';
    }
    echo '</div>';
}

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

// Add Favicon
function dnttvn_add_favicon() {
    $favicon_id = get_theme_mod('dnttvn_favicon', '');
    if ($favicon_id) {
        $favicon_url = wp_get_attachment_image_url($favicon_id, 'full');
        if ($favicon_url) {
            echo '<link rel="icon" type="image/png" href="' . esc_url($favicon_url) . '" />' . "\n";
            echo '<link rel="shortcut icon" type="image/png" href="' . esc_url($favicon_url) . '" />' . "\n";
            return;
        }
    }
    $favicon_url = get_template_directory_uri() . '/Logo-nhỏ CDTTVN.png';
    echo '<link rel="icon" type="image/png" href="' . esc_url($favicon_url) . '" />' . "\n";
    echo '<link rel="shortcut icon" type="image/png" href="' . esc_url($favicon_url) . '" />' . "\n";
}
add_action('wp_head', 'dnttvn_add_favicon');
add_action('admin_head', 'dnttvn_add_favicon');

// ========================================================================
// EXCEL UPLOAD & TABLE DISPLAY FEATURE
// ========================================================================

function dnttvn_allow_excel_upload($mimes) {
    $mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $mimes['xls']  = 'application/vnd.ms-excel';
    $mimes['csv']  = 'text/csv';
    return $mimes;
}
add_filter('upload_mimes', 'dnttvn_allow_excel_upload');

function dnttvn_fix_excel_mime_check($data, $file, $filename, $mimes) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (in_array(strtolower($ext), array('xlsx', 'xls', 'csv'))) {
        $mime_map = array(
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls'  => 'application/vnd.ms-excel',
            'csv'  => 'text/csv',
        );
        $data['ext']  = strtolower($ext);
        $data['type'] = $mime_map[strtolower($ext)];
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'dnttvn_fix_excel_mime_check', 10, 4);

function dnttvn_enqueue_excel_admin_scripts($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'))) return;
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, array('tin_tuc', 'cong_dong'))) return;

    wp_enqueue_media();
    wp_enqueue_script(
        'sheetjs-xlsx',
        'https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js',
        array(),
        '0.20.3',
        true
    );
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_excel_admin_scripts');

function dnttvn_excel_upload_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_excel_data', 'dnttvn_excel_nonce');

    $file_id   = get_post_meta($post->ID, '_excel_file_id', true);
    $file_name = get_post_meta($post->ID, '_excel_file_name', true);
    $table_data = get_post_meta($post->ID, '_excel_table_data', true);
    if (!is_array($table_data)) {
        $old_json = get_post_meta($post->ID, '_excel_table_json', true);
        if ($old_json && is_string($old_json)) {
            $table_data = json_decode($old_json, true);
        }
    }
    $table_json_for_input = (is_array($table_data) && !empty($table_data)) ? wp_json_encode($table_data) : '';
    $file_url  = $file_id ? wp_get_attachment_url($file_id) : '';
    ?>
    <div id="dnttvn-excel-upload-wrap">
        <input type="hidden" id="excel_file_id" name="excel_file_id" value="<?php echo esc_attr($file_id); ?>">
        <input type="hidden" id="excel_file_name" name="excel_file_name" value="<?php echo esc_attr($file_name); ?>">
        <input type="hidden" id="excel_table_json" name="excel_table_json" value="<?php echo esc_attr($table_json_for_input); ?>">

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 15px;">
            <button type="button" id="excel-upload-btn" class="button button-primary" style="background: #217346; border-color: #1a5c38;">
                📊 Chọn / Tải lên file Excel
            </button>
            <?php if ($file_id && $file_name) : ?>
            <span id="excel-file-info" style="color: #333; font-weight: 600;">
                📎 <?php echo esc_html($file_name); ?>
                <a href="<?php echo esc_url($file_url); ?>" target="_blank" style="margin-left: 5px;">(Tải xuống)</a>
            </span>
            <?php else : ?>
            <span id="excel-file-info" style="color: #999;">Chưa có file Excel</span>
            <?php endif; ?>
            <button type="button" id="excel-remove-btn" class="button" style="color: #dc3232; <?php echo $file_id ? '' : 'display:none;'; ?>">
                🗑️ Xóa file
            </button>
        </div>

        <div id="excel-reparse-bar" style="display:none; margin-bottom: 10px; padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
            <span style="color: #856404;">File đã lưu trước đó. </span>
            <button type="button" id="excel-reparse-btn" class="button">🔄 Parse lại từ file</button>
        </div>

        <div id="excel-preview-area" style="max-height: 500px; overflow: auto; border: 1px solid #ddd; border-radius: 6px; display: <?php echo $table_json_for_input ? 'block' : 'none'; ?>;">
            <div id="excel-preview-tabs" style="display: flex; flex-wrap: wrap; background: #f5f5f5; border-bottom: 1px solid #ddd;"></div>
            <div id="excel-preview-content" style="padding: 10px; overflow-x: auto;"></div>
        </div>

        <p style="margin-top: 8px; font-size: 12px; color: #666;">
            Hỗ trợ: .xlsx, .xls, .csv — Nội dung sẽ hiển thị dạng bảng với cuộn ngang trên trang chi tiết bài viết.
        </p>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var excelUploader = null;
        var currentSheetData = null;

        function renderJsonPreview() {
            var raw = $('#excel_table_json').val();
            if (!raw) {
                $('#excel-preview-area').hide();
                return;
            }
            try {
                var data = JSON.parse(raw);
                currentSheetData = data;
                renderSheetTabs(data);
                $('#excel-preview-area').show();
            } catch(e) {
                $('#excel-preview-content').html('<p style="color:red;">Lỗi parse JSON</p>');
                $('#excel-preview-area').show();
            }
        }

        function renderSheetTabs(sheetsData) {
            var $tabs = $('#excel-preview-tabs').empty();
            var $content = $('#excel-preview-content').empty();
            if (!sheetsData || !sheetsData.length) {
                $content.html('<p>Không có dữ liệu</p>');
                return;
            }
            sheetsData.forEach(function(sheet, idx) {
                var $tab = $('<div>')
                    .text(sheet.name || 'Sheet ' + (idx + 1))
                    .css({
                        padding: '8px 16px', cursor: 'pointer', fontWeight: '600',
                        fontSize: '13px', color: idx === 0 ? '#217346' : '#666',
                        borderBottom: idx === 0 ? '2px solid #217346' : '2px solid transparent',
                        background: idx === 0 ? '#fff' : 'transparent'
                    })
                    .data('idx', idx)
                    .on('click', function() {
                        $tabs.children().css({color: '#666', borderBottom: '2px solid transparent', background: 'transparent'});
                        $(this).css({color: '#217346', borderBottom: '2px solid #217346', background: '#fff'});
                        showSheet(sheetsData, idx);
                    });
                $tabs.append($tab);
            });
            showSheet(sheetsData, 0);
        }

        function showSheet(sheetsData, idx) {
            var $content = $('#excel-preview-content').empty();
            var sheet = sheetsData[idx];
            if (!sheet || !sheet.rows || !sheet.rows.length) {
                $content.html('<p style="color:#999;">Sheet trống</p>');
                return;
            }
            var html = '<div style="overflow-x:auto;"><table class="dnttvn-editor-table" style="border-collapse:collapse;width:100%;font-size:13px;">';
            sheet.rows.forEach(function(row, rIdx) {
                html += '<tr>';
                row.forEach(function(cell, cIdx) {
                    var tag = rIdx === 0 ? 'th' : 'td';
                    var val = (cell === null || cell === undefined) ? '' : String(cell);
                    var style = 'border:1px solid #ccc; padding:6px 10px; white-space:pre-line;';
                    if (rIdx === 0) style += 'background:#217346; color:#fff; font-weight:600;';
                    else if (rIdx % 2 === 0) style += 'background:#f0f7f0;';
                    html += '<' + tag + ' style="' + style + '">' + escHtml(val).replace(/\n/g, '<br>') + '</' + tag + '>';
                });
                html += '</tr>';
            });
            html += '</table></div>';
            var info = '<p style="font-size:12px;color:#888;margin:8px 0 0;">Dòng: ' + sheet.rows.length + ' | Cột: ' + (sheet.rows[0] ? sheet.rows[0].length : 0) + '</p>';
            $content.html(html + info);
        }

        function escHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function parseExcelFile(fileUrl, fileName) {
            $('#excel-preview-content').html('<p style="color:#217346;">⏳ Đang đọc file Excel...</p>');
            $('#excel-preview-area').show();

            var xhr = new XMLHttpRequest();
            xhr.open('GET', fileUrl, true);
            xhr.responseType = 'arraybuffer';
            xhr.onload = function() {
                if (xhr.status !== 200) {
                    $('#excel-preview-content').html('<p style="color:red;">Lỗi tải file: ' + xhr.status + '</p>');
                    return;
                }
                try {
                    var data = new Uint8Array(xhr.response);
                    var workbook = XLSX.read(data, {type: 'array', cellStyles: true, cellDates: true});
                    var sheetsData = [];
                    workbook.SheetNames.forEach(function(name) {
                        var ws = workbook.Sheets[name];
                        var jsonRows = XLSX.utils.sheet_to_json(ws, {header: 1, defval: '', raw: false});
                        if (jsonRows.length > 0) {
                            var maxCols = 0;
                            jsonRows.forEach(function(r){ if (r.length > maxCols) maxCols = r.length; });
                            jsonRows = jsonRows.map(function(r) {
                                while (r.length < maxCols) r.push('');
                                return r;
                            });
                        }
                        sheetsData.push({name: name, rows: jsonRows});
                    });
                    currentSheetData = sheetsData;
                    $('#excel_table_json').val(JSON.stringify(sheetsData));
                    renderSheetTabs(sheetsData);
                } catch(e) {
                    $('#excel-preview-content').html('<p style="color:red;">Lỗi đọc Excel: ' + e.message + '</p>');
                }
            };
            xhr.onerror = function() {
                $('#excel-preview-content').html('<p style="color:red;">Lỗi kết nối khi tải file</p>');
            };
            xhr.send();
        }

        $('#excel-upload-btn').on('click', function(e) {
            e.preventDefault();
            if (excelUploader) { excelUploader.open(); return; }
            excelUploader = wp.media({
                title: 'Chọn file Excel',
                button: {text: 'Chọn file này'},
                library: {type: ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel','text/csv']},
                multiple: false
            });
            excelUploader.on('select', function() {
                var attachment = excelUploader.state().get('selection').first().toJSON();
                $('#excel_file_id').val(attachment.id);
                $('#excel_file_name').val(attachment.filename);
                $('#excel-file-info').html('📎 <strong>' + attachment.filename + '</strong> <a href="' + attachment.url + '" target="_blank">(Tải xuống)</a>');
                $('#excel-remove-btn').show();
                $('#excel-reparse-bar').hide();
                parseExcelFile(attachment.url, attachment.filename);
            });
            excelUploader.open();
        });

        $('#excel-remove-btn').on('click', function() {
            if (!confirm('Xóa file Excel và dữ liệu bảng?')) return;
            $('#excel_file_id').val('');
            $('#excel_file_name').val('');
            $('#excel_table_json').val('');
            $('#excel-file-info').html('<span style="color:#999;">Chưa có file Excel</span>');
            $(this).hide();
            $('#excel-preview-area').hide();
            $('#excel-reparse-bar').hide();
            currentSheetData = null;
        });

        $('#excel-reparse-btn').on('click', function() {
            var fid = $('#excel_file_id').val();
            if (!fid) return;
            var $btn = $(this).prop('disabled', true).text('⏳ Đang parse...');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {action: 'dnttvn_get_attachment_url', attachment_id: fid, _wpnonce: '<?php echo wp_create_nonce("dnttvn_get_attachment_url"); ?>'},
                success: function(resp) {
                    if (resp.success && resp.data.url) {
                        parseExcelFile(resp.data.url, $('#excel_file_name').val());
                    }
                    $btn.prop('disabled', false).text('🔄 Parse lại từ file');
                },
                error: function() {
                    $btn.prop('disabled', false).text('🔄 Parse lại từ file');
                }
            });
        });

        if ($('#excel_file_id').val() && $('#excel_table_json').val()) {
            renderJsonPreview();
            $('#excel-reparse-bar').show();
        } else if ($('#excel_file_id').val() && !$('#excel_table_json').val()) {
            $('#excel-reparse-bar').show();
        }
    });
    </script>
    <style>
    #dnttvn-excel-upload-wrap table { font-size: 13px; }
    #excel-preview-area::-webkit-scrollbar { width: 6px; height: 6px; }
    #excel-preview-area::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
    </style>
    <?php
}

function dnttvn_get_attachment_url_ajax() {
    check_ajax_referer('dnttvn_get_attachment_url');
    $id = absint($_POST['attachment_id']);
    $url = $id ? wp_get_attachment_url($id) : '';
    if ($url) {
        wp_send_json_success(array('url' => $url));
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_dnttvn_get_attachment_url', 'dnttvn_get_attachment_url_ajax');

function dnttvn_save_excel_meta($post_id) {
    if (!isset($_POST['dnttvn_excel_nonce'])) return;
    if (!wp_verify_nonce($_POST['dnttvn_excel_nonce'], 'dnttvn_save_excel_data')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['excel_file_id'])) {
        $file_id = absint($_POST['excel_file_id']);
        if ($file_id > 0) {
            update_post_meta($post_id, '_excel_file_id', $file_id);
        } else {
            delete_post_meta($post_id, '_excel_file_id');
        }
    }
    if (isset($_POST['excel_file_name'])) {
        $name = sanitize_text_field($_POST['excel_file_name']);
        if ($name) {
            update_post_meta($post_id, '_excel_file_name', $name);
        } else {
            delete_post_meta($post_id, '_excel_file_name');
        }
    }
    if (isset($_POST['excel_table_json'])) {
        $json = wp_unslash($_POST['excel_table_json']);
        $decoded = json_decode($json, true);
        if (is_array($decoded) && !empty($decoded)) {
            update_post_meta($post_id, '_excel_table_data', $decoded);
        } else {
            delete_post_meta($post_id, '_excel_table_data');
        }
    }
}
add_action('save_post', 'dnttvn_save_excel_meta');

function dnttvn_render_excel_tables($post_id) {
    $file_name = get_post_meta($post_id, '_excel_file_name', true);
    $file_id = get_post_meta($post_id, '_excel_file_id', true);

    $sheets = get_post_meta($post_id, '_excel_table_data', true);
    if (!is_array($sheets) || empty($sheets)) {
        $json = get_post_meta($post_id, '_excel_table_json', true);
        if ($json && is_string($json)) {
            $sheets = json_decode($json, true);
        }
    }
    if (!is_array($sheets) || empty($sheets)) return;

    $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
    $has_multiple = count($sheets) > 1;

    echo '<div class="dnttvn-excel-section">';
    echo '<div class="dnttvn-excel-section-title">📊 Nội dung bảng dữ liệu</div>';

    if ($file_name) {
        echo '<div class="dnttvn-excel-file-info">📎 ' . esc_html($file_name);
        if ($file_url) {
            echo ' — <a href="' . esc_url($file_url) . '" target="_blank" rel="noopener" style="color: #667eea;">Tải file gốc</a>';
        }
        echo '</div>';
    }

    if ($has_multiple) {
        echo '<div class="dnttvn-excel-sheet-tabs">';
        foreach ($sheets as $idx => $sheet) {
            $name = !empty($sheet['name']) ? $sheet['name'] : 'Sheet ' . ($idx + 1);
            $active = $idx === 0 ? ' active' : '';
            echo '<div class="dnttvn-excel-sheet-tab' . $active . '" data-sheet="' . esc_attr($idx) . '">' . esc_html($name) . '</div>';
        }
        echo '</div>';
    }

    foreach ($sheets as $idx => $sheet) {
        $rows = isset($sheet['rows']) ? $sheet['rows'] : array();
        if (empty($rows)) continue;

        $active = $idx === 0 ? ' active' : '';
        $display = ($has_multiple && $idx > 0) ? '' : $active;
        echo '<div class="dnttvn-excel-sheet-content' . $display . '" data-sheet="' . esc_attr($idx) . '">';
        echo '<div class="dnttvn-table-scroll-wrapper">';
        echo '<div class="dnttvn-scroll-hint">← Kéo ngang để xem thêm →</div>';
        echo '<table class="dnttvn-editor-table">';

        foreach ($rows as $rIdx => $row) {
            echo '<tr>';
            if (!is_array($row)) { echo '</tr>'; continue; }
            foreach ($row as $cell) {
                $tag = ($rIdx === 0) ? 'th' : 'td';
                $val = ($cell === null || $cell === '') ? '&nbsp;' : nl2br(esc_html((string)$cell));
                echo '<' . $tag . '>' . $val . '</' . $tag . '>';
            }
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
}

function dnttvn_wrap_tables_in_scroll($content) {
    if (is_admin() || !is_string($content) || stripos($content, '<table') === false) {
        return $content;
    }
    $content = preg_replace(
        '/(<table(?:\s[^>]*)?>[\s\S]*?<\/table>)/i',
        '<div class="dnttvn-table-scroll-wrapper"><div class="dnttvn-scroll-hint">← Kéo ngang để xem thêm →</div>$1</div>',
        $content
    );
    return $content;
}
add_filter('the_content', 'dnttvn_wrap_tables_in_scroll', 99);
add_filter('dnttvn_display_content', 'dnttvn_wrap_tables_in_scroll', 99);

