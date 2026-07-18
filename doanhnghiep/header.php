<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php
    $dn_hero_file = get_template_directory() . '/assets/images/hero-poster.jpg';
    if (!is_readable($dn_hero_file)) {
        $dn_hero_file = get_template_directory() . '/assets/images/hero-poster.png';
    }
    $dn_hero_poster = '';
    if (is_readable($dn_hero_file)) {
        $dn_hero_poster = get_template_directory_uri() . '/assets/images/' . rawurlencode(basename($dn_hero_file));
        $dn_hero_poster .= '?v=' . (string) filemtime($dn_hero_file);
    }

    if (function_exists('dnttvn_get_meta_description')) {
        $meta_desc = dnttvn_get_meta_description();
        if (!empty($meta_desc)) :
            ?>
            <meta name="description" content="<?php echo esc_attr($meta_desc); ?>">
            <?php
        endif;
    }

    $og_title = wp_get_document_title();
    $og_url   = (is_singular()) ? get_permalink() : home_url(add_query_arg(array(), $wp->request ?? ''));
    $og_type  = is_singular() ? 'article' : 'website';
    $og_desc  = isset($meta_desc) ? $meta_desc : get_bloginfo('description');
    $og_image = function_exists('dnttvn_get_og_image') ? dnttvn_get_og_image() : '';
    if (empty($og_image) && $dn_hero_poster) {
        $og_image = $dn_hero_poster;
    }
    ?>
    <meta property="og:title" content="<?php echo esc_attr($og_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($og_desc); ?>">
    <meta property="og:type" content="<?php echo esc_attr($og_type); ?>">
    <meta property="og:url" content="<?php echo esc_url($og_url); ?>">
    <?php if (!empty($og_image)) : ?>
        <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($og_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($og_desc); ?>">
    <?php if (!empty($og_image)) : ?>
        <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    <?php endif; ?>
    <?php if ($dn_hero_poster) : ?>
    <link rel="preload" as="image" href="<?php echo esc_url($dn_hero_poster); ?>" fetchpriority="high">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class('dn-theme-redesign'); ?>>

<header class="dn-site-header" role="banner">
    <div class="dn-site-header__inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="dn-brand" title="<?php echo esc_attr(get_bloginfo('name')); ?> — Về trang chủ">
            <span class="dn-brand__logo">
                <?php
                $header_logo_id = get_theme_mod('dnttvn_header_logo', '');
                $logo_output    = '';
                if ($header_logo_id) {
                    $logo_output = wp_get_attachment_image($header_logo_id, 'full', false, array(
                        'alt'   => get_bloginfo('name'),
                        'class' => 'dn-brand__img',
                    ));
                }
                if ($logo_output) {
                    echo $logo_output;
                } elseif (has_custom_logo()) {
                    $logo_id = get_theme_mod('custom_logo');
                    if ($logo_id) {
                        echo wp_get_attachment_image($logo_id, 'full', false, array(
                            'alt'   => get_bloginfo('name'),
                            'class' => 'dn-brand__img',
                        ));
                    }
                } else {
                    $default_logo = get_template_directory_uri() . '/assets/images/logo.png';
                    echo '<img class="dn-brand__img" src="' . esc_url($default_logo) . '" alt="' . esc_attr(get_bloginfo('name')) . '" width="46" height="46">';
                }
                ?>
            </span>
            <span class="dn-brand__text">
                <span class="dn-brand__line">CỘNG ĐỒNG DOANH NGHIỆP</span>
                <span class="dn-brand__line dn-brand__line--accent">TRÍ TUỆ VIỆT NAM</span>
            </span>
        </a>

        <nav class="dn-main-nav" id="dnMainNav" aria-label="Menu chính">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'dn-nav-menu',
                'container'      => false,
                'fallback_cb'    => 'dnttvn_default_menu',
                'menu_id'        => 'mainMenu',
            ));
            ?>
        </nav>

        <div class="dn-hero-actions">
            <div class="dn-social-box">
                <span class="dn-social-box__label">KẾT NỐI VỚI CHÚNG TÔI</span>
                <div class="dn-social-box__icons">
                    <?php
                    $facebook_url = get_theme_mod('dnttvn_social_facebook_url', 'https://www.facebook.com/profile.php?id=61587839805007');
                    if (!$facebook_url) {
                        $facebook_url = 'https://www.facebook.com/profile.php?id=61587839805007';
                    }
                    ?>
                    <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="dn-social-icon dn-social-icon--fb" title="Facebook" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="dn-social-icon dn-social-icon--tt js-dang-cap-nhat" title="TikTok" aria-label="TikTok" data-alert="Đang cập nhật">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                    </a>
                    <a href="#" class="dn-social-icon dn-social-icon--zalo js-dang-cap-nhat" title="Zalo" aria-label="Zalo" data-alert="Đang cập nhật">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12.05 2C6.58 2 2.13 6.13 2.13 11.24c0 2.96 1.55 5.58 3.95 7.29L5.2 22l3.93-2.07c.97.27 2 .42 3.07.42h.09c5.47 0 9.91-4.13 9.91-9.24C22.2 6.13 17.51 2 12.05 2zm4.86 13.28H8.45l4.92-5.98H8.6V8.02h8.2l-4.96 5.98h5.07v1.28z"/></svg>
                    </a>
                    <a href="#" class="dn-social-icon dn-social-icon--yt js-dang-cap-nhat" title="YouTube" aria-label="YouTube" data-alert="Đang cập nhật">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            <button class="dn-hamburger" id="dnHamburger" type="button" aria-label="Mở menu" aria-expanded="false" aria-controls="dnMainNav">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<section class="dn-site-hero" aria-label="Banner">
    <?php if ($dn_hero_poster) : ?>
    <div class="dn-site-hero__media" aria-hidden="true">
        <img
            class="dn-site-hero__img"
            src="<?php echo esc_url($dn_hero_poster); ?>"
            alt=""
            width="2560"
            height="910"
            decoding="async"
            fetchpriority="high"
        >
    </div>
    <?php endif; ?>
    <div class="dn-site-hero__overlay">
        <div class="dn-site-hero__inner">
            <div class="dn-site-hero__copy">
                <h1 class="dn-site-hero__title">
                    <span class="dn-site-hero__title-top">CỘNG ĐỒNG</span>
                    <span class="dn-site-hero__title-em">DOANH NHÂN TRÍ TUỆ VIỆT NAM</span>
                </h1>
                <span class="dn-site-hero__rule" aria-hidden="true"></span>
                <p class="dn-site-hero__subtitle">“Nơi quy tụ Doanh nhân lựa chọn phát triển bằng trí tuệ và tinh thần phụng sự, hướng tới bền vững lâu dài”</p>
                <span class="dn-site-hero__flare" aria-hidden="true"></span>
            </div>
        </div>
    </div>
</section>
