<?php
/**
 * Template Name: Chi tiết Doanh nghiệp
 * 
 * Template for displaying single business detail page
 * Based on website/page-gioi-thieu-standalone.html layout
 */

get_header();

// Lấy post_id từ query để hiển thị chi tiết tin tức hoặc doanh nghiệp
$detail_post   = null;
$detail_post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;

if ($detail_post_id) {
    $detail_post = get_post($detail_post_id);
}
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
        <?php
        if ($detail_post && !is_wp_error($detail_post)) :
            setup_postdata($detail_post);
            $post_type = get_post_type($detail_post);

            // Nếu là doanh nghiệp: dùng layout business card
            if ($post_type === 'doanh_nghiep') :
            // Get custom fields
            $nganh_hang   = get_post_meta($detail_post_id, '_nganh_hang', true);
            $khu_vuc      = get_post_meta($detail_post_id, '_khu_vuc', true);
            $hinh_anh_phu = get_post_meta($detail_post_id, '_hinh_anh_phu', true);
            $gallery_raw  = get_post_meta($detail_post_id, '_gallery_images', true);
            $gallery_ids  = array_filter(array_map('absint', explode(',', (string) $gallery_raw)));
            $dia_chi      = get_post_meta($detail_post_id, '_dia_chi', true);
            $dien_thoai   = get_post_meta($detail_post_id, '_dien_thoai', true);
            $email_lh     = get_post_meta($detail_post_id, '_email_lien_he', true);
            $website_dn   = get_post_meta($detail_post_id, '_website_doanh_nghiep', true);
            $thong_tin_bs = get_post_meta($detail_post_id, '_thong_tin_bo_sung', true);
                
                // Get Featured Image (Hình chính)
                $featured_image_id  = get_post_thumbnail_id($detail_post_id);
                $featured_image_url = '';
                $featured_image_alt = '';
                if ($featured_image_id) {
                    $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                    $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                    if (empty($featured_image_alt)) {
                        $featured_image_alt = get_the_title($detail_post_id) . ' - Logo';
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
                            $small_image_alt = get_the_title($detail_post_id) . ' - Hình ảnh phụ';
                        }
                    }
                }
                
                // Get taxonomy terms
                $nganh_hang_terms = get_the_terms($detail_post_id, 'nganh_hang');
                $khu_vuc_terms    = get_the_terms($detail_post_id, 'khu_vuc');
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
                                <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(urlencode(get_the_title($detail_post_id))); ?>" 
                                     alt="<?php echo esc_attr(get_the_title($detail_post_id)); ?>" 
                                     class="business-main-image"
                                     loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="business-card-info-section">
                            <h4><?php echo esc_html(get_the_title($detail_post_id)); ?></h4>
                            
                            <?php if ($nganh_hang) : ?>
                                <div class="business-card-info">
                                    <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                    <p><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Đã bỏ hiển thị Ngành hàng (phân loại) để tránh trùng lặp -->
                            
                            <?php if ($khu_vuc) : ?>
                                <div class="business-card-info">
                                    <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                    <p><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Đã bỏ hiển thị Khu vực (phân loại) để tránh trùng lặp -->
                            
                            <p style="margin-top: 15px; color: #666; font-size: 14px;">
                                <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y', $detail_post_id); ?>
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
                                        $img_alt = get_the_title($detail_post_id) . ' - Hình ảnh ' . $img_id;
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
                                     alt="<?php echo esc_attr(get_the_title($detail_post_id) . ' - Hình ảnh phụ'); ?>" 
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
                            <?php echo apply_filters('the_content', $detail_post->post_content); ?>
                        </div>

                        <!-- Nội dung có cấu trúc (giống Tin tức) -->
                        <?php
                        $structured_content = get_post_meta($detail_post_id, '_structured_content', true);
                        if (!empty($structured_content)) {
                            $items = json_decode($structured_content, true);
                            if (is_array($items) && !empty($items)) :
                                ?>
                                <div class="structured-content-display" style="margin-top: 30px;">
                                    <?php foreach ($items as $item) : ?>
                                        <?php
                                        $has_content = !empty($item['heading']) || !empty($item['content']);
                                        $has_images = !empty($item['images']) && is_array($item['images']);
                                        if ($has_content || $has_images) :
                                        ?>
                                            <div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                                <?php if (!empty($item['heading'])) : ?>
                                                    <h3 style="font-size: 20px; font-weight: bold; color: #333; margin-bottom: 15px;">
                                                        <?php echo esc_html($item['heading']); ?>
                                                    </h3>
                                                <?php endif; ?>

                                                <?php
                                                // Display images if available
                                                $images = isset($item['images']) ? $item['images'] : array();
                                                $image_captions = isset($item['image_captions']) ? $item['image_captions'] : array();

                                                if (!empty($images) && is_array($images)) :
                                                    $main_image = $images[0]; // First image as main image
                                                    $thumbnail_images = array_slice($images, 1); // Rest as thumbnails
                                                ?>
                                                    <div class="structured-images-display" style="margin-bottom: 20px;">
                                                        <?php if ($main_image) : ?>
                                                            <div class="structured-main-image" style="text-align: center; margin-bottom: 15px;">
                                                                <?php
                                                                $main_image_url = wp_get_attachment_image_url($main_image, 'large');
                                                                $main_image_alt = !empty($image_captions[0]) ? $image_captions[0] : get_post_meta($main_image, '_wp_attachment_image_alt', true);
                                                                if (!$main_image_alt) $main_image_alt = esc_attr($item['heading']);
                                                                ?>
                                                                <img src="<?php echo esc_url($main_image_url); ?>"
                                                                     alt="<?php echo esc_attr($main_image_alt); ?>"
                                                                     style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;"
                                                                     class="structured-image-main"
                                                                     data-full-src="<?php echo esc_url($main_image_url); ?>"
                                                                     data-caption="<?php echo esc_attr(!empty($image_captions[0]) ? $image_captions[0] : ''); ?>">
                                                                <?php if (!empty($image_captions[0])) : ?>
                                                                    <p style="margin-top: 10px; font-size: 14px; color: #666; font-style: italic; text-align: center;">
                                                                        <?php echo esc_html($image_captions[0]); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($thumbnail_images)) : ?>
                                                            <div class="structured-thumbnails" style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-top: 15px;">
                                                                <?php foreach ($thumbnail_images as $index => $thumb_image) :
                                                                    $thumb_image_url = wp_get_attachment_image_url($thumb_image, 'medium');
                                                                    $thumb_caption = isset($image_captions[$index + 1]) ? $image_captions[$index + 1] : '';
                                                                    $thumb_alt = $thumb_caption ?: get_post_meta($thumb_image, '_wp_attachment_image_alt', true);
                                                                    if (!$thumb_alt) $thumb_alt = esc_attr($item['heading']);
                                                                ?>
                                                                    <div class="thumbnail-item" style="flex-shrink: 0;">
                                                                        <img src="<?php echo esc_url($thumb_image_url); ?>"
                                                                             alt="<?php echo esc_attr($thumb_alt); ?>"
                                                                             style="width: 100px; height: 75px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid #ddd; transition: border-color 0.3s;"
                                                                             class="structured-image-thumb"
                                                                             data-full-src="<?php echo esc_url(wp_get_attachment_image_url($thumb_image, 'large')); ?>"
                                                                             data-caption="<?php echo esc_attr($thumb_caption); ?>">
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
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
                    </div>
                </div>
                <?php
            
            // Nếu là tin tức: dùng layout tin tức chi tiết
            elseif ($post_type === 'tin_tuc') :
                ?>
                <div class="content-column">
                    <div class="column-header"><?php echo esc_html(get_the_title($detail_post_id)); ?></div>
                    <div class="column-content">
                        <div class="news-item">
                            <p class="news-date">
                                <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y', $detail_post_id); ?>
                                <?php 
                                $tac_gia = get_post_meta($detail_post_id, '_tin_tuc_tac_gia', true);
                                if ($tac_gia) {
                                    echo ' | <strong>Tác giả:</strong> ' . esc_html($tac_gia);
                                }
                                $nguon = get_post_meta($detail_post_id, '_tin_tuc_nguon', true);
                                if ($nguon) {
                                    echo ' | <strong>Nguồn:</strong> <a href="' . esc_url($nguon) . '" target="_blank">Xem nguồn</a>';
                                }
                                ?>
                            </p>
                            
                            <?php if (has_post_thumbnail($detail_post_id)) : ?>
                                <div style="margin-bottom: 20px; text-align: center;">
                                    <?php echo get_the_post_thumbnail($detail_post_id, 'large', array('style' => 'max-width: 100%; height: auto; border-radius: 8px;')); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div style="line-height: 1.8; font-size: 16px;">
                                <?php echo apply_filters('the_content', $detail_post->post_content); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            
            // Các loại post khác: hiển thị đơn giản
            else :
                ?>
                <div class="content-column">
                    <div class="column-header"><?php echo esc_html(get_the_title($detail_post_id)); ?></div>
                    <div class="column-content">
                        <div class="news-item">
                            <div style="line-height: 1.8; font-size: 16px;">
                                <?php echo apply_filters('the_content', $detail_post->post_content); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            endif;

            wp_reset_postdata();
        else :
            ?>
            <div class="content-column">
                <div class="column-header">Không tìm thấy nội dung</div>
                <div class="column-content">
                    <p>Không tìm thấy Tin tức hoặc Doanh nghiệp tương ứng. Vui lòng kiểm tra lại liên kết.</p>
                </div>
            </div>
            <?php
        endif;
        ?>
    </div>

    <!-- Lightbox xem ảnh lớn cho Doanh nghiệp (dùng chung) -->
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
                <?php echo dnttvn_render_banner_blocks('ad-block'); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
