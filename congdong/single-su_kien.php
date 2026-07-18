<?php
/**
 * Single Post Template for Sự kiện
 * Layout đồng bộ trang Cộng đồng; nội dung lấy từ database (CPT su_kien).
 */

get_header();

$single_sk_id = get_queried_object_id();
dnttvn_page_shell_start(get_the_title($single_sk_id) ?: 'Sự kiện');

while (have_posts()) :
    the_post();

    $ngay_mo     = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
    $ngay_kt     = get_post_meta(get_the_ID(), '_su_kien_ngay_ket_thuc', true);
    if (!$ngay_mo) {
        $ngay_mo = get_post_meta(get_the_ID(), '_su_kien_ngay', true);
    }
    $dia_diem    = get_post_meta(get_the_ID(), '_su_kien_dia_diem', true);
    $video_url   = get_post_meta(get_the_ID(), '_su_kien_video_url', true);
    $gallery_raw = get_post_meta(get_the_ID(), '_su_kien_gallery', true);
    $gallery_ids = $gallery_raw ? array_filter(array_map('absint', explode(',', $gallery_raw))) : array();

    $featured_image_id  = get_post_thumbnail_id();
    $featured_image_url = $featured_image_id ? wp_get_attachment_image_url($featured_image_id, 'large') : '';
    $featured_image_alt = $featured_image_id ? get_post_meta($featured_image_id, '_wp_attachment_image_alt', true) : '';
    if (empty($featured_image_alt)) {
        $featured_image_alt = get_the_title();
    }

    $sk_list_url = get_post_type_archive_link('su_kien');
    if (!$sk_list_url) {
        $sk_list_url = home_url('/su-kien/');
    }
    ?>
    <article class="cd-detail">
        <h1 class="cd-detail__title"><?php the_title(); ?></h1>
        <div class="cd-detail__meta">
            <?php if ($ngay_mo) : ?>
                <span>Mở: <?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_mo))); ?></span>
            <?php endif; ?>
            <?php if ($ngay_kt) : ?>
                <span>Kết thúc: <?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_kt))); ?></span>
            <?php endif; ?>
            <?php if ($dia_diem) : ?>
                <span><?php echo esc_html($dia_diem); ?></span>
            <?php endif; ?>
        </div>

        <?php if ($featured_image_url) : ?>
            <div class="cd-detail__hero" style="margin-bottom:18px;">
                <img src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" style="width:100%;max-height:360px;object-fit:cover;border-radius:10px;" loading="lazy">
            </div>
        <?php endif; ?>

        <?php if ($video_url) : ?>
            <div style="margin-bottom:20px;">
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
            <div class="cd-detail__gallery" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
                <?php foreach ($gallery_ids as $img_id) :
                    $thumb = wp_get_attachment_image_url($img_id, 'medium');
                    if (!$thumb) {
                        continue;
                    }
                    ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="" style="width:120px;height:90px;object-fit:cover;border-radius:8px;" loading="lazy">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="cd-detail__body entry-content">
            <?php the_content(); ?>

            <?php
            $items = function_exists('dnttvn_get_structured_content_array') ? dnttvn_get_structured_content_array(get_the_ID()) : array();
            if (!empty($items)) :
                foreach ($items as $item) :
                    if (empty($item['heading']) && empty($item['content']) && empty($item['content_items'])) {
                        continue;
                    }
                    ?>
                    <div class="structured-item-display" style="margin:24px 0;padding-bottom:16px;border-bottom:1px solid var(--cd-border,#e4e9f0);">
                        <?php if (!empty($item['heading'])) : ?>
                            <h2><?php echo esc_html($item['heading']); ?></h2>
                        <?php endif; ?>
                        <?php
                        if (!empty($item['content_items']) && is_array($item['content_items'])) {
                            foreach ($item['content_items'] as $content_item) {
                                $text = is_string($content_item) ? $content_item : (isset($content_item['text']) ? $content_item['text'] : '');
                                if ($text !== '') {
                                    $c = apply_filters('dnttvn_display_content', $text);
                                    echo '<div class="structured-content-text">' . (function_exists('dnttvn_kses_structured_content') ? dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)) : wp_kses_post(wpautop($c))) . '</div>';
                                }
                            }
                        } elseif (!empty($item['content'])) {
                            $c = apply_filters('dnttvn_display_content', $item['content']);
                            echo '<div class="structured-content-text">' . (function_exists('dnttvn_kses_structured_content') ? dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)) : wp_kses_post(wpautop($c))) . '</div>';
                        }
                        ?>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="cd-detail__nav" style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <?php
                $prev_post = get_previous_post(false, '', 'su_kien');
                if (!$prev_post) {
                    $prev_post = get_previous_post();
                }
                if ($prev_post && $prev_post->post_type === 'su_kien') :
                    ?>
                    <a class="cd-btn-outline" href="<?php echo esc_url(get_permalink($prev_post)); ?>">← <?php echo esc_html($prev_post->post_title); ?></a>
                <?php endif; ?>
            </div>
            <a class="cd-btn-outline" href="<?php echo esc_url($sk_list_url); ?>">← Danh sách sự kiện</a>
            <div>
                <?php
                $next_post = get_next_post(false, '', 'su_kien');
                if (!$next_post) {
                    $next_post = get_next_post();
                }
                if ($next_post && $next_post->post_type === 'su_kien') :
                    ?>
                    <a class="cd-btn-outline" href="<?php echo esc_url(get_permalink($next_post)); ?>"><?php echo esc_html($next_post->post_title); ?> →</a>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php
endwhile;

dnttvn_page_shell_end();
get_footer();
