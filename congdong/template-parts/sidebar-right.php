<?php
/**
 * Sidebar phải — Sự kiện sắp diễn ra + Website liên kết + CTA.
 * (Thay thế khối "Thành viên mới" bằng Website liên kết theo yêu cầu.)
 */
if (!defined('ABSPATH')) {
    exit;
}

$dang_ky_url = home_url('/dang-ky/');
$dk_page = get_page_by_path('dang-ky');
if ($dk_page) {
    $dang_ky_url = get_permalink($dk_page);
}
?>
<aside class="cd-side cd-side--right" aria-label="Thông tin bổ sung">
    <?php
    $sk_args = function_exists('dnttvn_su_kien_active_query_args')
        ? dnttvn_su_kien_active_query_args()
        : array('post_type' => 'su_kien', 'post_status' => 'publish', 'posts_per_page' => 4);
    $sk_args['posts_per_page'] = 4;
    $sk_q = new WP_Query($sk_args);
    if ($sk_q->have_posts()) :
        $sk_archive = get_post_type_archive_link('su_kien');
        $sk_url     = $sk_archive ? $sk_archive : home_url('/su-kien/');
        ?>
    <div class="cd-side-card">
        <div class="cd-side-card__head">
            <h2 class="cd-side-card__title">Sự kiện sắp diễn ra</h2>
            <a class="cd-side-card__more" href="<?php echo esc_url($sk_url); ?>">Xem tất cả</a>
        </div>
        <ul class="cd-event-list">
            <?php
            while ($sk_q->have_posts()) :
                $sk_q->the_post();
                $ngay = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
                $day  = $ngay ? date_i18n('d', strtotime($ngay)) : '';
                $mon  = $ngay ? 'THG ' . date_i18n('m', strtotime($ngay)) : '';
                ?>
                <li class="cd-event-item">
                    <div class="cd-event-item__date" aria-hidden="true">
                        <span class="cd-event-item__day"><?php echo esc_html($day ?: '—'); ?></span>
                        <span class="cd-event-item__mon"><?php echo esc_html($mon ?: ''); ?></span>
                    </div>
                    <div class="cd-event-item__body">
                        <a href="<?php the_permalink(); ?>" class="cd-event-item__title"><?php the_title(); ?></a>
                        <?php if ($ngay) : ?>
                            <time class="cd-event-item__meta"><?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay))); ?></time>
                        <?php endif; ?>
                    </div>
                </li>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="cd-side-card">
        <h2 class="cd-side-card__title">Website liên kết</h2>
        <ul class="cd-link-list">
            <?php
            $links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
            $links = array_slice($links, 0, 9);
            $has   = false;
            foreach ($links as $link) {
                if (empty($link['url'])) {
                    continue;
                }
                $has = true;
                echo '<li><a href="' . esc_url($link['url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($link['name']) . '</a></li>';
            }
            if (!$has) {
                echo '<li class="cd-side-empty">Chưa có website liên kết.</li>';
            }
            ?>
        </ul>
    </div>

    <div class="cd-side-cta">
        <div class="cd-side-cta__glow" aria-hidden="true"></div>
        <h2 class="cd-side-cta__title">Tham gia cộng đồng</h2>
        <p class="cd-side-cta__text">Kết nối doanh nhân trí tuệ, mở rộng cơ hội hợp tác và phát triển bền vững.</p>
        <a href="<?php echo esc_url($dang_ky_url); ?>" class="cd-side-cta__btn">Đăng ký tham gia ngay →</a>
    </div>
</aside>
