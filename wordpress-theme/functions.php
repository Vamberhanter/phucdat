<?php
/**
 * Functions and definitions for Cộng đồng Doanh nhân Trí tuệ Việt Nam Theme
 */

// Enqueue styles and scripts
function dnttvn_enqueue_styles() {
    wp_enqueue_style('dnttvn-main-style', get_template_directory_uri() . '/assets/style-gioi-thieu.css', array(), '1.0.0');
    wp_enqueue_script('dnttvn-main-script', get_template_directory_uri() . '/assets/script.js', array('jquery'), '1.0.0', true);
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
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
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
    );
    register_post_type('tin_tuc', $args);
}
add_action('init', 'dnttvn_register_tin_tuc_post_type', 0);

// Add custom meta boxes for Tin tức
function dnttvn_add_tin_tuc_meta_boxes() {
    add_meta_box(
        'tin_tuc_details',
        'Thông tin Tin tức',
        'dnttvn_tin_tuc_meta_box_callback',
        'tin_tuc',
        'normal',
        'high'
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

// Meta box callback for Tin tức details
function dnttvn_tin_tuc_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_tin_tuc_meta', 'dnttvn_tin_tuc_meta_nonce');
    
    $nguon = get_post_meta($post->ID, '_tin_tuc_nguon', true);
    $tac_gia = get_post_meta($post->ID, '_tin_tuc_tac_gia', true);
    $ngay_dang = get_post_meta($post->ID, '_tin_tuc_ngay_dang', true);
    $noi_bat = get_post_meta($post->ID, '_tin_tuc_noi_bat', true);
    
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
            <th><label for="tin_tuc_ngay_dang">Ngày đăng</label></th>
            <td>
                <input type="date" id="tin_tuc_ngay_dang" name="tin_tuc_ngay_dang" value="<?php echo esc_attr($ngay_dang); ?>" class="regular-text" />
                <p class="description">Ngày đăng tin (để trống sẽ dùng ngày hiện tại)</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_noi_bat">Tin nổi bật</label></th>
            <td>
                <label>
                    <input type="checkbox" id="tin_tuc_noi_bat" name="tin_tuc_noi_bat" value="1" <?php checked($noi_bat, '1'); ?> />
                    Đánh dấu tin này là tin nổi bật
                </label>
                <p class="description">Tin nổi bật sẽ được hiển thị ưu tiên trên trang chủ</p>
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
    
    if (isset($_POST['tin_tuc_ngay_dang'])) {
        update_post_meta($post_id, '_tin_tuc_ngay_dang', sanitize_text_field($_POST['tin_tuc_ngay_dang']));
    }
    
    if (isset($_POST['tin_tuc_noi_bat'])) {
        update_post_meta($post_id, '_tin_tuc_noi_bat', '1');
    } else {
        delete_post_meta($post_id, '_tin_tuc_noi_bat');
    }
}
add_action('save_post', 'dnttvn_save_tin_tuc_meta');

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

// Add admin styles for Tin tức editor
function dnttvn_tin_tuc_admin_styles() {
    global $post_type;
    if ($post_type == 'tin_tuc') {
        ?>
        <style>
            /* Improve editor area */
            #tin_tuc_details .inside {
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
            .postbox#tin_tuc_details {
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
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
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
    );
    register_post_type('doanh_nghiep', $args);
}
add_action('init', 'dnttvn_register_doanh_nghiep_post_type', 0);

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

// Add custom meta boxes for Doanh nghiệp
function dnttvn_add_doanh_nghiep_meta_boxes() {
    add_meta_box(
        'doanh_nghiep_details',
        'Thông tin Doanh nghiệp',
        'dnttvn_doanh_nghiep_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_doanh_nghiep_meta_boxes');

// Meta box callback
function dnttvn_doanh_nghiep_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_doanh_nghiep_meta', 'dnttvn_doanh_nghiep_meta_nonce');
    
    $nganh_hang = get_post_meta($post->ID, '_nganh_hang', true);
    $khu_vuc = get_post_meta($post->ID, '_khu_vuc', true);
    $hinh_anh_phu = get_post_meta($post->ID, '_hinh_anh_phu', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="nganh_hang">Ngành hàng</label></th>
            <td>
                <input type="text" id="nganh_hang" name="nganh_hang" value="<?php echo esc_attr($nganh_hang); ?>" class="regular-text" />
                <p class="description">Ví dụ: Công nghệ, Tài chính, Bán lẻ...</p>
            </td>
        </tr>
        <tr>
            <th><label for="khu_vuc">Khu vực</label></th>
            <td>
                <input type="text" id="khu_vuc" name="khu_vuc" value="<?php echo esc_attr($khu_vuc); ?>" class="regular-text" />
                <p class="description">Ví dụ: Hà Nội, TP. Hồ Chí Minh, Đà Nẵng...</p>
            </td>
        </tr>
        <tr>
            <th><label for="hinh_anh_phu">Hình ảnh phụ</label></th>
            <td>
                <input type="text" id="hinh_anh_phu" name="hinh_anh_phu" value="<?php echo esc_attr($hinh_anh_phu); ?>" class="regular-text" />
                <button type="button" class="button" id="upload_hinh_anh_phu">Chọn hình ảnh</button>
                <p class="description">
                    <strong>Lưu ý:</strong><br>
                    • <strong>Hình chính:</strong> Sử dụng "Featured Image" (Hình ảnh đại diện) ở sidebar bên phải - đây là logo/ảnh chính của doanh nghiệp<br>
                    • <strong>Hình ảnh phụ:</strong> Ảnh bổ sung hiển thị ở phần mô tả (có thể để trống). Nhập ID hoặc URL của hình ảnh, hoặc click "Chọn hình ảnh" để upload.
                </p>
                <?php if ($hinh_anh_phu) : ?>
                    <?php
                    $preview_id = is_numeric($hinh_anh_phu) ? absint($hinh_anh_phu) : attachment_url_to_postid($hinh_anh_phu);
                    if ($preview_id) {
                        echo '<div style="margin-top: 10px;">';
                        echo wp_get_attachment_image($preview_id, 'thumbnail');
                        echo '</div>';
                    } elseif (filter_var($hinh_anh_phu, FILTER_VALIDATE_URL)) {
                        echo '<div style="margin-top: 10px;"><img src="' . esc_url($hinh_anh_phu) . '" style="max-width: 150px; height: auto;" /></div>';
                    }
                    ?>
                <?php endif; ?>
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
    
    if (isset($_POST['nganh_hang'])) {
        update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_POST['nganh_hang']));
    }
    
    if (isset($_POST['khu_vuc'])) {
        update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_POST['khu_vuc']));
    }
    
    if (isset($_POST['hinh_anh_phu'])) {
        update_post_meta($post_id, '_hinh_anh_phu', sanitize_text_field($_POST['hinh_anh_phu']));
    }
}
add_action('save_post', 'dnttvn_save_doanh_nghiep_meta');

// Theme support
function dnttvn_theme_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    
    // Register navigation menu
    register_nav_menus(array(
        'primary' => 'Menu chính',
    ));
}
add_action('after_setup_theme', 'dnttvn_theme_setup');

// Auto-create "Danh sách Doanh nghiệp" page on theme activation
function dnttvn_create_doanh_nghiep_page() {
    // Check if page already exists
    $page_slug = 'page-doanh-nghiep';
    $existing_page = get_page_by_path($page_slug);
    
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
        // Update existing page to use correct template
        update_post_meta($existing_page->ID, '_wp_page_template', 'page-doanh-nghiep.php');
    }
}
add_action('after_switch_theme', 'dnttvn_create_doanh_nghiep_page');

// Default menu fallback
function dnttvn_default_menu() {
    echo '<ul class="menu" id="mainMenu">';
    echo '<li><a href="' . esc_url(home_url()) . '">Trang chủ</a></li>';
    $tin_tuc_link = get_post_type_archive_link('tin_tuc');
    if ($tin_tuc_link) {
        echo '<li><a href="' . esc_url($tin_tuc_link) . '">Tin tức</a></li>';
    }
    $doanh_nghiep_link = get_post_type_archive_link('doanh_nghiep');
    if ($doanh_nghiep_link) {
        echo '<li><a href="' . esc_url($doanh_nghiep_link) . '">Doanh nghiệp</a></li>';
    }
    $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
    if ($page_doanh_nghiep) {
        echo '<li><a href="' . esc_url(get_permalink($page_doanh_nghiep->ID)) . '">Danh sách Doanh nghiệp</a></li>';
    }
    echo '<li><a href="#">Giới thiệu</a></li>';
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
    $new_columns['nganh_hang'] = 'Ngành hàng';
    $new_columns['khu_vuc'] = 'Khu vực';
    $new_columns['nganh_hang_tax'] = 'Ngành hàng (Taxonomy)';
    $new_columns['khu_vuc_tax'] = 'Khu vực (Taxonomy)';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_doanh_nghiep_posts_columns', 'dnttvn_add_doanh_nghiep_admin_columns');

// Populate Admin Columns for Doanh nghiệp
function dnttvn_populate_doanh_nghiep_admin_columns($column, $post_id) {
    switch ($column) {
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
    if ($orderby == 'nganh_hang' || $orderby == 'khu_vuc') {
        $query->set('meta_key', '_' . $orderby);
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'dnttvn_handle_doanh_nghiep_sorting');

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

// Enqueue Admin Scripts for Media Uploader
function dnttvn_enqueue_admin_scripts($hook) {
    global $post_type;
    
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        if ($post_type == 'doanh_nghiep') {
            wp_enqueue_media();
            wp_enqueue_script('dnttvn-admin-script', get_template_directory_uri() . '/assets/admin-script.js', array('jquery'), '1.0.0', true);
        }
        
        // Enable full editor for Tin tức
        if ($post_type == 'tin_tuc') {
            wp_enqueue_media();
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
    $current_page = max(1, get_query_var('paged'));
    
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
    
    $base = str_replace($big, '%#%', esc_url(get_pagenum_link($big)));
    if (!empty($query_args)) {
        $base = add_query_arg($query_args, $base);
    }
    
    $pagination = paginate_links(array(
        'base'      => $base,
        'format'    => '?paged=%#%',
        'current'   => $current_page,
        'total'     => $total_pages,
        'prev_text' => '&laquo; Trước',
        'next_text' => 'Sau &raquo;',
        'type'      => 'list',
        'end_size'  => 2,
        'mid_size'  => 2,
    ));
    
    return '<div class="pagination">' . $pagination . '</div>';
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
        'mime_type' => 'image',
    )));
    
    // Banner 2
    $wp_customize->add_setting('dnttvn_banner_2', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_2', array(
        'label'     => 'Banner 2',
        'section'   => 'dnttvn_header_banners',
        'mime_type' => 'image',
    )));
    
    // Banner 3
    $wp_customize->add_setting('dnttvn_banner_3', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_3', array(
        'label'     => 'Banner 3',
        'section'   => 'dnttvn_header_banners',
        'mime_type' => 'image',
    )));
    
    // Banner 4
    $wp_customize->add_setting('dnttvn_banner_4', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_4', array(
        'label'     => 'Banner 4',
        'section'   => 'dnttvn_header_banners',
        'mime_type' => 'image',
    )));
    
    // Banner 5
    $wp_customize->add_setting('dnttvn_banner_5', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_5', array(
        'label'     => 'Banner 5',
        'section'   => 'dnttvn_header_banners',
        'mime_type' => 'image',
    )));
}
add_action('customize_register', 'dnttvn_customize_register');

// Create Admin Settings Page for Ad Blocks
function dnttvn_add_admin_menu() {
    add_menu_page(
        'Quản lý Banner',
        'Quản lý Banner',
        'manage_options',
        'dnttvn-banner-settings',
        'dnttvn_banner_settings_page',
        'dashicons-images-alt2',
        30
    );
}
add_action('admin_menu', 'dnttvn_add_admin_menu');

// Banner Settings Page
function dnttvn_banner_settings_page() {
    if (isset($_POST['dnttvn_save_banners']) && check_admin_referer('dnttvn_save_banners_action', 'dnttvn_save_banners_nonce')) {
        // Save VVIP Ad Blocks
        if (isset($_POST['vvip_banners'])) {
            update_option('dnttvn_vvip_banners', array_map('absint', $_POST['vvip_banners']));
        }
        if (isset($_POST['vvip_links'])) {
            update_option('dnttvn_vvip_links', array_map('esc_url_raw', $_POST['vvip_links']));
        }
        
        // Save VIP Ad Blocks
        if (isset($_POST['vip_banners'])) {
            update_option('dnttvn_vip_banners', array_map('absint', $_POST['vip_banners']));
        }
        if (isset($_POST['vip_links'])) {
            update_option('dnttvn_vip_links', array_map('esc_url_raw', $_POST['vip_links']));
        }
        
        // Save Standard Ad Blocks
        if (isset($_POST['standard_banners'])) {
            update_option('dnttvn_standard_banners', array_map('absint', $_POST['standard_banners']));
        }
        if (isset($_POST['standard_links'])) {
            update_option('dnttvn_standard_links', array_map('esc_url_raw', $_POST['standard_links']));
        }
        
        echo '<div class="notice notice-success"><p>Đã lưu banner thành công!</p></div>';
    }
    
    $vvip_banners = get_option('dnttvn_vvip_banners', array());
    $vvip_links = get_option('dnttvn_vvip_links', array());
    $vip_banners = get_option('dnttvn_vip_banners', array());
    $vip_links = get_option('dnttvn_vip_links', array());
    $standard_banners = get_option('dnttvn_standard_banners', array());
    $standard_links = get_option('dnttvn_standard_links', array());
    
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Banner</h1>
        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_banners_action', 'dnttvn_save_banners_nonce'); ?>
            
            <h2>Banner VVIP (Tối đa 2 banner)</h2>
            <div id="vvip-banners-container">
                <?php
                $vvip_count = max(2, count($vvip_banners));
                for ($i = 0; $i < $vvip_count; $i++) {
                    $banner_id = isset($vvip_banners[$i]) ? $vvip_banners[$i] : '';
                    $banner_url = isset($vvip_links[$i]) ? $vvip_links[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VVIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label>Hình ảnh/Video:</label><br>
                            <input type="hidden" name="vvip_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button upload-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">Xóa</button>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : ?>
                                <?php echo wp_get_attachment_image($banner_id, 'medium'); ?>
                            <?php endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vvip_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            
            <h2>Banner VIP (Tối đa 2 banner)</h2>
            <div id="vip-banners-container">
                <?php
                $vip_count = max(2, count($vip_banners));
                for ($i = 0; $i < $vip_count; $i++) {
                    $banner_id = isset($vip_banners[$i]) ? $vip_banners[$i] : '';
                    $banner_url = isset($vip_links[$i]) ? $vip_links[$i] : '';
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
                            <?php if ($banner_id) : ?>
                                <?php echo wp_get_attachment_image($banner_id, 'medium'); ?>
                            <?php endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vip_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            
            <h2>Banner Standard (Tối đa 2 banner)</h2>
            <div id="standard-banners-container">
                <?php
                $standard_count = max(2, count($standard_banners));
                for ($i = 0; $i < $standard_count; $i++) {
                    $banner_id = isset($standard_banners[$i]) ? $standard_banners[$i] : '';
                    $banner_url = isset($standard_links[$i]) ? $standard_links[$i] : '';
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
                            <?php if ($banner_id) : ?>
                                <?php echo wp_get_attachment_image($banner_id, 'medium'); ?>
                            <?php endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="standard_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                    </div>
                    <?php
                }
                ?>
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
                if (attachment.type === 'image') {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 300px;">');
                } else if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width: 300px;"></video>');
                }
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

// Enqueue admin script for banner management
function dnttvn_enqueue_banner_admin_scripts($hook) {
    if ($hook != 'toplevel_page_dnttvn-banner-settings') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_banner_admin_scripts');
