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
            $nganh_hang    = get_post_meta(get_the_ID(), '_nganh_hang', true);
            $khu_vuc       = get_post_meta(get_the_ID(), '_khu_vuc', true);
            $hinh_anh_phu  = get_post_meta(get_the_ID(), '_hinh_anh_phu', true);
            $gallery_raw   = get_post_meta(get_the_ID(), '_gallery_images', true);
            $gallery_ids   = array_filter(array_map('absint', explode(',', (string) $gallery_raw)));
            $dia_chi       = get_post_meta(get_the_ID(), '_dia_chi', true);
            $dien_thoai    = get_post_meta(get_the_ID(), '_dien_thoai', true);
            $email_lh      = get_post_meta(get_the_ID(), '_email_lien_he', true);
            $website_dn    = get_post_meta(get_the_ID(), '_website_doanh_nghiep', true);
            $thong_tin_bs  = get_post_meta(get_the_ID(), '_thong_tin_bo_sung', true);
            
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
            
                    // Get Hình ảnh phụ (Small image) - single fallback if no gallery
                    $small_image_id  = null;
                    $small_image_url = '';
                    $small_image_alt = '';
                    if (!$gallery_ids && $hinh_anh_phu) {
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
                        
                        <?php if ($khu_vuc) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Đã bỏ hiển thị lặp lại Ngành hàng / Khu vực theo taxonomy để tránh trùng lặp -->
                        
                        <p style="margin-top: 15px; color: #666; font-size: 14px;">
                            <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?>
                        </p>
                    </div>
                </div>
                <div class="business-card-content">
                    <!-- Hình ảnh phụ / Thư viện hình -->
                    <?php if ($gallery_ids) : ?>
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <?php
                                $first = true;
                                foreach ($gallery_ids as $img_id) :
                                    $img_url = wp_get_attachment_image_url($img_id, 'large');
                                    if (!$img_url) {
                                        continue;
                                    }
                                    $img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                    if (empty($img_alt)) {
                                        $img_alt = get_the_title() . ' - Hình ảnh ' . $img_id;
                                    }
                                    ?>
                                    <div class="business-card-small-image-slide <?php echo $first ? 'active' : ''; ?>"
                                         data-full="<?php echo esc_url($img_url); ?>"
                                         data-alt="<?php echo esc_attr($img_alt); ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" 
                                             alt="<?php echo esc_attr($img_alt); ?>" 
                                             class="business-small-image"
                                             loading="lazy">
                                    </div>
                                    <?php
                                    $first = false;
                                endforeach;
                                ?>
                            </div>
                            <div class="business-card-small-image-nav">
                                <button type="button" class="business-card-small-image-prev">&#10094;</button>
                                <button type="button" class="business-card-small-image-next">&#10095;</button>
                            </div>
                        </div>

                        <!-- Thư viện hình (thumbnail dưới dạng lưới) -->
                        <div class="business-card-gallery" style="margin-top: 12px;">
                            <?php
                            foreach ($gallery_ids as $img_id) :
                                $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                if (!$thumb_url) {
                                    $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                                }
                                if (!$thumb_url) {
                                    continue;
                                }
                                $thumb_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                ?>
                                <img src="<?php echo esc_url($thumb_url); ?>"
                                     alt="<?php echo esc_attr($thumb_alt); ?>"
                                     data-full="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'large')); ?>"
                                     class="business-gallery-thumb" />
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($small_image_url || ($small_image_id && $small_image_url)) : ?>
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
                    
                    <!-- Thông tin bổ sung (Địa chỉ, Điện thoại, Email, Website) -->
                    <?php if ($dia_chi || $dien_thoai || $email_lh || $website_dn || $thong_tin_bs) : ?>
                        <div style="margin-bottom: 25px; padding: 15px 18px; border-radius: 8px; background:#f8f9fa; border:1px solid #e0e0e0;">
                            <h3 style="margin-top:0; margin-bottom:12px; font-size:18px; color:#06202e;">Thông tin liên hệ & bổ sung</h3>
                            <ul style="list-style:none; margin:0; padding:0; font-size:14px; color:#444;">
                                <?php if ($dia_chi) : ?>
                                    <li style="margin-bottom:6px;"><strong>Địa chỉ:</strong> <?php echo esc_html($dia_chi); ?></li>
                                <?php endif; ?>
                                <?php if ($dien_thoai) : ?>
                                    <li style="margin-bottom:6px;"><strong>Điện thoại:</strong> <?php echo esc_html($dien_thoai); ?></li>
                                <?php endif; ?>
                                <?php if ($email_lh) : ?>
                                    <li style="margin-bottom:6px;"><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($email_lh); ?>"><?php echo esc_html($email_lh); ?></a></li>
                                <?php endif; ?>
                                <?php if ($website_dn) : ?>
                                    <?php
                                    $website_raw = trim($website_dn);
                                    $website_href = $website_raw;
                                    if ($website_href && !preg_match('~^https?://~i', $website_href)) {
                                        $website_href = '//' . $website_href;
                                    }
                                    ?>
                                    <li style="margin-bottom:6px;"><strong>Website:</strong> <a href="<?php echo esc_url($website_href); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($website_raw); ?></a></li>
                                <?php endif; ?>
                            </ul>
                            <?php if ($thong_tin_bs) : ?>
                                <div style="margin-top:10px; font-size:14px; color:#555; line-height:1.7;">
                                    <?php echo wp_kses_post(wpautop($thong_tin_bs)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Mô tả doanh nghiệp -->
                    <div class="business-card-description" style="font-size: 16px; line-height: 1.8;">
                        <h3 style="margin-top:0; margin-bottom:10px; font-size:18px; color:#06202e;">Mô tả chi tiết doanh nghiệp</h3>
                        <?php the_content(); ?>
                    </div>

                    <!-- Nội dung có cấu trúc (giống Tin tức) -->
                    <?php
                    $structured_content = get_post_meta(get_the_ID(), '_structured_content', true);
                    if (!empty($structured_content)) {
                        $items = json_decode($structured_content, true);
                        if (is_array($items) && !empty($items)) :
                            ?>
                            <div class="structured-content-display" style="margin-top: 30px;">
                                <?php foreach ($items as $item) : ?>
                                    <?php if (!empty($item['heading']) || !empty($item['content'])) : ?>
                                        <div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                            <?php if (!empty($item['heading'])) : ?>
                                                <h3 style="font-size: 20px; font-weight: bold; color: #333; margin-bottom: 15px;">
                                                    <?php echo esc_html($item['heading']); ?>
                                                </h3>
                                            <?php endif; ?>
                                            <?php if (!empty($item['content'])) : ?>
                                                <div style="line-height: 1.8; font-size: 16px; color: #555;">
                                                    <?php echo wp_kses_post(wpautop($item['content'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php
                        endif;
                    }
                    ?>

                    <!-- (Khối Thông tin bổ sung đã được dời lên phía trên mô tả) -->
                    
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

    <!-- Lightbox xem ảnh lớn cho Doanh nghiệp -->
    <div class="business-lightbox" id="business-lightbox" aria-hidden="true">
        <div class="business-lightbox-backdrop"></div>
        <div class="business-lightbox-inner">
            <button type="button" class="business-lightbox-close" aria-label="Đóng">×</button>
            <button type="button" class="business-lightbox-prev" aria-label="Ảnh trước">&#10094;</button>
            <img src="" alt="" class="business-lightbox-image" />
            <button type="button" class="business-lightbox-next" aria-label="Ảnh sau">&#10095;</button>
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
