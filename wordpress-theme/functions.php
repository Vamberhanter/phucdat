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
    
    // Register navigation menu
    register_nav_menus(array(
        'primary' => 'Menu chính',
    ));
}
add_action('after_setup_theme', 'dnttvn_theme_setup');

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
