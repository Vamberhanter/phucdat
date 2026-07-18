<?php
/**
 * Template Name: Chi Tiết Doanh nghiệp
 * Description: Template hiển thị chi tiết doanh nghiệp tại trang trang-doanh-nghiep-chi-tiet
 */

get_header();

$trang_dn_detail_url = home_url('/trang-doanh-nghiep-chi-tiet/');
$post_id_param = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
$doanh_nghiep_post = ($post_id_param > 0) ? get_post($post_id_param) : null;
$current_doanh_nghiep_id = ($doanh_nghiep_post && $doanh_nghiep_post->post_type === 'doanh_nghiep') ? $doanh_nghiep_post->ID : 0;

$page_doanh_nghiep = get_page_by_path('danh-sach-doanh-nghiep');
if (!$page_doanh_nghiep) {
    $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
}
$doanh_nghiep_list_url = $page_doanh_nghiep ? get_permalink($page_doanh_nghiep->ID) : home_url('/danh-sach-doanh-nghiep/');
?>

<div class="dn-directory-outer dn-directory-outer--boxed">
<main class="main-content dn-directory-main dn-directory-main--detail">
    <!-- Left Sidebar: chỉ Danh sách Doanh nghiệp (không Cộng đồng / Tin tức) -->
    <div class="sidebar-column">
        <div class="dn-sidebar-dn-list">
            <div class="column-header mobile-toggle collapsed">Danh Mục Hàng</div>
            <div class="column-content mobile-collapsed">
                <ul class="linked-websites">
                    <li><a href="<?php echo esc_url($doanh_nghiep_list_url); ?>">Danh sách Doanh nghiệp</a></li>
                    <?php
                    $doanh_nghiep_args = array(
                        'post_type'      => 'doanh_nghiep',
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                        'orderby'        => 'menu_order date',
                        'order'          => 'ASC',
                    );
                    $doanh_nghiep_query = new WP_Query($doanh_nghiep_args);
                    if ($doanh_nghiep_query->have_posts()) :
                        while ($doanh_nghiep_query->have_posts()) :
                            $doanh_nghiep_query->the_post();
                            $li_class = ((int) $current_doanh_nghiep_id === (int) get_the_ID()) ? 'current-item' : '';
                            ?>
                            <li class="<?php echo esc_attr($li_class); ?>">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </li>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </ul>
            </div>
        </div>

        <div class="column-header mobile-toggle collapsed">Web liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                foreach ($community_links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <?php
        if ($current_doanh_nghiep_id > 0 && $doanh_nghiep_post) {
            global $post;
            $original_post = $post;
            $post = $doanh_nghiep_post;
            setup_postdata($post);

            $nganh_hang    = get_post_meta($post->ID, '_nganh_hang', true);
            $khu_vuc       = get_post_meta($post->ID, '_khu_vuc', true);
            $hinh_anh_phu  = get_post_meta($post->ID, '_hinh_anh_phu', true);
            $gallery_raw   = get_post_meta($post->ID, '_gallery_images', true);
            $gallery_ids   = array_filter(array_map('absint', explode(',', (string) $gallery_raw)));
            if (function_exists('dnttvn_doanh_nghiep_gallery_card_max')) {
                $gallery_ids = array_slice($gallery_ids, 0, dnttvn_doanh_nghiep_gallery_card_max());
            } else {
                $gallery_ids = array_slice($gallery_ids, 0, 5);
            }
            $dia_chi       = get_post_meta($post->ID, '_dia_chi', true);
            $dien_thoai    = get_post_meta($post->ID, '_dien_thoai', true);
            $email_lh      = get_post_meta($post->ID, '_email_lien_he', true);
            $website_dn    = get_post_meta($post->ID, '_website_doanh_nghiep', true);
            $thong_tin_bs  = get_post_meta($post->ID, '_thong_tin_bo_sung', true);
            $mo_ta_ngan    = get_post_meta($post->ID, '_doanh_nghiep_mo_ta_ngan', true);
            $mo_ta_ngan    = is_string($mo_ta_ngan) ? trim($mo_ta_ngan) : '';
            if ($mo_ta_ngan !== '' && function_exists('dnttvn_dn_reg_trim_to_word_limit')) {
                $mo_lim     = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;
                $mo_ta_ngan = dnttvn_dn_reg_trim_to_word_limit($mo_ta_ngan, $mo_lim);
            }

            $featured_image_id = get_post_thumbnail_id($post->ID);
            $featured_image_url = '';
            $featured_image_alt = '';
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                if (empty($featured_image_alt)) {
                    $featured_image_alt = get_the_title($post->ID) . ' - Logo';
                }
            }

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
                        $small_image_alt = get_the_title($post->ID) . ' - Hình ảnh phụ';
                    }
                }
            }
            ?>
            <div class="content-column">
                <?php 
                $ten_day_du = get_post_meta($post->ID, '_ten_day_du', true);
                $display_title = !empty($ten_day_du) ? $ten_day_du : $post->post_title;
                $dn_date_dmY   = get_the_date('d/m/Y', $post);
                $dn_date_iso   = get_the_date('c', $post);
                ?>
                <div id="dn-noi-dung" class="column-header dn-detail-title-block dn-detail-title-block--anchor">
                    <div class="dn-detail-title-block__inner">
                        <span class="dn-detail-title-block__name"><?php echo esc_html($display_title); ?></span>
                        <time class="dn-detail-title-block__date" datetime="<?php echo esc_attr($dn_date_iso); ?>">Ngày đăng: <?php echo esc_html($dn_date_dmY); ?></time>
                    </div>
                </div>
                <div class="column-content">
            <div class="business-card dn-detail-business-card" style="max-width: 100%;">
                <div class="business-card-left">
                    <div class="business-card-image">
                        <?php if ($featured_image_url) : ?>
                            <img src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" class="business-main-image" loading="lazy">
                        <?php else : ?>
                            <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(urlencode(get_the_title())); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="business-main-image" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <?php if ($nganh_hang) : ?>
                        <div class="business-card-info dn-dn-meta-around-img dn-dn-meta-under-main-img">
                            <svg class="business-card-info-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <p style="margin:0;"><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($khu_vuc) : ?>
                        <div class="business-card-info dn-dn-meta-around-img dn-dn-meta-under-main-img">
                            <svg class="business-card-info-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            <p style="margin:0;"><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="business-card-content">
                    <?php if ($gallery_ids) : ?>
                        <div class="business-card-small-image dn-dn-main-gallery">
                            <div class="business-card-small-image-slider">
                                <?php
                                $first = true;
                                foreach ($gallery_ids as $img_id) :
                                    $img_url = wp_get_attachment_image_url($img_id, 'large');
                                    if (!$img_url) {
                                        continue;
                                    }
                                    $img_full = wp_get_attachment_image_url($img_id, 'full') ?: $img_url;
                                    $img_alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                    if (empty($img_alt)) {
                                        $img_alt = get_the_title($post->ID) . ' - Hình ảnh ' . $img_id;
                                    }
                                    ?>
                                    <div class="business-card-small-image-slide <?php echo $first ? 'active' : ''; ?>" data-full="<?php echo esc_url($img_full); ?>" data-alt="<?php echo esc_attr($img_alt); ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="business-small-image" loading="lazy">
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
                        <div class="business-card-gallery" style="margin-top: 12px;">
                            <?php foreach ($gallery_ids as $img_id) :
                                $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                if (!$thumb_url) $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                                if (!$thumb_url) continue;
                                $thumb_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                ?>
                                <?php
                                $thumb_full = wp_get_attachment_image_url($img_id, 'full') ?: wp_get_attachment_image_url($img_id, 'large');
                                ?>
                                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($thumb_alt); ?>" data-full="<?php echo esc_url($thumb_full ? $thumb_full : $thumb_url); ?>" class="business-gallery-thumb" />
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($small_image_url || ($small_image_id && $small_image_url)) : ?>
                        <div class="business-card-small-image">
                            <?php if ($small_image_id) : ?>
                                <img src="<?php echo esc_url($small_image_url); ?>" alt="<?php echo esc_attr($small_image_alt); ?>" class="business-small-image" loading="lazy">
                            <?php else : ?>
                                <img src="<?php echo esc_url($small_image_url); ?>" alt="<?php echo esc_attr(get_the_title($post->ID) . ' - Hình ảnh phụ'); ?>" class="business-small-image" loading="lazy">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($dia_chi || $dien_thoai || $email_lh || $website_dn || $thong_tin_bs) : ?>
                        <div style="margin-bottom: 25px; padding: 15px 18px; border-radius: 8px; background:#f8f9fa; border:1px solid #e0e0e0;">
                            <h3 style="margin-top:0; margin-bottom:12px; font-size:20px; color:#06202e;">Thông tin liên hệ & bổ sung</h3>
                            <ul style="list-style:none; margin:0; padding:0; font-size:16px; color:#444;">
                                <?php if ($dia_chi) : ?><li style="margin-bottom:6px;"><strong>Địa chỉ:</strong> <?php echo esc_html($dia_chi); ?></li><?php endif; ?>
                                <?php if ($dien_thoai) : ?><li style="margin-bottom:6px;"><strong>Điện thoại:</strong> <?php echo esc_html($dien_thoai); ?></li><?php endif; ?>
                                <?php if ($email_lh) : ?><li style="margin-bottom:6px;"><strong>Email:</strong> <a href="mailto:<?php echo esc_attr($email_lh); ?>"><?php echo esc_html($email_lh); ?></a></li><?php endif; ?>
                                <?php if ($website_dn) :
                                    $website_raw = trim($website_dn);
                                    $website_href = $website_raw;
                                    if ($website_href && !preg_match('~^https?://~i', $website_href)) $website_href = '//' . $website_href;
                                    ?>
                                    <li style="margin-bottom:6px;"><strong>Website:</strong> <a href="<?php echo esc_url($website_href); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($website_raw); ?></a></li>
                                <?php endif; ?>
                            </ul>
                            <?php if ($thong_tin_bs) : ?>
                                <div style="margin-top:10px; font-size:16px; color:#555; line-height:1.7;"><?php echo wp_kses_post(wpautop($thong_tin_bs)); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div> <!-- close business-card-content -->

                <?php
                $dn_detail_raw_content = get_post_field('post_content', $post->ID, 'raw');
                $dn_detail_raw_content = is_string($dn_detail_raw_content) ? trim($dn_detail_raw_content) : '';
                $dn_detail_strip = $dn_detail_raw_content !== '' ? trim(wp_strip_all_tags($dn_detail_raw_content)) : '';
                $dn_mo_strip     = $mo_ta_ngan !== '' ? trim(wp_strip_all_tags($mo_ta_ngan)) : '';
                $dn_show_post_body = ($dn_detail_strip !== '' && ($dn_mo_strip === '' || $dn_detail_strip !== $dn_mo_strip));
                if ($dn_show_post_body) :
                    ?>
                <div class="business-card-description business-card-description--detail-below" style="font-size: 16px; line-height: 1.8; text-align: left; margin-top: 0;">
                    <style>
                        .business-card-description img {
                            max-width: 100%;
                            height: auto;
                            display: block;
                            margin: 15px 0;
                        }
                        .business-card-description p,
                        .business-card-description div,
                        .structured-content-display p,
                        .structured-content-display div {
                            text-align: left !important;
                        }
                    </style>
                    <h3 style="margin-top:0; margin-bottom:10px; font-size:20px; color:#06202e; text-align: left;">Mô tả chi tiết doanh nghiệp</h3>
                    <?php the_content(); ?>
                </div>
                    <?php
                endif;
                ?>
            </div> <!-- close business-card -->

            <?php
            $items = dnttvn_get_structured_content_array($post->ID);
            if (function_exists('dnttvn_doanh_nghiep_filter_public_structured')) {
                $items = dnttvn_doanh_nghiep_filter_public_structured($items);
            }
            if (!empty($items)) :
                ?>
                <div class="structured-content-display dn-sc-detail" style="margin-top: 30px;">
                    <?php foreach ($items as $item) : ?>
                        <?php if (!empty($item['heading']) || !empty($item['content'])) : ?>
                            <div class="structured-item-display dn-sc-detail__block" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                <?php if (!empty($item['heading'])) : ?><h3 class="dn-sc-detail__heading"><?php echo esc_html($item['heading']); ?></h3><?php endif; ?>
                                <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                    <?php foreach ($item['content_items'] as $content_item) : if (is_string($content_item)) { $text = $content_item; $ci_images = []; $ci_captions = []; } else { $text = $content_item['text'] ?? ''; $ci_images = $content_item['images'] ?? []; $ci_captions = $content_item['image_captions'] ?? []; } ?>
                                        <div class="content-item-display dn-sc-detail__item" style="margin-bottom: 30px;">
                                            <?php if (!empty($text)) : ?><?php $c = $text; ?><div class="dn-sc-html"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div><?php endif; ?>
                                            <?php
                                            if (!empty($ci_images) && function_exists('dnttvn_render_doanh_nghiep_ci_images_block')) {
                                                echo dnttvn_render_doanh_nghiep_ci_images_block($ci_images, $ci_captions);
                                            }
                                            ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php elseif (!empty($item['content'])) : ?>
                                    <?php $c = $item['content']; ?>
                                    <div class="dn-sc-html"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php
            endif;
            ?>

            <?php
            if (function_exists('dnttvn_render_doanh_nghiep_noi_dung_slider')) {
                echo dnttvn_render_doanh_nghiep_noi_dung_slider((int) $post->ID);
            }
            ?>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #667eea; display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div style="margin-bottom: 10px;">
                    <?php
                    $prev_post = get_previous_post(false, '', 'nganh_hang');
                    if (!$prev_post) $prev_post = get_previous_post();
                    if ($prev_post) :
                        ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">← <?php echo esc_html($prev_post->post_title); ?></a>
                    <?php endif; ?>
                </div>
                <div>
                    <?php
                    $next_post = get_next_post(false, '', 'nganh_hang');
                    if (!$next_post) $next_post = get_next_post();
                    if ($next_post) :
                        ?>
                        <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;"><?php echo esc_html($next_post->post_title); ?> →</a>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- close column-content -->
    </div> <!-- close content-column -->
            <?php
            wp_reset_postdata();
            if (isset($original_post)) {
                $post = $original_post;
            }
        } else {
            ?>
            <div class="content-column">
                <p>Không tìm thấy doanh nghiệp. <a href="<?php echo esc_url($doanh_nghiep_list_url); ?>">Quay lại danh sách Doanh nghiệp</a>.</p>
            </div>
            <?php
        }
        ?>
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

    <!-- Right Sidebar: Theo ngành hàng (banner đã lưu) -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Theo ngành hàng</div>
        <div class="column-content mobile-collapsed">
            <?php
            $right_banner_html = dnttvn_render_banner_blocks('ad-block');
            if (!empty($right_banner_html)) :
            ?>
            <div class="ad-section">
                <?php echo $right_banner_html; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>

<?php get_footer(); ?>
