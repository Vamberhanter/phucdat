<?php
/**
 * Single Post Template for Cộng đồng
 */

get_header();

$single_cd_id = get_queried_object_id();
dnttvn_page_shell_start(get_the_title($single_cd_id), $single_cd_id);
?>

            <?php while (have_posts()) : the_post(); ?>
                <h1 class="cd-detail__title"><?php the_title(); ?></h1>
                <div class="column-content column-content-ve-cong-dong-detail">
                    <div class="news-item">
                        <?php
                        $skip_catalog = function_exists('dnttvn_is_tong_quan_cong_dong_post')
                            && dnttvn_is_tong_quan_cong_dong_post(get_the_ID());
                        $catalog_raw = get_post_meta(get_the_ID(), '_cong_dong_catalog_links', true);
                        if (is_string($catalog_raw) && $catalog_raw !== '') {
                            $catalog_links = json_decode($catalog_raw, true);
                        } else {
                            $catalog_links = is_array($catalog_raw) ? $catalog_raw : array();
                        }
                        $catalog_links = is_array($catalog_links) ? $catalog_links : array();
                        $catalog_links = array_values(array_filter($catalog_links, function ($item) {
                            return is_array($item) && isset($item['url']) && (string) $item['url'] !== '';
                        }));
                        if (!$skip_catalog && !empty($catalog_links)) :
                            ?>
                        <section class="catalog-links-block catalog-links-block-first" aria-labelledby="catalog-heading-cong-dong">
                            
                            <?php foreach ($catalog_links as $idx => $item) :
                                $url = trim((string) $item['url']);
                                if ($url === '') continue;
                                $label = isset($item['label']) && trim((string) $item['label']) !== '' ? trim((string) $item['label']) : $url;
                                $iframe_id = 'catalog-iframe-cong-dong-' . (int) $idx;
                                ?>
                            <div class="catalog-link-item">
                                <p class="catalog-link-title">
                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($label); ?></a>
                                    
                                </p>
                                <div class="flipbook-wrapper" style="max-width: 1000px; margin: 20px auto; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
                                    <div style="position: relative; padding-top: 70%; height: 0; width: 100%;">
                                        <iframe id="<?php echo esc_attr($iframe_id); ?>" src="<?php echo esc_url($url); ?>" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" allowfullscreen="true" allow="clipboard-write" title="<?php echo esc_attr($label); ?>"></iframe>
                                    </div>
                                    <p style="text-align: center; font-family: sans-serif; color: #666; font-size: 14px; margin-top: 10px;">💡 <i>Mẹo: Nhấn vào biểu tượng ô vuông ở góc để xem toàn màn hình</i></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </section>
                        <?php endif; ?>

                        <?php
                        $is_noi_bat = get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);
                        if ($is_noi_bat === '1') {
                            echo '<div style="margin-bottom: 15px;"><span style="display: inline-block; background: #667eea; color: #fff; padding: 6px 14px; border-radius: 4px; font-size: 14px; font-weight: 600;">Bài nổi bật</span></div>';
                        }
                        ?>
                        <div class="news-date" style="margin-bottom: 15px;">
                            <?php
                            $thoi_gian_dang = get_post_meta(get_the_ID(), '_cong_dong_thoi_gian_dang', true);
                            if ($thoi_gian_dang) {
                                $dt = date_create_from_format('Y-m-d\TH:i', $thoi_gian_dang);
                                if (!$dt) $dt = date_create_from_format('Y-m-d H:i:s', $thoi_gian_dang);
                                echo '<span class="news-date-line" style="display: block;"><strong>Ngày đăng:</strong> ' . ($dt ? $dt->format('d/m/Y') : esc_html($thoi_gian_dang)) . '</span>';
                                if ($dt) {
                                    echo '<span class="news-date-line" style="display: block;"><strong>Giờ đăng:</strong> ' . esc_html($dt->format('H:i')) . '</span>';
                                }
                            } else {
                                echo '<span class="news-date-line" style="display: block;"><strong>Ngày đăng:</strong> ' . esc_html(get_the_date('d/m/Y')) . '</span>';
                            }
                            ?>
                        </div>
                        <?php
                        $mo_ta_ngan = get_post_meta(get_the_ID(), '_cong_dong_mo_ta_ngan', true);
                        if ($mo_ta_ngan) : ?>
                        <div class="news-excerpt" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #667eea; color: #444;">
                            <?php echo wp_kses_post($mo_ta_ngan); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php
                        // Gộp hình chính + hình phụ vào chung 1 khung
                        $detail_gallery_items = array();
                        if (has_post_thumbnail()) {
                            $feat_id = get_post_thumbnail_id();
                            $url = wp_get_attachment_image_url($feat_id, 'large');
                            if (!$url) $url = wp_get_attachment_url($feat_id);
                            if ($url) {
                                $mime = get_post_mime_type($feat_id);
                                $detail_gallery_items[] = array(
                                    'url' => $url,
                                    'mime' => $mime ?: 'image/jpeg',
                                    'is_video' => is_string($mime) && strpos($mime, 'video') === 0,
                                    'is_pdf' => ((string) $mime) === 'application/pdf'
                                );
                            }
                        }
                        $hinh_phu_raw = get_post_meta(get_the_ID(), '_cong_dong_hinh_phu', true);
                        if (is_string($hinh_phu_raw)) { $decoded = json_decode($hinh_phu_raw, true); $hinh_phu_array = is_array($decoded) ? $decoded : array(); } else { $hinh_phu_array = is_array($hinh_phu_raw) ? $hinh_phu_raw : (!empty($hinh_phu_raw) ? array($hinh_phu_raw) : array()); }
                        $hinh_phu_array = array_values(array_filter(array_map('absint', (array) $hinh_phu_array)));
                        foreach ($hinh_phu_array as $aid) {
                            $aid = intval($aid);
                            if ($aid <= 0) continue;
                            $mime = get_post_mime_type($aid);
                            $url = wp_get_attachment_url($aid);
                            if ($url) $detail_gallery_items[] = array(
                                'url' => $url,
                                'mime' => $mime ?: 'image/jpeg',
                                'is_video' => is_string($mime) && strpos($mime, 'video') === 0,
                                'is_pdf' => ((string) $mime) === 'application/pdf'
                            );
                        }
                        if (!empty($detail_gallery_items)) :
                            $first = $detail_gallery_items[0];
                            $has_multi = count($detail_gallery_items) > 1;
                        ?>
                            <div class="detail-gallery-unified" data-detail-gallery-items="<?php echo esc_attr(wp_json_encode($detail_gallery_items)); ?>" style="margin-bottom: 24px;">
                                <script type="application/json" class="detail-gallery-json"><?php echo str_replace('</script>', '<\/script>', wp_json_encode($detail_gallery_items)); ?></script>
                                <div class="detail-gallery-frame">
                                    <?php if ($has_multi) : ?><button type="button" class="detail-gallery-prev" aria-label="Ảnh trước">‹</button><?php endif; ?>
                                    <div class="detail-gallery-main" role="button" tabindex="0" title="Bấm xem to hơn">
                                        <?php if (!empty($first['is_video'])) : ?>
                                            <video class="detail-gallery-media" controls><source src="<?php echo esc_url($first['url']); ?>" type="<?php echo esc_attr($first['mime']); ?>"></video>
                                        <?php elseif (!empty($first['is_pdf'])) : ?>
                                            <iframe class="detail-gallery-media detail-gallery-pdf" src="<?php echo esc_url($first['url']); ?>" title="PDF" loading="lazy"></iframe>
                                        <?php else : ?>
                                            <img class="detail-gallery-media" src="<?php echo esc_url($first['url']); ?>" alt="" />
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($has_multi) : ?><button type="button" class="detail-gallery-next" aria-label="Ảnh sau">›</button><?php endif; ?>
                                </div>
                                <div class="detail-gallery-thumbs">
                                    <?php foreach ($detail_gallery_items as $idx => $item) : ?>
                                        <div class="detail-gallery-thumb <?php echo $idx === 0 ? 'active' : ''; ?> <?php echo !empty($item['is_pdf']) ? 'is-pdf' : ''; ?>" data-index="<?php echo (int) $idx; ?>" role="button" tabindex="0">
                                            <?php if (!empty($item['is_video'])) : ?>
                                                <video><source src="<?php echo esc_url($item['url']); ?>" type="<?php echo esc_attr($item['mime']); ?>"></video>
                                            <?php elseif (!empty($item['is_pdf'])) : ?>
                                                <div class="detail-gallery-thumb-pdf">PDF</div>
                                            <?php else : ?>
                                                <img src="<?php echo esc_url($item['url']); ?>" alt="" />
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Display structured content if available
                        $items = dnttvn_get_structured_content_array(get_the_ID());
                        $is_featured = get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);

                        if (!empty($items)) {
                                ?>
                                <div class="structured-content-display" style="margin-top: 30px;">
                                    <?php foreach ($items as $item) : ?>
                                        <?php
                                        $has_heading_or_content = !empty($item['heading']) || !empty($item['content']);
                                        $has_content_items = !empty($item['content_items']) && is_array($item['content_items']);
                                        $has_content = $has_heading_or_content || $has_content_items;
                                        // Don't show images in content if post is featured (uses hình phụ instead)
                                        $has_images = !$is_featured && !empty($item['images']) && is_array($item['images']);
                                        if ($has_content || $has_images) :
                                        ?>
                                            <div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                                <?php if (!empty($item['heading'])) : ?>
                                                    <h3 style="font-size: 26px; font-weight: bold; color: #333; margin-bottom: 15px;">
                                                        <?php echo esc_html($item['heading']); ?>
                                                    </h3>
                                                <?php endif; ?>

                                                <?php
                                                // Display images gallery if available
                                                $images = isset($item['images']) ? $item['images'] : array();
                                                $image_captions = isset($item['image_captions']) ? $item['image_captions'] : array();

                                                if (!empty($images) && is_array($images)) :
                                                ?>
                                                    <div class="structured-images-gallery" style="margin: 20px 0;">
                                                        <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">
                                                            <?php foreach ($images as $img_index => $attachment_id) :
                                                                $attachment_id = intval($attachment_id);
                                                                if ($attachment_id > 0) :
                                                                    $mime_type = get_post_mime_type($attachment_id);
                                                                    $is_video = strpos($mime_type, 'video') === 0;
                                                                    $full_url = wp_get_attachment_url($attachment_id);
                                                            ?>
                                                                <div class="gallery-item-display" style="flex: 0 0 auto;">
                                                                    <?php if ($is_video) : ?>
                                                                        <video style="max-width: 250px; max-height: 180px; width: auto; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" controls>
                                                                            <source src="<?php echo esc_url($full_url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                                                            Trình duyệt của bạn không hỗ trợ video.
                                                                        </video>
                                                                    <?php else : ?>
                                                                        <img src="<?php echo esc_url($full_url); ?>"
                                                                             alt="<?php echo esc_attr(isset($image_captions[$img_index]) ? $image_captions[$img_index] : 'Ảnh ' . ($img_index + 1)); ?>"
                                                                             style="max-width: 250px; max-height: 180px; width: auto; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; object-fit: cover; transition: transform 0.2s ease;"
                                                                             data-full-src="<?php echo esc_url($full_url); ?>"
                                                                             data-type="image" />
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($image_captions[$img_index])) : ?>
                                                                        <p style="margin-top: 8px; font-size: 13px; color: #666; font-style: italic; text-align: center; max-width: 250px;">
                                                                            <?php echo esc_html($image_captions[$img_index]); ?>
                                                                        </p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                                    <?php foreach ($item['content_items'] as $content_item) : ?>
                                                        <?php
                                                        if (is_string($content_item)) {
                                                            $text = $content_item;
                                                            $ci_images = array();
                                                            $ci_captions = array();
                                                        } else {
                                                            $text = isset($content_item['text']) ? $content_item['text'] : '';
                                                            $ci_images = isset($content_item['images']) ? $content_item['images'] : array();
                                                            $ci_captions = isset($content_item['image_captions']) ? $content_item['image_captions'] : array();
                                                        }
                                                        ?>
                                                        <div class="content-item-display" style="margin-bottom: 30px;">
                                                            <?php if (!empty($text)) : ?><?php $c = apply_filters('dnttvn_display_content', $text); ?><div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555; margin-bottom: 15px;"><?php echo dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div><?php endif; ?>
                                                            <?php if (!empty($ci_images)) : ?><div class="content-item-images-gallery" style="display: flex; flex-wrap: wrap; gap: 15px;"><?php foreach ($ci_images as $ci_img_idx => $ci_img_id) : $ci_img_id = intval($ci_img_id); if ($ci_img_id > 0) : $ci_mime = get_post_mime_type($ci_img_id); $ci_is_video = strpos($ci_mime, 'video') === 0; $ci_url = wp_get_attachment_url($ci_img_id); $ci_caption = isset($ci_captions[$ci_img_idx]) ? $ci_captions[$ci_img_idx] : ''; ?><div style="flex: 0 0 auto;"><?php if ($ci_is_video) : ?><video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="<?php echo esc_url($ci_url); ?>" type="<?php echo esc_attr($ci_mime); ?>"></video><?php else : ?><img src="<?php echo esc_url($ci_url); ?>" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;"><?php endif; ?><?php if ($ci_caption) : ?><p style="margin-top: 5px; font-size: 13px; color: #666; max-width: 250px;"><?php echo esc_html($ci_caption); ?></p><?php endif; ?></div><?php endif; endforeach; ?></div><?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php elseif (!empty($item['content'])) : ?>
                                                    <div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555;">
                                                        <?php
                                                        $c = apply_filters('dnttvn_display_content', $item['content']);
                                                        echo dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c));
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <?php
                            } else {
                                // Fallback to regular content
                                ?>
                                <div style="line-height: 1.8; font-size: 20px;">
                                    <?php
                                    if (!empty(get_the_content())) {
                                        the_content();
                                    } else {
                                        echo '<p style="color: #666; font-style: italic;">Nội dung bài viết đang được cập nhật...</p>';
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        ?>
                        
                        <?php
                        if (function_exists('dnttvn_render_excel_tables')) {
                            dnttvn_render_excel_tables(get_the_ID());
                        }
                        ?>

                        <?php
                        $categories = get_the_terms(get_the_ID(), 'category');
                        $tags = get_the_terms(get_the_ID(), 'post_tag');
                        if (($categories && !is_wp_error($categories)) || ($tags && !is_wp_error($tags))) : ?>
                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                            <?php if ($categories && !is_wp_error($categories)) : ?>
                            <strong>Danh mục:</strong>
                            <?php
                            $cat_names = array();
                            foreach ($categories as $cat) {
                                $cat_names[] = '<a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a>';
                            }
                            echo ' ' . implode(', ', $cat_names);
                            ?>
                            <br>
                            <?php endif; ?>
                            <?php if ($tags && !is_wp_error($tags)) : ?>
                            <strong>Thẻ:</strong>
                            <?php
                            $tag_names = array();
                            foreach ($tags as $tag) {
                                $tag_names[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>';
                            }
                            echo ' ' . implode(', ', $tag_names);
                            ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php
                        $flipbook_url = get_post_meta(get_the_ID(), '_cong_dong_flipbook_url', true);
                        if ($flipbook_url) :
                            $flipbook_title = esc_attr(get_the_title());
                            ?>
                        <div class="flipbook-embed" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                            <h3 style="font-size: 20px; margin-bottom: 15px; color: #333;">Flipbook</h3>
                            <div class="flipbook-wrapper" style="max-width: 1000px; margin: 20px auto; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
                                <div style="position: relative; padding-top: 70%; height: 0; width: 100%;">
                                    <iframe src="<?php echo esc_url($flipbook_url); ?>" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" allowfullscreen="true" allow="clipboard-write" title="<?php echo $flipbook_title; ?>"></iframe>
                                </div>
                                <p style="text-align: center; font-family: sans-serif; color: #666; font-size: 14px; margin-top: 10px;">💡 <i>Mẹo: Nhấn vào biểu tượng ô vuông ở góc để xem toàn màn hình</i></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Navigation to previous/next post -->
                        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #667eea; display: flex; justify-content: space-between;">
                            <div>
                                <?php
                                $prev_post = get_previous_post();
                                if ($prev_post && function_exists('dnttvn_get_cong_dong_detail_url')) :
                                    ?>
                                    <a href="<?php echo esc_url(dnttvn_get_cong_dong_detail_url($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                        ← <?php echo esc_html($prev_post->post_title); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php
                                $next_post = get_next_post();
                                if ($next_post && function_exists('dnttvn_get_cong_dong_detail_url')) :
                                    ?>
                                    <a href="<?php echo esc_url(dnttvn_get_cong_dong_detail_url($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                        <?php echo esc_html($next_post->post_title); ?> →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

<?php dnttvn_page_shell_end(); ?>

<style>
    /* .current-item dùng chung từ style-gioi-thieu.css (.about-list .current-item) */
    .cong-dong-detail h2 {
        color: #333;
        margin-bottom: 10px;
    }
    
    .button {
        display: inline-block;
        background: #2271b1;
        color: #fff;
        padding: 8px 15px;
        text-decoration: none;
        border-radius: 4px;
        transition: background 0.3s;
        font-size: 14px;
    }
    
    .button:hover {
        background: #135e96;
    }
</style>

<?php get_footer(); ?>
