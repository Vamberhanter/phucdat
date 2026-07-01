<?php
/**
 * The main template file for displaying the homepage with news posts
 * 
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display the homepage when no more specific template is available.
 */

get_header();
?>

<main class="main-content">
    <!-- Left Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Về Cộng đồng DNTTVN</div>
        <div class="column-content mobile-collapsed">
            <ul class="about-list">
                <?php
                // Hiển thị danh sách các bài viết Cộng đồng ở cột trái
                $cong_dong_args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                );
                $cong_dong_query = new WP_Query($cong_dong_args);

                if ($cong_dong_query->have_posts()) :
                    while ($cong_dong_query->have_posts()) :
                        $cong_dong_query->the_post();
                        $is_noi_bat = get_post_meta(get_the_ID(), '_cong_dong_noi_bat', true);
                        $li_class   = ($is_noi_bat == '1') ? 'highlight-item' : '';
                        ?>
                        <li class="<?php echo esc_attr($li_class); ?>">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <li><a href="#">Chưa có bài viết Cộng đồng</a></li>
                    <?php
                endif;
                ?>
            </ul>
        </div>
        <?php if (function_exists('dnttvn_render_left_sidebar_thanh_vien_block')) dnttvn_render_left_sidebar_thanh_vien_block(); ?>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
            <div class="column-header">Cộng đồng Doanh nhân</div>
            
            <div class="column-cta" style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                <p style="margin: 0; font-size: 14px; color: #555;">Bạn là doanh nhân? Hãy gia nhập cộng đồng cùng chúng tôi.</p>
                <a href="<?php echo esc_url(home_url('/dang-ky/')); ?>" class="cta-button" style="background: #667eea; color: #fff; padding: 8px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 13px; transition: background 0.3s;">Đăng ký tham gia ngay</a>
            </div>

            <?php
            $paged = 1;
            if (get_query_var('paged')) {
                $paged = (int) get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = (int) get_query_var('page');
            } elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $paged = absint($_GET['paged']);
            }

            $dn_list_page = get_page_by_path('doanh-nhan');
            $dn_list_url  = $dn_list_page ? get_permalink($dn_list_page->ID) : home_url('/doanh-nhan/');

            $home_ten   = isset($_GET['ten_doanh_nhan']) ? sanitize_text_field(wp_unslash($_GET['ten_doanh_nhan'])) : '';
            $home_kv    = isset($_GET['khu_vuc']) ? sanitize_title(wp_unslash($_GET['khu_vuc'])) : '';
            $home_cv    = isset($_GET['chuc_vu']) ? sanitize_text_field(wp_unslash($_GET['chuc_vu'])) : '';
            $home_sort  = isset($_GET['sort_by']) ? sanitize_text_field(wp_unslash($_GET['sort_by'])) : 'menu_order';
            ?>

            <!-- Tìm kiếm Doanh nhân -->
            <div class="top-search-section">
                <h3>Tìm kiếm Doanh nhân</h3>
                <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-form-row">
                        <div class="form-group">
                            <label for="home_ten_dn">Tên doanh nhân</label>
                            <input type="text" name="ten_doanh_nhan" id="home_ten_dn" placeholder="Nhập tên doanh nhân" value="<?php echo esc_attr($home_ten); ?>">
                        </div>
                        <div class="form-group">
                            <label for="home_khu_vuc">Khu vực</label>
                            <select name="khu_vuc" id="home_khu_vuc">
                                <option value="">Chọn khu vực</option>
                                <?php
                                $kv_terms = get_terms(array('taxonomy' => 'khu_vuc', 'hide_empty' => false));
                                if (!is_wp_error($kv_terms)) {
                                    foreach ($kv_terms as $term) {
                                        $sel = ($home_kv === $term->slug) ? ' selected' : '';
                                        echo '<option value="' . esc_attr($term->slug) . '"' . $sel . '>' . esc_html($term->name) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="home_chuc_vu">Chức vụ</label>
                            <select name="chuc_vu" id="home_chuc_vu">
                                <option value="">Chọn chức vụ</option>
                                <?php
                                $all_cv = dnttvn_get_all_chuc_vu();
                                foreach ($all_cv as $cv_val) {
                                    $sel = ($home_cv === $cv_val) ? ' selected' : '';
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

            <!-- Sắp xếp -->
            <div class="sort-section">
                <form method="get" action="<?php echo esc_url(home_url('/')); ?>" id="sort-form-home">
                    <?php
                    if ($home_ten !== '') echo '<input type="hidden" name="ten_doanh_nhan" value="' . esc_attr($home_ten) . '">';
                    if ($home_kv !== '')  echo '<input type="hidden" name="khu_vuc" value="' . esc_attr($home_kv) . '">';
                    if ($home_cv !== '')  echo '<input type="hidden" name="chuc_vu" value="' . esc_attr($home_cv) . '">';
                    ?>
                    <div class="sort-controls">
                        <label for="sort_by_home">Sắp xếp theo:</label>
                        <select name="sort_by" id="sort_by_home" onchange="document.getElementById('sort-form-home').submit();">
                            <option value="menu_order" <?php selected($home_sort, 'menu_order'); ?>>Thứ tự</option>
                            <option value="date_desc" <?php selected($home_sort, 'date_desc'); ?>>Mới nhất</option>
                            <option value="date_asc" <?php selected($home_sort, 'date_asc'); ?>>Cũ nhất</option>
                            <option value="title_asc" <?php selected($home_sort, 'title_asc'); ?>>Tên A-Z</option>
                            <option value="title_desc" <?php selected($home_sort, 'title_desc'); ?>>Tên Z-A</option>
                            <option value="khu_vuc" <?php selected($home_sort, 'khu_vuc'); ?>>Khu vực</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Danh sách thẻ doanh nhân -->
            <div class="column-content column-content-home-doanh-nhan">
                <?php
                $dn_args = array(
                    'post_type'      => 'doanh_nhan',
                    'post_status'    => 'publish',
                    'posts_per_page' => 6,
                    'paged'          => $paged,
                );

                if ($home_cv !== '') {
                    $dn_args['meta_query'] = array(
                        array(
                            'key'     => '_doanh_nhan_chuc_vu',
                            'value'   => $home_cv,
                            'compare' => 'LIKE',
                        ),
                    );
                }

                if ($home_ten !== '') {
                    $dn_args['s'] = $home_ten;
                }
                if ($home_kv !== '') {
                    $dn_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'khu_vuc',
                            'field'    => 'slug',
                            'terms'    => $home_kv,
                        ),
                    );
                }

                switch ($home_sort) {
                    case 'menu_order':
                        $dn_args['orderby'] = 'menu_order date';
                        $dn_args['order']   = 'ASC';
                        break;
                    case 'date_asc':
                        $dn_args['orderby'] = 'date';
                        $dn_args['order']   = 'ASC';
                        break;
                    case 'title_asc':
                        $dn_args['orderby'] = 'title';
                        $dn_args['order']   = 'ASC';
                        break;
                    case 'title_desc':
                        $dn_args['orderby'] = 'title';
                        $dn_args['order']   = 'DESC';
                        break;
                    case 'khu_vuc':
                        $dn_args['orderby'] = 'meta_value';
                        $dn_args['meta_key'] = '_doanh_nhan_chuc_vu';
                        $dn_args['order']   = 'ASC';
                        break;
                    case 'date_desc':
                        $dn_args['orderby'] = 'date';
                        $dn_args['order']   = 'DESC';
                        break;
                    default:
                        $dn_args['orderby'] = 'menu_order date';
                        $dn_args['order']   = 'ASC';
                        break;
                }

                $dn_query = new WP_Query($dn_args);

                if ($dn_query->have_posts()) :
                    ?>
                    <div id="home-doanh-nhan-list" class="content-columns home-doanh-nhan-grid">
                        <?php
                        while ($dn_query->have_posts()) :
                            $dn_query->the_post();
                            get_template_part('template-parts/card', 'doanh-nhan');
                        endwhile;
                        ?>
                    </div>

                    <?php if ($dn_query->max_num_pages > 1 && function_exists('dnttvn_custom_pagination')) : ?>
                        <div class="pagination-wrapper">
                            <?php echo dnttvn_custom_pagination($dn_query, array('ten_doanh_nhan', 'khu_vuc', 'chuc_vu', 'sort_by')); ?>
                        </div>
                    <?php endif; ?>

                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="business-card business-card-layout" style="padding: 30px; text-align: center;">
                        <?php if ($home_ten !== '' || $home_kv !== '' || $home_cv !== '') : ?>
                            <p>Không tìm thấy doanh nhân phù hợp với tìm kiếm.</p>
                        <?php else : ?>
                            <p>Chưa có doanh nhân đã duyệt để hiển thị. <a href="<?php echo esc_url(home_url('/dang-ky/')); ?>">Đăng ký tham gia</a>.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
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
