<?php
/**
 * Archive CPT Sự kiện — layout đồng bộ trang Cộng đồng.
 * Nội dung lấy từ database (CPT su_kien). Không đổi cấu trúc meta/CPT.
 */

get_header();

$paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;
$search_q = isset($_GET['sk_q']) ? sanitize_text_field(wp_unslash($_GET['sk_q'])) : '';

dnttvn_page_shell_start('Sự kiện');
?>

<h1 class="cd-detail__title">Sự kiện</h1>

<?php
$args = array(
    'post_type'      => 'su_kien',
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
if ($search_q !== '') {
    $args['s'] = $search_q;
}
$sk_query = new WP_Query($args);
$list_url = get_post_type_archive_link('su_kien');
if (!$list_url) {
    $list_url = home_url('/su-kien/');
}
?>

<form class="cd-toolbar" method="get" action="<?php echo esc_url($list_url); ?>">
    <div class="cd-toolbar__search">
        <input type="search" name="sk_q" value="<?php echo esc_attr($search_q); ?>" placeholder="Tìm kiếm sự kiện...">
    </div>
    <div class="cd-toolbar__filters">
        <button type="submit" class="cd-toolbar__btn">Tìm kiếm</button>
    </div>
</form>
<p class="cd-toolbar__count">Tổng số: <strong><?php echo (int) $sk_query->found_posts; ?> sự kiện</strong></p>

<div class="cd-article-list">
    <?php if ($sk_query->have_posts()) : ?>
        <?php while ($sk_query->have_posts()) : $sk_query->the_post();
            $ngay_mo  = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
            if (!$ngay_mo) {
                $ngay_mo = get_post_meta(get_the_ID(), '_su_kien_ngay', true);
            }
            $dia_diem = get_post_meta(get_the_ID(), '_su_kien_dia_diem', true);
            $excerpt  = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 28);
            ?>
            <article class="cd-article-row">
                <div class="cd-article-row__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                </div>
                <div class="cd-article-row__body">
                    <h2 class="cd-article-row__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="cd-article-row__meta">
                        <?php if ($ngay_mo) : ?>
                            <span><?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_mo))); ?></span>
                        <?php endif; ?>
                        <?php if ($dia_diem) : ?>
                            <span><?php echo esc_html($dia_diem); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($excerpt) : ?>
                        <p class="cd-article-row__excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>
                    <?php endif; ?>
                </div>
                <a class="cd-article-row__link" href="<?php the_permalink(); ?>">Xem chi tiết →</a>
            </article>
        <?php endwhile; ?>
        <div class="pagination-wrapper">
            <?php echo function_exists('dnttvn_custom_pagination') ? dnttvn_custom_pagination($sk_query) : ''; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p class="cd-empty">Chưa có sự kiện trong hệ thống. Thêm sự kiện tại <strong>WP Admin → Sự kiện</strong>.</p>
    <?php endif; ?>
</div>

<?php
dnttvn_page_shell_end();
get_footer();
