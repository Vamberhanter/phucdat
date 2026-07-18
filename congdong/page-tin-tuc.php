<?php
/**
 * Template Name: Trang Tin Tức
 * Description: Template hiển thị danh sách tin tức
 */

get_header();

$paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;
$tin_tuc_args = array(
    'post_type'      => 'tin_tuc',
    'posts_per_page' => 10,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        array(
            'key'     => '_tin_tuc_noi_bat',
            'compare' => 'NOT EXISTS',
        ),
    ),
);

$featured_args = array(
    'post_type'      => 'tin_tuc',
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => '_tin_tuc_noi_bat',
            'value'   => '1',
            'compare' => '=',
        ),
    ),
    'orderby' => 'date',
    'order'   => 'DESC',
);

$featured_query = new WP_Query($featured_args);
$regular_query  = new WP_Query($tin_tuc_args);

dnttvn_page_shell_start('Tin tức');
?>
<h1 class="cd-detail__title">Tin Tức Cộng Đồng</h1>

<?php if ($featured_query->have_posts()) : ?>
    <section class="cd-toolbar" aria-label="Tin nổi bật">
        <p class="cd-toolbar__count"><strong>📌 Tin Nổi Bật</strong></p>
    </section>
    <div class="cd-article-list">
        <?php
        while ($featured_query->have_posts()) :
            $featured_query->the_post();
            $detail_url = dnttvn_get_tin_tuc_detail_url(get_the_ID());
            $excerpt    = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30);
            $tac_gia    = get_post_meta(get_the_ID(), '_tin_tuc_tac_gia', true);
            ?>
            <article class="cd-article-row cd-article-row--featured">
                <?php if (has_post_thumbnail()) : ?>
                    <a class="cd-article-row__thumb" href="<?php echo esc_url($detail_url); ?>">
                        <?php the_post_thumbnail('medium', array('alt' => esc_attr(get_the_title()))); ?>
                    </a>
                <?php else : ?>
                    <div class="cd-article-row__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    </div>
                <?php endif; ?>
                <div class="cd-article-row__body">
                    <h2 class="cd-article-row__title">
                        <a href="<?php echo esc_url($detail_url); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="cd-article-row__meta">
                        <span><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                        <?php if ($tac_gia) : ?>
                            <span><?php echo esc_html($tac_gia); ?></span>
                        <?php endif; ?>
                        <span class="cd-badge">Nổi bật</span>
                    </div>
                    <p class="cd-article-row__excerpt"><?php echo esc_html($excerpt); ?></p>
                </div>
                <a class="cd-article-row__link" href="<?php echo esc_url($detail_url); ?>">Đọc tiếp →</a>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
<?php endif; ?>

<section class="cd-toolbar" aria-label="Danh sách tin tức">
    <p class="cd-toolbar__count">Tổng số: <strong><?php echo (int) $regular_query->found_posts; ?> tin tức</strong></p>
</section>

<div class="cd-article-list">
    <?php if ($regular_query->have_posts()) : ?>
        <?php
        while ($regular_query->have_posts()) :
            $regular_query->the_post();
            $detail_url = dnttvn_get_tin_tuc_detail_url(get_the_ID());
            $excerpt    = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 25);
            $tac_gia    = get_post_meta(get_the_ID(), '_tin_tuc_tac_gia', true);
            ?>
            <article class="cd-article-row">
                <?php if (has_post_thumbnail()) : ?>
                    <a class="cd-article-row__thumb" href="<?php echo esc_url($detail_url); ?>">
                        <?php the_post_thumbnail('thumbnail', array('alt' => esc_attr(get_the_title()))); ?>
                    </a>
                <?php else : ?>
                    <div class="cd-article-row__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    </div>
                <?php endif; ?>
                <div class="cd-article-row__body">
                    <h2 class="cd-article-row__title">
                        <a href="<?php echo esc_url($detail_url); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="cd-article-row__meta">
                        <span><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                        <?php if ($tac_gia) : ?>
                            <span><?php echo esc_html($tac_gia); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="cd-article-row__excerpt"><?php echo esc_html($excerpt); ?></p>
                </div>
                <a class="cd-article-row__link" href="<?php echo esc_url($detail_url); ?>">Xem chi tiết →</a>
            </article>
            <?php
        endwhile;
        ?>
        <div class="pagination-wrapper">
            <?php
            echo paginate_links(array(
                'total'     => $regular_query->max_num_pages,
                'current'   => $paged,
                'prev_text' => '← Trang trước',
                'next_text' => 'Trang sau →',
                'base'      => home_url('/tin-tuc/%_%'),
                'format'    => 'page/%#%/',
            ));
            ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p class="cd-empty">Chưa có tin tức nào được đăng.</p>
    <?php endif; ?>
</div>

<?php
dnttvn_page_shell_end();
get_footer();
