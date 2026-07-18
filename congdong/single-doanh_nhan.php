<?php
/**
 * Single Post Template for Doanh nhân
 * Hồ sơ cân đối: ảnh + intro, thông tin dạng hàng (tên dài không làm lệch cột).
 */

get_header();

$single_dn_id = get_queried_object_id();
dnttvn_page_shell_start(get_the_title($single_dn_id));
?>

        <?php while (have_posts()) : the_post(); ?>
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
                $featured_image_alt = get_the_title();
                if ($featured_image_id) {
                    $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'large');
                    $alt_meta = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                    if ($alt_meta) {
                        $featured_image_alt = $alt_meta;
                    }
                }

                $chuc_vu    = get_post_meta(get_the_ID(), '_doanh_nhan_chuc_vu', true);
                $ten_dn     = get_post_meta(get_the_ID(), '_doanh_nhan_cong_ty', true);
                $ngay_sinh  = get_post_meta(get_the_ID(), '_doanh_nhan_ngay_sinh', true);
                $gioi_tinh  = get_post_meta(get_the_ID(), '_doanh_nhan_gioi_tinh', true);
                $dien_thoai = get_post_meta(get_the_ID(), '_doanh_nhan_dien_thoai', true);
                $dn_email   = get_post_meta(get_the_ID(), '_doanh_nhan_email', true);
                $dia_chi    = get_post_meta(get_the_ID(), '_doanh_nhan_dia_chi', true);

                $has_info = ($ngay_sinh || $gioi_tinh || $dien_thoai || $dn_email || $dia_chi || $chuc_vu || $ten_dn || $nganh_hang || $khu_vuc);
                ?>

                <article class="cd-dn-profile">
                    <header class="cd-dn-profile__hero">
                        <div class="cd-dn-profile__photo">
                            <?php if ($featured_image_url) : ?>
                                <img src="<?php echo esc_url($featured_image_url); ?>"
                                     alt="<?php echo esc_attr($featured_image_alt); ?>"
                                     loading="lazy">
                            <?php else : ?>
                                <span class="cd-dn-profile__photo-ph" aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(), 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="cd-dn-profile__intro">
                            <h1 class="cd-dn-profile__name"><?php the_title(); ?></h1>
                            <?php if ($chuc_vu) : ?>
                                <p class="cd-dn-profile__role"><?php echo esc_html($chuc_vu); ?></p>
                            <?php endif; ?>
                            <?php if ($ten_dn) : ?>
                                <p class="cd-dn-profile__company"><?php echo esc_html($ten_dn); ?></p>
                            <?php endif; ?>
                            <ul class="cd-dn-profile__chips">
                                <?php if ($khu_vuc) : ?>
                                    <li class="cd-dn-profile__chip">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                        <?php echo esc_html($khu_vuc); ?>
                                    </li>
                                <?php endif; ?>
                                <?php if ($nganh_hang) : ?>
                                    <li class="cd-dn-profile__chip">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        <?php echo esc_html($nganh_hang); ?>
                                    </li>
                                <?php endif; ?>
                                <li class="cd-dn-profile__chip cd-dn-profile__chip--muted">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                                    <?php echo esc_html(get_the_date('d/m/Y')); ?>
                                </li>
                            </ul>
                        </div>
                    </header>

                    <?php if ($has_info) : ?>
                    <section class="cd-dn-profile__info" aria-labelledby="cd-dn-info-title">
                        <h2 id="cd-dn-info-title" class="cd-dn-profile__info-title">Thông tin doanh nhân</h2>
                        <dl class="cd-dn-profile__fields">
                            <?php if ($ngay_sinh) : ?>
                            <div class="cd-dn-profile__field">
                                <dt>Ngày sinh</dt>
                                <dd><?php echo esc_html(date_i18n('d/m/Y', strtotime($ngay_sinh))); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($gioi_tinh) : ?>
                            <div class="cd-dn-profile__field">
                                <dt>Giới tính</dt>
                                <dd><?php echo esc_html($gioi_tinh); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($dien_thoai) : ?>
                            <div class="cd-dn-profile__field">
                                <dt>Số điện thoại</dt>
                                <dd><a href="tel:<?php echo esc_attr($dien_thoai); ?>"><?php echo esc_html($dien_thoai); ?></a></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($dn_email) : ?>
                            <div class="cd-dn-profile__field">
                                <dt>Email</dt>
                                <dd><a href="mailto:<?php echo esc_attr($dn_email); ?>"><?php echo esc_html($dn_email); ?></a></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($chuc_vu) : ?>
                            <div class="cd-dn-profile__field">
                                <dt>Chức vụ hiện nay</dt>
                                <dd><?php echo esc_html($chuc_vu); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($ten_dn) : ?>
                            <div class="cd-dn-profile__field cd-dn-profile__field--wide">
                                <dt>Doanh nghiệp / Đơn vị</dt>
                                <dd><?php echo esc_html($ten_dn); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($nganh_hang) : ?>
                            <div class="cd-dn-profile__field cd-dn-profile__field--wide">
                                <dt>Lĩnh vực hoạt động</dt>
                                <dd><?php echo esc_html($nganh_hang); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($khu_vuc) : ?>
                            <div class="cd-dn-profile__field">
                                <dt>Khu vực</dt>
                                <dd><?php echo esc_html($khu_vuc); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($dia_chi) : ?>
                            <div class="cd-dn-profile__field cd-dn-profile__field--wide">
                                <dt>Địa chỉ liên hệ</dt>
                                <dd><?php echo esc_html($dia_chi); ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </section>
                    <?php endif; ?>

                    <?php
                    $dn_hinh_phu = get_post_meta(get_the_ID(), '_doanh_nhan_hinh_phu', true);
                    if ($dn_hinh_phu) :
                        $hp_url = '';
                        $hp_alt = get_the_title() . ' - Hình ảnh phụ';
                        if (is_numeric($dn_hinh_phu)) {
                            $hp_url = wp_get_attachment_image_url(absint($dn_hinh_phu), 'large');
                            $hp_alt_meta = get_post_meta(absint($dn_hinh_phu), '_wp_attachment_image_alt', true);
                            if ($hp_alt_meta) {
                                $hp_alt = $hp_alt_meta;
                            }
                        } else {
                            $hp_url = esc_url($dn_hinh_phu);
                        }
                        if ($hp_url) :
                            ?>
                            <figure class="cd-dn-profile__aside-media">
                                <img src="<?php echo esc_url($hp_url); ?>" alt="<?php echo esc_attr($hp_alt); ?>" loading="lazy">
                            </figure>
                            <?php
                        endif;
                    endif;
                    ?>

                    <section class="cd-dn-profile__body">
                        <h2 class="cd-dn-profile__body-title">Mô tả chi tiết doanh nhân</h2>
                        <div class="cd-dn-profile__content entry-content">
                            <?php the_content(); ?>
                        </div>
                    </section>

                    <?php
                    $items = function_exists('dnttvn_get_structured_content_array') ? dnttvn_get_structured_content_array(get_the_ID()) : array();
                    if (!empty($items)) :
                        ?>
                        <div class="structured-content-display cd-dn-profile__structured">
                            <?php foreach ($items as $item) : ?>
                                <?php if (!empty($item['heading']) || !empty($item['content']) || !empty($item['content_items'])) : ?>
                                    <div class="structured-item-display">
                                        <?php if (!empty($item['heading'])) : ?>
                                            <h3><?php echo esc_html($item['heading']); ?></h3>
                                        <?php endif; ?>
                                        <?php if (!empty($item['content_items']) && is_array($item['content_items'])) : ?>
                                            <?php foreach ($item['content_items'] as $content_item) :
                                                if (is_string($content_item)) {
                                                    $text = $content_item;
                                                    $ci_images = array();
                                                    $ci_captions = array();
                                                } else {
                                                    $text = $content_item['text'] ?? '';
                                                    $ci_images = $content_item['images'] ?? array();
                                                    $ci_captions = $content_item['image_captions'] ?? array();
                                                }
                                                ?>
                                                <div class="content-item-display">
                                                    <?php if ($text !== '') :
                                                        $c = $text;
                                                        ?>
                                                        <div class="structured-content-text"><?php echo wp_kses_post(preg_match('/\s*</', $c) ? $c : wpautop($c)); ?></div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($ci_images)) : ?>
                                                        <div class="cd-media-gallery">
                                                            <?php foreach ($ci_images as $ci_img_idx => $ci_img_id) :
                                                                $ci_img_id = intval($ci_img_id);
                                                                if ($ci_img_id > 0 && function_exists('dnttvn_render_media_item')) {
                                                                    $cap = isset($ci_captions[$ci_img_idx]) ? (string) $ci_captions[$ci_img_idx] : '';
                                                                    dnttvn_render_media_item($ci_img_id, $cap, 'medium');
                                                                }
                                                            endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php elseif (!empty($item['content'])) : ?>
                                            <div class="structured-content-text"><?php echo wpautop(wp_kses_post($item['content'])); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <nav class="cd-dn-profile__nav" aria-label="Điều hướng hồ sơ">
                        <div>
                            <?php
                            $prev_post = get_previous_post(false, '', 'nganh_nghe');
                            if (!$prev_post) {
                                $prev_post = get_previous_post();
                            }
                            if ($prev_post) :
                                ?>
                                <a class="cd-btn-outline" href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>">← <?php echo esc_html($prev_post->post_title); ?></a>
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
                                <a class="cd-btn-outline" href="<?php echo esc_url(get_permalink($next_post->ID)); ?>"><?php echo esc_html($next_post->post_title); ?> →</a>
                            <?php endif; ?>
                        </div>
                    </nav>
                </article>
        <?php endwhile; ?>

<?php dnttvn_page_shell_end(); ?>

<?php get_footer(); ?>
