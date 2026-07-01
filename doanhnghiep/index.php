<?php
/**
 * Theme Doanh nghiệp DNTTVN: Trang chủ chuyển tới Danh sách Doanh nghiệp
 * Không có Tin tức / Cộng đồng
 */

$page_dn = get_page_by_path('danh-sach-doanh-nghiep');
if (!$page_dn) {
    $page_dn = get_page_by_path('page-doanh-nghiep');
}
if ($page_dn) {
    wp_redirect(get_permalink($page_dn->ID), 302);
    exit;
}
get_header();
?>
<main class="main-content">
    <div class="content-column" style="padding: 40px 20px; text-align: center;">
        <p>Trang Danh sách Doanh nghiệp chưa được tạo. Vui lòng tạo trang với slug <strong>danh-sach-doanh-nghiep</strong> và gán template "Trang Doanh nghiệp".</p>
    </div>
</main>
<?php get_footer(); ?>
