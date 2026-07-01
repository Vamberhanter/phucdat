<?php
/**
 * Template Name: Xem trước đăng ký Doanh nghiệp
 * Description: Hiển thị thẻ + nội dung chi tiết như sau duyệt (dữ liệu từ hồ sơ đăng ký). Truy cập bằng ?dn_preview_id=&dn_preview_key= hoặc quản trị có quyền sửa bài.
 */

get_header();

$app_id  = function_exists('dnttvn_dn_reg_resolve_preview_app_id') ? dnttvn_dn_reg_resolve_preview_app_id() : 0;
$payload = ($app_id && function_exists('dnttvn_dn_reg_build_approval_payload')) ? dnttvn_dn_reg_build_approval_payload($app_id) : null;

$page_dn = get_page_by_path('danh-sach-doanh-nghiep');
if (!$page_dn) {
    $page_dn = get_page_by_path('page-doanh-nghiep');
}
$dn_list_url = $page_dn ? get_permalink($page_dn->ID) : home_url('/danh-sach-doanh-nghiep/');
$form_url    = function_exists('dnttvn_get_dn_registration_page_url') ? dnttvn_get_dn_registration_page_url() : home_url('/dang-ky-doanh-nghiep/');
?>

<div class="dn-directory-outer dn-directory-outer--boxed">
<main class="main-content dn-directory-main dn-directory-main--detail">
    <div class="sidebar-column">
        <div class="dn-sidebar-dn-list">
            <div class="column-header mobile-toggle collapsed">Danh sách Doanh nghiệp</div>
            <div class="column-content mobile-collapsed">
                <ul class="linked-websites">
                    <li><a href="<?php echo esc_url($dn_list_url); ?>">Danh sách Doanh nghiệp</a></li>
                    <li><a href="<?php echo esc_url($form_url); ?>">Đăng ký doanh nghiệp</a></li>
                </ul>
            </div>
        </div>
        <div class="column-header mobile-toggle collapsed">Web liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                foreach ($community_links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="main-center">
        <div class="dn-directory-hero">
            <h1 class="dn-directory-hero__title"><?php echo esc_html(get_the_title()); ?></h1>
        </div>

        <?php if (!$payload) : ?>
            <div class="dn-reg-error-card" role="alert" style="margin-top:20px;">
                <p class="dn-reg-error-card__title">Không thể hiển thị xem trước</p>
                <p class="dn-reg-error-card__text">Liên kết không hợp lệ hoặc đã hết hiệu lực. Vui lòng dùng liên kết được gửi kèm sau khi nộp hồ sơ, hoặc đăng nhập quản trị và mở từ màn hình sửa hồ sơ đăng ký.</p>
                <p class="dn-reg-error-card__text" style="margin-top:10px;"><a href="<?php echo esc_url($form_url); ?>">← Quay lại form đăng ký</a></p>
            </div>
        <?php else : ?>
            <div class="dn-reg-intro" style="margin-bottom:20px;border-color:#fde68a;background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                <p style="margin:0;"><strong>Xem trước</strong> — giao diện dưới đây mô phỏng bài <strong>Doanh nghiệp</strong> sau khi duyệt (chưa tạo bài trên danh bạ). Hồ sơ: <strong>#<?php echo (int) $app_id; ?></strong></p>
            </div>

            <?php
            $thumb            = (int) $payload['thumb'];
            $gallery_ids = array_map('absint', (array) $payload['gallery_public']);
            if (function_exists('dnttvn_doanh_nghiep_gallery_card_max')) {
                $gallery_ids = array_slice($gallery_ids, 0, dnttvn_doanh_nghiep_gallery_card_max());
            } else {
                $gallery_ids = array_slice($gallery_ids, 0, 5);
            }
            $mo_ta = isset($payload['mo_ta_ngan']) ? (string) $payload['mo_ta_ngan'] : '';
            $mo_ta = trim($mo_ta);
            if ($mo_ta !== '' && function_exists('dnttvn_dn_reg_trim_to_word_limit')) {
                $mo_lim = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;
                $mo_ta  = dnttvn_dn_reg_trim_to_word_limit($mo_ta, $mo_lim);
            }
            $display_title    = $payload['ten_day_du'] !== '' ? $payload['ten_day_du'] : $payload['title'];
            $nganh_hang       = $payload['nganh_label'];
            $khu_vuc          = $payload['khu_vuc_label'];
            $dia_chi          = $payload['dia_chi'];
            $dien_thoai       = $payload['dien_thoai'];
            $email_lh         = $payload['email_lien_he'];
            $website_dn       = $payload['website'];

            $featured_image_url = '';
            $featured_image_alt = '';
            if ($thumb) {
                $featured_image_url = wp_get_attachment_image_url($thumb, 'full');
                $featured_image_alt = get_post_meta($thumb, '_wp_attachment_image_alt', true);
                if ($featured_image_alt === '') {
                    $featured_image_alt = $display_title . ' - Logo';
                }
            }
            ?>

            <div class="content-column">
                <div class="column-header"><?php echo esc_html($display_title); ?></div>
                <div class="column-content">
                    <div class="business-card dn-detail-business-card" style="max-width: 100%;">
                        <div class="business-card-left">
                            <div class="business-card-image">
                                <?php if ($featured_image_url) : ?>
                                    <img src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" class="business-main-image" loading="lazy">
                                <?php else : ?>
                                    <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(urlencode($display_title)); ?>" alt="" class="business-main-image" loading="lazy">
                                <?php endif; ?>
                            </div>
                            <?php if ($nganh_hang) : ?>
                                <div class="business-card-info dn-dn-meta-around-img dn-dn-meta-under-main-img">
                                    <svg class="business-card-info-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    <p style="margin:0;"><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if ($khu_vuc) : ?>
                                <div class="business-card-info dn-dn-meta-around-img dn-dn-meta-under-main-img">
                                    <svg class="business-card-info-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                    <p style="margin:0;"><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="business-card-info-section">
                                <p style="margin-top: 8px; color: #666; font-size: 14px;"><em>Xem trước — chưa duyệt</em></p>
                            </div>
                        </div>
                        <div class="business-card-content">
                            <?php if ($gallery_ids) : ?>
                                <div class="business-card-small-image dn-dn-main-gallery">
                                    <div class="business-card-small-image-slider">
                                        <?php
                                        $first = true;
                                        foreach ($gallery_ids as $img_id) :
                                            $img_url = wp_get_attachment_image_url($img_id, 'large');
                                            if (!$img_url) {
                                                continue;
                                            }
                                            $img_full = wp_get_attachment_image_url($img_id, 'full') ?: $img_url;
                                            $img_alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                            if ($img_alt === '') {
                                                $img_alt = $display_title . ' - Hình ảnh ' . $img_id;
                                            }
                                            ?>
                                            <div class="business-card-small-image-slide <?php echo $first ? 'active' : ''; ?>" data-full="<?php echo esc_url($img_full); ?>" data-alt="<?php echo esc_attr($img_alt); ?>">
                                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" class="business-small-image" loading="lazy">
                                            </div>
                                            <?php
                                            $first = false;
                                        endforeach;
                                        ?>
                                    </div>
                                    <div class="business-card-small-image-nav">
                                        <button type="button" class="business-card-small-image-prev" aria-label="Ảnh trước">&#10094;</button>
                                        <button type="button" class="business-card-small-image-next" aria-label="Ảnh sau">&#10095;</button>
                                    </div>
                                </div>
                                <div class="business-card-gallery" style="margin-top: 12px;">
                                    <?php foreach ($gallery_ids as $img_id) :
                                        $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                        if (!$thumb_url) {
                                            $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
                                        }
                                        if (!$thumb_url) {
                                            continue;
                                        }
                                        $thumb_alt  = get_post_meta($img_id, '_wp_attachment_image_alt', true);
                                        $thumb_full = wp_get_attachment_image_url($img_id, 'full') ?: wp_get_attachment_image_url($img_id, 'large');
                                        ?>
                                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($thumb_alt); ?>" data-full="<?php echo esc_url($thumb_full ? $thumb_full : $thumb_url); ?>" class="business-gallery-thumb" loading="lazy" />
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($dia_chi || $dien_thoai || $email_lh || $website_dn) : ?>
                                <div style="margin-bottom: 25px; padding: 15px 18px; border-radius: 8px; background:#f8f9fa; border:1px solid #e0e0e0;">
                                    <h3 style="margin-top:0; margin-bottom:12px; font-size:20px; color:#06202e;">Thông tin liên hệ</h3>
                                    <ul style="list-style:none; margin:0; padding:0; font-size:16px; color:#444;">
                                        <?php if ($dia_chi) : ?><li style="margin-bottom:6px;"><strong>Địa chỉ:</strong> <?php echo esc_html($dia_chi); ?></li><?php endif; ?>
                                        <?php if ($dien_thoai) : ?><li style="margin-bottom:6px;"><strong>Điện thoại:</strong> <?php echo esc_html($dien_thoai); ?></li><?php endif; ?>
                                        <?php if ($email_lh) : ?><li style="margin-bottom:6px;"><strong>Email:</strong> <?php echo esc_html($email_lh); ?></li><?php endif; ?>
                                        <?php if ($website_dn) :
                                            $website_raw = trim($website_dn);
                                            $website_href = $website_raw;
                                            if ($website_href && !preg_match('~^https?://~i', $website_href)) {
                                                $website_href = '//' . $website_href;
                                            }
                                            ?>
                                            <li style="margin-bottom:6px;"><strong>Website:</strong> <a href="<?php echo esc_url($website_href); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($website_raw); ?></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $items = isset($payload['structured']) ? $payload['structured'] : array();
                    if (!empty($items)) :
                        ?>
                        <div class="structured-content-display dn-sc-detail" style="margin-top: 30px;">
                            <?php foreach ($items as $item) : ?>
                                <?php if (!empty($item['heading']) || !empty($item['content'])) : ?>
                                    <div class="structured-item-display dn-sc-detail__block" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                        <?php if (!empty($item['heading'])) : ?><h3 class="dn-sc-detail__heading"><?php echo esc_html($item['heading']); ?></h3><?php endif; ?>
                                        <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                            <?php foreach ($item['content_items'] as $content_item) : ?>
                                                <?php
                                                if (is_string($content_item)) {
                                                    $text = $content_item;
                                                    $ci_images = array();
                                                    $ci_captions = array();
                                                } else {
                                                    $text = isset($content_item['text']) ? $content_item['text'] : '';
                                                    $ci_images = isset($content_item['images']) ? $content_item['images'] : array();
                                                    $ci_captions = isset($content_item['image_captions']) ? $content_item['image_captions'] : array();
                                                }
                                                ?>
                                                <div class="content-item-display dn-sc-detail__item" style="margin-bottom: 30px;">
                                                    <?php if (!empty($text)) : ?>
                                                        <?php $c = $text; ?>
                                                        <div class="dn-sc-html"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div>
                                                    <?php endif; ?>
                                                    <?php
                                                    if (!empty($ci_images) && function_exists('dnttvn_render_doanh_nghiep_ci_images_block')) {
                                                        echo dnttvn_render_doanh_nghiep_ci_images_block($ci_images, $ci_captions);
                                                    }
                                                    ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php elseif (!empty($item['content'])) : ?>
                                            <?php $c = $item['content']; ?>
                                            <div class="dn-sc-html"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $slider_ids = isset($payload['slider_ids']) ? $payload['slider_ids'] : array();
                    if (!empty($slider_ids) && function_exists('dnttvn_render_doanh_nghiep_noi_dung_slider')) {
                        echo dnttvn_render_doanh_nghiep_noi_dung_slider(0, $slider_ids, 'Hình ảnh từ hồ sơ đăng ký', '');
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
</div>

<div class="business-lightbox" id="business-lightbox" aria-hidden="true">
    <div class="business-lightbox-backdrop"></div>
    <div class="business-lightbox-inner">
        <button type="button" class="business-lightbox-close" aria-label="Đóng">×</button>
        <button type="button" class="business-lightbox-prev" aria-label="Ảnh trước">&#10094;</button>
        <img src="" alt="" class="business-lightbox-image" />
        <button type="button" class="business-lightbox-next" aria-label="Ảnh sau">&#10095;</button>
    </div>
</div>

<?php
get_footer();
