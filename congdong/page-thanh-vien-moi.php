<?php
/**
 * Template Name: Trang thành viên mới
 * Dùng cho trang Giá trị nhận được của thành viên (hiển thị thành viên đăng ký đã duyệt + nội dung).
 */

get_header();

$slug = get_post()->post_name;
$option_map = array(
    'gia-tri-thanh-vien'    => 'dnttvn_page_gia_tri_content',
);
$content = isset($option_map[$slug]) ? get_option($option_map[$slug], '') : '';
$show_approved_members = ($slug === 'gia-tri-thanh-vien');
?>

<?php dnttvn_page_shell_start(get_the_title()); ?>
<h1 class="cd-detail__title"><?php the_title(); ?></h1>
                <?php
                // Danh sách bài "Giá trị thành viên" (CPT), quản lý giống Quy trình
                $gia_tri_query = new WP_Query(array(
                    'post_type'      => 'gia_tri_thanh_vien',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                ));
                if ($gia_tri_query->have_posts()) : ?>
                    <div class="accordion-list accordion-list-quy-trinh">
                        <?php while ($gia_tri_query->have_posts()) : $gia_tri_query->the_post();
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
                                                        // Render media gallery giống Quy trình
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
                <?php endif; ?>

                <?php if ($content !== '') : ?>
                    <?php $content = apply_filters('dnttvn_display_content', $content); ?>
                    <div class="thanh-vien-page-content entry-content" style="margin-top: 24px;">
                        <?php echo wp_kses_post(wpautop($content)); ?>
                    </div>
                <?php endif; ?>

                <?php if ($show_approved_members) : ?>
                    <?php
                    $approved = new WP_Query(array(
                        'post_type'      => 'dang_ky',
                        'posts_per_page' => -1,
                        'post_status'    => 'publish',
                        'meta_key'       => '_dang_ky_status',
                        'meta_value'     => 'approved',
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ));
                    if ($approved->have_posts()) :
                        ?>
                        <div class="thanh-vien-daduyet-section" style="margin-top: 32px;">
                            <h2 class="thanh-vien-daduyet-title" style="font-size: 20px; margin: 0 0 16px; color: #333;">Thành viên đã duyệt</h2>
                            <div class="thanh-vien-daduyet-list dnttvn-table-scroll-wrapper">
                                <div class="dnttvn-scroll-hint" aria-hidden="true">← Kéo ngang để xem thêm →</div>
                                <table class="thanh-vien-table dnttvn-editor-table">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Họ và tên</th>
                                            <th>SĐT / Zalo</th>
                                            <th>Tên doanh nghiệp</th>
                                            <th>Ngành nghề</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $stt = 0; while ($approved->have_posts()) : $approved->the_post(); $stt++; ?>
                                        <tr>
                                            <td><?php echo (int) $stt; ?></td>
                                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_dang_ky_ho_ten', true)); ?></td>
                                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_dang_ky_sdt', true)); ?></td>
                                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_dang_ky_ten_dn', true)); ?></td>
                                            <td><?php echo esc_html(get_post_meta(get_the_ID(), '_dang_ky_nganh_nghe', true)); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                        wp_reset_postdata();
                    endif;
                    ?>
                <?php endif; ?>
<?php dnttvn_page_shell_end(); ?>

<?php get_footer(); ?>
