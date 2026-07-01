<?php
/**
 * Template Name: Phụng Doanh nhân
 * Hiển thị danh sách bài Phụng Doanh nhân (tiêu đề + hình + mô tả); bấm vào mở accordion nội dung bên trong.
 */

get_header();

$args = array(
    'post_type'      => 'phung_doanh_nhan',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
);
$query = new WP_Query($args);
?>

<main class="main-content">
    <!-- Left Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <ul class="about-list">
                <?php
                $cong_dong_args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                );
                $cong_dong_query = new WP_Query($cong_dong_args);
                if ($cong_dong_query->have_posts()) :
                    while ($cong_dong_query->have_posts()) :
                        $cong_dong_query->the_post();
                        $is_noi_bat = get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);
                        $li_class   = ($is_noi_bat == '1') ? 'highlight-item' : '';
                        ?>
                        <li class="<?php echo esc_attr($li_class); ?>">
                            <a href="<?php echo esc_url(function_exists('dnttvn_get_cong_dong_detail_url') ? dnttvn_get_cong_dong_detail_url(get_the_ID()) : get_permalink()); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <li><a href="#">Chưa có bài viết Cộng đồng</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
            <div class="column-header"><?php the_title(); ?></div>
            <div class="column-content">
                <?php if ($query->have_posts()) : ?>
                    <div class="accordion-list accordion-list-quy-trinh">
                        <?php while ($query->have_posts()) : $query->the_post();
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
                                                    $media_ids = isset($sec['media_ids']) && is_array($sec['media_ids']) ? array_values(array_filter(array_map('absint', $sec['media_ids']))) : array();
                                                    $media_captions = isset($sec['media_captions']) && is_array($sec['media_captions']) ? array_values(array_map('sanitize_text_field', $sec['media_captions'])) : array();
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
                    <p class="no-items-message">Chưa có bài nào trong mục này. Vui lòng thêm bài tại <strong>Phụng Doanh nhân</strong> trong menu quản trị.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sidebar-column">
        <?php get_template_part('template-parts/sidebar-su-kien'); ?>

        <div class="column-header mobile-toggle collapsed">Website liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                $community_links = array_slice($community_links, 0, 9);
                foreach ($community_links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</main>

<?php get_footer(); ?>

