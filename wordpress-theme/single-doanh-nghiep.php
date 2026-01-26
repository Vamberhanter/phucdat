<?php
/**
 * Single Post Template for Doanh nghiệp
 */

get_header();
?>

<main class="main-content">
    <div class="main-center" style="max-width: 1000px; margin: 0 auto;">
        <?php while (have_posts()) : the_post(); ?>
            <div class="business-card">
                <div class="business-card-left">
                    <div class="business-card-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php else : ?>
                            <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=<?php echo esc_attr(get_the_title()); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="business-card-info-section">
                        <h4><?php the_title(); ?></h4>
                        <?php
                        $nganh_hang = get_post_meta(get_the_ID(), '_nganh_hang', true);
                        $khu_vuc = get_post_meta(get_the_ID(), '_khu_vuc', true);
                        ?>
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
                    </div>
                </div>
                <div class="business-card-content">
                    <?php
                    $hinh_anh_phu = get_post_meta(get_the_ID(), '_hinh_anh_phu', true);
                    if ($hinh_anh_phu) :
                        ?>
                        <div class="business-card-small-image">
                            <?php
                            $image_id = is_numeric($hinh_anh_phu) ? $hinh_anh_phu : attachment_url_to_postid($hinh_anh_phu);
                            if ($image_id) {
                                echo wp_get_attachment_image($image_id, 'large');
                            } else {
                                echo '<img src="' . esc_url($hinh_anh_phu) . '" alt="Hình ảnh phụ">';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="business-card-description">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
