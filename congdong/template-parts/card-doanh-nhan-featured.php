<?php
/**
 * Thẻ doanh nhân tiêu biểu — ảnh tròn, tên, chức vụ, trích dẫn, nút xem hồ sơ.
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

$title   = get_the_title($post);
$chuc_vu = get_post_meta($post_id, '_doanh_nhan_chuc_vu', true);
if (!is_string($chuc_vu)) {
    $chuc_vu = '';
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
$quote       = (is_string($raw_excerpt) && $raw_excerpt !== '') ? wp_strip_all_tags($raw_excerpt) : '';
if ($quote === '') {
    $content = get_post_field('post_content', $post_id);
    $quote   = wp_trim_words(strip_shortcodes((string) $content), 18, '…');
} else {
    $quote = wp_trim_words($quote, 18, '…');
}

$detail_url = get_permalink($post);
?>
<article class="dn-profile-card">
    <div class="dn-profile-card__avatar-wrap">
        <?php if ($featured_url) : ?>
            <img src="<?php echo esc_url($featured_url); ?>" alt="<?php echo esc_attr($featured_alt); ?>" class="dn-profile-card__avatar" loading="lazy">
        <?php else : ?>
            <span class="dn-profile-card__avatar dn-profile-card__avatar--ph" aria-hidden="true"><?php echo esc_html(mb_substr($title, 0, 1)); ?></span>
        <?php endif; ?>
    </div>
    <h3 class="dn-profile-card__name"><?php echo esc_html($title); ?></h3>
    <?php if ($chuc_vu !== '') : ?>
        <p class="dn-profile-card__role"><?php echo esc_html($chuc_vu); ?></p>
    <?php endif; ?>
    <?php if ($quote !== '') : ?>
        <p class="dn-profile-card__quote">“<?php echo esc_html($quote); ?>”</p>
    <?php endif; ?>
    <?php if ($khu_vuc !== '') : ?>
        <p class="dn-profile-card__loc">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
            <?php echo esc_html($khu_vuc); ?>
        </p>
    <?php endif; ?>
    <a href="<?php echo esc_url($detail_url); ?>" class="dn-profile-card__btn">Xem hồ sơ</a>
</article>
