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
        <div class="dn-side-panel dn-sidebar-industry">
            <div class="column-header dn-panel-tab dn-sidebar-industry__header">Mục lục ngành hàng</div>
            <div class="dn-sidebar-industry__body">
                <ul class="dn-sidebar-industry__list">
                    <?php
                    $dn_page_url = get_permalink();
                    $dn_industry_icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.2"/><rect x="14" y="3" width="7" height="7" rx="1.2"/><rect x="3" y="14" width="7" height="7" rx="1.2"/><rect x="14" y="14" width="7" height="7" rx="1.2"/></svg>';
                    foreach ($dn_nganh_terms as $dn_term) {
                        $dn_term_url = add_query_arg('nganh_hang', $dn_term->slug, $dn_page_url);
                        $dn_count    = isset($dn_term->count) ? (int) $dn_term->count : 0;
                        echo '<li class="dn-sidebar-industry__item"><a href="' . esc_url($dn_term_url) . '">';
                        echo '<span class="dn-sidebar-industry__icon">' . $dn_industry_icon . '</span>';
                        echo '<span class="dn-sidebar-industry__name">' . esc_html($dn_term->name) . '</span>';
                        if ($dn_count > 0) {
                            echo '<span class="dn-sidebar-industry__count">(' . esc_html((string) $dn_count) . ')</span>';
                        }
                        echo '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <div class="dn-side-panel dn-sidebar-dn-list">
            <div class="column-header dn-panel-tab mobile-toggle collapsed">Danh Mục Hàng</div>
            <div class="column-content mobile-collapsed">
                <div class="dn-side-card">
                    <ul class="linked-websites">
                        <?php
                        $dn_query = new WP_Query(array('post_type' => 'doanh_nghiep', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order date', 'order' => 'ASC'));
                        if ($dn_query->have_posts()) :
                            while ($dn_query->have_posts()) : $dn_query->the_post();
                                ?><li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li><?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            ?><li class="dn-side-card__empty"><span>Chưa có mục nào.</span></li><?php
                        endif;
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="dn-side-panel">
            <div class="column-header dn-panel-tab mobile-toggle collapsed">Web liên kết</div>
            <div class="column-content mobile-collapsed">
                <div class="dn-side-card">
                    <ul class="linked-websites">
                        <?php
                        $community_links = function_exists('dnttvn_get_community_links') ? dnttvn_get_community_links() : array();
                        $has_link = false;
                        foreach ($community_links as $link) {
                            if (!empty($link['url'])) {
                                $has_link = true;
                                echo '<li><a href="' . esc_url($link['url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($link['name']) . '</a></li>';
                            }
                        }
                        if (!$has_link) {
                            echo '<li class="dn-side-card__empty"><span>Chưa có liên kết.</span></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
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
                        <button type="submit" class="search-button">
                            <svg class="search-button__icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                            Tìm kiếm
                        </button>
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
        <?php
        $dn_list_filters = dnttvn_parse_doanh_nghiep_list_filters();
        $paged           = $dn_list_filters['paged'];
        $args            = dnttvn_build_doanh_nghiep_list_query_args($dn_list_filters);
        $doanh_nghiep_query = new WP_Query($args);
        $dn_max_pages    = (int) $doanh_nghiep_query->max_num_pages;
        $dn_filter_attrs = array(
            'ten_doanh_nghiep' => $dn_list_filters['ten_doanh_nghiep'],
            'khu_vuc'          => $dn_list_filters['khu_vuc'],
            'nganh_hang'       => $dn_list_filters['nganh_hang'],
            'sort_by'          => $dn_list_filters['sort_by'],
        );
        ?>
        <div
            id="doanh-nghiep-list"
            class="content-columns"
            data-dn-infinite="1"
            data-dn-page="<?php echo esc_attr((string) $paged); ?>"
            data-dn-max-pages="<?php echo esc_attr((string) $dn_max_pages); ?>"
            data-dn-filters="<?php echo esc_attr(wp_json_encode($dn_filter_attrs)); ?>"
        >
            <?php
            if ($doanh_nghiep_query->have_posts()) :
                ?>
                <div class="dn-list-cards">
                    <?php echo dnttvn_render_doanh_nghiep_list_page_html($doanh_nghiep_query, $dn_list_filters); ?>
                </div>
                <?php if ($dn_max_pages > 1) : ?>
                    <div class="dn-infinite-sentinel" aria-hidden="true"></div>
                    <div class="dn-infinite-status" hidden>Đang tải thêm...</div>
                    <div class="pagination-wrapper">
                        <?php echo dnttvn_custom_pagination($doanh_nghiep_query); ?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="business-card">
                    <p>Không tìm thấy doanh nghiệp nào. Vui lòng thêm doanh nghiệp từ trang quản trị WordPress.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar: Banner / CTA -->
    <div class="sidebar-column">
        <div class="dn-side-panel">
            <div class="column-header dn-panel-tab mobile-toggle collapsed">Theo ngành hàng</div>
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
    </div>
</main>
</div>

<?php get_footer(); ?>
