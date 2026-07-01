<?php
/**
 * Template Name: Trang Cộng đồng
 *
 * Template for displaying Community posts (Cộng đồng)
 * Based on index.php structure
 */

// Debug: Uncomment these lines to debug
/*
if (WP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
*/

// Force create necessary pages if they don't exist
if (function_exists('dnttvn_create_necessary_pages')) {
    dnttvn_create_necessary_pages();
}

get_header();

$post_id_param = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
$current_post_id = 0;
if ($post_id_param > 0) {
    $p = get_post($post_id_param);
    if ($p && $p->post_type === 'cong_dong') {
        $current_post_id = $p->ID;
    }
}
if ($current_post_id === 0) {
    $current_post_id = get_queried_object_id();
}
?>

<main class="main-content">
    <!-- Left Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <ul class="about-list">
                <?php
                // Query Cộng đồng posts for sidebar
                $sidebar_args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                );
                $sidebar_query = new WP_Query($sidebar_args);
                
                if ($sidebar_query->have_posts()) :
                    while ($sidebar_query->have_posts()) : $sidebar_query->the_post();
                        $post_id    = get_the_ID();
                        $is_noi_bat = get_post_meta($post_id, '_cong_dong_noi_bat', true);
                        $li_class   = ($is_noi_bat == '1') ? 'highlight-item' : '';

                        // Nếu đang ở bài này thì tô nổi bật thêm class current-item
                        if ((int) $current_post_id === (int) $post_id) {
                            $li_class .= ' current-item';
                        }
                        ?>
                        <li class="<?php echo esc_attr(trim($li_class)); ?>">
                            <a href="<?php echo esc_url(function_exists('dnttvn_get_cong_dong_detail_url') ? dnttvn_get_cong_dong_detail_url($post_id) : get_permalink($post_id)); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <li><a href="#">Chưa có bài viết Cộng đồng</a></li>
                    <?php
                endif;
                ?>
            </ul>
        </div>
        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
            <?php
            $cong_dong_post = ($post_id_param > 0) ? get_post($post_id_param) : null;
            $current_cong_dong_id = ($cong_dong_post && $cong_dong_post->post_type === 'cong_dong') ? $cong_dong_post->ID : 0;
            $cong_dong_list_url = get_permalink();
            if ($current_cong_dong_id > 0 && $cong_dong_post) {
                global $post;
                $original_post = $post;
                $post = $cong_dong_post;
                setup_postdata($post);
                ?>
                <div class="column-header entry-title"><?php echo esc_html($post->post_title); ?></div>
                <div class="column-content column-content-ve-cong-dong-detail">
                    <div class="news-item">
                        <?php if (get_post_meta($post->ID, '_cong_dong_noi_bat', true) === '1') : ?>
                        <div style="margin-bottom: 15px;"><span style="display: inline-block; background: #667eea; color: #fff; padding: 6px 14px; border-radius: 4px; font-size: 14px; font-weight: 600;">Bài nổi bật</span></div>
                        <?php endif; ?>
                        <div class="news-date" style="margin-bottom: 15px;">
                            <?php
                            $thoi_gian_dang = get_post_meta($post->ID, '_cong_dong_thoi_gian_dang', true);
                            if ($thoi_gian_dang) {
                                $dt = date_create_from_format('Y-m-d\TH:i', $thoi_gian_dang);
                                if (!$dt) $dt = date_create_from_format('Y-m-d H:i:s', $thoi_gian_dang);
                                echo '<span class="news-date-line" style="display: block;"><strong>Ngày đăng:</strong> ' . ($dt ? $dt->format('d/m/Y') : esc_html($thoi_gian_dang)) . '</span>';
                                if ($dt) echo '<span class="news-date-line" style="display: block;"><strong>Giờ đăng:</strong> ' . esc_html($dt->format('H:i')) . '</span>';
                            } else {
                                echo '<span class="news-date-line" style="display: block;"><strong>Ngày đăng:</strong> ' . esc_html(get_the_date('d/m/Y', $post->ID)) . '</span>';
                            }
                            ?>
                        </div>
                        <?php $mo_ta_ngan = get_post_meta($post->ID, '_cong_dong_mo_ta_ngan', true); if ($mo_ta_ngan) : ?>
                        <div class="news-excerpt" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #667eea; color: #444;"><?php echo wp_kses_post($mo_ta_ngan); ?></div>
                        <?php endif; ?>
                        <?php
                        $detail_gallery_items = array();
                        if (has_post_thumbnail($post->ID)) {
                            $feat_id = get_post_thumbnail_id($post->ID);
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
                        $hinh_phu_raw = get_post_meta($post->ID, '_cong_dong_hinh_phu', true);
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
                        <?php endif;
                        $items = function_exists('dnttvn_get_structured_content_array') ? dnttvn_get_structured_content_array($post->ID) : array();
                        $is_featured = get_post_meta($post->ID, '_cong_dong_noi_bat', true);
                        if (!empty($items)) {
                            echo '<div class="structured-content-display" style="margin-top: 30px;">';
                            foreach ($items as $item) {
                                $has_heading_or_content = !empty($item['heading']) || !empty($item['content']);
                                $has_content_items = !empty($item['content_items']) && is_array($item['content_items']);
                                $has_content = $has_heading_or_content || $has_content_items;
                                $has_images = !$is_featured && !empty($item['images']) && is_array($item['images']);
                                if (!$has_content && !$has_images) continue;
                                echo '<div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">';
                                if (!empty($item['heading'])) echo '<h3 style="font-size: 26px; font-weight: bold; color: #333; margin-bottom: 15px;">' . esc_html($item['heading']) . '</h3>';
                                $images = isset($item['images']) ? $item['images'] : array();
                                $image_captions = isset($item['image_captions']) ? $item['image_captions'] : array();
                                if (!empty($images) && is_array($images)) {
                                    echo '<div class="structured-images-gallery" style="margin: 20px 0;"><div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;">';
                                    foreach ($images as $img_index => $attachment_id) {
                                        $attachment_id = intval($attachment_id);
                                        if ($attachment_id <= 0) continue;
                                        $mime_type = get_post_mime_type($attachment_id);
                                        $is_video = strpos($mime_type, 'video') === 0;
                                        $full_url = wp_get_attachment_url($attachment_id);
                                        if (!$full_url) continue;
                                        echo '<div class="gallery-item-display" style="flex: 0 0 auto;">';
                                        if ($is_video) echo '<video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="' . esc_url($full_url) . '" type="' . esc_attr($mime_type) . '"></video>';
                                        else echo '<img src="' . esc_url($full_url) . '" alt="" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;">';
                                        if (!empty($image_captions[$img_index])) echo '<p style="margin-top: 8px; font-size: 13px; color: #666; font-style: italic; text-align: center; max-width: 250px;">' . esc_html($image_captions[$img_index]) . '</p>';
                                        echo '</div>';
                                    }
                                    echo '</div></div>';
                                }
                                if (!empty($item['content_items']) && is_array($item['content_items'])) {
                                    foreach ($item['content_items'] as $content_item) {
                                        $text = is_string($content_item) ? $content_item : (isset($content_item['text']) ? $content_item['text'] : '');
                                        $ci_images = is_array($content_item) && isset($content_item['images']) ? $content_item['images'] : array();
                                        $ci_captions = is_array($content_item) && isset($content_item['image_captions']) ? $content_item['image_captions'] : array();
                                        echo '<div class="content-item-display" style="margin-bottom: 30px;">';
                                        if (!empty($text)) { $c = apply_filters('dnttvn_display_content', $text); echo '<div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555; margin-bottom: 15px;">' . dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)) . '</div>'; }
                                        if (!empty($ci_images)) {
                                            echo '<div class="content-item-images-gallery" style="display: flex; flex-wrap: wrap; gap: 15px;">';
                                            foreach ($ci_images as $ci_img_idx => $ci_img_id) {
                                                $ci_img_id = intval($ci_img_id);
                                                if ($ci_img_id <= 0) continue;
                                                $ci_mime = get_post_mime_type($ci_img_id);
                                                $ci_is_video = strpos($ci_mime, 'video') === 0;
                                                $ci_url = wp_get_attachment_url($ci_img_id);
                                                $ci_caption = isset($ci_captions[$ci_img_idx]) ? $ci_captions[$ci_img_idx] : '';
                                                if (!$ci_url) continue;
                                                echo '<div style="flex: 0 0 auto;">';
                                                if ($ci_is_video) echo '<video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="' . esc_url($ci_url) . '" type="' . esc_attr($ci_mime) . '"></video>';
                                                else echo '<img src="' . esc_url($ci_url) . '" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;">';
                                                if ($ci_caption) echo '<p style="margin-top: 5px; font-size: 13px; color: #666; max-width: 250px;">' . esc_html($ci_caption) . '</p>';
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                    }
                                } elseif (!empty($item['content'])) {
                                    $c = apply_filters('dnttvn_display_content', $item['content']);
                                    echo '<div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555;">' . dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)) . '</div>';
                                }
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div style="line-height: 1.8; font-size: 20px;">';
                            if (!empty($post->post_content)) echo apply_filters('the_content', $post->post_content);
                            else echo '<p style="color: #666; font-style: italic;">Nội dung bài viết đang được cập nhật...</p>';
                            echo '</div>';
                        }
                        if (function_exists('dnttvn_render_excel_tables')) {
                            dnttvn_render_excel_tables($post->ID);
                        }
                        $categories = get_the_terms($post->ID, 'category');
                        $tags = get_the_terms($post->ID, 'post_tag');
                        if (($categories && !is_wp_error($categories)) || ($tags && !is_wp_error($tags))) {
                            echo '<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">';
                            if ($categories && !is_wp_error($categories)) {
                                $cat_names = array(); foreach ($categories as $cat) { $cat_names[] = '<a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a>'; }
                                echo '<div style="margin-bottom: 10px;"><strong>Danh mục:</strong> ' . implode(', ', $cat_names) . '</div>';
                            }
                            if ($tags && !is_wp_error($tags)) {
                                $tag_names = array(); foreach ($tags as $tag) { $tag_names[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>'; }
                                echo '<div><strong>Thẻ:</strong> ' . implode(', ', $tag_names) . '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                        <?php
                        $flipbook_url = get_post_meta($post->ID, '_cong_dong_flipbook_url', true);
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

                        <div class="back-to-cong-dong-list" style="margin-top: 40px; padding-top: 20px; padding-bottom: 20px; border-top: 2px solid #667eea;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                <?php
                                $prev_post = get_previous_post();
                                $next_post = get_next_post();
                                if ($prev_post && function_exists('dnttvn_get_cong_dong_detail_url')) : ?>
                                <a href="<?php echo esc_url(dnttvn_get_cong_dong_detail_url($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">← <?php echo esc_html($prev_post->post_title); ?></a>
                                <?php else : ?><span></span><?php endif; ?>
                                <?php if ($next_post && function_exists('dnttvn_get_cong_dong_detail_url')) : ?>
                                <a href="<?php echo esc_url(dnttvn_get_cong_dong_detail_url($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;"><?php echo esc_html($next_post->post_title); ?> →</a>
                                <?php else : ?><span></span><?php endif; ?>
                            </div>
                            <div style="text-align: center; padding-top: 15px; border-top: 1px solid #eee;">
                                <a href="<?php echo esc_url($cong_dong_list_url); ?>" class="button" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">← Quay lại danh sách Cộng đồng</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $post = $original_post;
                wp_reset_postdata();
            } else {
            ?>
            <div class="column-header">Bài viết Cộng đồng</div>
            
            <!-- Sort Section -->
            <div class="sort-section" style="margin-bottom: 20px;">
                <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="cong-dong-sort-form">
                    <div class="sort-controls">
                        <label for="cong_dong_sort_by">Sắp xếp:</label>
                        <select name="cong_dong_sort_by" id="cong_dong_sort_by" onchange="document.getElementById('cong-dong-sort-form').submit();">
                            <option value="menu_order" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'menu_order' ? 'selected' : ''; ?>>Thứ tự đăng bài</option>
                            <option value="date_desc" <?php echo (isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'date_desc') || !isset($_GET['cong_dong_sort_by']) ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="date_asc" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'date_asc' ? 'selected' : ''; ?>>Cũ nhất</option>
                            <option value="title_asc" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'title_asc' ? 'selected' : ''; ?>>Tiêu đề A-Z</option>
                            <option value="title_desc" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'title_desc' ? 'selected' : ''; ?>>Tiêu đề Z-A</option>
                            <option value="noi_bat" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'noi_bat' ? 'selected' : ''; ?>>Bài nổi bật trước</option>
                        </select>
                    </div>

                </form>
            </div>
            
            <div class="column-content">
                <?php
                // Get sort parameter
                $cong_dong_sort_by = isset($_GET['cong_dong_sort_by']) ? sanitize_text_field($_GET['cong_dong_sort_by']) : 'date_desc';
                
                // Phân trang & sắp xếp cho Cộng đồng
                $paged_cong_dong = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => 10,
                    'post_status'    => 'publish',
                    'paged'          => $paged_cong_dong,
                );
                
                // Handle sorting
                switch ($cong_dong_sort_by) {
                    case 'menu_order':
                        $args['orderby'] = 'menu_order date';
                        $args['order'] = 'ASC';
                        break;
                    case 'date_asc':
                        $args['orderby'] = 'date';
                        $args['order'] = 'ASC';
                        break;
                    case 'title_asc':
                        $args['orderby'] = 'title';
                        $args['order'] = 'ASC';
                        break;
                    case 'title_desc':
                        $args['orderby'] = 'title';
                        $args['order'] = 'DESC';
                        break;
                    case 'noi_bat':
                        $args['meta_key'] = '_cong_dong_noi_bat';
                        $args['orderby'] = 'meta_value date';
                        $args['order'] = 'DESC';
                        break;
                    case 'date_desc':
                    default:
                        $args['orderby'] = 'date';
                        $args['order'] = 'DESC';
                        break;
                }
                
                $cong_dong_query = new WP_Query($args);
                
                if ($cong_dong_query->have_posts()) :
                    while ($cong_dong_query->have_posts()) : $cong_dong_query->the_post();
                        // Show excerpt/summary for list view
                        ?>
                        <div class="news-item" style="display: flex; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="news-thumbnail" style="flex-shrink: 0;">
                                    <a href="<?php echo esc_url(function_exists('dnttvn_get_cong_dong_detail_url') ? dnttvn_get_cong_dong_detail_url(get_the_ID()) : get_permalink()); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                        <?php the_post_thumbnail('medium', array('style' => 'width: 120px; height: 80px; object-fit: cover; border-radius: 4px;', 'alt' => esc_attr(get_the_title()))); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="news-content" style="flex: 1;">
                                <h3 style="margin: 0 0 8px 0; font-size: 14px;">
                                    <a href="<?php echo esc_url(function_exists('dnttvn_get_cong_dong_detail_url') ? dnttvn_get_cong_dong_detail_url(get_the_ID()) : get_permalink()); ?>" style="text-decoration: none; color: #333;"><?php the_title(); ?></a>
                                </h3>
                                <p class="news-date" style="margin: 0 0 8px 0; font-size: 12px; color: #666;">
                                    <?php echo get_the_date('d/m/Y'); ?>
                                </p>
                                <div class="news-excerpt" style="font-size: 10px; color: #555; line-height: 1.4;">
                                    <?php
                                    // Try to get custom short description first
                                    $mo_ta_ngan = get_post_meta(get_the_ID(), '_cong_dong_mo_ta_ngan', true);
                                    if ($mo_ta_ngan) {
                                        echo esc_html($mo_ta_ngan);
                                    } elseif (has_excerpt()) {
                                        the_excerpt();
                                    } else {
                                        echo wp_trim_words(get_the_content(), 30);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    ?>
                    <div class="pagination-wrapper">
                        <?php
                        echo dnttvn_custom_pagination($cong_dong_query);
                        ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="news-item">
                        <p>Chưa có bài viết Cộng đồng nào. Vui lòng thêm bài viết từ trang quản trị WordPress.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            <?php } ?>
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
