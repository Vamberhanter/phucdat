<?php
/**
 * Template Name: Hỏi đáp Cộng đồng
 * Hiển thị danh sách bài Hỏi đáp (tiêu đề + hình + mô tả); bấm vào mở accordion nội dung bên trong.
 */

get_header();

$hoi_dap_args = array(
    'post_type'      => 'hoi_dap',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
);
$hoi_dap_query = new WP_Query($hoi_dap_args);
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
                <?php if ($hoi_dap_query->have_posts()) : ?>
                    <div class="accordion-list accordion-list-hoi-dap">
                        <?php while ($hoi_dap_query->have_posts()) : $hoi_dap_query->the_post();
                            $post_id = get_the_ID();
                            $excerpt = has_excerpt() ? get_the_excerpt() : '';
                            $sections = function_exists('dnttvn_get_sections_array') ? dnttvn_get_sections_array($post_id, '_hoi_dap_sections') : array();
                        ?>
                            <article class="accordion-item" data-post-id="<?php echo esc_attr($post_id); ?>">
                                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-body-<?php echo esc_attr($post_id); ?>" id="accordion-header-<?php echo esc_attr($post_id); ?>">
                                    <div class="accordion-header-inner">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="accordion-thumbnail">
                                                <?php
                                                // Thumbnails trong danh sách Hỏi đáp: thêm loading="lazy" để giảm FCP/TBT
                                                the_post_thumbnail('medium', array(
                                                    'alt'     => esc_attr(get_the_title()),
                                                    'loading' => 'lazy',
                                                ));
                                                ?>
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
                                        <?php if (!empty($sections)) : ?>
                                            <div class="accordion-sections">
                                                <?php foreach ($sections as $sec) :
                                                    $h = isset($sec['heading']) ? $sec['heading'] : '';
                                                    $c = isset($sec['content']) ? $sec['content'] : '';
                                                    if ($h === '' && $c === '') continue;
                                                ?>
                                                    <div class="accordion-section">
                                                        <?php if ($h !== '') : ?><h4 class="accordion-section-title"><?php echo esc_html($h); ?></h4><?php endif; ?>
                                                        <?php if ($c !== '') : ?><?php $c = apply_filters('dnttvn_display_content', $c); ?><div class="accordion-section-content entry-content"><?php echo wp_kses_post(wpautop($c)); ?></div><?php endif; ?>
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
