<?php
/**
 * Template Name: Chi Tiết Tin Tức
 * Layout hiện đại 3 cột — đồng bộ danh sách Tin tức / Cộng đồng.
 */

get_header();

$post_id_param = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
$tin_tuc_post = ($post_id_param > 0) ? get_post($post_id_param) : null;
$current_tin_tuc_id = ($tin_tuc_post && $tin_tuc_post->post_type === 'tin_tuc') ? $tin_tuc_post->ID : 0;

$crumb = ($current_tin_tuc_id && $tin_tuc_post) ? $tin_tuc_post->post_title : 'Tin tức';
dnttvn_page_shell_start($crumb);

$tin_tuc_list_page = get_page_by_path('tin-tuc');
$back_url = $tin_tuc_list_page ? get_permalink($tin_tuc_list_page) : home_url('/tin-tuc/');

if ($current_tin_tuc_id > 0 && $tin_tuc_post) {
    global $post;
    $original_post = $post;
    $post = $tin_tuc_post;
    setup_postdata($post);

    $thoi_gian_dang = get_post_meta($post->ID, '_tin_tuc_thoi_gian_dang', true);
    $ngay_dang      = get_post_meta($post->ID, '_tin_tuc_ngay_dang', true);
    $date_label     = get_the_date('d/m/Y', $post->ID);
    if ($thoi_gian_dang) {
        $dt = date_create_from_format('Y-m-d\TH:i', $thoi_gian_dang);
        if (!$dt) {
            $dt = date_create_from_format('Y-m-d H:i:s', $thoi_gian_dang);
        }
        if ($dt) {
            $date_label = $dt->format('d/m/Y');
        }
    } elseif ($ngay_dang) {
        $d = date_create_from_format('Y-m-d', $ngay_dang);
        if ($d) {
            $date_label = $d->format('d/m/Y');
        }
    }
    $tac_gia  = get_post_meta($post->ID, '_tin_tuc_tac_gia', true);
    $nguon    = get_post_meta($post->ID, '_tin_tuc_nguon', true);
    $is_noi_bat = get_post_meta($post->ID, '_tin_tuc_noi_bat', true) === '1';
    ?>
    <article class="cd-detail">
        <h1 class="cd-detail__title"><?php echo esc_html($post->post_title); ?></h1>
        <div class="cd-detail__meta">
            <span><?php echo esc_html($date_label); ?></span>
            <?php if ($tac_gia) : ?>
                <span><?php echo esc_html($tac_gia); ?></span>
            <?php endif; ?>
            <?php if ($is_noi_bat) : ?>
                <span class="cd-badge">Nổi bật</span>
            <?php endif; ?>
        </div>
        <?php if ($nguon) : ?>
            <p class="cd-detail__source"><strong>Nguồn:</strong> <a href="<?php echo esc_url($nguon); ?>" target="_blank" rel="noopener">Xem nguồn</a></p>
        <?php endif; ?>
        <?php if (get_the_excerpt($post->ID)) : ?>
            <div class="cd-detail__excerpt"><?php echo wp_kses_post(get_the_excerpt($post->ID)); ?></div>
        <?php endif; ?>

        <div class="cd-detail__body entry-content column-content-tin-tuc-detail">
            <div class="news-item">
                        <?php
                        $catalog_raw = get_post_meta($post->ID, '_tin_tuc_catalog_links', true);
                        if (is_string($catalog_raw) && $catalog_raw !== '') {
                            $catalog_links = json_decode($catalog_raw, true);
                        } else {
                            $catalog_links = is_array($catalog_raw) ? $catalog_raw : array();
                        }
                        $catalog_links = is_array($catalog_links) ? $catalog_links : array();
                        $catalog_links = array_values(array_filter($catalog_links, function ($item) {
                            return is_array($item) && isset($item['url']) && (string) $item['url'] !== '';
                        }));
                        if (!empty($catalog_links)) :
                            ?>
                        <section class="catalog-links-block catalog-links-block-first" aria-labelledby="catalog-heading-tin-tuc">
                            
                            <?php foreach ($catalog_links as $idx => $item) :
                                $url = trim((string) $item['url']);
                                if ($url === '') continue;
                                $label = isset($item['label']) && trim((string) $item['label']) !== '' ? trim((string) $item['label']) : $url;
                                $iframe_id = 'catalog-iframe-tin-tuc-' . (int) $idx;
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
                        // Gộp hình chính + hình phụ vào chung 1 khung
                        $detail_gallery_items = array();
                        if (has_post_thumbnail($post->ID)) {
                            $feat_id = get_post_thumbnail_id($post->ID);
                            $url = wp_get_attachment_image_url($feat_id, 'large');
                            if (!$url) $url = wp_get_attachment_url($feat_id);
                            if ($url) {
                                $mime = get_post_mime_type($feat_id);
                                $detail_gallery_items[] = array(
                                    'id'       => $feat_id,
                                    'url'      => $url,
                                    'mime'     => $mime ?: 'image/jpeg',
                                    'is_video' => is_string($mime) && strpos($mime, 'video') === 0,
                                    'is_pdf'   => ((string) $mime) === 'application/pdf'
                                );
                            }
                        }
                        $hinh_phu_raw = get_post_meta($post->ID, '_tin_tuc_hinh_phu', true);
                        if (is_string($hinh_phu_raw)) { $decoded = json_decode($hinh_phu_raw, true); $hinh_phu_array = is_array($decoded) ? $decoded : array(); } else { $hinh_phu_array = is_array($hinh_phu_raw) ? $hinh_phu_raw : (!empty($hinh_phu_raw) ? array($hinh_phu_raw) : array()); }
                        $hinh_phu_array = array_values(array_filter(array_map('absint', (array) $hinh_phu_array)));
                        foreach ($hinh_phu_array as $aid) {
                            $aid = intval($aid);
                            if ($aid <= 0) continue;
                            $mime = get_post_mime_type($aid);
                            $url = wp_get_attachment_url($aid);
                            if ($url) $detail_gallery_items[] = array(
                                'id'       => $aid,
                                'url'      => $url,
                                'mime'     => $mime ?: 'image/jpeg',
                                'is_video' => is_string($mime) && strpos($mime, 'video') === 0,
                                'is_pdf'   => ((string) $mime) === 'application/pdf'
                            );
                        }

                        $image_layout = get_post_meta($post->ID, '_tin_tuc_hinh_noi_dung_layout', true);
                        if (!$image_layout) $image_layout = 'slideshow';

                        if (!empty($detail_gallery_items)) :
                            $first = $detail_gallery_items[0];
                            $has_multi = count($detail_gallery_items) > 1;
                            $only_images = array_filter($detail_gallery_items, function($it){ return !$it['is_video'] && !$it['is_pdf']; });

                            if ($image_layout === 'photo_grid') : 
                                $grid_class = (count($detail_gallery_items) === 3) ? 'tintuc-photo-grid tintuc-photo-grid-3' : 'tintuc-photo-grid';
                            ?>
                            <!-- Photo Grid layout -->
                            <div class="<?php echo esc_attr($grid_class); ?>" style="margin-bottom: 24px;">
                                <?php foreach ($detail_gallery_items as $idx => $item) :
                                    $full_url = $item['url'];
                                    $thumb_url = (!$item['is_video'] && !$item['is_pdf']) ? wp_get_attachment_image_url($item['id'], 'large') : $full_url;
                                    if (!$thumb_url) $thumb_url = $full_url;
                                ?>
                                    <div class="tintuc-photo-grid-item" role="button" tabindex="0" data-full="<?php echo esc_url($full_url); ?>">
                                        <?php if ($item['is_video']) : ?>
                                            <video autoplay muted loop playsinline><source src="<?php echo esc_url($full_url); ?>" type="<?php echo esc_attr($item['mime']); ?>"></video>
                                        <?php elseif ($item['is_pdf']) : ?>
                                            <div class="tintuc-photo-grid-pdf">PDF</div>
                                        <?php else : ?>
                                            <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif ($image_layout === 'multi_photo' && $has_multi) : ?>
                            <!-- Multi-photo layout: ảnh đầu lớn + phần còn lại nhỏ bên phải -->
                            <div class="tintuc-multiphoto" style="margin-bottom: 24px;" data-gallery="<?php echo esc_attr(wp_json_encode($detail_gallery_items)); ?>">
                                <div class="tintuc-multiphoto-main" role="button" tabindex="0">
                                    <div class="multiphoto-main-viewport">
                                        <?php if ($first['is_video']) : ?>
                                            <video class="tintuc-multiphoto-img" controls><source src="<?php echo esc_url($first['url']); ?>" type="<?php echo esc_attr($first['mime']); ?>"></video>
                                        <?php elseif ($first['is_pdf']) : ?>
                                            <div class="tintuc-multiphoto-pdf">PDF</div>
                                        <?php else : ?>
                                            <?php $th = wp_get_attachment_image_url($first['id'], 'large'); if (!$th) $th = $first['url']; ?>
                                            <img class="tintuc-multiphoto-img" src="<?php echo esc_url($th); ?>" alt="" loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="tintuc-multiphoto-side">
                                    <?php foreach (array_slice($detail_gallery_items, 1) as $idx => $item) :
                                        $full_url = $item['url'];
                                        $thumb_url = (!$item['is_video'] && !$item['is_pdf']) ? wp_get_attachment_image_url($item['id'], 'medium') : $full_url;
                                        if (!$thumb_url) $thumb_url = $full_url;
                                    ?>
                                        <div class="tintuc-multiphoto-side-item" role="button" tabindex="0" data-full="<?php echo esc_url($full_url); ?>">
                                            <?php if ($item['is_video']) : ?>
                                                <video autoplay muted loop playsinline><source src="<?php echo esc_url($full_url); ?>" type="<?php echo esc_attr($item['mime']); ?>"></video>
                                            <?php elseif ($item['is_pdf']) : ?>
                                                <div class="tintuc-multiphoto-pdf">PDF</div>
                                            <?php else : ?>
                                                <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        <?php else : ?>
                            <!-- Slideshow layout (default) -->
                            <div class="detail-gallery-unified" data-detail-gallery-items="<?php echo esc_attr(wp_json_encode($detail_gallery_items)); ?>" style="margin-bottom: 24px;">
                                <script type="application/json" class="detail-gallery-json"><?php echo str_replace('</script>', '<\/script>', wp_json_encode($detail_gallery_items)); ?></script>
                                <div class="detail-gallery-frame">
                                    <?php if ($has_multi) : ?><button type="button" class="detail-gallery-prev" aria-label="Ảnh trước">&#8249;</button><?php endif; ?>
                                    <div class="detail-gallery-main" role="button" tabindex="0" title="Bấm xem to hơn">
                                        <?php if (!empty($first['is_video'])) : ?>
                                            <video class="detail-gallery-media" controls><source src="<?php echo esc_url($first['url']); ?>" type="<?php echo esc_attr($first['mime']); ?>"></video>
                                        <?php elseif (!empty($first['is_pdf'])) : ?>
                                            <iframe class="detail-gallery-media detail-gallery-pdf" src="<?php echo esc_url($first['url']); ?>" title="PDF" loading="lazy"></iframe>
                                        <?php else : ?>
                                            <img class="detail-gallery-media" src="<?php echo esc_url($first['url']); ?>" alt="" />
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($has_multi) : ?><button type="button" class="detail-gallery-next" aria-label="Ảnh sau">&#8250;</button><?php endif; ?>
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

                        <?php endif; // end item layout if
                        endif; // end if !empty($detail_gallery_items)
                        ?>

                        <?php
                        // Nội dung: Structured Content đầy đủ (giống single-tin-tuc) hoặc the_content
                        $items = function_exists('dnttvn_get_structured_content_array') ? dnttvn_get_structured_content_array($post->ID) : array();
                        $is_featured = get_post_meta($post->ID, '_tin_tuc_noi_bat', true);

                        if (!empty($items)) {
                            $structured_has_output = false;
                            echo '<div class="structured-content-display" style="margin-top: 30px;">';
                            foreach ($items as $item) {
                                $has_heading_or_content = !empty($item['heading']) || !empty($item['content']);
                                $has_content_items = !empty($item['content_items']) && is_array($item['content_items']);
                                $has_content = $has_heading_or_content || $has_content_items;
                                $has_images = !$is_featured && !empty($item['images']) && is_array($item['images']);
                                if (!$has_content && !$has_images) continue;

                                $structured_has_output = true;
                                echo '<div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">';
                                if (!empty($item['heading'])) {
                                    echo '<h3 style="font-size: 26px; font-weight: bold; color: #333; margin-bottom: 15px;">' . esc_html($item['heading']) . '</h3>';
                                }

                                $images = isset($item['images']) ? $item['images'] : array();
                                $image_captions = isset($item['image_captions']) ? $item['image_captions'] : array();
                                $item_image_layout = $item['image_layout'] ?? 'slideshow';

                                if (!empty($images) && is_array($images)) {
                                    $structured_gallery_items = array();
                                    foreach ($images as $img_index => $attachment_id) {
                                        $attachment_id = intval($attachment_id);
                                        if ($attachment_id <= 0) continue;
                                        $mime_type = get_post_mime_type($attachment_id);
                                        $url = wp_get_attachment_url($attachment_id);
                                        if ($url) $structured_gallery_items[] = array(
                                            'id'       => $attachment_id,
                                            'url'      => $url,
                                            'mime'     => $mime_type ?: 'image/jpeg',
                                            'is_video' => strpos($mime_type, 'video') === 0,
                                            'is_pdf'   => ((string) $mime_type) === 'application/pdf',
                                            'caption'  => $image_captions[$img_index] ?? ''
                                        );
                                    }

                                    if (!empty($structured_gallery_items)) :
                                        if ($item_image_layout === 'photo_grid') : 
                                            $s_grid_class = (count($structured_gallery_items) === 3) ? 'tintuc-photo-grid tintuc-photo-grid-3' : 'tintuc-photo-grid';
                                        ?>
                                            <div class="<?php echo esc_attr($s_grid_class); ?>" style="margin: 20px 0;">
                                                <?php foreach ($structured_gallery_items as $gallery_item) :
                                                    $thumb = (!$gallery_item['is_video'] && !$gallery_item['is_pdf']) ? wp_get_attachment_image_url($gallery_item['id'], 'large') : $gallery_item['url'];
                                                ?>
                                                    <div class="tintuc-photo-grid-item" role="button" tabindex="0" data-full="<?php echo esc_url($gallery_item['url']); ?>">
                                                        <?php if ($gallery_item['is_video']) : ?>
                                                            <video autoplay muted loop playsinline><source src="<?php echo esc_url($gallery_item['url']); ?>" type="<?php echo esc_attr($gallery_item['mime']); ?>"></video>
                                                        <?php elseif ($gallery_item['is_pdf']) : ?>
                                                            <div class="tintuc-photo-grid-pdf">PDF</div>
                                                        <?php else : ?>
                                                            <img src="<?php echo esc_url($thumb ?: $gallery_item['url']); ?>" alt="" loading="lazy">
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php elseif ($item_image_layout === 'multi_photo' && count($structured_gallery_items) > 1) :
                                            $first_gi = $structured_gallery_items[0];
                                        ?>
                                            <div class="tintuc-multiphoto" style="margin: 20px 0;" data-gallery="<?php echo esc_attr(wp_json_encode($structured_gallery_items)); ?>">
                                                <div class="tintuc-multiphoto-main" role="button" tabindex="0" data-full="<?php echo esc_url($first_gi['url']); ?>">
                                                    <div class="multiphoto-main-viewport">
                                                        <?php if ($first_gi['is_video']) : ?>
                                                            <video class="tintuc-multiphoto-img" controls><source src="<?php echo esc_url($first_gi['url']); ?>" type="<?php echo esc_attr($first_gi['mime']); ?>"></video>
                                                        <?php elseif ($first_gi['is_pdf']) : ?>
                                                            <div class="tintuc-multiphoto-pdf">PDF</div>
                                                        <?php else : ?>
                                                            <img class="tintuc-multiphoto-img" src="<?php echo esc_url(wp_get_attachment_image_url($first_gi['id'], 'large') ?: $first_gi['url']); ?>" alt="" loading="lazy">
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="tintuc-multiphoto-side">
                                                    <?php foreach (array_slice($structured_gallery_items, 1) as $side_gi) : ?>
                                                        <div class="tintuc-multiphoto-side-item" role="button" tabindex="0" data-full="<?php echo esc_url($side_gi['url']); ?>">
                                                            <?php if ($side_gi['is_video']) : ?>
                                                                <video autoplay muted loop playsinline><source src="<?php echo esc_url($side_gi['url']); ?>" type="<?php echo esc_attr($side_gi['mime']); ?>"></video>
                                                            <?php elseif ($side_gi['is_pdf']) : ?>
                                                                <div class="tintuc-multiphoto-pdf">PDF</div>
                                                            <?php else : ?>
                                                                <img src="<?php echo esc_url(wp_get_attachment_image_url($side_gi['id'], 'medium') ?: $side_gi['url']); ?>" alt="" loading="lazy">
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                            <div class="structured-images-gallery" style="margin: 20px 0;"><div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">
                                            <?php foreach ($structured_gallery_items as $gallery_item) : ?>
                                                <div class="gallery-item-display" style="flex: 0 0 auto;" role="button" tabindex="0" data-full="<?php echo esc_url($gallery_item['url']); ?>">
                                                    <?php if ($gallery_item['is_video']) : ?>
                                                        <video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="<?php echo esc_url($gallery_item['url']); ?>" type="<?php echo esc_attr($gallery_item['mime']); ?>"></video>
                                                    <?php elseif ($gallery_item['is_pdf']) : ?>
                                                        <div style="width: 200px; height: 150px; display: flex; align-items: center; justify-content: center; background: #eee; border-radius: 8px;">PDF</div>
                                                    <?php else : ?>
                                                        <img src="<?php echo esc_url(wp_get_attachment_image_url($gallery_item['id'], 'medium') ?: $gallery_item['url']); ?>" alt="" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <?php if ($gallery_item['caption']) : ?>
                                                        <p style="margin-top: 8px; font-size: 13px; color: #666; font-style: italic; text-align: center; max-width: 250px;"><?php echo esc_html($gallery_item['caption']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                            </div></div>
                                        <?php endif;
                                    endif;
                                }
                                if (!empty($item['content_items']) && is_array($item['content_items'])) {
                                    foreach ($item['content_items'] as $content_item) {
                                        $text = is_string($content_item) ? $content_item : (isset($content_item['text']) ? $content_item['text'] : '');
                                        $ci_images = is_array($content_item) && isset($content_item['images']) ? $content_item['images'] : array();
                                        $ci_captions = is_array($content_item) && isset($content_item['image_captions']) ? $content_item['image_captions'] : array();
                                        echo '<div class="content-item-display" style="margin-bottom: 30px;">';
                                        if (!empty($text)) {
                                            $c = apply_filters('dnttvn_display_content', $text); echo '<div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555; margin-bottom: 15px;">' . dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)) . '</div>';
                                        }
                                        if (!empty($ci_images)) {
                                            $ci_gallery_items = array();
                                            foreach ($ci_images as $ci_img_idx => $ci_img_id) {
                                                $ci_img_id = intval($ci_img_id);
                                                if ($ci_img_id <= 0) continue;
                                                $ci_mime = get_post_mime_type($ci_img_id);
                                                $ci_url = wp_get_attachment_url($ci_img_id);
                                                $ci_caption = isset($ci_captions[$ci_img_idx]) ? $ci_captions[$ci_img_idx] : '';
                                                if ($ci_url) $ci_gallery_items[] = array(
                                                    'id'       => $ci_img_id,
                                                    'url'      => $ci_url,
                                                    'mime'     => $ci_mime ?: 'image/jpeg',
                                                    'is_video' => strpos($ci_mime, 'video') === 0,
                                                    'is_pdf'   => ((string) $ci_mime) === 'application/pdf',
                                                    'caption'  => $ci_caption
                                                );
                                            }

                                            if (!empty($ci_gallery_items)) :
                                                if ($item_image_layout === 'photo_grid') : 
                                                    $ci_grid_class = (count($ci_gallery_items) === 3) ? 'tintuc-photo-grid tintuc-photo-grid-3' : 'tintuc-photo-grid';
                                                ?>
                                                    <div class="<?php echo esc_attr($ci_grid_class); ?>" style="margin: 15px 0;">
                                                        <?php foreach ($ci_gallery_items as $gi) : ?>
                                                            <div class="tintuc-photo-grid-item" role="button" tabindex="0" data-full="<?php echo esc_url($gi['url']); ?>">
                                                                <?php if ($gi['is_video']) : ?>
                                                                    <video autoplay muted loop playsinline><source src="<?php echo esc_url($gi['url']); ?>" type="<?php echo esc_attr($gi['mime']); ?>"></video>
                                                                <?php elseif ($gi['is_pdf']) : ?>
                                                                    <div class="tintuc-photo-grid-pdf">PDF</div>
                                                                <?php else : ?>
                                                                    <img src="<?php echo esc_url(wp_get_attachment_image_url($gi['id'], 'large') ?: $gi['url']); ?>" alt="" loading="lazy">
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php elseif ($item_image_layout === 'multi_photo' && count($ci_gallery_items) > 1) :
                                                    $f_gi = $ci_gallery_items[0];
                                                ?>
                                                    <div class="tintuc-multiphoto" style="margin: 15px 0;" data-gallery="<?php echo esc_attr(wp_json_encode($ci_gallery_items)); ?>">
                                                        <div class="tintuc-multiphoto-main" role="button" tabindex="0" data-full="<?php echo esc_url($f_gi['url']); ?>">
                                                            <div class="multiphoto-main-viewport">
                                                                <?php if ($f_gi['is_video']) : ?>
                                                                    <video class="tintuc-multiphoto-img" controls><source src="<?php echo esc_url($f_gi['url']); ?>" type="<?php echo esc_attr($f_gi['mime']); ?>"></video>
                                                                <?php elseif ($f_gi['is_pdf']) : ?>
                                                                    <div class="tintuc-multiphoto-pdf">PDF</div>
                                                                <?php else : ?>
                                                                    <img class="tintuc-multiphoto-img" src="<?php echo esc_url(wp_get_attachment_image_url($f_gi['id'], 'large') ?: $f_gi['url']); ?>" alt="" loading="lazy">
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="tintuc-multiphoto-side">
                                                            <?php foreach (array_slice($ci_gallery_items, 1) as $s_gi) : ?>
                                                                <div class="tintuc-multiphoto-side-item" role="button" tabindex="0" data-full="<?php echo esc_url($s_gi['url']); ?>">
                                                                    <?php if ($s_gi['is_video']) : ?>
                                                                        <video autoplay muted loop playsinline><source src="<?php echo esc_url($s_gi['url']); ?>" type="<?php echo esc_attr($s_gi['mime']); ?>"></video>
                                                                    <?php elseif ($s_gi['is_pdf']) : ?>
                                                                        <div class="tintuc-multiphoto-pdf">PDF</div>
                                                                    <?php else : ?>
                                                                        <img src="<?php echo esc_url(wp_get_attachment_image_url($s_gi['id'], 'medium') ?: $s_gi['url']); ?>" alt="" loading="lazy">
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="content-item-images-gallery" style="display: flex; flex-wrap: wrap; gap: 15px;">
                                                    <?php foreach ($ci_gallery_items as $gi) : ?>
                                                        <div style="flex: 0 0 auto;" role="button" tabindex="0" data-full="<?php echo esc_url($gi['url']); ?>">
                                                            <?php if ($gi['is_video']) : ?>
                                                                <video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="<?php echo esc_url($gi['url']); ?>" type="<?php echo esc_attr($gi['mime']); ?>"></video>
                                                            <?php elseif ($gi['is_pdf']) : ?>
                                                                <div style="width: 200px; height: 150px; display: flex; align-items: center; justify-content: center; background: #eee; border-radius: 8px;">PDF</div>
                                                            <?php else : ?>
                                                                <img src="<?php echo esc_url(wp_get_attachment_image_url($gi['id'], 'medium') ?: $gi['url']); ?>" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;">
                                                            <?php endif; ?>
                                                            <?php if ($gi['caption']) : ?>
                                                                <p style="margin-top: 5px; font-size: 13px; color: #666; max-width: 250px;"><?php echo esc_html($gi['caption']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    </div>
                                                <?php endif;
                                            endif;
                                        }
                                        echo '</div>';
                                    }
                                } elseif (!empty($item['content'])) {
                                    $c = $item['content'];
                                    $c = apply_filters('dnttvn_display_content', $item['content']); echo '<div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555;">' . dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)) . '</div>';
                                }
                                echo '</div>'; // end structured-item-display
                            } // end foreach $items
                            echo '</div>'; // end structured-content-display
                            if (!$structured_has_output) {
                                echo '<div style="line-height: 1.8; font-size: 20px;">' . apply_filters('the_content', $post->post_content) . '</div>';
                            }
                        } else {
                            echo '<div style="line-height: 1.8; font-size: 20px;">';
                            if (!empty($post->post_content)) {
                                echo apply_filters('the_content', $post->post_content);
                            } else {
                                echo '<p style="color: #666; font-style: italic;">Nội dung bài viết đang được cập nhật...</p>';
                            }
                            echo '</div>';
                        }
                        ?>

                        <?php
                        if (function_exists('dnttvn_render_excel_tables')) {
                            dnttvn_render_excel_tables($post->ID);
                        }
                        ?>

                        <?php
                        $categories = get_the_terms($post->ID, 'category');
                        $tags = get_the_terms($post->ID, 'post_tag');
                        if (($categories && !is_wp_error($categories)) || ($tags && !is_wp_error($tags))) : ?>
                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                            <?php if ($categories && !is_wp_error($categories)) : ?>
                            <div style="margin-bottom: 10px;"><strong>Danh mục:</strong>
                                <?php
                                $cat_names = array();
                                foreach ($categories as $cat) {
                                    $cat_names[] = '<a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a>';
                                }
                                echo ' ' . implode(', ', $cat_names);
                                ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($tags && !is_wp_error($tags)) : ?>
                            <div><strong>Thẻ:</strong>
                                <?php
                                $tag_names = array();
                                foreach ($tags as $tag) {
                                    $tag_names[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>';
                                }
                                echo ' ' . implode(', ', $tag_names);
                                ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php
                        $flipbook_url = get_post_meta($post->ID, '_tin_tuc_flipbook_url', true);
                        if ($flipbook_url) :
                            $flipbook_title = esc_attr($post->post_title);
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
            </div>
        </div>
        <div class="cd-detail__nav">
            <a class="cd-btn-outline" href="<?php echo esc_url($back_url); ?>">← Quay lại danh sách</a>
        </div>
    </article>
    <?php
    $post = $original_post;
    wp_reset_postdata();
} else {
    echo '<h1 class="cd-detail__title">Không tìm thấy bài viết</h1>';
    echo '<p class="cd-empty">Bài viết không tồn tại hoặc đã bị xóa.</p>';
    echo '<div class="cd-detail__nav"><a class="cd-btn-outline" href="' . esc_url($back_url) . '">← Quay lại danh sách</a></div>';
}

dnttvn_page_shell_end();
get_footer();
?>
<!-- Lightbox overlay for Photo Grid & Multi-photo layouts -->
<div id="tintuc-lightbox" class="tintuc-lightbox-overlay" role="dialog" aria-modal="true" aria-label="Xem ảnh lớn">
    <span class="tintuc-lightbox-close" id="tintuc-lightbox-close" aria-label="Đóng">&times;</span>
    <span class="tintuc-lightbox-nav tintuc-lightbox-prev" id="tintuc-lightbox-prev" aria-label="Ảnh trước">&#8249;</span>
    <span class="tintuc-lightbox-nav tintuc-lightbox-next" id="tintuc-lightbox-next" aria-label="Ảnh sau">&#8250;</span>
    <img class="tintuc-lightbox-img" id="tintuc-lightbox-img" src="" alt="">
    <span class="tintuc-lightbox-counter" id="tintuc-lightbox-counter"></span>
</div>

<script>
(function() {
    'use strict';

    // --- Lightbox elements ---
    var overlay = document.getElementById('tintuc-lightbox');
    var lightboxImg = document.getElementById('tintuc-lightbox-img');
    var closeBtn = document.getElementById('tintuc-lightbox-close');
    var prevNav = document.getElementById('tintuc-lightbox-prev');
    var nextNav = document.getElementById('tintuc-lightbox-next');
    var counterEl = document.getElementById('tintuc-lightbox-counter');

    var currentGallery = [];
    var currentGalleryIndex = -1;
    var lightboxSwipeStartX = 0;

    if (!overlay) return;

    function updateLightboxImage() {
        if (currentGalleryIndex < 0 || currentGalleryIndex >= currentGallery.length) return;
        
        lightboxImg.style.opacity = '0';
        lightboxImg.style.transform = 'scale(0.96)';
        
        var newSrc = currentGallery[currentGalleryIndex];
        var tempImg = new Image();
        tempImg.onload = function() {
            lightboxImg.src = newSrc;
            requestAnimationFrame(function() {
                lightboxImg.style.opacity = '1';
                lightboxImg.style.transform = 'scale(1)';
            });
        };
        tempImg.onerror = function() {
            lightboxImg.src = newSrc;
            lightboxImg.style.opacity = '1';
            lightboxImg.style.transform = 'scale(1)';
        };
        tempImg.src = newSrc;

        var hasMulti = currentGallery.length > 1;
        prevNav.style.display = hasMulti ? 'flex' : 'none';
        nextNav.style.display = hasMulti ? 'flex' : 'none';
        counterEl.textContent = hasMulti ? (currentGalleryIndex + 1) + ' / ' + currentGallery.length : '';
        counterEl.style.display = hasMulti ? 'block' : 'none';
    }

    function openLightbox(src, gallery) {
        if (!src) return;
        
        currentGallery = gallery || [src];
        currentGalleryIndex = currentGallery.indexOf(src);
        if (currentGalleryIndex === -1) {
            currentGallery = [src];
            currentGalleryIndex = 0;
        }

        overlay.style.display = 'flex';
        requestAnimationFrame(function() {
            overlay.classList.add('active');
        });
        updateLightboxImage();
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        overlay.classList.remove('active');
        setTimeout(function() {
            overlay.style.display = 'none';
            lightboxImg.src = '';
        }, 300);
        document.body.style.overflow = '';
        currentGallery = [];
        currentGalleryIndex = -1;
    }

    function navPrev(e) {
        if (e) e.stopPropagation();
        if (currentGallery.length <= 1) return;
        currentGalleryIndex = (currentGalleryIndex > 0) ? currentGalleryIndex - 1 : currentGallery.length - 1;
        updateLightboxImage();
    }

    function navNext(e) {
        if (e) e.stopPropagation();
        if (currentGallery.length <= 1) return;
        currentGalleryIndex = (currentGalleryIndex < currentGallery.length - 1) ? currentGalleryIndex + 1 : 0;
        updateLightboxImage();
    }

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay || e.target === closeBtn || e.target === lightboxImg) closeLightbox();
    });

    if (prevNav) prevNav.addEventListener('click', navPrev);
    if (nextNav) nextNav.addEventListener('click', navNext);

    document.addEventListener('keydown', function(e) {
        if (!overlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navPrev();
        if (e.key === 'ArrowRight') navNext();
    });

    overlay.addEventListener('touchstart', function(e) {
        lightboxSwipeStartX = e.changedTouches[0].screenX;
    }, {passive: true});
    overlay.addEventListener('touchend', function(e) {
        var dx = e.changedTouches[0].screenX - lightboxSwipeStartX;
        if (Math.abs(dx) > 50) {
            if (dx > 0) navPrev(); else navNext();
        }
    }, {passive: true});

    // --- Slideshow handler ---
    document.querySelectorAll('.detail-gallery-unified').forEach(function(gallery) {
        var main = gallery.querySelector('.detail-gallery-main');
        var frame = gallery.querySelector('.detail-gallery-frame');
        var thumbs = gallery.querySelectorAll('.detail-gallery-thumb');
        var prevBtn = gallery.querySelector('.detail-gallery-prev');
        var nextBtn = gallery.querySelector('.detail-gallery-next');
        var currentIndex = 0;
        
        var jsonEl = gallery.querySelector('.detail-gallery-json');
        var items = [];
        try { items = JSON.parse(jsonEl.textContent); } catch(e) { return; }

        if (items.length > 1) {
            var counter = document.createElement('div');
            counter.className = 'detail-gallery-counter';
            counter.textContent = '1 / ' + items.length;
            frame.appendChild(counter);
        }

        function showItem(index) {
            if (index < 0) index = items.length - 1;
            if (index >= items.length) index = 0;
            currentIndex = index;

            var item = items[index];
            main.style.opacity = '0';
            
            setTimeout(function() {
                var html = '';
                if (item.is_video) {
                    html = '<video class="detail-gallery-media" controls><source src="' + item.url + '" type="' + item.mime + '"></video>';
                } else if (item.is_pdf) {
                    html = '<iframe class="detail-gallery-media detail-gallery-pdf" src="' + item.url + '" title="PDF" loading="lazy"></iframe>';
                } else {
                    html = '<img class="detail-gallery-media" src="' + item.url + '" alt="" />';
                }
                main.innerHTML = html;
                main.style.opacity = '1';
                
                thumbs.forEach(function(t, i) {
                    if (i === index) t.classList.add('active');
                    else t.classList.remove('active');
                });

                var counterEl = frame.querySelector('.detail-gallery-counter');
                if (counterEl) counterEl.textContent = (index + 1) + ' / ' + items.length;
            }, 300);
        }

        thumbs.forEach(function(thumb, i) {
            thumb.addEventListener('click', function() { showItem(i); });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                showItem(currentIndex - 1);
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                showItem(currentIndex + 1);
            });
        }
        
        main.addEventListener('click', function() {
            var currentItem = items[currentIndex];
            if (!currentItem) return;
            var galleryUrls = [];
            for (var i = 0; i < items.length; i++) galleryUrls.push(items[i].url);
            openLightbox(currentItem.url, galleryUrls);
        });

        var swipeStartX = 0;
        frame.addEventListener('touchstart', function(e) {
            swipeStartX = e.changedTouches[0].screenX;
        }, {passive: true});
        frame.addEventListener('touchend', function(e) {
            var dx = e.changedTouches[0].screenX - swipeStartX;
            if (Math.abs(dx) > 50) {
                if (dx > 0) showItem(currentIndex - 1); else showItem(currentIndex + 1);
            }
        }, {passive: true});
    });

    function getGalleryFromContainer(el) {
        var container = el.closest('.tintuc-photo-grid, .tintuc-multiphoto');
        if (!container) return [el.getAttribute('data-full')];
        
        var nodeList = container.querySelectorAll('[data-full]');
        var gallery = [];
        for (var i = 0; i < nodeList.length; i++) gallery.push(nodeList[i].getAttribute('data-full'));
        return gallery;
    }

    // --- Multi-photo handler ---
    document.querySelectorAll('.tintuc-multiphoto').forEach(function(container) {
        var viewport = container.querySelector('.multiphoto-main-viewport');
        var main = container.querySelector('.tintuc-multiphoto-main');
        var sideItems = container.querySelectorAll('.tintuc-multiphoto-side-item');
        var currentIndex = 0;
        
        var galleryData = [];
        try { galleryData = JSON.parse(container.getAttribute('data-gallery') || '[]'); } catch(e) { return; }

        var galleryUrls = [];
        for (var g = 0; g < galleryData.length; g++) galleryUrls.push(galleryData[g].url);

        function showItem(index) {
            if (index < 0) index = galleryData.length - 1;
            if (index >= galleryData.length) index = 0;
            currentIndex = index;
            var item = galleryData[index];
            if (!item) return;

            viewport.style.opacity = '0';
            setTimeout(function() {
                var html = '';
                if (item.is_video) html = '<video class="tintuc-multiphoto-img" controls><source src="' + item.url + '" type="' + item.mime + '"></video>';
                else if (item.is_pdf) html = '<div class="tintuc-multiphoto-pdf">PDF</div>';
                else html = '<img class="tintuc-multiphoto-img" src="' + item.url + '" alt="" loading="lazy">';
                
                viewport.innerHTML = html;
                viewport.style.opacity = '1';
                for (var s = 0; s < sideItems.length; s++) {
                    if (s === index - 1) sideItems[s].classList.add('active');
                    else sideItems[s].classList.remove('active');
                }
            }, 350);
        }

        var maxVisible = 4;
        var badgeIndex = -1;
        for (var mv = 0; mv < sideItems.length; mv++) {
            if (mv >= maxVisible) sideItems[mv].style.display = 'none';
            else if (mv === maxVisible - 1 && sideItems.length > maxVisible) {
                badgeIndex = mv;
                var badge = document.createElement('div');
                badge.className = 'tintuc-multiphoto-more-badge';
                badge.textContent = '+' + (sideItems.length - maxVisible);
                sideItems[mv].appendChild(badge);
            }
        }

        for (var si = 0; si < sideItems.length; si++) {
            (function(i) {
                sideItems[i].addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (i === badgeIndex) {
                        openLightbox(galleryData[i + 1].url, galleryUrls);
                    } else {
                        showItem(i + 1);
                    }
                });
            })(si);
        }

        main.addEventListener('click', function() {
            var currentItem = galleryData[currentIndex];
            if (!currentItem) return;
            openLightbox(currentItem.url, galleryUrls);
        });
    });

    // --- Generic click handler for Lightbox ---
    var dataFullEls = document.querySelectorAll('[data-full]');
    for (var df = 0; df < dataFullEls.length; df++) {
        (function(el) {
            if (el.closest('.detail-gallery-unified')) return;
            if (el.closest('.tintuc-multiphoto')) return;
            
            var handler = function() { 
                var gallery = getGalleryFromContainer(el);
                openLightbox(el.getAttribute('data-full'), gallery); 
            };
            el.addEventListener('click', handler);
            el.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') handler(); });
            el.style.cursor = 'pointer';
        })(dataFullEls[df]);
    }
})();
</script>