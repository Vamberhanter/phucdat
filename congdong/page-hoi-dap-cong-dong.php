<?php
/**
 * Template Name: Hỏi đáp Cộng đồng
 * Hiển thị danh sách bài Hỏi đáp (tiêu đề + hình + mô tả); bấm vào mở accordion nội dung bên trong.
 */

get_header();

$hoi_dap_args = array(
    'post_type'      => 'hoi_dap',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
);
$hoi_dap_query = new WP_Query($hoi_dap_args);
?>

<?php dnttvn_page_shell_start(get_the_title()); ?>
<h1 class="cd-detail__title"><?php the_title(); ?></h1>
                <?php if ($hoi_dap_query->have_posts()) : ?>
                    <div class="accordion-list accordion-list-hoi-dap">
                        <?php while ($hoi_dap_query->have_posts()) : $hoi_dap_query->the_post();
                            $post_id = get_the_ID();
                            $excerpt = has_excerpt() ? get_the_excerpt() : '';
                            $sections = function_exists('dnttvn_get_sections_array') ? dnttvn_get_sections_array($post_id, '_hoi_dap_sections') : array();
                        ?>
                            <article class="accordion-item" data-post-id="<?php echo esc_attr($post_id); ?>">
                                <div class="accordion-header" role="button" tabindex="0" aria-expanded="false" aria-controls="accordion-body-<?php echo esc_attr($post_id); ?>" id="accordion-header-<?php echo esc_attr($post_id); ?>">
                                    <div class="accordion-header-inner">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="accordion-thumbnail">
                                                <?php
                                                // Thumbnails trong danh sách Hỏi đáp: thêm loading="lazy" để giảm FCP/TBT
                                                the_post_thumbnail('medium', array(
                                                    'alt'     => esc_attr(get_the_title()),
                                                    'loading' => 'lazy',
                                                ));
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="accordion-summary">
                                            <h3 class="accordion-title"><?php the_title(); ?></h3>
                                            <?php if ($excerpt) : ?>
                                                <div class="accordion-excerpt"><?php echo esc_html($excerpt); ?></div>
                                            <?php endif; ?>
                                            <span class="accordion-toggle-icon" aria-hidden="true"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-body" id="accordion-body-<?php echo esc_attr($post_id); ?>" role="region" aria-labelledby="accordion-header-<?php echo esc_attr($post_id); ?>" hidden>
                                    <div class="accordion-body-inner">
                                        <?php if (!empty($sections)) : ?>
                                            <div class="accordion-sections">
                                                <?php foreach ($sections as $sec) :
                                                    $h = isset($sec['heading']) ? $sec['heading'] : '';
                                                    $c = isset($sec['content']) ? $sec['content'] : '';
                                                    if ($h === '' && $c === '') continue;
                                                ?>
                                                    <div class="accordion-section">
                                                        <?php if ($h !== '') : ?><h4 class="accordion-section-title"><?php echo esc_html($h); ?></h4><?php endif; ?>
                                                        <?php if ($c !== '') : ?><?php $c = apply_filters('dnttvn_display_content', $c); ?><div class="accordion-section-content entry-content"><?php echo wp_kses_post(wpautop($c)); ?></div><?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else : ?>
                                            <p class="accordion-no-sections">Chưa có mục nội dung.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                       <?php endif; ?>
<?php dnttvn_page_shell_end(); ?>

<?php get_footer(); ?>
