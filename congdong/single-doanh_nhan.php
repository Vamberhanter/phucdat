<?php
/**
 * Single Post Template for Doanh nhân
 * Layout 100% giống single-doanh-nghiep (sidebar trái + nội dung + sidebar phải)
 */

get_header();
?>

<main class="main-content">
    <!-- Left Sidebar: Về Cộng đồng DNTTVN -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <ul class="about-list">
                <?php
                $cong_dong_query = new WP_Query(array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                ));
                if ($cong_dong_query->have_posts()) :
                    while ($cong_dong_query->have_posts()) : $cong_dong_query->the_post();
                        $is_noi_bat = get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);
                        $li_class   = ($is_noi_bat == '1') ? 'highlight-item' : '';
                        ?>
                        <li class="<?php echo esc_attr($li_class); ?>">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </li>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <li><a href="#">Chưa có bài viết Cộng đồng</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
        <?php while (have_posts()) : the_post(); ?>
                <div class="column-header"><?php the_title(); ?></div>
                <div class="column-content">
        <?php
            $nganh_terms = get_the_terms(get_the_ID(), 'nganh_nghe');
            $nganh_hang  = '';
            if ($nganh_terms && !is_wp_error($nganh_terms)) {
                $nganh_hang = implode(', ', wp_list_pluck($nganh_terms, 'name'));
            }

            $kv_terms = get_the_terms(get_the_ID(), 'khu_vuc');
            $khu_vuc  = '';
            if ($kv_terms && !is_wp_error($kv_terms)) {
                $khu_vuc = implode(', ', wp_list_pluck($kv_terms, 'name'));
            }

            $featured_image_id  = get_post_thumbnail_id();
            $featured_image_url = '';
            $featured_image_alt = '';
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                if (empty($featured_image_alt)) {
                    $featured_image_alt = get_the_title();
                }
            }

            $chuc_vu    = get_post_meta(get_the_ID(), '_doanh_nhan_chuc_vu', true);
            $ten_dn     = get_post_meta(get_the_ID(), '_doanh_nhan_cong_ty', true);
            $ngay_sinh  = get_post_meta(get_the_ID(), '_doanh_nhan_ngay_sinh', true);
            $gioi_tinh  = get_post_meta(get_the_ID(), '_doanh_nhan_gioi_tinh', true);
            $dien_thoai = get_post_meta(get_the_ID(), '_doanh_nhan_dien_thoai', true);
            $dn_email   = get_post_meta(get_the_ID(), '_doanh_nhan_email', true);
            $dia_chi    = get_post_meta(get_the_ID(), '_doanh_nhan_dia_chi', true);
        ?>
            <div class="business-card" style="max-width: 100%;">
                <div class="business-card-left">
                    <div class="business-card-image">
                        <?php if ($featured_image_url) : ?>
                            <img src="<?php echo esc_url($featured_image_url); ?>"
                                 alt="<?php echo esc_attr($featured_image_alt); ?>"
                                 class="business-main-image"
                                 loading="lazy">
                        <?php else : ?>
                            <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(urlencode(get_the_title())); ?>"
                                 alt="<?php echo esc_attr(get_the_title()); ?>"
                                 class="business-main-image"
                                 loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="business-card-info-section">
                        <?php if ($nganh_hang) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($khu_vuc) : ?>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                            </div>
                        <?php endif; ?>

                        <p style="margin-top: 15px; color: #666; font-size: 14px;">
                            <strong>Ngày đăng:</strong> <?php echo get_the_date('d/m/Y'); ?>
                        </p>
                    </div>
                </div>
                <div class="business-card-content">
                    <?php if ($ngay_sinh || $gioi_tinh || $dien_thoai || $dn_email || $dia_chi || $chuc_vu || $ten_dn || $nganh_hang || $khu_vuc) : ?>
                        <div class="dn-detail-info-box">
                            <h3 class="dn-detail-info-title">Thông tin doanh nhân</h3>
                            <div class="dn-detail-info-grid">
                                <?php if ($ngay_sinh) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                                    <div><span class="dn-detail-info-label">Ngày sinh</span><span class="dn-detail-info-value"><?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_sinh))); ?></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($gioi_tinh) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                                    <div><span class="dn-detail-info-label">Giới tính</span><span class="dn-detail-info-value"><?php echo esc_html($gioi_tinh); ?></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($dien_thoai) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                                    <div><span class="dn-detail-info-label">Số điện thoại</span><span class="dn-detail-info-value"><a href="tel:<?php echo esc_attr($dien_thoai); ?>" style="color:#d35400; text-decoration:none;"><?php echo esc_html($dien_thoai); ?></a></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($dn_email) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                    <div><span class="dn-detail-info-label">Email</span><span class="dn-detail-info-value"><a href="mailto:<?php echo esc_attr($dn_email); ?>" style="color:#d35400; text-decoration:none;"><?php echo esc_html($dn_email); ?></a></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($dia_chi) : ?>
                                <div class="dn-detail-info-item dn-detail-info-full">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                    <div><span class="dn-detail-info-label">Địa chỉ liên hệ</span><span class="dn-detail-info-value"><?php echo esc_html($dia_chi); ?></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($chuc_vu) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 4h4v2h-4V4zm10 15H4v-2h16v2zm0-5H4V8h4v2h2V8h4v2h2V8h4v6z"/></svg>
                                    <div><span class="dn-detail-info-label">Chức vụ hiện nay</span><span class="dn-detail-info-value"><?php echo esc_html($chuc_vu); ?></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($ten_dn) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                    <div><span class="dn-detail-info-label">Doanh nghiệp / Đơn vị</span><span class="dn-detail-info-value"><?php echo esc_html($ten_dn); ?></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($nganh_hang) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    <div><span class="dn-detail-info-label">Lĩnh vực hoạt động</span><span class="dn-detail-info-value"><?php echo esc_html($nganh_hang); ?></span></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($khu_vuc) : ?>
                                <div class="dn-detail-info-item">
                                    <svg class="dn-detail-info-icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                    <div><span class="dn-detail-info-label">Khu vực</span><span class="dn-detail-info-value"><?php echo esc_html($khu_vuc); ?></span></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                    $dn_hinh_phu = get_post_meta(get_the_ID(), '_doanh_nhan_hinh_phu', true);
                    if ($dn_hinh_phu) :
                        $hp_url = '';
                        $hp_alt = get_the_title() . ' - Hình ảnh phụ';
                        if (is_numeric($dn_hinh_phu)) {
                            $hp_url = wp_get_attachment_image_url(absint($dn_hinh_phu), 'large');
                            $hp_alt_meta = get_post_meta(absint($dn_hinh_phu), '_wp_attachment_image_alt', true);
                            if ($hp_alt_meta) $hp_alt = $hp_alt_meta;
                        } else {
                            $hp_url = esc_url($dn_hinh_phu);
                        }
                        if ($hp_url) :
                    ?>
                        <div class="business-card-small-image" style="margin-top: 15px;">
                            <img src="<?php echo esc_url($hp_url); ?>"
                                 alt="<?php echo esc_attr($hp_alt); ?>"
                                 class="business-small-image"
                                 loading="lazy">
                        </div>
                    <?php endif; endif; ?>
                </div>
                </div>

                <!-- Mô tả chi tiết doanh nhân (full width, sát mép trái) -->
                <div class="business-card-description" style="font-size: 20px; line-height: 1.8;">
                    <h3 style="margin-top:0; margin-bottom:10px; font-size:18px; color:#06202e;">Mô tả chi tiết doanh nhân</h3>
                    <?php the_content(); ?>
                </div>

                <?php
                $items = function_exists('dnttvn_get_structured_content_array') ? dnttvn_get_structured_content_array(get_the_ID()) : array();
                if (!empty($items)) :
                ?>
                <div class="structured-content-display" style="margin-top: 30px;">
                    <?php foreach ($items as $item) : ?>
                        <?php if (!empty($item['heading']) || !empty($item['content'])) : ?>
                            <div class="structured-item-display" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                                <?php if (!empty($item['heading'])) : ?>
                                    <h3 style="font-size: 26px; font-weight: bold; color: #333; margin-bottom: 15px;">
                                        <?php echo esc_html($item['heading']); ?>
                                    </h3>
                                <?php endif; ?>
                                <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                    <?php foreach ($item['content_items'] as $content_item) : if (is_string($content_item)) { $text = $content_item; $ci_images = []; $ci_captions = []; } else { $text = $content_item['text'] ?? ''; $ci_images = $content_item['images'] ?? []; $ci_captions = $content_item['image_captions'] ?? []; } ?>
                                        <div class="content-item-display" style="margin-bottom: 30px;">
                                            <?php if (!empty($text)) : ?><?php $c = $text; ?><div style="line-height: 1.8; font-size: 20px; color: #555; margin-bottom: 15px;"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div><?php endif; ?>
                                            <?php if (!empty($ci_images)) : ?><div class="content-item-images-gallery" style="display: flex; flex-wrap: wrap; gap: 15px;"><?php foreach ($ci_images as $ci_img_idx => $ci_img_id) : $ci_img_id = intval($ci_img_id); if ($ci_img_id > 0) : $ci_mime = get_post_mime_type($ci_img_id); $ci_is_video = strpos($ci_mime, 'video') === 0; $ci_url = wp_get_attachment_url($ci_img_id); $ci_caption = $ci_captions[$ci_img_idx] ?? ''; ?><div style="flex: 0 0 auto;"><?php if ($ci_is_video) : ?><video style="max-width: 250px; max-height: 180px; border-radius: 8px;" controls><source src="<?php echo esc_url($ci_url); ?>" type="<?php echo esc_attr($ci_mime); ?>"></video><?php else : ?><img src="<?php echo esc_url($ci_url); ?>" style="max-width: 250px; max-height: 180px; border-radius: 8px; object-fit: cover;"><?php endif; ?><?php if ($ci_caption) : ?><p style="margin-top: 5px; font-size: 13px; color: #666; max-width: 250px;"><?php echo esc_html($ci_caption); ?></p><?php endif; ?></div><?php endif; endforeach; ?></div><?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php elseif (!empty($item['content'])) : ?>
                                    <div style="line-height: 1.8; font-size: 20px; color: #555;"><?php echo wpautop(wp_kses_post($item['content'])); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Navigation to previous/next post -->
                <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #667eea; display: flex; justify-content: space-between; flex-wrap: wrap;">
                    <div style="margin-bottom: 10px;">
                        <?php
                        $prev_post = get_previous_post(false, '', 'nganh_nghe');
                        if (!$prev_post) {
                            $prev_post = get_previous_post();
                        }
                        if ($prev_post) :
                            ?>
                            <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                ← <?php echo esc_html($prev_post->post_title); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php
                        $next_post = get_next_post(false, '', 'nganh_nghe');
                        if (!$next_post) {
                            $next_post = get_next_post();
                        }
                        if ($next_post) :
                            ?>
                            <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo esc_html($next_post->post_title); ?> →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
        <?php endwhile; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sidebar-column">
        <?php get_template_part('template-parts/sidebar-su-kien'); ?>

        <div class="column-header mobile-toggle collapsed">Website liên kết</div>
        <div class="column-content mobile-collapsed">
            <ul class="linked-websites">
                <?php
                $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                $community_links = array_slice($community_links, 0, 9);
                foreach ($community_links as $link) {
                    if (!empty($link['url'])) {
                        echo '<li><a href="' . esc_url($link['url']) . '">' . esc_html($link['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
</main>

<?php get_footer(); ?>
