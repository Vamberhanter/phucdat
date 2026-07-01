<?php
/**
 * Template Name: Trang Tin Tức
 * Description: Template hiển thị danh sách tin tức
 */

get_header();
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
                            <a href="<?php echo esc_url(dnttvn_get_cong_dong_detail_url(get_the_ID())); ?>"><?php the_title(); ?></a>
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
            <div class="column-header">Tin Tức Cộng Đồng</div>
            <div class="column-content">
                <?php
                // Query tin tức posts
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $tin_tuc_args = array(
                    'post_type'      => 'tin_tuc',
                    'posts_per_page' => 10,
                    'paged'          => $paged,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'meta_query'     => array(
                        array(
                            'key'     => '_tin_tuc_noi_bat',
                            'compare' => 'NOT EXISTS' // Show featured posts first, then regular posts
                        )
                    )
                );

                // First get featured posts
                $featured_args = array(
                    'post_type'      => 'tin_tuc',
                    'posts_per_page' => -1,
                    'meta_query'     => array(
                        array(
                            'key'   => '_tin_tuc_noi_bat',
                            'value' => '1',
                            'compare' => '='
                        )
                    ),
                    'orderby' => 'date',
                    'order'   => 'DESC'
                );

                $featured_query = new WP_Query($featured_args);
                $regular_query = new WP_Query($tin_tuc_args);
                ?>

                <!-- Featured News -->
                <?php if ($featured_query->have_posts()) : ?>
                    <div class="featured-news-section" style="margin-bottom: 40px;">
                        <h3 style="color: #007cba; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #007cba;">📌 Tin Nổi Bật</h3>
                        <?php while ($featured_query->have_posts()) : $featured_query->the_post();
                            $detail_url = dnttvn_get_tin_tuc_detail_url(get_the_ID());
                            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30);
                        ?>
                            <div class="featured-news-item" style="background: #f8f9fa; padding: 20px; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid #007cba;">
                                <div style="display: flex; gap: 20px;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div style="flex-shrink: 0;">
                                            <a href="<?php echo esc_url($detail_url); ?>">
                                                <?php the_post_thumbnail('medium', array('style' => 'width: 200px; height: 150px; object-fit: cover; border-radius: 4px;')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 10px 0; font-size: 18px;">
                                            <a href="<?php echo esc_url($detail_url); ?>" style="text-decoration: none; color: #333;"><?php the_title(); ?></a>
                                        </h4>
                                        <div class="news-meta news-date" style="margin: 0 0 15px 0; font-size: 14px; color: #666;">
                                            <span class="news-date-line" style="display: block;"><strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?></span>
                                            <?php
                                            $tac_gia = get_post_meta(get_the_ID(), '_tin_tuc_tac_gia', true);
                                            if ($tac_gia) {
                                                echo '<span class="news-date-line" style="display: block;"><strong>Tác giả:</strong> ' . esc_html($tac_gia) . '</span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="news-excerpt" style="font-size: 12px; color: #555; line-height: 1.6;">
                                            <?php echo esc_html($excerpt); ?>
                                        </div>
                                        <div style="margin-top: 15px;">
                                            <a href="<?php echo esc_url($detail_url); ?>" class="read-more-btn" style="background: #007cba; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-size: 14px;">Đọc tiếp →</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                <?php endif; ?>

                <!-- Regular News -->
                <div class="regular-news-section">
                    <h3 style="margin-bottom: 20px; color: #333;">📄 Tất Cả Tin Tức</h3>

                    <?php if ($regular_query->have_posts()) : ?>
                        <div class="news-list">
                            <?php while ($regular_query->have_posts()) : $regular_query->the_post();
                                $detail_url = dnttvn_get_tin_tuc_detail_url(get_the_ID());
                                $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25);
                            ?>
                                <div class="news-item" style="display: flex; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="news-thumbnail" style="flex-shrink: 0;">
                                            <a href="<?php echo esc_url($detail_url); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                                                <?php the_post_thumbnail('medium', array('style' => 'width: 120px; height: 80px; object-fit: cover; border-radius: 4px;', 'alt' => esc_attr(get_the_title()))); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="news-content" style="flex: 1;">
                                        <h3 style="margin: 0 0 8px 0; font-size: 14px;">
                                            <a href="<?php echo esc_url($detail_url); ?>" style="text-decoration: none; color: #333;"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="news-date" style="margin: 0 0 8px 0; font-size: 12px; color: #666;">
                                            <span class="news-date-line" style="display: block;"><?php echo get_the_date('d/m/Y'); ?></span>
                                            <?php
                                            $tac_gia = get_post_meta(get_the_ID(), '_tin_tuc_tac_gia', true);
                                            if ($tac_gia) {
                                                echo '<span class="news-date-line" style="display: block;"><strong>Tác giả:</strong> ' . esc_html($tac_gia) . '</span>';
                                            }
                                            ?>
                                        </div>
                                        <div class="news-excerpt" style="font-size: 10px; color: #555; line-height: 1.4;">
                                            <?php echo esc_html($excerpt); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- Pagination -->
                        <div class="pagination" style="margin-top: 40px; text-align: center;">
                            <?php
                            echo paginate_links(array(
                                'total' => $regular_query->max_num_pages,
                                'current' => $paged,
                                'prev_text' => '← Trang trước',
                                'next_text' => 'Trang sau →',
                                'base' => home_url('/tin-tuc/%_%'),
                                'format' => 'page/%#%/',
                            ));
                            ?>
                        </div>

                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <p style="text-align: center; color: #666; padding: 40px;">Chưa có tin tức nào được đăng.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Website liên kết (tối đa 9 link), quản lý trong Banner Header → Website liên kết -->
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