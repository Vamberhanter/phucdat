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
                <?php
                // Hiển thị danh sách các bài viết Cộng đồng ở cột trái (đồng bộ với trang chủ)
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
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <li><a href="#">Chưa có bài viết Cộng đồng</a></li>
                    <?php
                endif;
                ?>
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
            // For custom page templates, use $_GET['paged'] instead of get_query_var('paged')
            $paged = 1;
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $paged = absint($_GET['paged']);
            }
            
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

            // Get detail page for business/news
            $detail_page      = get_page_by_path('trang-doanh-nghiep-chi-tiet');
            $detail_page_base = $detail_page ? get_permalink($detail_page->ID) : home_url('/trang-doanh-nghiep-chi-tiet/');

            if ($doanh_nghiep_query->have_posts()) :
                $post_count = 0;
                while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                    $post_count++;
                    // Get custom fields
                    $nganh_hang = get_post_meta(get_the_ID(), '_nganh_hang', true);
                    $khu_vuc    = get_post_meta(get_the_ID(), '_khu_vuc', true);
                    $hinh_anh_phu = get_post_meta(get_the_ID(), '_hinh_anh_phu', true);
                    
                    // Get taxonomy terms as fallback nếu meta trống
                    $nganh_hang_terms = get_the_terms(get_the_ID(), 'nganh_hang');
                    $khu_vuc_terms    = get_the_terms(get_the_ID(), 'khu_vuc');
                    
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
                    
                    // Get description/excerpt (ưu tiên Mô tả ngắn trong meta)
                    $description = get_post_meta(get_the_ID(), '_doanh_nghiep_mo_ta_ngan', true);
                    if (!$description) {
                        if (has_excerpt()) {
                            $description = get_the_excerpt();
                        } else {
                            $content = get_the_content();
                            $description = wp_trim_words(strip_shortcodes($content), 50, '...');
                        }
                    }
                    // Build detail URL with post_id for this business
                    $detail_url = add_query_arg(
                        'post_id',
                        get_the_ID(),
                        $detail_page_base
                    );
                    ?>
                    <a href="<?php echo esc_url($detail_url); ?>" class="business-card-link">
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
                                <?php
                                // Ưu tiên dùng meta; nếu trống thì dùng tên taxonomy
                                if (!$nganh_hang && $nganh_hang_terms && !is_wp_error($nganh_hang_terms)) {
                                    $term_names = wp_list_pluck($nganh_hang_terms, 'name');
                                    $nganh_hang = implode(', ', $term_names);
                                }
                                if (!$khu_vuc && $khu_vuc_terms && !is_wp_error($khu_vuc_terms)) {
                                    $term_names = wp_list_pluck($khu_vuc_terms, 'name');
                                    $khu_vuc    = implode(', ', $term_names);
                                }
                                ?>
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
                    </a>
                    <?php
                    // View mobile: chèn banner xen kẽ 1 thẻ DN – 1 banner,
                    // và vẫn giữ thứ tự theo toàn bộ danh sách (tính cả khi phân trang).
                    // posts_per_page hiện đang là 6
                    $posts_per_page   = $args['posts_per_page'];
                    $global_index     = ($paged - 1) * $posts_per_page + ($post_count - 1); // 0-based index
                    $mobile_banner_html = dnttvn_render_banner_blocks('ad-block-mobile', $global_index, 1);
                    if (!empty($mobile_banner_html)) {
                        echo '<div class="ad-section-mobile">' . $mobile_banner_html . '</div>';
                    }
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
                <?php echo dnttvn_render_banner_blocks('ad-block'); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
