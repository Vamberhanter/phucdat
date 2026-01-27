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
            
            $sticky_query = new WP_Query($sticky_args);
            
            if ($sticky_query->have_posts()) :
                ?>
                <ul class="sticky-news-list">
                    <?php
                    while ($sticky_query->have_posts()) : $sticky_query->the_post();
                        ?>
                        <li class="sticky-news-item">
                            <a href="<?php the_permalink(); ?>">
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
                        
                        <div style="line-height: 1.8; font-size: 16px;">
                            <?php the_content(); ?>
                        </div>
                        
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
                if ($doanh_nghiep_query->have_posts()) :
                    while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                        ?>
                        <li><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></li>
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
