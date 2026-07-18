<?php
/**
 * Sidebar trái — mục lục Cộng đồng + Thành viên (layout hiện đại).
 *
 * @var array $args {
 *   @type int $current_id ID bài cong_dong đang xem (optional).
 * }
 */
if (!defined('ABSPATH')) {
    exit;
}

$current_id = 0;
if (!empty($args['current_id'])) {
    $current_id = (int) $args['current_id'];
} elseif (isset($GLOBALS['dnttvn_current_cong_dong_id'])) {
    $current_id = (int) $GLOBALS['dnttvn_current_cong_dong_id'];
}

$cong_dong_page = get_page_by_path('cong-dong');
$cong_dong_hub  = $cong_dong_page ? get_permalink($cong_dong_page) : home_url('/cong-dong/');

$overview_post = function_exists('dnttvn_get_tong_quan_cong_dong_post')
    ? dnttvn_get_tong_quan_cong_dong_post()
    : null;
$overview_id   = ($overview_post instanceof WP_Post) ? (int) $overview_post->ID : 0;
$overview_url  = $overview_id > 0 && function_exists('dnttvn_get_cong_dong_detail_url')
    ? dnttvn_get_cong_dong_detail_url($overview_id)
    : $cong_dong_hub;
$is_overview_active = $overview_id > 0
    ? ($current_id === $overview_id)
    : (is_page('cong-dong') && empty($_GET['post_id']));
?>
<aside class="cd-side cd-side--left" aria-label="Mục lục cộng đồng">
    <div class="cd-side-card">
        <h2 class="cd-side-card__title">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            Cộng đồng
        </h2>
        <ul class="cd-nav-list">
            <li class="cd-nav-list__item<?php echo $is_overview_active ? ' is-active' : ''; ?>">
                <a href="<?php echo esc_url($overview_url); ?>"><?php
                    echo esc_html(
                        ($overview_post instanceof WP_Post && $overview_post->post_title !== '')
                            ? $overview_post->post_title
                            : 'TỔNG QUAN CỘNG ĐỒNG'
                    );
                ?></a>
            </li>
            <?php
            $sidebar_q = new WP_Query(array(
                'post_type'      => 'cong_dong',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order date',
                'order'          => 'ASC',
            ));
            if ($sidebar_q->have_posts()) :
                while ($sidebar_q->have_posts()) :
                    $sidebar_q->the_post();
                    $pid = get_the_ID();
                    // Tránh trùng với mục “Tổng quan cộng đồng” ở trên
                    if ($overview_id > 0 && (int) $pid === $overview_id) {
                        continue;
                    }
                    $title_norm = trim(wp_strip_all_tags(get_the_title()));
                    $title_l    = function_exists('mb_strtolower') ? mb_strtolower($title_norm, 'UTF-8') : strtolower($title_norm);
                    if ($title_l === 'tổng quan cộng đồng' || get_post_field('post_name', $pid) === 'tong-quan-cong-dong') {
                        continue;
                    }
                    $active = ((int) $current_id === (int) $pid);
                    $url    = function_exists('dnttvn_get_cong_dong_detail_url')
                        ? dnttvn_get_cong_dong_detail_url($pid)
                        : get_permalink($pid);
                    ?>
                    <li class="cd-nav-list__item<?php echo $active ? ' is-active' : ''; ?>">
                        <a href="<?php echo esc_url($url); ?>"><?php the_title(); ?></a>
                    </li>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
    </div>

    <div class="cd-side-card">
        <h2 class="cd-side-card__title">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            Thành viên
        </h2>
        <ul class="cd-nav-list">
            <?php
            $member_pages = array(
                'gia-tri-thanh-vien'              => 'Giá trị nhận được của thành viên',
                'quy-trinh-gia-nhap'              => 'Quy trình gia nhập cộng đồng',
                'phung-doanh-nhan'                => 'Phụng sự doanh nhân',
                'phung-su-con-doanh-nhan'         => 'Phụng sự Con Doanh nhân',
                'nghia-vu-thanh-vien-cong-dong'   => 'Nghĩa vụ thành viên cộng đồng',
                'hoi-dap-cong-dong'               => 'Hỏi đáp về cộng đồng',
            );
            foreach ($member_pages as $slug => $label) {
                $page = get_page_by_path($slug);
                $url  = $page ? get_permalink($page) : home_url('/' . $slug . '/');
                $on   = is_page($slug);
                echo '<li class="cd-nav-list__item' . ($on ? ' is-active' : '') . '"><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
            }
            ?>
        </ul>
    </div>
</aside>
