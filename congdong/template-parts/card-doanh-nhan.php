<?php
/**
 * Thẻ Doanh nhân — cấu trúc HTML + class 100% giống thẻ Doanh nghiệp.
 *
 * @package CongDong
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
if ($post_id < 1) {
    return;
}

$post = get_post($post_id);
if (!$post || $post->post_type !== 'doanh_nhan' || $post->post_status !== 'publish') {
    return;
}

$title = get_the_title($post);

$nganh_terms = get_the_terms($post_id, 'nganh_nghe');
$nganh_hang  = '';
if ($nganh_terms && !is_wp_error($nganh_terms)) {
    $nganh_hang = implode(', ', wp_list_pluck($nganh_terms, 'name'));
}

$kv_terms = get_the_terms($post_id, 'khu_vuc');
$khu_vuc  = '';
if ($kv_terms && !is_wp_error($kv_terms)) {
    $khu_vuc = implode(', ', wp_list_pluck($kv_terms, 'name'));
}

$featured_id  = get_post_thumbnail_id($post_id);
$featured_url = $featured_id ? wp_get_attachment_image_url($featured_id, 'medium') : '';
$featured_alt = $featured_id ? get_post_meta($featured_id, '_wp_attachment_image_alt', true) : '';
if (empty($featured_alt)) {
    $featured_alt = $title;
}

$raw_excerpt = get_post_field('post_excerpt', $post_id, 'raw');
$description = (is_string($raw_excerpt) && $raw_excerpt !== '') ? wp_strip_all_tags($raw_excerpt) : '';
if ($description === '') {
    $content = get_post_field('post_content', $post_id);
    $description = wp_trim_words(strip_shortcodes((string) $content), 50, '...');
}

$detail_url = get_permalink($post);
?>
<a href="<?php echo esc_url($detail_url); ?>" class="business-card-link">
<div class="business-card business-card-layout">
    <div class="business-card-title"><?php echo esc_html($title); ?></div>
    <div class="business-card-body">
        <div class="business-card-col-main">
            <div class="business-card-image">
                <?php if ($featured_url) : ?>
                    <img src="<?php echo esc_url($featured_url); ?>" alt="<?php echo esc_attr($featured_alt); ?>" class="business-main-image" loading="lazy">
                <?php else : ?>
                    <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(urlencode($title)); ?>" alt="<?php echo esc_attr($title); ?>" class="business-main-image" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="business-card-meta">
                <?php if ($nganh_hang) : ?>
                    <div class="business-card-info">
                        <svg class="business-card-info-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        <p><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($khu_vuc) : ?>
                    <div class="business-card-info">
                        <svg class="business-card-info-icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        <p><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="business-card-col-side">
            <div class="business-card-description">
                <?php if ($description) : ?>
                    <p><strong>Mô tả:</strong> <?php echo wp_kses_post($description); ?></p>
                <?php else : ?>
                    <p><strong>Mô tả:</strong> <em>Chưa có mô tả.</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</a>
