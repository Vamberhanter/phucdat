<?php
/**
 * Template Name: Quy trình gia nhập
 * Hiển thị danh sách bài Quy trình (tiêu đề + hình + mô tả); bấm vào mở accordion nội dung bên trong.
 */

get_header();

$quy_trinh_args = array(
    'post_type'      => 'quy_trinh',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
);
$quy_trinh_query = new WP_Query($quy_trinh_args);
?>

<?php dnttvn_page_shell_start(get_the_title()); ?>
<h1 class="cd-detail__title"><?php the_title(); ?></h1>
                <?php if ($quy_trinh_query->have_posts()) : ?>
                    <div class="accordion-list accordion-list-quy-trinh">
                        <?php while ($quy_trinh_query->have_posts()) : $quy_trinh_query->the_post();
                            $post_id = get_the_ID();
                            $excerpt = has_excerpt() ? get_the_excerpt() : '';
                            $sections = function_exists('dnttvn_get_sections_array') ? dnttvn_get_sections_array($post_id, '_quy_trinh_sections') : array();
                        ?>
                            <article class="accordion-item" data-post-id="<?php echo esc_attr($post_id); ?>">
                                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-body-<?php echo esc_attr($post_id); ?>" id="accordion-header-<?php echo esc_attr($post_id); ?>">
                                    <div class="accordion-header-inner">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="accordion-thumbnail">
                                                <?php the_post_thumbnail('medium', array('alt' => esc_attr(get_the_title()))); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="accordion-summary">
                                            <h3 class="accordion-title"><?php the_title(); ?></h3>
                                            <?php if ($excerpt) : ?>
                                                <div class="accordion-excerpt"><?php echo esc_html($excerpt); ?></div>
                                            <?php endif; ?>
                                            <span class="accordion-toggle-icon" aria-hidden="true"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-body" id="accordion-body-<?php echo esc_attr($post_id); ?>" role="region" aria-labelledby="accordion-header-<?php echo esc_attr($post_id); ?>" hidden>
                                    <div class="accordion-body-inner">
                                        <?php
                                        // Hình chính (gallery lớn) nếu có
                                        $main_ids = get_post_meta($post_id, '_quy_trinh_main_media', true);
                                        if (is_string($main_ids)) {
                                            $decoded_main = json_decode($main_ids, true);
                                            $main_ids = is_array($decoded_main) ? $decoded_main : array();
                                        }
                                        $main_ids = is_array($main_ids) ? array_values(array_filter(array_map('absint', $main_ids))) : array();
                                        if (!empty($main_ids)) :
                                            $detail_gallery_items = array();
                                            foreach ($main_ids as $aid) {
                                                $aid = (int) $aid;
                                                if ($aid <= 0) continue;
                                                $mime = get_post_mime_type($aid);
                                                $url = '';
                                                if (is_string($mime) && strpos($mime, 'image') === 0) {
                                                    $url = wp_get_attachment_image_url($aid, 'large');
                                                    if (!$url) $url = wp_get_attachment_url($aid);
                                                } else {
                                                    $url = wp_get_attachment_url($aid);
                                                }
                                                if (!$url) continue;
                                                $is_video = is_string($mime) && strpos($mime, 'video') === 0;
                                                $is_pdf = (string) $mime === 'application/pdf';
                                                $detail_gallery_items[] = array(
                                                    'url'      => $url,
                                                    'mime'     => $mime ?: 'image/jpeg',
                                                    'is_video' => $is_video,
                                                    'is_pdf'   => $is_pdf,
                                                    'caption'  => '',
                                                );
                                            }
                                            if (!empty($detail_gallery_items)) :
                                                $first = $detail_gallery_items[0];
                                                $has_multi = count($detail_gallery_items) > 1;
                                            ?>
                                                <div class="detail-gallery-unified" style="margin: 0 0 18px;" data-detail-gallery-items="<?php echo esc_attr(wp_json_encode($detail_gallery_items)); ?>">
                                                    <script type="application/json" class="detail-gallery-json"><?php echo str_replace('</script>', '<\/script>', wp_json_encode($detail_gallery_items)); ?></script>
                                                    <div class="detail-gallery-frame">
                                                        <?php if ($has_multi) : ?><button type="button" class="detail-gallery-prev" aria-label="Trước">‹</button><?php endif; ?>
                                                        <div class="detail-gallery-main" role="button" tabindex="0" title="Bấm xem to hơn">
                                                            <?php if (!empty($first['is_video'])) : ?>
                                                                <video class="detail-gallery-media" controls><source src="<?php echo esc_url($first['url']); ?>" type="<?php echo esc_attr($first['mime']); ?>"></video>
                                                            <?php elseif (!empty($first['is_pdf'])) : ?>
                                                                <iframe class="detail-gallery-media detail-gallery-pdf" src="<?php echo esc_url($first['url']); ?>" title="PDF" loading="lazy"></iframe>
                                                            <?php else : ?>
                                                                <img class="detail-gallery-media" src="<?php echo esc_url($first['url']); ?>" alt="" />
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($has_multi) : ?><button type="button" class="detail-gallery-next" aria-label="Sau">›</button><?php endif; ?>
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
                                            <?php
                                            endif;
                                        endif;
                                        ?>
                                        <?php if (!empty($sections)) : ?>
                                            <div class="accordion-sections">
                                                <?php foreach ($sections as $sec) :
                                                    $h = isset($sec['heading']) ? $sec['heading'] : '';
                                                    $c = isset($sec['content']) ? $sec['content'] : '';
                                                    // Multi media (new) + legacy (single image)
                                                    $media_ids = array();
                                                    $media_captions = array();
                                                    if (!empty($sec['media_ids']) && is_array($sec['media_ids'])) {
                                                        $media_ids = array_values(array_filter(array_map('absint', $sec['media_ids'])));
                                                    }
                                                    if (!empty($sec['media_captions']) && is_array($sec['media_captions'])) {
                                                        $media_captions = array_values(array_map('sanitize_text_field', $sec['media_captions']));
                                                    }
                                                    $img_id = isset($sec['image_id']) ? absint($sec['image_id']) : 0;
                                                    $img_caption = isset($sec['image_caption']) ? (string) $sec['image_caption'] : '';
                                                    if (empty($media_ids) && $img_id) {
                                                        $media_ids = array($img_id);
                                                        $media_captions = array($img_caption);
                                                    }
                                                    if ($h === '' && $c === '' && empty($media_ids)) continue;
                                                ?>
                                                    <div class="accordion-section">
                                                        <?php if ($h !== '') : ?><h4 class="accordion-section-title"><?php echo esc_html($h); ?></h4><?php endif; ?>
                                                        <?php
                                                        // Render media gallery like Tin tức unified gallery
                                                        $detail_gallery_items = array();
                                                        foreach ((array) $media_ids as $m_idx => $aid) {
                                                            $aid = (int) $aid;
                                                            if ($aid <= 0) continue;
                                                            $mime = get_post_mime_type($aid);
                                                            $url = '';
                                                            if (is_string($mime) && strpos($mime, 'image') === 0) {
                                                                $url = wp_get_attachment_image_url($aid, 'large');
                                                                if (!$url) $url = wp_get_attachment_url($aid);
                                                            } else {
                                                                $url = wp_get_attachment_url($aid);
                                                            }
                                                            if (!$url) continue;
                                                            $is_video = is_string($mime) && strpos($mime, 'video') === 0;
                                                            $is_pdf = (string) $mime === 'application/pdf';
                                                            $cap = isset($media_captions[$m_idx]) ? (string) $media_captions[$m_idx] : '';
                                                            $detail_gallery_items[] = array(
                                                                'url' => $url,
                                                                'mime' => $mime ?: 'image/jpeg',
                                                                'is_video' => $is_video,
                                                                'is_pdf' => $is_pdf,
                                                                'caption' => $cap,
                                                            );
                                                        }
                                                        if (!empty($detail_gallery_items)) :
                                                            $first = $detail_gallery_items[0];
                                                            $has_multi = count($detail_gallery_items) > 1;
                                                        ?>
                                                            <div class="detail-gallery-unified" style="margin: 12px 0 18px;" data-detail-gallery-items="<?php echo esc_attr(wp_json_encode($detail_gallery_items)); ?>">
                                                                <script type="application/json" class="detail-gallery-json"><?php echo str_replace('</script>', '<\/script>', wp_json_encode($detail_gallery_items)); ?></script>
                                                                <div class="detail-gallery-frame">
                                                                    <?php if ($has_multi) : ?><button type="button" class="detail-gallery-prev" aria-label="Trước">‹</button><?php endif; ?>
                                                                    <div class="detail-gallery-main" role="button" tabindex="0" title="Bấm xem to hơn">
                                                                        <?php if (!empty($first['is_video'])) : ?>
                                                                            <video class="detail-gallery-media" controls><source src="<?php echo esc_url($first['url']); ?>" type="<?php echo esc_attr($first['mime']); ?>"></video>
                                                                        <?php elseif (!empty($first['is_pdf'])) : ?>
                                                                            <iframe class="detail-gallery-media detail-gallery-pdf" src="<?php echo esc_url($first['url']); ?>" title="PDF" loading="lazy"></iframe>
                                                                        <?php else : ?>
                                                                            <img class="detail-gallery-media" src="<?php echo esc_url($first['url']); ?>" alt="" />
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <?php if ($has_multi) : ?><button type="button" class="detail-gallery-next" aria-label="Sau">›</button><?php endif; ?>
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
                                                        <?php if ($c !== '') : ?><?php $c = apply_filters('dnttvn_display_content', $c); ?><div class="accordion-section-content entry-content"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div><?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else : ?>
                                            <p class="accordion-no-sections">Chưa có mục nội dung.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p class="no-items-message">Chưa có bài nào trong Quy trình gia nhập. Vui lòng thêm bài tại <strong>Quy trình gia nhập</strong> trong menu quản trị.</p>
                <?php endif; ?>
<?php dnttvn_page_shell_end(); ?>

<?php get_footer(); ?>
