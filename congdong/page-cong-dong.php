<?php
/**
 * Template Name: Trang Cộng đồng
 * Layout hiện đại 3 cột — đồng bộ trang chủ.
 */

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

$search_q = isset($_GET['cd_q']) ? sanitize_text_field(wp_unslash($_GET['cd_q'])) : '';
$sort_by  = isset($_GET['cong_dong_sort_by']) ? sanitize_text_field(wp_unslash($_GET['cong_dong_sort_by'])) : 'date_desc';

$crumb = $current_post_id ? get_the_title($current_post_id) : 'Cộng đồng';
get_template_part('template-parts/breadcrumb', null, array('current' => $crumb));
?>

<main class="cd-page">
    <div class="cd-page__inner">
        <?php get_template_part('template-parts/sidebar', 'left', array('current_id' => $current_post_id)); ?>

        <div class="cd-main">
            <?php
            $cong_dong_post       = ($post_id_param > 0) ? get_post($post_id_param) : null;
            $current_cong_dong_id = ($cong_dong_post && $cong_dong_post->post_type === 'cong_dong') ? $cong_dong_post->ID : 0;
            $cong_dong_list_url   = get_permalink();

            if ($current_cong_dong_id > 0 && $cong_dong_post) :
                global $post;
                $original_post = $post;
                $post = $cong_dong_post;
                setup_postdata($post);
                ?>
                <article class="cd-main-card cd-detail">
                    <h1 class="cd-detail__title"><?php echo esc_html($post->post_title); ?></h1>
                    <?php if (!dnttvn_is_tong_quan_cong_dong_post($post->ID)) : ?>
                    <div class="cd-detail__meta">
                        <span><?php echo esc_html(get_the_date('d/m/Y', $post->ID)); ?></span>
                        <?php if (get_post_meta($post->ID, '_cong_dong_noi_bat', true) === '1') : ?>
                            <span class="cd-badge">Nổi bật</span>
                        <?php endif; ?>
                    </div>
                    <?php
                    $mo_ta_ngan = get_post_meta($post->ID, '_cong_dong_mo_ta_ngan', true);
                    if ($mo_ta_ngan) :
                        ?>
                        <div class="cd-detail__excerpt"><?php echo wp_kses_post($mo_ta_ngan); ?></div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <div class="cd-detail__body entry-content">
                        <?php dnttvn_render_cong_dong_detail_content($post->ID); ?>
                    </div>

                    <div class="cd-detail__nav">
                        <a class="cd-btn-outline" href="<?php echo esc_url($cong_dong_list_url); ?>">← Quay lại danh sách</a>
                    </div>
                </article>
                <?php
                $post = $original_post;
                wp_reset_postdata();
            else :
                $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;
                $args  = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => 10,
                    'post_status'    => 'publish',
                    'paged'          => $paged,
                );
                if ($search_q !== '') {
                    $args['s'] = $search_q;
                }
                switch ($sort_by) {
                    case 'menu_order':
                        $args['orderby'] = 'menu_order date';
                        $args['order']   = 'ASC';
                        break;
                    case 'date_asc':
                        $args['orderby'] = 'date';
                        $args['order']   = 'ASC';
                        break;
                    case 'title_asc':
                        $args['orderby'] = 'title';
                        $args['order']   = 'ASC';
                        break;
                    case 'title_desc':
                        $args['orderby'] = 'title';
                        $args['order']   = 'DESC';
                        break;
                    case 'noi_bat':
                        $args['meta_key'] = '_cong_dong_noi_bat';
                        $args['orderby']  = 'meta_value date';
                        $args['order']    = 'DESC';
                        break;
                    default:
                        $args['orderby'] = 'date';
                        $args['order']   = 'DESC';
                        break;
                }
                $cong_dong_query = new WP_Query($args);
                ?>
                <section class="cd-main-card">
                    <form class="cd-toolbar" method="get" action="<?php echo esc_url($cong_dong_list_url); ?>">
                        <div class="cd-toolbar__search">
                            <input type="search" name="cd_q" value="<?php echo esc_attr($search_q); ?>" placeholder="Tìm kiếm bài viết...">
                        </div>
                        <div class="cd-toolbar__filters">
                            <label>
                                <span class="screen-reader-text">Sắp xếp</span>
                                <select name="cong_dong_sort_by" onchange="this.form.submit()">
                                    <option value="date_desc" <?php selected($sort_by, 'date_desc'); ?>>Sắp xếp: Mới nhất</option>
                                    <option value="date_asc" <?php selected($sort_by, 'date_asc'); ?>>Cũ nhất</option>
                                    <option value="title_asc" <?php selected($sort_by, 'title_asc'); ?>>Tiêu đề A-Z</option>
                                    <option value="menu_order" <?php selected($sort_by, 'menu_order'); ?>>Thứ tự</option>
                                    <option value="noi_bat" <?php selected($sort_by, 'noi_bat'); ?>>Nổi bật</option>
                                </select>
                            </label>
                            <button type="submit" class="cd-toolbar__btn">Tìm kiếm</button>
                        </div>
                    </form>

                    <p class="cd-toolbar__count">Tổng số: <strong><?php echo (int) $cong_dong_query->found_posts; ?> bài viết</strong></p>

                    <div class="cd-article-list">
                        <?php
                        if ($cong_dong_query->have_posts()) :
                            while ($cong_dong_query->have_posts()) :
                                $cong_dong_query->the_post();
                                $url = function_exists('dnttvn_get_cong_dong_detail_url')
                                    ? dnttvn_get_cong_dong_detail_url(get_the_ID())
                                    : get_permalink();
                                $mo_ta = get_post_meta(get_the_ID(), '_cong_dong_mo_ta_ngan', true);
                                if (!$mo_ta) {
                                    $mo_ta = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 28);
                                }
                                ?>
                                <article class="cd-article-row">
                                    <div class="cd-article-row__icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                    </div>
                                    <div class="cd-article-row__body">
                                        <h2 class="cd-article-row__title">
                                            <a href="<?php echo esc_url($url); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        <div class="cd-article-row__meta">
                                            <span><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                                            <span><?php echo esc_html(get_the_author()); ?></span>
                                        </div>
                                        <p class="cd-article-row__excerpt"><?php echo esc_html(wp_strip_all_tags($mo_ta)); ?></p>
                                    </div>
                                    <a class="cd-article-row__link" href="<?php echo esc_url($url); ?>">Xem chi tiết →</a>
                                </article>
                                <?php
                            endwhile;
                            ?>
                            <div class="pagination-wrapper">
                                <?php echo function_exists('dnttvn_custom_pagination') ? dnttvn_custom_pagination($cong_dong_query) : ''; ?>
                            </div>
                            <?php
                            wp_reset_postdata();
                        else :
                            echo '<p class="cd-empty">Chưa có bài viết Cộng đồng.</p>';
                        endif;
                        ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <?php get_template_part('template-parts/sidebar', 'right'); ?>
    </div>
</main>

<?php get_footer(); ?>
