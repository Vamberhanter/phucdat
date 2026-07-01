<?php
/**
 * Single Post Template for Sự kiện
 * Layout giống single-doanh_nhan (sidebar trái + nội dung + sidebar phải)
 */

get_header();
?>

<main class="main-content">
    <!-- Left Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Danh sách Sự kiện</div>
        <div class="column-content mobile-collapsed">
            <ul class="su-kien-sidebar-list">
                <?php
                $sk_query = new WP_Query(array(
                    'post_type'      => 'su_kien',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                ));
                if ($sk_query->have_posts()) :
                    while ($sk_query->have_posts()) : $sk_query->the_post();
                        $sk_thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                        $sk_ngay  = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
                        if (!$sk_ngay) $sk_ngay = get_post_meta(get_the_ID(), '_su_kien_ngay', true);
                        $li_class = (get_the_ID() === get_queried_object_id()) ? 'current-item' : '';
                        ?>
                        <li class="su-kien-sidebar-item <?php echo esc_attr($li_class); ?>">
                            <a href="<?php the_permalink(); ?>">
                                <?php if ($sk_thumb) : ?>
                                    <img src="<?php echo esc_url($sk_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="su-kien-thumb" loading="lazy" />
                                <?php else : ?>
                                    <span class="su-kien-thumb-placeholder"></span>
                                <?php endif; ?>
                                <span class="su-kien-info">
                                    <span class="su-kien-title"><?php the_title(); ?></span>
                                    <?php if ($sk_ngay) : ?>
                                        <span class="su-kien-date"><?php echo esc_html(date_i18n('d/m/Y', strtotime($sk_ngay))); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        </li>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
        </div>

        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
        <?php while (have_posts()) : the_post(); ?>
                <div class="column-header"><?php the_title(); ?></div>
                <div class="column-content">
        <?php
            $ngay_mo      = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
            $ngay_kt      = get_post_meta(get_the_ID(), '_su_kien_ngay_ket_thuc', true);
            if (!$ngay_mo) $ngay_mo = get_post_meta(get_the_ID(), '_su_kien_ngay', true);
            $dia_diem     = get_post_meta(get_the_ID(), '_su_kien_dia_diem', true);
            $video_url    = get_post_meta(get_the_ID(), '_su_kien_video_url', true);
            $gallery_raw  = get_post_meta(get_the_ID(), '_su_kien_gallery', true);
            $gallery_ids  = $gallery_raw ? array_filter(array_map('absint', explode(',', $gallery_raw))) : array();

            $featured_image_id  = get_post_thumbnail_id();
            $featured_image_url = '';
            $featured_image_alt = '';
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                if (empty($featured_image_alt)) {
                    $featured_image_alt = get_the_title();
                }
            }
        ?>
            <div class="business-card" style="max-width: 100%;">
                <div class="business-card-left">
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
                        <?php if ($ngay_mo) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                                </svg>
                                <p><strong>Ngày mở:</strong> <?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_mo))); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if ($ngay_kt) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                                </svg>
                                <p><strong>Ngày kết thúc:</strong> <?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_kt))); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($dia_diem) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Địa điểm:</strong> <?php echo esc_html($dia_diem); ?></p>
                            </div>
                        <?php endif; ?>

                        <p style="margin-top: 15px; color: #666; font-size: 14px;">
                            <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?>
                        </p>
                    </div>
                </div>
                <div class="business-card-content">
                    <?php if ($video_url) : ?>
                        <div style="margin-bottom: 20px;">
                            <?php
                            if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $yt_match)) {
                                echo '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;"><iframe src="https://www.youtube.com/embed/' . esc_attr($yt_match[1]) . '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen></iframe></div>';
                            } elseif (preg_match('/vimeo\.com\/(\d+)/', $video_url, $vm_match)) {
                                echo '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;"><iframe src="https://player.vimeo.com/video/' . esc_attr($vm_match[1]) . '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen></iframe></div>';
                            } else {
                                echo '<video controls style="max-width:100%;border-radius:8px;"><source src="' . esc_url($video_url) . '"></video>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($gallery_ids) : ?>
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <?php
                                $first = true;
                                foreach ($gallery_ids as $img_id) :
                                    $img_url = wp_get_attachment_image_url($img_id, 'large');
                                    if (!$img_url) continue;
                                    $img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                    if (empty($img_alt)) $img_alt = get_the_title() . ' - Hình ảnh';
                                    ?>
                                    <div class="business-card-small-image-slide <?php echo $first ? 'active' : ''; ?>"
                                         data-full="<?php echo esc_url($img_url); ?>"
                                         data-alt="<?php echo esc_attr($img_alt); ?>">
                                        <img src="<?php echo esc_url($img_url); ?>"
                                             alt="<?php echo esc_attr($img_alt); ?>"
                                             class="business-small-image"
                                             loading="lazy">
                                    </div>
                                    <?php $first = false;
                                endforeach; ?>
                            </div>
                            <div class="business-card-small-image-nav">
                                <button type="button" class="business-card-small-image-prev">&#10094;</button>
                                <button type="button" class="business-card-small-image-next">&#10095;</button>
                            </div>
                        </div>
                        <div class="business-card-gallery" style="margin-top: 12px;">
                            <?php foreach ($gallery_ids as $img_id) :
                                $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                if (!$thumb_url) continue;
                                $thumb_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                ?>
                                <img src="<?php echo esc_url($thumb_url); ?>"
                                     alt="<?php echo esc_attr($thumb_alt); ?>"
                                     data-full="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'large')); ?>"
                                     class="business-gallery-thumb" />
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                </div>

                <!-- Mô tả sự kiện (full width) -->
                <div class="business-card-description" style="font-size: 20px; line-height: 1.8;">
                    <h3 style="margin-top:0; margin-bottom:10px; font-size:18px; color:#06202e;">Mô tả chi tiết sự kiện</h3>
                    <?php the_content(); ?>
                </div>

                <?php
                $items = function_exists('dnttvn_get_structured_content_array') ? dnttvn_get_structured_content_array(get_the_ID()) : array();
                if (!empty($items)) :
                ?>
                <div class="structured-content-display" style="margin-top: 30px;">
                    <?php foreach ($items as $item) : ?>
                        <?php if (!empty($item['heading']) || !empty($item['content'])) : ?>
                            <div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                <?php if (!empty($item['heading'])) : ?>
                                    <h3 style="font-size: 26px; font-weight: bold; color: #333; margin-bottom: 15px;">
                                        <?php echo esc_html($item['heading']); ?>
                                    </h3>
                                <?php endif; ?>
                                <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                    <?php foreach ($item['content_items'] as $content_item) : if (is_string($content_item)) { $text = $content_item; $ci_images = []; $ci_captions = []; } else { $text = $content_item['text'] ?? ''; $ci_images = $content_item['images'] ?? []; $ci_captions = $content_item['image_captions'] ?? []; } ?>
                                        <div class="content-item-display" style="margin-bottom: 30px;">
                                            <?php if (!empty($text)) : ?><?php $c = $text; ?><div style="line-height: 1.8; font-size: 20px; color: #555; margin-bottom: 15px;"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div><?php endif; ?>
                                            <?php if (!empty($ci_images)) : ?><div class="content-item-images-gallery" style="display: flex; flex-wrap: wrap; gap: 15px;"><?php foreach ($ci_images as $ci_img_idx => $ci_img_id) : $ci_img_id = intval($ci_img_id); if ($ci_img_id > 0) : $ci_mime = get_post_mime_type($ci_img_id); $ci_is_video = strpos($ci_mime, 'video') === 0; $ci_url = wp_get_attachment_url($ci_img_id); $ci_caption = $ci_captions[$ci_img_idx] ?? ''; ?><div style="flex: 0 0 auto;"><?php if ($ci_is_video) : ?><video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="<?php echo esc_url($ci_url); ?>" type="<?php echo esc_attr($ci_mime); ?>"></video><?php else : ?><img src="<?php echo esc_url($ci_url); ?>" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;"><?php endif; ?><?php if ($ci_caption) : ?><p style="margin-top: 5px; font-size: 13px; color: #666; max-width: 250px;"><?php echo esc_html($ci_caption); ?></p><?php endif; ?></div><?php endif; endforeach; ?></div><?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php elseif (!empty($item['content'])) : ?>
                                    <div style="line-height: 1.8; font-size: 20px; color: #555;"><?php echo wpautop(wp_kses_post($item['content'])); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Navigation -->
                <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #667eea; display: flex; justify-content: space-between; flex-wrap: wrap;">
                    <div style="margin-bottom: 10px;">
                        <?php
                        $prev_post = get_previous_post();
                        if ($prev_post) : ?>
                            <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                &larr; <?php echo esc_html($prev_post->post_title); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php
                        $next_post = get_next_post();
                        if ($next_post) : ?>
                            <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo esc_html($next_post->post_title); ?> &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
        <?php endwhile; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sidebar-column">
        <?php get_template_part('template-parts/sidebar-su-kien'); ?>

        <div class="column-header mobile-toggle collapsed">Thông tin thêm</div>
        <div class="column-content mobile-collapsed">
            <?php
            if (function_exists('dnttvn_render_banner_blocks')) {
                $right_banner_html = dnttvn_render_banner_blocks('ad-block');
                if (!empty($right_banner_html)) :
                ?>
                <div class="ad-section">
                    <?php echo $right_banner_html; ?>
                </div>
                <?php endif;
            }
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
