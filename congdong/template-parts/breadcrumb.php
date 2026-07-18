<?php
/**
 * Breadcrumb nội trang.
 *
 * @var array $args {
 *   @type string $current Tiêu đề trang hiện tại.
 * }
 */
if (!defined('ABSPATH')) {
    exit;
}
$current = !empty($args['current']) ? $args['current'] : wp_get_document_title();
?>
<nav class="cd-breadcrumb" aria-label="Đường dẫn">
    <div class="cd-breadcrumb__inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="cd-breadcrumb__home" title="Trang chủ" aria-label="Trang chủ">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        </a>
        <span class="cd-breadcrumb__sep" aria-hidden="true">›</span>
        <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
        <span class="cd-breadcrumb__sep" aria-hidden="true">›</span>
        <span class="cd-breadcrumb__current"><?php echo esc_html($current); ?></span>
    </div>
</nav>
