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
            <th><label for="hinh_anh_phu">Hình ảnh phụ (URL hoặc ID)</label></th>
            <td>
                <input type="text" id="hinh_anh_phu" name="hinh_anh_phu" value="<?php echo esc_attr($hinh_anh_phu); ?>" class="regular-text" />
                <button type="button" class="button" id="upload_hinh_anh_phu">Chọn hình ảnh</button>
                <p class="description">Có thể nhập URL hoặc ID của hình ảnh đã upload</p>
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
}
add_action('after_setup_theme', 'dnttvn_theme_setup');
