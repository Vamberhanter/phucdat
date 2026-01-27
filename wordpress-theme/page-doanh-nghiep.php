<?php
/**
 * Template Name: Trang Doanh nghiệp
 * 
 * Template for displaying the business directory page
 */

get_header();
?>

<main class="main-content">
    <!-- Left Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <ul class="about-list">
                <li><a href="#">Điều lệ tổ chức hoạt động</a></li>
                <li><a href="#">Danh sách thành viên sáng lập</a></li>
                <li><a href="#">Cấu trúc Cộng đồng</a></li>
                <li><a href="#">Danh sách Lãnh đạo điều hành</a></li>
                <li class="highlight-item">
                    <a href="#">Tìm hiểu trở thành thành viên mới</a>
                </li>
                <li><a href="#">Giá trị nhận được của thành viên</a></li>
                <li><a href="#">Quy trình gia nhập Cộng đồng</a></li>
                <li><a href="#">Hỏi đáp về Cộng đồng</a></li>
            </ul>
        </div>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <!-- Top Search Section -->
        <div class="top-search-section">
            <h3>Tìm kiếm Doanh nghiệp</h3>
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>">
                <div class="search-form-row">
                    <div class="form-group">
                        <label>* Tên Doanh nghiệp</label>
                        <input type="text" name="ten_doanh_nghiep" placeholder="Nhập tên doanh nghiệp" value="<?php echo isset($_GET['ten_doanh_nghiep']) ? esc_attr($_GET['ten_doanh_nghiep']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>* Khu vực</label>
                        <select name="khu_vuc">
                            <option value="">Chọn khu vực</option>
                            <?php
                            $khu_vuc_terms = get_terms(array(
                                'taxonomy'   => 'khu_vuc',
                                'hide_empty' => false,
                            ));
                            foreach ($khu_vuc_terms as $term) {
                                $selected = (isset($_GET['khu_vuc']) && $_GET['khu_vuc'] == $term->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>* Ngành hàng</label>
                        <select name="nganh_hang">
                            <option value="">Chọn ngành hàng</option>
                            <?php
                            $nganh_hang_terms = get_terms(array(
                                'taxonomy'   => 'nganh_hang',
                                'hide_empty' => false,
                            ));
                            foreach ($nganh_hang_terms as $term) {
                                $selected = (isset($_GET['nganh_hang']) && $_GET['nganh_hang'] == $term->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="search-button">Tìm kiếm</button>
                </div>
            </form>
        </div>

        <!-- Sort Section -->
        <div class="sort-section">
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="sort-form">
                <?php
                // Preserve search and filter parameters
                if (isset($_GET['ten_doanh_nghiep']) && !empty($_GET['ten_doanh_nghiep'])) {
                    echo '<input type="hidden" name="ten_doanh_nghiep" value="' . esc_attr($_GET['ten_doanh_nghiep']) . '">';
                }
                if (isset($_GET['khu_vuc']) && !empty($_GET['khu_vuc'])) {
                    echo '<input type="hidden" name="khu_vuc" value="' . esc_attr($_GET['khu_vuc']) . '">';
                }
                if (isset($_GET['nganh_hang']) && !empty($_GET['nganh_hang'])) {
                    echo '<input type="hidden" name="nganh_hang" value="' . esc_attr($_GET['nganh_hang']) . '">';
                }
                ?>
                <div class="sort-controls">
                    <label for="sort_by">Sắp xếp theo:</label>
                    <select name="sort_by" id="sort_by" onchange="document.getElementById('sort-form').submit();">
                        <option value="menu_order" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'menu_order' ? 'selected' : ''; ?>>Thứ tự đăng bài</option>
                        <option value="date_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'date_desc') || !isset($_GET['sort_by']) ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="date_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'date_asc' ? 'selected' : ''; ?>>Cũ nhất</option>
                        <option value="title_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'title_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                        <option value="title_desc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'title_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                        <option value="nganh_hang" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'nganh_hang' ? 'selected' : ''; ?>>Ngành hàng</option>
                        <option value="khu_vuc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'khu_vuc' ? 'selected' : ''; ?>>Khu vực</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Business Cards Grid -->
        <div class="content-columns">
            <?php
            // Get current page number
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            
            // Get sort parameter
            $sort_by = isset($_GET['sort_by']) ? sanitize_text_field($_GET['sort_by']) : 'date_desc';
            
            $args = array(
                'post_type'      => 'doanh_nghiep',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'paged'          => $paged,
            );
            
            // Handle sorting
            switch ($sort_by) {
                case 'menu_order':
                    $args['orderby'] = 'menu_order date';
                    $args['order'] = 'ASC';
                    break;
                case 'date_asc':
                    $args['orderby'] = 'date';
                    $args['order'] = 'ASC';
                    break;
                case 'title_asc':
                    $args['orderby'] = 'title';
                    $args['order'] = 'ASC';
                    break;
                case 'title_desc':
                    $args['orderby'] = 'title';
                    $args['order'] = 'DESC';
                    break;
                case 'nganh_hang':
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = '_nganh_hang';
                    $args['order'] = 'ASC';
                    break;
                case 'khu_vuc':
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = '_khu_vuc';
                    $args['order'] = 'ASC';
                    break;
                case 'date_desc':
                default:
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                    break;
            }

            // Filter by search
            if (isset($_GET['ten_doanh_nghiep']) && !empty($_GET['ten_doanh_nghiep'])) {
                $args['s'] = sanitize_text_field($_GET['ten_doanh_nghiep']);
            }

            // Filter by taxonomy
            $tax_query = array();
            if (isset($_GET['khu_vuc']) && !empty($_GET['khu_vuc'])) {
                $tax_query[] = array(
                    'taxonomy' => 'khu_vuc',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['khu_vuc']),
                );
            }
            if (isset($_GET['nganh_hang']) && !empty($_GET['nganh_hang'])) {
                $tax_query[] = array(
                    'taxonomy' => 'nganh_hang',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['nganh_hang']),
                );
            }
            if (!empty($tax_query)) {
                $args['tax_query'] = $tax_query;
            }

            $doanh_nghiep_query = new WP_Query($args);

            if ($doanh_nghiep_query->have_posts()) :
                while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                    // Get custom fields
                    $nganh_hang = get_post_meta(get_the_ID(), '_nganh_hang', true);
                    $khu_vuc = get_post_meta(get_the_ID(), '_khu_vuc', true);
                    $hinh_anh_phu = get_post_meta(get_the_ID(), '_hinh_anh_phu', true);
                    
                    // Get Featured Image (Hình chính) - This is the main logo/image of the business
                    $featured_image_id = get_post_thumbnail_id();
                    $featured_image_url = '';
                    $featured_image_alt = '';
                    if ($featured_image_id) {
                        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'medium');
                        $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                        if (empty($featured_image_alt)) {
                            $featured_image_alt = get_the_title() . ' - Logo';
                        }
                    }
                    
                    // Get Hình ảnh phụ (Small image) - This is the additional image
                    $small_image_id = null;
                    $small_image_url = '';
                    $small_image_alt = '';
                    if ($hinh_anh_phu) {
                        // Check if it's a numeric ID
                        if (is_numeric($hinh_anh_phu)) {
                            $small_image_id = absint($hinh_anh_phu);
                        } else {
                            // Try to get attachment ID from URL
                            $small_image_id = attachment_url_to_postid($hinh_anh_phu);
                            if (!$small_image_id) {
                                // If not found, use as direct URL
                                $small_image_url = esc_url($hinh_anh_phu);
                            }
                        }
                        
                        if ($small_image_id) {
                            $small_image_url = wp_get_attachment_image_url($small_image_id, 'medium');
                            $small_image_alt = get_post_meta($small_image_id, '_wp_attachment_image_alt', true);
                            if (empty($small_image_alt)) {
                                $small_image_alt = get_the_title() . ' - Hình ảnh phụ';
                            }
                        }
                    }
                    
                    // Get description/excerpt
                    $description = '';
                    if (has_excerpt()) {
                        $description = get_the_excerpt();
                    } else {
                        $content = get_the_content();
                        $description = wp_trim_words(strip_shortcodes($content), 50, '...');
                    }
                    ?>
                    <div class="business-card">
                        <div class="business-card-left">
                            <!-- Hình chính (Featured Image) - Logo/Ảnh đại diện chính của doanh nghiệp -->
                            <div class="business-card-image">
                                <?php if ($featured_image_url) : ?>
                                    <img src="<?php echo esc_url($featured_image_url); ?>" 
                                         alt="<?php echo esc_attr($featured_image_alt); ?>" 
                                         class="business-main-image"
                                         loading="lazy">
                                <?php else : ?>
                                    <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(urlencode(get_the_title())); ?>" 
                                         alt="<?php echo esc_attr(get_the_title()); ?>" 
                                         class="business-main-image"
                                         loading="lazy">
                                <?php endif; ?>
                            </div>
                            <div class="business-card-info-section">
                                <h4><?php the_title(); ?></h4>
                                <?php if ($nganh_hang) : ?>
                                    <div class="business-card-info">
                                        <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                        <p><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($khu_vuc) : ?>
                                    <div class="business-card-info">
                                        <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                        <p><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="business-card-content">
                            <!-- Hình ảnh phụ (Small Image) - Ảnh bổ sung của doanh nghiệp -->
                            <?php if ($small_image_url || ($small_image_id && $small_image_url)) : ?>
                                <div class="business-card-small-image">
                                    <?php if ($small_image_id) : ?>
                                        <img src="<?php echo esc_url($small_image_url); ?>" 
                                             alt="<?php echo esc_attr($small_image_alt); ?>" 
                                             class="business-small-image"
                                             loading="lazy">
                                    <?php else : ?>
                                        <img src="<?php echo esc_url($small_image_url); ?>" 
                                             alt="<?php echo esc_attr(get_the_title() . ' - Hình ảnh phụ'); ?>" 
                                             class="business-small-image"
                                             loading="lazy">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <!-- Mô tả doanh nghiệp -->
                            <div class="business-card-description">
                                <?php if ($description) : ?>
                                    <p><?php echo wp_kses_post($description); ?></p>
                                <?php else : ?>
                                    <p><em>Chưa có mô tả.</em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                
                // Pagination
                if ($doanh_nghiep_query->max_num_pages > 1) :
                    ?>
                    <div class="pagination-wrapper">
                        <?php
                        echo dnttvn_custom_pagination($doanh_nghiep_query);
                        ?>
                    </div>
                    <?php
                endif;
            else :
                ?>
                <div class="business-card">
                    <p>Không tìm thấy doanh nghiệp nào. Vui lòng thêm doanh nghiệp từ trang quản trị WordPress.</p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Theo ngành hàng</div>
        <div class="column-content mobile-collapsed">
            <div class="ad-section">
                <?php
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
                
                // Display banners according to order
                foreach ($order_array as $banner_type) {
                    if (!isset($banner_data[$banner_type])) continue;
                    
                    $data = $banner_data[$banner_type];
                    $banners = $data['banners'];
                    $links = $data['links'];
                    
                    if (!empty($banners)) {
                        foreach ($banners as $index => $banner_id) {
                            if ($banner_id) {
                                $banner_url = wp_get_attachment_image_url($banner_id, 'full');
                                $banner_alt = get_post_meta($banner_id, '_wp_attachment_image_alt', true);
                                $link_url = isset($links[$index]) ? $links[$index] : '';
                                if ($banner_url) {
                                    ?>
                                    <div class="ad-block <?php echo esc_attr($data['type']); ?>">
                                        <h4><?php echo esc_html($data['title']); ?></h4>
                                        <div class="ad-type"><?php echo esc_html($data['label']); ?></div>
                                        <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                                        <?php if ($link_url) : ?>
                                            <a href="<?php echo esc_url($link_url); ?>" target="_blank">
                                        <?php endif; ?>
                                        <?php
                                        $mime_type = get_post_mime_type($banner_id);
                                        if (strpos($mime_type, 'video') !== false) {
                                            echo '<video src="' . esc_url($banner_url) . '" controls style="width: 100%; max-width: 100%;"></video>';
                                        } else {
                                            echo '<img src="' . esc_url($banner_url) . '" alt="' . esc_attr($banner_alt) . '" style="width: 100%; max-width: 100%;">';
                                        }
                                        ?>
                                        <?php if ($link_url) : ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    } else {
                        // Default placeholder
                        ?>
                        <div class="ad-block <?php echo esc_attr($data['type']); ?>">
                            <h4><?php echo esc_html($data['title']); ?></h4>
                            <div class="ad-type"><?php echo esc_html($data['label']); ?></div>
                            <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                            <div class="ad-placeholder">Banner/Video <?php echo esc_html($data['label']); ?></div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
