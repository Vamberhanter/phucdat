<?php
/**
 * Template Name: Trang Cộng đồng
 * 
 * Template for displaying Community posts (Cộng đồng)
 * Based on index.php structure
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
                // Query Cộng đồng posts for sidebar
                $sidebar_args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                );
                $sidebar_query = new WP_Query($sidebar_args);
                
                // ID bài hiện tại (nếu đang xem chi tiết Cộng đồng)
                $current_post_id = get_queried_object_id();
                
                if ($sidebar_query->have_posts()) :
                    while ($sidebar_query->have_posts()) : $sidebar_query->the_post();
                        $post_id    = get_the_ID();
                        $is_noi_bat = get_post_meta($post_id, '_cong_dong_noi_bat', true);
                        $li_class   = ($is_noi_bat == '1') ? 'highlight-item' : '';

                        // Nếu đang ở bài này thì tô nổi bật thêm class current-item
                        if ((int) $current_post_id === (int) $post_id) {
                            $li_class .= ' current-item';
                        }
                        ?>
                        <li class="<?php echo esc_attr(trim($li_class)); ?>">
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
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
            <div class="column-header">Bài viết Cộng đồng</div>
            
            <!-- Sort Section -->
            <div class="sort-section" style="margin-bottom: 20px;">
                <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="cong-dong-sort-form">
                    <div class="sort-controls">
                        <label for="cong_dong_sort_by">Sắp xếp:</label>
                        <select name="cong_dong_sort_by" id="cong_dong_sort_by" onchange="document.getElementById('cong-dong-sort-form').submit();">
                            <option value="menu_order" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'menu_order' ? 'selected' : ''; ?>>Thứ tự đăng bài</option>
                            <option value="date_desc" <?php echo (isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'date_desc') || !isset($_GET['cong_dong_sort_by']) ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="date_asc" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'date_asc' ? 'selected' : ''; ?>>Cũ nhất</option>
                            <option value="title_asc" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'title_asc' ? 'selected' : ''; ?>>Tiêu đề A-Z</option>
                            <option value="title_desc" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'title_desc' ? 'selected' : ''; ?>>Tiêu đề Z-A</option>
                            <option value="noi_bat" <?php echo isset($_GET['cong_dong_sort_by']) && $_GET['cong_dong_sort_by'] == 'noi_bat' ? 'selected' : ''; ?>>Bài nổi bật trước</option>
                        </select>
                    </div>

                </form>
            </div>
            
            <div class="column-content">
                <?php
                // Get sort parameter
                $cong_dong_sort_by = isset($_GET['cong_dong_sort_by']) ? sanitize_text_field($_GET['cong_dong_sort_by']) : 'date_desc';
                
                // Phân trang & sắp xếp cho Cộng đồng
                $paged_cong_dong = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array(
                    'post_type'      => 'cong_dong',
                    'posts_per_page' => 10,
                    'post_status'    => 'publish',
                    'paged'          => $paged_cong_dong,
                );
                
                // Handle sorting
                switch ($cong_dong_sort_by) {
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
                    case 'noi_bat':
                        $args['meta_key'] = '_cong_dong_noi_bat';
                        $args['orderby'] = 'meta_value date';
                        $args['order'] = 'DESC';
                        break;
                    case 'date_desc':
                    default:
                        $args['orderby'] = 'date';
                        $args['order'] = 'DESC';
                        break;
                }
                
                $cong_dong_query = new WP_Query($args);
                
                if ($cong_dong_query->have_posts()) :
                    while ($cong_dong_query->have_posts()) : $cong_dong_query->the_post();
                        // Show excerpt/summary for list view
                        ?>
                        <div class="news-item">
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <p class="news-date"><?php echo get_the_date('d/m/Y'); ?></p>
                            <?php
                            // Try to get custom short description first
                            $mo_ta_ngan = get_post_meta(get_the_ID(), '_cong_dong_mo_ta_ngan', true);
                            if ($mo_ta_ngan) {
                                echo '<p>' . esc_html($mo_ta_ngan) . '</p>';
                            } elseif (has_excerpt()) {
                                the_excerpt();
                            } else {
                                echo '<p>' . wp_trim_words(get_the_content(), 50) . '</p>';
                            }
                            ?>
                        </div>
                        <?php
                    endwhile;
                    ?>
                    <div class="pagination-wrapper">
                        <?php
                        echo dnttvn_custom_pagination($cong_dong_query);
                        ?>
                    </div>
                    <?php
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="news-item">
                        <p>Chưa có bài viết Cộng đồng nào. Vui lòng thêm bài viết từ trang quản trị WordPress.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sidebar-column">
        <div class="column-header mobile-toggle collapsed">Website liên kết</div>
        <div class="column-content mobile-collapsed">
            <h4 style="margin-bottom: 15px; color: #333;">Danh sách Doanh nghiệp</h4>
            <ul class="linked-websites">
                <?php
                // Get page link for "Danh sách Doanh nghiệp"
                $page_doanh_nghiep = get_page_by_path('danh-sach-doanh-nghiep');
                if (!$page_doanh_nghiep) {
                    $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
                }
                if ($page_doanh_nghiep) {
                    $doanh_nghiep_page_url = get_permalink($page_doanh_nghiep->ID);
                } else {
                    $doanh_nghiep_page_url = home_url('/danh-sach-doanh-nghiep/');
                }
                ?>
                <li><a href="<?php echo esc_url($doanh_nghiep_page_url); ?>">Danh sách Doanh nghiệp</a></li>
                <?php
                $doanh_nghiep_args = array(
                    'post_type'      => 'doanh_nghiep',
                    'posts_per_page' => 4,
                    'post_status'    => 'publish',
                );
                $doanh_nghiep_query = new WP_Query($doanh_nghiep_args);

                // Detail page for doanh_nghiep
                $detail_page      = get_page_by_path('trang-doanh-nghiep-chi-tiet');
                $detail_page_base = $detail_page ? get_permalink($detail_page->ID) : home_url('/trang-doanh-nghiep-chi-tiet/');

                if ($doanh_nghiep_query->have_posts()) :
                    while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                        $detail_url = add_query_arg('post_id', get_the_ID(), $detail_page_base);
                        ?>
                        <li><a href="<?php echo esc_url($detail_url); ?>"><?php the_title(); ?></a></li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
            <h4 style="margin-top: 25px; margin-bottom: 15px; color: #333;">Cộng đồng</h4>
            <ul class="linked-websites">
                <?php
                $community_links = dnttvn_get_community_links();
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
