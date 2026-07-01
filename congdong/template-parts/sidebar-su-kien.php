<?php
/**
 * Template Part: Sự kiện sidebar block (cột phải)
 * Include vào right sidebar: get_template_part('template-parts/sidebar-su-kien');
 */
?>
<div class="column-header mobile-toggle collapsed">Sự kiện</div>
<div class="column-content mobile-collapsed">
    <?php
    $sk_args = function_exists('dnttvn_su_kien_active_query_args') ? dnttvn_su_kien_active_query_args() : array('post_type' => 'su_kien', 'post_status' => 'publish', 'posts_per_page' => 10);
    $sk_query = new WP_Query($sk_args);
    if ($sk_query->have_posts()) :
        while ($sk_query->have_posts()) : $sk_query->the_post();
            $sk_thumb     = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            $sk_ngay_mo   = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
            $sk_ngay_kt   = get_post_meta(get_the_ID(), '_su_kien_ngay_ket_thuc', true);
            $sk_video     = get_post_meta(get_the_ID(), '_su_kien_video_url', true);
            $sk_gallery   = get_post_meta(get_the_ID(), '_su_kien_gallery', true);
            $sk_gal_ids   = $sk_gallery ? array_filter(array_map('absint', explode(',', $sk_gallery))) : array();
            ?>
            <div class="su-kien-right-card">
                <a href="<?php the_permalink(); ?>" class="su-kien-right-title"><?php the_title(); ?></a>
                <?php if ($sk_ngay_mo) : ?>
                    <div class="su-kien-right-date">
                        <?php echo esc_html(date_i18n('d/m/Y', strtotime($sk_ngay_mo))); ?>
                        <?php if ($sk_ngay_kt) : ?> &ndash; <?php echo esc_html(date_i18n('d/m/Y', strtotime($sk_ngay_kt))); ?><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($sk_video) :
                    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $sk_video, $yt)) : ?>
                        <div class="su-kien-right-video"><iframe src="https://www.youtube.com/embed/<?php echo esc_attr($yt[1]); ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo esc_attr($yt[1]); ?>" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>
                    <?php elseif (preg_match('/vimeo\.com\/(\d+)/', $sk_video, $vm)) : ?>
                        <div class="su-kien-right-video"><iframe src="https://player.vimeo.com/video/<?php echo esc_attr($vm[1]); ?>?autoplay=1&muted=1" allow="autoplay" allowfullscreen></iframe></div>
                    <?php else : ?>
                        <div class="su-kien-right-video"><video autoplay muted loop playsinline><source src="<?php echo esc_url($sk_video); ?>"></video></div>
                    <?php endif;
                endif; ?>

                <?php if ($sk_thumb && !$sk_video) : ?>
                    <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($sk_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="su-kien-right-img" loading="lazy" /></a>
                <?php endif; ?>

                <?php if (!empty($sk_gal_ids)) : ?>
                    <div class="su-kien-right-gallery">
                        <?php foreach (array_slice($sk_gal_ids, 0, 4) as $gimg_id) :
                            $gimg_url = wp_get_attachment_image_url($gimg_id, 'thumbnail');
                            if ($gimg_url) : ?>
                                <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($gimg_url); ?>" alt="" class="su-kien-right-gallery-thumb" loading="lazy" /></a>
                            <?php endif;
                        endforeach;
                        if (count($sk_gal_ids) > 4) : ?>
                            <a href="<?php the_permalink(); ?>" class="su-kien-right-gallery-more">+<?php echo count($sk_gal_ids) - 4; ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile;
        wp_reset_postdata();
    else : ?>
        <p style="padding:10px 15px; color:#888; font-size:13px;">Chưa có sự kiện nào đang diễn ra.</p>
    <?php endif; ?>
</div>
