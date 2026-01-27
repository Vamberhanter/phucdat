<?php
/**
 * Single Post Template for Doanh nghiệp
 * Layout giống trang page-doanh-nghiep với sidebar trái và phải
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
        <?php while (have_posts()) : the_post(); 
            // Get custom fields
            $nganh_hang = get_post_meta(get_the_ID(), '_nganh_hang', true);
            $khu_vuc = get_post_meta(get_the_ID(), '_khu_vuc', true);
            $hinh_anh_phu = get_post_meta(get_the_ID(), '_hinh_anh_phu', true);
            
            // Get Featured Image (Hình chính)
            $featured_image_id = get_post_thumbnail_id();
            $featured_image_url = '';
            $featured_image_alt = '';
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                if (empty($featured_image_alt)) {
                    $featured_image_alt = get_the_title() . ' - Logo';
                }
            }
            
            // Get Hình ảnh phụ (Small image)
            $small_image_id = null;
            $small_image_url = '';
            $small_image_alt = '';
            if ($hinh_anh_phu) {
                if (is_numeric($hinh_anh_phu)) {
                    $small_image_id = absint($hinh_anh_phu);
                } else {
                    $small_image_id = attachment_url_to_postid($hinh_anh_phu);
                    if (!$small_image_id) {
                        $small_image_url = esc_url($hinh_anh_phu);
                    }
                }
                
                if ($small_image_id) {
                    $small_image_url = wp_get_attachment_image_url($small_image_id, 'full');
                    $small_image_alt = get_post_meta($small_image_id, '_wp_attachment_image_alt', true);
                    if (empty($small_image_alt)) {
                        $small_image_alt = get_the_title() . ' - Hình ảnh phụ';
                    }
                }
            }
            
            // Get taxonomy terms
            $nganh_hang_terms = get_the_terms(get_the_ID(), 'nganh_hang');
            $khu_vuc_terms = get_the_terms(get_the_ID(), 'khu_vuc');
            ?>
            <div class="business-card" style="max-width: 100%;">
                <div class="business-card-left">
                    <!-- Hình chính (Featured Image) -->
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
                        
                        <?php if ($nganh_hang_terms && !is_wp_error($nganh_hang_terms)) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng (Taxonomy):</strong> 
                                    <?php
                                    $term_names = array();
                                    foreach ($nganh_hang_terms as $term) {
                                        $term_names[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                                    }
                                    echo implode(', ', $term_names);
                                    ?>
                                </p>
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
                        
                        <?php if ($khu_vuc_terms && !is_wp_error($khu_vuc_terms)) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực (Taxonomy):</strong> 
                                    <?php
                                    $term_names = array();
                                    foreach ($khu_vuc_terms as $term) {
                                        $term_names[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                                    }
                                    echo implode(', ', $term_names);
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <p style="margin-top: 15px; color: #666; font-size: 14px;">
                            <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?>
                        </p>
                    </div>
                </div>
                <div class="business-card-content">
                    <!-- Hình ảnh phụ (Small Image) -->
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
                    <div class="business-card-description" style="font-size: 16px; line-height: 1.8;">
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Navigation to previous/next post -->
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #667eea; display: flex; justify-content: space-between; flex-wrap: wrap;">
                        <div style="margin-bottom: 10px;">
                            <?php
                            $prev_post = get_previous_post(false, '', 'nganh_hang');
                            if (!$prev_post) {
                                $prev_post = get_previous_post();
                            }
                            if ($prev_post) :
                                ?>
                                <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                    ← <?php echo esc_html($prev_post->post_title); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php
                            $next_post = get_next_post(false, '', 'nganh_hang');
                            if (!$next_post) {
                                $next_post = get_next_post();
                            }
                            if ($next_post) :
                                ?>
                                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                    <?php echo esc_html($next_post->post_title); ?> →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
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
