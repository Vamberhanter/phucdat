<?php
/**
 * Trang chủ — layout hiện đại theo mockup (hero, feature bar, 2 cột).
 * Không có nút Đăng nhập.
 */
get_header();

// Ảnh nền hero
$desktop_url = get_template_directory_uri() . '/assets/images/hero-bg.png';
$mobile_url  = $desktop_url;

$dang_ky_url = home_url('/dang-ky/');
$dang_ky_page = get_page_by_path('dang-ky');
if ($dang_ky_page) {
    $dang_ky_url = get_permalink($dang_ky_page);
}

$home_ten  = isset($_GET['ten_doanh_nhan']) ? sanitize_text_field(wp_unslash($_GET['ten_doanh_nhan'])) : '';
$home_kv   = isset($_GET['khu_vuc']) ? sanitize_title(wp_unslash($_GET['khu_vuc'])) : '';
$home_ng   = isset($_GET['nganh_nghe']) ? sanitize_title(wp_unslash($_GET['nganh_nghe'])) : '';
$approved_count = function_exists('dnttvn_count_approved_dang_ky')
    ? dnttvn_count_approved_dang_ky()
    : 0;
// Hiển thị tối thiểu 50+ (mốc khởi điểm cộng đồng)
$member_count = max(50, (int) $approved_count);
$member_label = number_format_i18n($member_count);
?>

<section class="hero hero--home">
    <div class="hero__media">
        <picture>
            <?php if ($mobile_url && $mobile_url !== $desktop_url) : ?>
            <source media="(max-width: 767px)" srcset="<?php echo esc_url($mobile_url); ?>">
            <?php endif; ?>
            <img class="hero__img" src="<?php echo esc_url($desktop_url); ?>" alt="<?php esc_attr_e('Cộng đồng Doanh nhân Trí tuệ Việt Nam'); ?>" fetchpriority="high" decoding="async">
        </picture>
    </div>
    <div class="hero__overlay">
        <div class="hero__inner">
            <h1 class="hero__title">
                CỘNG ĐỒNG DOANH NHÂN<br>
                <span class="hero__title-em">TRÍ TUỆ VIỆT NAM</span>
            </h1>
            <p class="hero__subtitle">“Nơi kết nối giá trị – chia sẻ tri thức – hợp tác phát triển – phụng sự xã hội”</p>
            <div class="hero__actions">
                <a href="<?php echo esc_url($dang_ky_url); ?>" class="btn btn--hero-primary">
                    <svg class="btn-icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                    Đăng ký tham gia ngay
                </a>
                <a href="#" class="btn btn--hero-secondary js-dang-cap-nhat" data-alert="Đang cập nhật">
                    <span class="btn-play" aria-hidden="true">▶</span>
                    Xem video giới thiệu
                </a>
            </div>
        </div>
    </div>
</section>

<section class="feature-bar" aria-label="Giá trị cộng đồng">
    <div class="feature-bar__inner">
        <div class="feature-bar__items">
            <div class="feature-item">
                <span class="feature-item__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </span>
                <span class="feature-item__body">
                    <span class="feature-item__text">Kết nối giá trị</span>
                    <span class="feature-item__desc">Kết nối doanh nhân trên toàn quốc</span>
                </span>
            </div>
            <div class="feature-item">
                <span class="feature-item__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                </span>
                <span class="feature-item__body">
                    <span class="feature-item__text">Chia sẻ tri thức</span>
                    <span class="feature-item__desc">Học hỏi và chia sẻ kinh nghiệm thực tiễn</span>
                </span>
            </div>
            <div class="feature-item">
                <span class="feature-item__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                </span>
                <span class="feature-item__body">
                    <span class="feature-item__text">Hợp tác phát triển</span>
                    <span class="feature-item__desc">Cơ hội hợp tác và phát triển bền vững</span>
                </span>
            </div>
            <div class="feature-item">
                <span class="feature-item__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </span>
                <span class="feature-item__body">
                    <span class="feature-item__text">Phụng sự xã hội</span>
                    <span class="feature-item__desc">Hướng tới cộng đồng và giá trị nhân văn</span>
                </span>
            </div>
        </div>
        <div class="feature-bar__stat">
            <div class="feature-bar__stat-num"><?php echo esc_html($member_label); ?>+</div>
            <div class="feature-bar__stat-label">Doanh nhân đã tham gia</div>
        </div>
    </div>
</section>

<div class="home-layout">
    <div class="home-layout__main">
        <section class="home-card home-search">
            <h2 class="home-section-title">Tìm kiếm Doanh nhân</h2>
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="home-search__form">
                <div class="home-search__row">
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
                                    echo '<option value="' . esc_attr($term->slug) . '"' . selected($home_kv, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="home_nganh">Lĩnh vực</label>
                        <select name="nganh_nghe" id="home_nganh">
                            <option value="">Chọn lĩnh vực</option>
                            <?php
                            $ng_terms = get_terms(array('taxonomy' => 'nganh_nghe', 'hide_empty' => false));
                            if (!is_wp_error($ng_terms)) {
                                foreach ($ng_terms as $term) {
                                    echo '<option value="' . esc_attr($term->slug) . '"' . selected($home_ng, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group form-group--btn">
                        <label class="search-button-label">&nbsp;</label>
                        <button type="submit" class="search-button">Tìm kiếm</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="home-card home-featured">
            <h2 class="home-section-title">Doanh nhân tiêu biểu</h2>
            <div class="home-featured__grid">
                <?php
                $dn_args = array(
                    'post_type'      => 'doanh_nhan',
                    'post_status'    => 'publish',
                    'posts_per_page' => 6,
                    'orderby'        => 'menu_order date',
                    'order'          => 'ASC',
                );
                if ($home_ten !== '') {
                    $dn_args['s'] = $home_ten;
                }
                if ($home_kv !== '') {
                    $dn_args['tax_query'][] = array(
                        'taxonomy' => 'khu_vuc',
                        'field'    => 'slug',
                        'terms'    => $home_kv,
                    );
                }
                if ($home_ng !== '') {
                    $dn_args['tax_query'][] = array(
                        'taxonomy' => 'nganh_nghe',
                        'field'    => 'slug',
                        'terms'    => $home_ng,
                    );
                }
                if (!empty($dn_args['tax_query']) && count($dn_args['tax_query']) > 1) {
                    $dn_args['tax_query']['relation'] = 'AND';
                }
                $dn_query = new WP_Query($dn_args);
                if ($dn_query->have_posts()) :
                    while ($dn_query->have_posts()) :
                        $dn_query->the_post();
                        get_template_part('template-parts/card', 'doanh-nhan-featured');
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <p class="home-empty">Chưa có doanh nhân để hiển thị. <a href="<?php echo esc_url($dang_ky_url); ?>">Đăng ký tham gia</a>.</p>
                <?php endif; ?>
            </div>
            <div class="home-featured__more">
                <button type="button"
                    class="btn btn--outline js-dang-cap-nhat"
                    data-alert="Bạn phải trở thành thành viên của cộng đồng để xem thêm">
                    Xem thêm
                </button>
            </div>
        </section>

        <?php
        $sk_args = function_exists('dnttvn_su_kien_active_query_args')
            ? dnttvn_su_kien_active_query_args()
            : array('post_type' => 'su_kien', 'post_status' => 'publish', 'posts_per_page' => 3);
        $sk_args['posts_per_page'] = 3;
        $sk_query = new WP_Query($sk_args);
        if ($sk_query->have_posts()) :
            ?>
        <section class="home-card home-events">
            <h2 class="home-section-title">Sự kiện sắp diễn ra</h2>
            <div class="home-events__list">
                <?php
                while ($sk_query->have_posts()) :
                    $sk_query->the_post();
                    $sk_ngay = get_post_meta(get_the_ID(), '_su_kien_ngay_mo', true);
                    $thumb   = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="home-event-card">
                        <?php if ($thumb) : ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="" class="home-event-card__img" loading="lazy">
                        <?php else : ?>
                            <div class="home-event-card__img home-event-card__img--placeholder" aria-hidden="true"></div>
                        <?php endif; ?>
                        <div class="home-event-card__body">
                            <h3 class="home-event-card__title"><?php the_title(); ?></h3>
                            <?php if ($sk_ngay) : ?>
                                <time class="home-event-card__date"><?php echo esc_html(date_i18n('d/m/Y', strtotime($sk_ngay))); ?></time>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <aside class="home-layout__side">
        <section class="home-card home-news" id="tin-tuc-noi-bat">
            <h2 class="home-section-title">Tin tức nổi bật</h2>
            <ul class="home-news__list">
                <?php
                $news_q = new WP_Query(array(
                    'post_type'      => 'tin_tuc',
                    'post_status'    => 'publish',
                    'posts_per_page' => 5,
                    'meta_key'       => '_tin_tuc_noi_bat',
                    'meta_value'     => '1',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ));
                if (!$news_q->have_posts()) {
                    $news_q = new WP_Query(array(
                        'post_type'      => 'tin_tuc',
                        'post_status'    => 'publish',
                        'posts_per_page' => 5,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ));
                }
                if ($news_q->have_posts()) :
                    while ($news_q->have_posts()) :
                        $news_q->the_post();
                        $n_url   = function_exists('dnttvn_get_tin_tuc_detail_url') ? dnttvn_get_tin_tuc_detail_url(get_the_ID()) : get_permalink();
                        $n_thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                        ?>
                        <li class="home-news__item">
                            <a href="<?php echo esc_url($n_url); ?>" class="home-news__link">
                                <?php if ($n_thumb) : ?>
                                    <img src="<?php echo esc_url($n_thumb); ?>" alt="" class="home-news__thumb" loading="lazy">
                                <?php endif; ?>
                                <span class="home-news__meta">
                                    <span class="home-news__title"><?php the_title(); ?></span>
                                    <time class="home-news__date"><?php echo esc_html(get_the_date('d/m/Y')); ?></time>
                                    <span class="home-news__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 12, '…')); ?></span>
                                </span>
                            </a>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<li class="home-empty">Chưa có tin tức.</li>';
                endif;
                ?>
            </ul>
        </section>

        <section class="home-cta-card">
            <div class="home-cta-card__glow" aria-hidden="true"></div>
            <h2 class="home-cta-card__title">Tham gia cộng đồng</h2>
            <p class="home-cta-card__text">Kết nối doanh nhân trí tuệ, mở rộng cơ hội hợp tác và phát triển bền vững.</p>
            <a href="<?php echo esc_url($dang_ky_url); ?>" class="home-cta-card__btn">Đăng ký ngay</a>
        </section>
    </aside>
</div>

<?php get_footer(); ?>
