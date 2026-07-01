<?php
/**
 * Template Name: Chi tiết Tin tức
 * 
 * Template for displaying single news post detail page
 * Based on website/index.html layout
 */

get_header();
?>

<main class="main-content">
    <!-- Left Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <?php
            // Get sticky/pinned posts for tin_tuc post type
            $sticky_args = array(
                'post_type'      => 'tin_tuc',
                'posts_per_page' => 5,
                'post_status'    => 'publish',
                'meta_query'     => array(
                    array(
                        'key'     => '_tin_tuc_noi_bat',
                        'value'   => '1',
                        'compare' => '='
                    )
                ),
                'orderby'        => 'menu_order date',
                'order'          => 'ASC',
            );
            
            // Detail page for news/business
            // Use standard WordPress permalink
            
            $sticky_query = new WP_Query($sticky_args);
            
            if ($sticky_query->have_posts()) :
                ?>
                <ul class="sticky-news-list">
                    <?php
                    while ($sticky_query->have_posts()) : $sticky_query->the_post();
                        // Use page-based URL with post_id parameter
                        $detail_url = add_query_arg('post_id', get_the_ID(), home_url('/trang-tin-tuc-chi-tiet/'));
                        ?>
                        <li class="sticky-news-item">
                            <a href="<?php echo esc_url($detail_url); ?>">
                                <h5><?php the_title(); ?></h5>
                                <span class="sticky-news-date"><?php echo get_the_date('d/m/Y'); ?></span>
                            </a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </ul>
                <?php
            else :
                ?>
                <p style="padding: 15px; color: #666; font-size: 14px;">Chưa có tin tức được ghim.</p>
                <?php
            endif;
            ?>
        </div>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
            <?php while (have_posts()) : the_post(); ?>
                <div class="column-header"><?php the_title(); ?></div>
                <div class="column-content">
                    <div class="news-item">
                        <div class="news-date" style="margin-bottom: 15px;">
                            <span class="news-date-line" style="display: block;"><strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?></span>
                            <?php
                            $tac_gia = get_post_meta(get_the_ID(), '_tin_tuc_tac_gia', true);
                            if ($tac_gia) {
                                echo '<span class="news-date-line" style="display: block;"><strong>Tác giả:</strong> ' . esc_html($tac_gia) . '</span>';
                            }
                            $nguon = get_post_meta(get_the_ID(), '_tin_tuc_nguon', true);
                            if ($nguon) {
                                echo '<span class="news-date-line" style="display: block;"><strong>Nguồn:</strong> <a href="' . esc_url($nguon) . '" target="_blank">Xem nguồn</a></span>';
                            }
                            ?>
                        </div>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div style="margin-bottom: 20px; text-align: center;">
                                <?php the_post_thumbnail('large', array('style' => 'max-width: 100%; height: auto; border-radius: 8px;')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        // Display structured content if available
                        $items = dnttvn_get_structured_content_array(get_the_ID());
                        $is_featured = get_post_meta(get_the_ID(), '_tin_tuc_noi_bat', true) || get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);

                        if (!empty($items)) {
                                ?>
                                <div class="structured-content-display" style="margin-top: 30px;">
                                    <?php foreach ($items as $item) : ?>
                                        <?php
                                        $has_content = !empty($item['heading']) || !empty($item['content']);
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

                                                <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                                    <?php foreach ($item['content_items'] as $content_item) : if (is_string($content_item)) { $text = $content_item; $ci_images = []; $ci_captions = []; } else { $text = $content_item['text'] ?? ''; $ci_images = $content_item['images'] ?? []; $ci_captions = $content_item['image_captions'] ?? []; } ?>
                                                        <div class="content-item-display" style="margin-bottom: 30px;">
                                                            <?php if (!empty($text)) : ?><?php $c = apply_filters('dnttvn_display_content', $text); ?><div class="entry-content structured-content-text" style="line-height: 1.8; font-size: 20px; color: #555; margin-bottom: 15px;"><?php echo dnttvn_kses_structured_content(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div><?php endif; ?>
                                                            <?php if (!empty($ci_images)) : ?><div class="content-item-images-gallery" style="display: flex; flex-wrap: wrap; gap: 15px;"><?php foreach ($ci_images as $ci_img_idx => $ci_img_id) : $ci_img_id = intval($ci_img_id); if ($ci_img_id > 0) : $ci_mime = get_post_mime_type($ci_img_id); $ci_is_video = strpos($ci_mime, 'video') === 0; $ci_url = wp_get_attachment_url($ci_img_id); $ci_caption = $ci_captions[$ci_img_idx] ?? ''; ?><div style="flex: 0 0 auto;"><?php if ($ci_is_video) : ?><video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="<?php echo esc_url($ci_url); ?>" type="<?php echo esc_attr($ci_mime); ?>"></video><?php else : ?><img src="<?php echo esc_url($ci_url); ?>" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;"><?php endif; ?><?php if ($ci_caption) : ?><p style="margin-top: 5px; font-size: 13px; color: #666; max-width: 250px;"><?php echo esc_html($ci_caption); ?></p><?php endif; ?></div><?php endif; endforeach; ?></div><?php endif; ?>
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
                                    <?php the_content(); ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        
                        <?php
                        // Display categories and tags
                        $categories = get_the_terms(get_the_ID(), 'category');
                        $tags = get_the_terms(get_the_ID(), 'post_tag');
                        ?>
                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                                <strong>Danh mục:</strong>
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
                            <div style="margin-top: 15px;">
                                <strong>Thẻ:</strong>
                                <?php
                                $tag_names = array();
                                foreach ($tags as $tag) {
                                    $tag_names[] = '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a>';
                                }
                                echo ' ' . implode(', ', $tag_names);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Navigation to previous/next post -->
                        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #667eea; display: flex; justify-content: space-between;">
                            <div>
                                <?php
                                $prev_post = get_previous_post();
                                if ($prev_post) :
                                    ?>
                                    <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                        ← <?php echo esc_html($prev_post->post_title); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php
                                $next_post = get_next_post();
                                if ($next_post) :
                                    ?>
                                    <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                        <?php echo esc_html($next_post->post_title); ?> →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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
