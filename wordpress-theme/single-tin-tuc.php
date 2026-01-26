<?php
/**
 * Single Post Template for Tin tức
 */

get_header();
?>

<main class="main-content">
    <div class="main-center" style="max-width: 800px; margin: 0 auto;">
        <article class="content-column">
            <?php while (have_posts()) : the_post(); ?>
                <div class="column-header"><?php the_title(); ?></div>
                <div class="column-content">
                    <p class="news-date"><?php echo get_the_date('d/m/Y'); ?></p>
                    <?php if (has_post_thumbnail()) : ?>
                        <div style="margin-bottom: 20px;">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="news-item">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </article>
    </div>
</main>

<?php get_footer(); ?>
