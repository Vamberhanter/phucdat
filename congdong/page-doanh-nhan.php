<?php
/**
 * Template Name: Trang Danh sách Doanh nhân
 *
 * Hiển thị tất cả doanh nhân đã publish (thêm tay hoặc từ đơn đăng ký đã duyệt).
 * Giao diện thẻ và bộ lọc tương tự trang Doanh nghiệp.
 */

get_header();

$page_dn_list = get_page_by_path('doanh-nhan');
$dn_list_url  = $page_dn_list ? get_permalink($page_dn_list->ID) : get_permalink();

$paged = 1;
if (get_query_var('paged')) {
    $paged = (int) get_query_var('paged');
} elseif (get_query_var('page')) {
    $paged = (int) get_query_var('page');
} elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
    $paged = absint($_GET['paged']);
}

$ten_doanh_nhan = isset($_GET['ten_doanh_nhan']) ? sanitize_text_field(wp_unslash($_GET['ten_doanh_nhan'])) : '';
$khu_vuc_slug   = isset($_GET['khu_vuc']) ? sanitize_title(wp_unslash($_GET['khu_vuc'])) : '';
$chuc_vu        = isset($_GET['chuc_vu']) ? sanitize_text_field(wp_unslash($_GET['chuc_vu'])) : '';
$sort_by        = isset($_GET['sort_by']) ? sanitize_text_field(wp_unslash($_GET['sort_by'])) : 'menu_order';

$args = array(
    'post_type'      => 'doanh_nhan',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'paged'          => $paged,
);

if ($chuc_vu !== '') {
    $args['meta_query'] = array(
        array(
            'key'     => '_doanh_nhan_chuc_vu',
            'value'   => $chuc_vu,
            'compare' => 'LIKE',
        ),
    );
}

if ($ten_doanh_nhan !== '') {
    $args['s'] = $ten_doanh_nhan;
}

if ($khu_vuc_slug !== '') {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'khu_vuc',
            'field'    => 'slug',
            'terms'    => $khu_vuc_slug,
        ),
    );
}

switch ($sort_by) {
    case 'date_asc':
        $args['orderby'] = 'date';
        $args['order']   = 'ASC';
        break;
    case 'title_asc':
        $args['orderby'] = 'title';
        $args['order']   = 'ASC';
        break;
    case 'title_desc':
        $args['orderby'] = 'title';
        $args['order']   = 'DESC';
        break;
    case 'menu_order':
        $args['orderby'] = 'menu_order date';
        $args['order']   = 'ASC';
        break;
    case 'date_desc':
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
        break;
    default:
        $args['orderby'] = 'menu_order date';
        $args['order']   = 'ASC';
        break;
}

$query = new WP_Query($args);

dnttvn_page_shell_start('Doanh nhân');
?>
<h1 class="cd-detail__title">Danh sách Doanh nhân</h1>
        <div class="top-search-section">
            <h3>Tìm kiếm Doanh nhân</h3>
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>">
                <div class="search-form-row">
                    <div class="form-group">
                        <label for="ten_doanh_nhan">Tên doanh nhân</label>
                        <input type="text" name="ten_doanh_nhan" id="ten_doanh_nhan" placeholder="Nhập tên doanh nhân" value="<?php echo esc_attr($ten_doanh_nhan); ?>">
                    </div>
                    <div class="form-group">
                        <label for="khu_vuc_dn">Khu vực</label>
                        <select name="khu_vuc" id="khu_vuc_dn">
                            <option value="">Chọn khu vực</option>
                            <?php
                            $kv_terms = get_terms(array(
                                'taxonomy'   => 'khu_vuc',
                                'hide_empty' => false,
                            ));
                            if (!is_wp_error($kv_terms)) {
                                foreach ($kv_terms as $term) {
                                    $sel = ($khu_vuc_slug === $term->slug) ? ' selected' : '';
                                    echo '<option value="' . esc_attr($term->slug) . '"' . $sel . '>' . esc_html($term->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="chuc_vu">Chức vụ</label>
                        <select name="chuc_vu" id="chuc_vu">
                            <option value="">Chọn chức vụ</option>
                            <?php
                            $all_cv = dnttvn_get_all_chuc_vu();
                            foreach ($all_cv as $cv_val) {
                                $sel = ($chuc_vu === $cv_val) ? ' selected' : '';
                                echo '<option value="' . esc_attr($cv_val) . '"' . $sel . '>' . esc_html($cv_val) . '</option>';
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

        <div class="sort-section">
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="sort-form-doanh-nhan">
                <?php
                if ($ten_doanh_nhan !== '') {
                    echo '<input type="hidden" name="ten_doanh_nhan" value="' . esc_attr($ten_doanh_nhan) . '">';
                }
                if ($khu_vuc_slug !== '') {
                    echo '<input type="hidden" name="khu_vuc" value="' . esc_attr($khu_vuc_slug) . '">';
                }
                if ($chuc_vu !== '') {
                    echo '<input type="hidden" name="chuc_vu" value="' . esc_attr($chuc_vu) . '">';
                }
                ?>
                <div class="sort-controls">
                    <label for="sort_by_dn">Sắp xếp theo:</label>
                    <select name="sort_by" id="sort_by_dn" onchange="document.getElementById('sort-form-doanh-nhan').submit();">
                        <option value="menu_order" <?php selected($sort_by, 'menu_order'); ?>>Thứ tự đăng bài</option>
                        <option value="date_desc" <?php selected($sort_by, 'date_desc'); ?>>Mới nhất</option>
                        <option value="date_asc" <?php selected($sort_by, 'date_asc'); ?>>Cũ nhất</option>
                        <option value="title_asc" <?php selected($sort_by, 'title_asc'); ?>>Tên A-Z</option>
                        <option value="title_desc" <?php selected($sort_by, 'title_desc'); ?>>Tên Z-A</option>
                    </select>
                </div>
            </form>
        </div>

        <div id="doanh-nhan-list" class="content-columns">
            <?php
            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post();
                    get_template_part('template-parts/card', 'doanh-nhan');
                endwhile;
                wp_reset_postdata();

                if ($query->max_num_pages > 1) :
                    ?>
                    <div class="pagination-wrapper">
                        <?php
                        echo dnttvn_custom_pagination(
                            $query,
                            array('ten_doanh_nhan', 'khu_vuc', 'chuc_vu', 'sort_by')
                        );
                        ?>
                    </div>
                    <?php
                endif;
            else :
                ?>
                <div class="business-card business-card-layout">
                    <div class="column-content" style="padding:24px;">
                        <p>Chưa có doanh nhân nào từ đăng ký đã duyệt, hoặc không có kết quả phù hợp bộ lọc.</p>
                    </div>
                </div>
                <?php
            endif;
            ?>
        </div>

<?php dnttvn_page_shell_end(); ?>

<?php
get_footer();
