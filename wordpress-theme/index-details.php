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
            $detail_page      = get_page_by_path('trang-doanh-nghiep-chi-tiet');
            $detail_page_base = $detail_page ? get_permalink($detail_page->ID) : home_url('/trang-doanh-nghiep-chi-tiet/');
            
            $sticky_query = new WP_Query($sticky_args);
            
            if ($sticky_query->have_posts()) :
                ?>
                <ul class="sticky-news-list">
                    <?php
                    while ($sticky_query->have_posts()) : $sticky_query->the_post();
                        // Build detail URL with post_id for this news item
                        $detail_url = add_query_arg(
                            'post_id',
                            get_the_ID(),
                            $detail_page_base
                        );
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
                        <p class="news-date">
                            <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?>
                            <?php 
                            $tac_gia = get_post_meta(get_the_ID(), '_tin_tuc_tac_gia', true);
                            if ($tac_gia) {
                                echo ' | <strong>Tác giả:</strong> ' . esc_html($tac_gia);
                            }
                            $nguon = get_post_meta(get_the_ID(), '_tin_tuc_nguon', true);
                            if ($nguon) {
                                echo ' | <strong>Nguồn:</strong> <a href="' . esc_url($nguon) . '" target="_blank">Xem nguồn</a>';
                            }
                            ?>
                        </p>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div style="margin-bottom: 20px; text-align: center;">
                                <?php the_post_thumbnail('large', array('style' => 'max-width: 100%; height: auto; border-radius: 8px;')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        // Display structured content if available
                        $structured_content = get_post_meta(get_the_ID(), '_structured_content', true);
                        if (!empty($structured_content)) {
                            $items = json_decode($structured_content, true);
                            if (is_array($items) && !empty($items)) {
                                ?>
                                <div class="structured-content-display" style="margin-top: 30px;">
                                    <?php foreach ($items as $item) : ?>
                                        <?php
                                        $has_content = !empty($item['heading']) || !empty($item['content']);
                                        $has_images = !empty($item['images']) && is_array($item['images']);
                                        if ($has_content || $has_images) :
                                        ?>
                                            <div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                                <?php if (!empty($item['heading'])) : ?>
                                                    <h3 style="font-size: 20px; font-weight: bold; color: #333; margin-bottom: 15px;">
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

                                                <?php if (!empty($item['content'])) : ?>
                                                    <div style="line-height: 1.8; font-size: 16px; color: #555;">
                                                        <?php echo wp_kses_post(wpautop($item['content'])); ?>
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
                                <div style="line-height: 1.8; font-size: 16px;">
                                    <?php the_content(); ?>
                                </div>
                                <?php
                            }
                        } else {
                            // No structured content, show regular content
                            ?>
                            <div style="line-height: 1.8; font-size: 16px;">
                                <?php the_content(); ?>
                            </div>
                            <?php
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
        <div class="column-header mobile-toggle collapsed">Website liên kết</div>
        <div class="column-content mobile-collapsed">
            <h4 style="margin-bottom: 15px; color: #333;">Danh sách Doanh nghiệp</h4>
            <ul class="linked-websites">
                <?php
                // Get page link for "Danh sách Doanh nghiệp"
                $page_doanh_nghiep = get_page_by_path('danh-sach-doanh-nghiep');
                if (!$page_doanh_nghiep) {
                    $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
                }
                if ($page_doanh_nghiep) {
                    $doanh_nghiep_page_url = get_permalink($page_doanh_nghiep->ID);
                } else {
                    $doanh_nghiep_page_url = home_url('/danh-sach-doanh-nghiep/');
                }
                ?>
                <li><a href="<?php echo esc_url($doanh_nghiep_page_url); ?>">Danh sách Doanh nghiệp</a></li>
                <?php
                $doanh_nghiep_args = array(
                    'post_type'      => 'doanh_nghiep',
                    'posts_per_page' => 4,
                    'post_status'    => 'publish',
                );
                $doanh_nghiep_query = new WP_Query($doanh_nghiep_args);

                // Detail page for doanh_nghiep
                $detail_page      = get_page_by_path('trang-doanh-nghiep-chi-tiet');
                $detail_page_base = $detail_page ? get_permalink($detail_page->ID) : home_url('/trang-doanh-nghiep-chi-tiet/');

                if ($doanh_nghiep_query->have_posts()) :
                    while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                        // Build detail URL with post_id for this business
                        $detail_url = add_query_arg(
                            'post_id',
                            get_the_ID(),
                            $detail_page_base
                        );
                        ?>
                        <li><a href="<?php echo esc_url($detail_url); ?>"><?php the_title(); ?></a></li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
            <h4 style="margin-top: 25px; margin-bottom: 15px; color: #333;">Cộng đồng</h4>
            <ul class="linked-websites">
                <li><a href="#">Cộng đồng Doanh nhân Trẻ</a></li>
                <li><a href="#">Cộng đồng Khởi nghiệp</a></li>
                <li><a href="#">Cộng đồng Đầu tư</a></li>
            </ul>
        </div>
    </div>
</main>

<?php get_footer(); ?>
