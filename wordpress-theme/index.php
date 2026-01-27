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
                <li><a href="#">Điều lệ tổ chức hoạt động</a></li>
                <li><a href="#">Danh sách thành viên sáng lập</a></li>
                <li><a href="#">Cấu trúc Cộng đồng</a></li>
                <li><a href="#">Danh sách Lãnh đạo điều hành</a></li>
                <li class="highlight-item">
                    <a href="#">Tìm hiểu trở thành thành viên mới</a>
                </li>
                <li><a href="#">Giá trị nhận được của thành viên</a></li>
                <li><a href="#">Quy trình gia nhập Cộng đồng</a></li>
                <li><a href="#">Hỏi đáp về Cộng đồng</a></li>
            </ul>
        </div>
    </div>

    <!-- Center Content -->
    <div class="main-center">
        <div class="content-column">
            <div class="column-header">Tin tức Cộng đồng</div>
            <div class="column-content">
                <?php
                $args = array(
                    'post_type'      => 'tin_tuc',
                    'posts_per_page' => 10,
                    'post_status'    => 'publish',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );
                $tin_tuc_query = new WP_Query($args);

                if ($tin_tuc_query->have_posts()) :
                    while ($tin_tuc_query->have_posts()) : $tin_tuc_query->the_post();
                        ?>
                        <div class="news-item">
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            <p class="news-date"><?php echo get_the_date('d/m/Y'); ?></p>
                            <?php if (has_excerpt()) : ?>
                                <p><?php the_excerpt(); ?></p>
                            <?php else : ?>
                                <p><?php echo wp_trim_words(get_the_content(), 50); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="news-item">
                        <p>Chưa có tin tức nào. Vui lòng thêm tin tức từ trang quản trị WordPress.</p>
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
                $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
                if ($page_doanh_nghiep) {
                    $doanh_nghiep_page_url = get_permalink($page_doanh_nghiep->ID);
                } else {
                    // Fallback: try to get by slug or use home_url
                    $doanh_nghiep_page_url = home_url('/page-doanh-nghiep/');
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
                if ($doanh_nghiep_query->have_posts()) :
                    while ($doanh_nghiep_query->have_posts()) : $doanh_nghiep_query->the_post();
                        ?>
                        <li><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </ul>
            <h4 style="margin-top: 25px; margin-bottom: 15px; color: #333;">Cộng đồng</h4>
            <ul class="linked-websites">
                <li><a href="#">Cộng đồng Doanh nhân Trẻ</a></li>
                <li><a href="#">Cộng đồng Khởi nghiệp</a></li>
                <li><a href="#">Cộng đồng Đầu tư</a></li>
            </ul>
        </div>
    </div>
</main>

<?php get_footer(); ?>
