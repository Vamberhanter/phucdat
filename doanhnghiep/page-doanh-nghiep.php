<?php
/**
 * Template Name: Trang Doanh nghiệp
 * 
 * Template for displaying the business directory page
 */

get_header();

$dn_filtered = (isset($_GET['ten_doanh_nghiep']) && $_GET['ten_doanh_nghiep'] !== '')
    || (isset($_GET['khu_vuc']) && $_GET['khu_vuc'] !== '')
    || (isset($_GET['nganh_hang']) && $_GET['nganh_hang'] !== '');
$dn_boxed_home = !$dn_filtered;

$dn_nganh_terms = get_terms(
    array(
        'taxonomy'   => 'nganh_hang',
        'hide_empty' => false,
    )
);
if (is_wp_error($dn_nganh_terms)) {
    $dn_nganh_terms = array();
}
?>

<div class="dn-directory-outer dn-directory-outer--boxed">
<main class="main-content dn-directory-main<?php echo $dn_boxed_home ? ' dn-directory-main--home' : ' dn-directory-main--filtered'; ?>">
    <!-- Left Sidebar: chỉ Danh sách Doanh nghiệp (không Cộng đồng / Tin tức) -->
    <?php
    $page_dn = get_page_by_path('danh-sach-doanh-nghiep');
    if (!$page_dn) {
        $page_dn = get_page_by_path('page-doanh-nghiep');
    }
    $dn_list_url = $page_dn ? get_permalink($page_dn->ID) : home_url('/danh-sach-doanh-nghiep/');
    ?>
    <div class="sidebar-column">
        <?php if ($dn_boxed_home && !empty($dn_nganh_terms)) : ?>
        <div class="dn-sidebar-industry">
            <div class="column-header dn-sidebar-industry__header">Mục lục ngành hàng</div>
            <div class="dn-sidebar-industry__body">
                <ul class="dn-sidebar-industry__list">
                    <?php
                    $dn_page_url = get_permalink();
                    foreach ($dn_nganh_terms as $dn_term) {
                        $dn_term_url = add_query_arg('nganh_hang', $dn_term->slug, $dn_page_url);
                        $dn_count    = isset($dn_term->count) ? (int) $dn_term->count : 0;
                        echo '<li class="dn-sidebar-industry__item"><a href="' . esc_url($dn_term_url) . '">' . esc_html($dn_term->name);
                        if ($dn_count > 0) {
                            echo ' <span class="dn-sidebar-industry__count">(' . esc_html((string) $dn_count) . ')</span>';
                        }
                        echo '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <div class="dn-sidebar-dn-list">
            <div class="column-header mobile-toggle collapsed">Danh sách Doanh nghiệp</div>
            <div class="column-content mobile-collapsed">
                <ul class="linked-websites">
                    <?php
                    $dn_query = new WP_Query(array('post_type' => 'doanh_nghiep', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order date', 'order' => 'ASC'));
                    if ($dn_query->have_posts()) :
                        while ($dn_query->have_posts()) : $dn_query->the_post();
                            ?><li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li><?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
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

    <!-- Center Content -->
    <div class="main-center">
        <?php if ($dn_boxed_home) : ?>
        <div class="dn-directory-hero">
            <h1 class="dn-directory-hero__title"><?php echo esc_html(get_the_title()); ?></h1>
        </div>
        <?php endif; ?>
        <!-- Top Search Section -->
        <div class="top-search-section">
            <h3>Tìm kiếm Doanh nghiệp</h3>
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>">
                <div class="search-form-row">
                    <div class="form-group">
                        <label>* Tên Doanh nghiệp</label>
                        <input type="text" name="ten_doanh_nghiep" placeholder="Nhập tên doanh nghiệp" value="<?php echo isset($_GET['ten_doanh_nghiep']) ? esc_attr($_GET['ten_doanh_nghiep']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>* Khu vực</label>
                        <select name="khu_vuc">
                            <option value="">Chọn khu vực</option>
                            <?php
                            $khu_vuc_terms = get_terms(array(
                                'taxonomy'   => 'khu_vuc',
                                'hide_empty' => false,
                            ));
                            foreach ($khu_vuc_terms as $term) {
                                $selected = (isset($_GET['khu_vuc']) && $_GET['khu_vuc'] == $term->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>* Ngành hàng</label>
                        <select name="nganh_hang">
                            <option value="">Chọn ngành hàng</option>
                            <?php
                            $nganh_hang_terms = get_terms(array(
                                'taxonomy'   => 'nganh_hang',
                                'hide_empty' => false,
                            ));
                            foreach ($nganh_hang_terms as $term) {
                                $selected = (isset($_GET['nganh_hang']) && $_GET['nganh_hang'] == $term->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group form-group-button">
                        <label class="search-button-label">&nbsp;</label>
                        <button type="submit" class="search-button">Tìm kiếm</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sort Section -->
        <div class="sort-section">
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="sort-form">
                <?php
                // Preserve search and filter parameters
                if (isset($_GET['ten_doanh_nghiep']) && !empty($_GET['ten_doanh_nghiep'])) {
                    echo '<input type="hidden" name="ten_doanh_nghiep" value="' . esc_attr($_GET['ten_doanh_nghiep']) . '">';
                }
                if (isset($_GET['khu_vuc']) && !empty($_GET['khu_vuc'])) {
                    echo '<input type="hidden" name="khu_vuc" value="' . esc_attr($_GET['khu_vuc']) . '">';
                }
                if (isset($_GET['nganh_hang']) && !empty($_GET['nganh_hang'])) {
                    echo '<input type="hidden" name="nganh_hang" value="' . esc_attr($_GET['nganh_hang']) . '">';
                }
                ?>
                <div class="sort-controls">
                    <label for="sort_by">Sắp xếp theo:</label>
                    <select name="sort_by" id="sort_by" onchange="document.getElementById('sort-form').submit();">
                        <option value="menu_order" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'menu_order' ? 'selected' : ''; ?>>Thứ tự đăng bài</option>
                        <option value="date_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'date_desc') || !isset($_GET['sort_by']) ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="date_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'date_asc' ? 'selected' : ''; ?>>Cũ nhất</option>
                        <option value="title_asc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'title_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                        <option value="title_desc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'title_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                        <option value="nganh_hang" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'nganh_hang' ? 'selected' : ''; ?>>Ngành hàng</option>
                        <option value="khu_vuc" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] == 'khu_vuc' ? 'selected' : ''; ?>>Khu vực</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Business Cards Grid -->
        <div id="doanh-nghiep-list" class="content-columns">
            <?php
            // Get current page number
            // For custom page templates, use $_GET['paged'] instead of get_query_var('paged')
            $paged = 1;
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $paged = absint($_GET['paged']);
            }
            
            // Get sort parameter
            $sort_by = isset($_GET['sort_by']) ? sanitize_text_field($_GET['sort_by']) : 'date_desc';
            
            $args = array(
                'post_type'      => 'doanh_nghiep',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'paged'          => $paged,
            );
            
            // Handle sorting
            switch ($sort_by) {
                case 'menu_order':
                    $args['orderby'] = 'menu_order date';
                    $args['order'] = 'ASC';
                    break;
                case 'date_asc':
                    $args['orderby'] = 'date';
                    $args['order'] = 'ASC';
                    break;
                case 'title_asc':
                    $args['orderby'] = 'title';
                    $args['order'] = 'ASC';
                    break;
                case 'title_desc':
                    $args['orderby'] = 'title';
                    $args['order'] = 'DESC';
                    break;
                case 'nganh_hang':
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = '_nganh_hang';
                    $args['order'] = 'ASC';
                    break;
                case 'khu_vuc':
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = '_khu_vuc';
                    $args['order'] = 'ASC';
                    break;
                case 'date_desc':
                default:
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                    break;
            }

            // Filter by search
            if (isset($_GET['ten_doanh_nghiep']) && !empty($_GET['ten_doanh_nghiep'])) {
                $args['s'] = sanitize_text_field($_GET['ten_doanh_nghiep']);
            }

            // Filter by taxonomy
            $tax_query = array();
            if (isset($_GET['khu_vuc']) && !empty($_GET['khu_vuc'])) {
                $tax_query[] = array(
                    'taxonomy' => 'khu_vuc',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['khu_vuc']),
                );
            }
            if (isset($_GET['nganh_hang']) && !empty($_GET['nganh_hang'])) {
                $tax_query[] = array(
                    'taxonomy' => 'nganh_hang',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['nganh_hang']),
                );
            }
            if (!empty($tax_query)) {
                $args['tax_query'] = $tax_query;
            }

            $doanh_nghiep_query = new WP_Query($args);

            // Use standard WordPress permalinks for doanh_nghiep

            if ($doanh_nghiep_query->have_posts()) :
                $post_count = 0;
                while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                    $post_count++;
                    // Get custom fields
                    $nganh_hang   = get_post_meta(get_the_ID(), '_nganh_hang', true);
                    $khu_vuc      = get_post_meta(get_the_ID(), '_khu_vuc', true);
                    $hinh_anh_phu = get_post_meta(get_the_ID(), '_hinh_anh_phu', true);
                    $dia_chi_dn   = get_post_meta(get_the_ID(), '_dia_chi', true);
                    $dien_thoai   = get_post_meta(get_the_ID(), '_dien_thoai', true);
                    $gallery_raw  = get_post_meta(get_the_ID(), '_gallery_images', true);
                    $gallery_ids  = array_filter(array_map('absint', explode(',', (string) $gallery_raw)));

                    // Get taxonomy terms as fallback nếu meta trống
                    $nganh_hang_terms = get_the_terms(get_the_ID(), 'nganh_hang');
                    $khu_vuc_terms    = get_the_terms(get_the_ID(), 'khu_vuc');
                    
                    // Get Featured Image (Hình chính) - This is the main logo/image of the business
                    $featured_image_id = get_post_thumbnail_id();
                    $featured_image_url = '';
                    $featured_image_alt = '';
                    if ($featured_image_id) {
                        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'medium');
                        $featured_image_alt = get_post_meta($featured_image_id, '_wp_attachment_image_alt', true);
                        if (empty($featured_image_alt)) {
                            $featured_image_alt = get_the_title() . ' - Logo';
                        }
                    }
                    
                    // Hình phụ đơn (attachment ID hoặc URL ngoài media — đều hiện ở hàng thumbnail listing)
                    $small_image_id   = null;
                    $hinh_phu_url     = '';
                    if ($hinh_anh_phu) {
                        if (is_numeric($hinh_anh_phu)) {
                            $small_image_id = absint($hinh_anh_phu);
                        } else {
                            $small_image_id = attachment_url_to_postid($hinh_anh_phu);
                            if (!$small_image_id && is_string($hinh_anh_phu) && filter_var($hinh_anh_phu, FILTER_VALIDATE_URL)) {
                                $hinh_phu_url = $hinh_anh_phu;
                            }
                        }
                    }

                    // Get description/excerpt (ưu tiên Mô tả ngắn trong meta)
                    $description = get_post_meta(get_the_ID(), '_doanh_nghiep_mo_ta_ngan', true);
                    if (!$description) {
                        if (has_excerpt()) {
                            $description = get_the_excerpt();
                        } else {
                            $content = get_the_content();
                            $description = wp_trim_words(strip_shortcodes($content), 50, '...');
                        }
                    }
                    // Use standard WordPress URL for doanh nghiệp
                    $detail_url = get_permalink();
                    $website_dn = get_post_meta(get_the_ID(), '_website_doanh_nghiep', true);
                    $website_dn = is_string($website_dn) ? trim($website_dn) : '';
                    $card_url   = $detail_url;
                    $card_extra = '';
                    $card_aria  = 'Xem thông tin doanh nghiệp ' . get_the_title();
                    if ($website_dn !== '') {
                        $website_href = $website_dn;
                        if (! preg_match('~^https?://~i', $website_href)) {
                            $website_href = '//' . $website_href;
                        }
                        $card_url_try = esc_url($website_href);
                        if ($card_url_try) {
                            $card_url   = $card_url_try;
                            $card_extra = ' target="_blank" rel="noopener noreferrer"';
                            $card_aria  = 'Truy cập website doanh nghiệp ' . get_the_title();
                        }
                    }
                    if (!$nganh_hang && $nganh_hang_terms && !is_wp_error($nganh_hang_terms)) {
                        $term_names = wp_list_pluck($nganh_hang_terms, 'name');
                        $nganh_hang = implode(', ', $term_names);
                    }
                    if (!$khu_vuc && $khu_vuc_terms && !is_wp_error($khu_vuc_terms)) {
                        $term_names = wp_list_pluck($khu_vuc_terms, 'name');
                        $khu_vuc    = implode(', ', $term_names);
                    }

                    $thumb_ids = $gallery_ids;
                    if ($small_image_id && ! in_array($small_image_id, $thumb_ids, true)) {
                        array_unshift($thumb_ids, $small_image_id);
                    }
                    if (empty($thumb_ids) && !$hinh_phu_url && $featured_image_id) {
                        $thumb_ids = array($featured_image_id);
                    }
                    $dn_card_thumb_max = function_exists('dnttvn_doanh_nghiep_gallery_card_max') ? dnttvn_doanh_nghiep_gallery_card_max() : 5;
                    $thumb_ids           = array_slice(array_unique(array_filter($thumb_ids)), 0, $dn_card_thumb_max);
                    if ($hinh_phu_url) {
                        $thumb_ids = array_slice($thumb_ids, 0, max(0, $dn_card_thumb_max - 1));
                    }
                    $description_show = $description;
                    if ($description_show && function_exists('dnttvn_dn_reg_trim_to_word_limit')) {
                        $mo_lim           = defined('DNTTVN_DN_REG_MAX_WORDS_MO_TA') ? (int) DNTTVN_DN_REG_MAX_WORDS_MO_TA : 200;
                        $description_show = dnttvn_dn_reg_trim_to_word_limit(trim(wp_strip_all_tags($description_show)), $mo_lim);
                    }
                    ?>
                    <div class="business-card-yp-wrap">
                    <div class="business-card business-card-layout business-card-layout--yp">
                        <div class="business-card-yp-header">
                            <span class="business-card-yp-title"><?php the_title(); ?></span>
                        </div>
                        <div class="business-card-body">
                            <div class="business-card-yp-row">
                                <div class="business-card-yp-logo-stack">
                                    <div class="business-card-yp-logo">
                                        <?php if ($featured_image_url) : ?>
                                            <img src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" class="business-main-image" loading="lazy">
                                        <?php else : ?>
                                            <img src="https://via.placeholder.com/160x160/667eea/ffffff?text=<?php echo esc_attr(urlencode(get_the_title())); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="business-main-image" loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="business-card-yp-info">
                                    <?php if ($nganh_hang) : ?>
                                        <div class="business-card-yp-info-line business-card-yp-info-line--nganh">
                                            <svg class="business-card-yp-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20 6h-2.18c.07-.44.18-.86.18-1.3C18 2.55 15.87.85 13.5 1.24c-1.18.2-2.2.88-2.93 1.82L10 3.8l-.57-.74C8.7 2.12 7.68 1.44 6.5 1.24 4.13.85 2 2.55 2 4.7c0 .44.11.86.18 1.3H0v2h2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8h2V6h-4zM11 4.56c.35-.6 1-.93 1.64-1.02C14 3.33 15.16 4.4 15.16 5.6c0 .14-.03.27-.05.4H11V4.56zm-5.5-.98C6.14 3.33 7.3 4.4 7.3 5.6c0 .14-.03.27-.05.4H4.89c-.02-.13-.05-.26-.05-.4 0-.9.66-1.73 1.66-1.62z" /></svg>
                                            <span><?php echo esc_html($nganh_hang); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($khu_vuc) : ?>
                                        <div class="business-card-yp-info-line business-card-yp-info-line--khuvuc">
                                            <svg class="business-card-yp-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                            <span><?php echo esc_html($khu_vuc); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($dia_chi_dn) : ?>
                                        <div class="business-card-yp-info-line">
                                            <svg class="business-card-yp-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                            <span><?php echo esc_html($dia_chi_dn); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($dien_thoai) : ?>
                                        <div class="business-card-yp-info-line">
                                            <svg class="business-card-yp-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.21z"/></svg>
                                            <span><?php echo esc_html($dien_thoai); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="business-card-description business-card-description--listing-full business-card-description--yp">
                                <?php if ($description_show) : ?>
                                    <p style="margin:0 0 6px;font-size:13px;font-weight:600;color:#333;">Mô tả ngắn</p>
                                    <?php echo wp_kses_post(wpautop(esc_html($description_show))); ?>
                                <?php else : ?>
                                    <p><em>Chưa có mô tả.</em></p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($thumb_ids) || $hinh_phu_url) : ?>
                            <div class="business-card-yp-thumbs" aria-hidden="true">
                                <?php
                                if ($hinh_phu_url) {
                                    echo '<span class="business-card-yp-thumb"><img src="' . esc_url($hinh_phu_url) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy" width="72" height="72" /></span>';
                                }
                                foreach ($thumb_ids as $tid) {
                                    $tu = wp_get_attachment_image_url($tid, 'thumbnail');
                                    if (!$tu) {
                                        $tu = wp_get_attachment_image_url($tid, 'medium');
                                    }
                                    if (!$tu) {
                                        continue;
                                    }
                                    $ta = get_post_meta($tid, '_wp_attachment_image_alt', true);
                                    echo '<span class="business-card-yp-thumb"><img src="' . esc_url($tu) . '" alt="' . esc_attr($ta) . '" loading="lazy" width="72" height="72" /></span>';
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="business-card-yp-footer">
                            <a href="<?php echo esc_url($detail_url . '#dn-noi-dung'); ?>" class="business-card-yp-detail-link">Xem chi tiết</a>
                        </div>
                    </div>
                    <a href="<?php echo esc_url($card_url); ?>" class="business-card-link business-card-link--yp business-card-link--yp-stretch" aria-label="<?php echo esc_attr($card_aria); ?>"<?php echo $card_extra; ?>></a>
                    </div>
                    <?php
                    // View mobile: chèn banner xen kẽ 1 thẻ DN – 1 banner,
                    // và vẫn giữ thứ tự theo toàn bộ danh sách (tính cả khi phân trang).
                    // posts_per_page hiện đang là 6
                    $posts_per_page   = $args['posts_per_page'];
                    $global_index     = ($paged - 1) * $posts_per_page + ($post_count - 1); // 0-based index
                    $mobile_banner_html = dnttvn_render_banner_blocks('ad-block-mobile', $global_index, 1);
                    if (!empty($mobile_banner_html)) {
                        echo '<div class="ad-section-mobile">' . $mobile_banner_html . '</div>';
                    }
                endwhile;
                wp_reset_postdata();
                
                // Pagination
                if ($doanh_nghiep_query->max_num_pages > 1) :
                    ?>
                    <div class="pagination-wrapper">
                        <?php
                        echo dnttvn_custom_pagination($doanh_nghiep_query);
                        ?>
                    </div>
                    <?php
                endif;
            else :
                ?>
                <div class="business-card">
                    <p>Không tìm thấy doanh nghiệp nào. Vui lòng thêm doanh nghiệp từ trang quản trị WordPress.</p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>

    <!-- Right Sidebar: Theo ngành hàng (banner đã lưu) -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Theo ngành hàng</div>
        <div class="column-content mobile-collapsed">
            <?php if ($dn_boxed_home) : ?>
            <div class="dn-sidebar-cta">
                <p class="dn-sidebar-cta__q">Bạn là doanh nghiệp?</p>
                <p class="dn-sidebar-cta__text">Liên hệ ban quản trị để cập nhật hoặc giới thiệu thông tin trên danh bạ.</p>
            </div>
            <?php endif; ?>
            <?php
            $right_banner_html = dnttvn_render_banner_blocks('ad-block');
            if (!empty($right_banner_html)) :
            ?>
            <div class="ad-section">
                <?php echo $right_banner_html; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>

<?php get_footer(); ?>
