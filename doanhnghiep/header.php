<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php
    // SEO meta description
    if (function_exists('dnttvn_get_meta_description')) {
        $meta_desc = dnttvn_get_meta_description();
        if (!empty($meta_desc)) :
            ?>
            <meta name="description" content="<?php echo esc_attr($meta_desc); ?>">
            <?php
        endif;
    }

    // Open Graph & Twitter Cards
    $og_title = wp_get_document_title();
    $og_url   = (is_singular()) ? get_permalink() : home_url(add_query_arg(array(), $wp->request ?? ''));
    $og_type  = is_singular() ? 'article' : 'website';
    $og_desc  = isset($meta_desc) ? $meta_desc : get_bloginfo('description');
    $og_image = function_exists('dnttvn_get_og_image') ? dnttvn_get_og_image() : '';
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

    <?php
    // Placeholder Google Analytics (chưa có ID, chỉ là khung sẵn)
    /*
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    */
    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <!-- Header Section - Cấu trúc: Tiêu đề (trên) | LOGO | BANNER | Kênh liên kết (hàng dưới). Không có tên miền. (đồng bộ cộng đồng) -->
    <header class="site-header site-header-doanhnghiep">
        <div class="header-top-container">
            <div class="header-title-block">
                <h1 class="site-title">CỘNG ĐỒNG DOANH NGHIỆP TRÍ TUỆ VIỆT NAM</h1>
            </div>
            <div class="header-main-row">
                <div class="logo-section">
                    <div class="logo-show-top-half">
                    <?php
                    $logo_url = esc_url(home_url('/'));
                    $header_logo_id = get_theme_mod('dnttvn_header_logo', '');
                    $logo_output = '';
                    if ($header_logo_id) {
                        $logo_output = wp_get_attachment_image($header_logo_id, 'full', false, array('alt' => get_bloginfo('name')));
                    }
                    if ($logo_output) : ?>
                        <a href="<?php echo $logo_url; ?>" rel="home" class="logo-link custom-logo-link" title="<?php echo esc_attr(get_bloginfo('name')); ?> - Về trang chủ"><?php echo $logo_output; ?></a>
                    <?php elseif (has_custom_logo()) :
                        $logo_id = get_theme_mod('custom_logo');
                        $logo_img = $logo_id ? wp_get_attachment_image($logo_id, 'full', false, array('alt' => get_bloginfo('name'))) : '';
                        if ($logo_img) : ?>
                        <a href="<?php echo $logo_url; ?>" rel="home" class="logo-link custom-logo-link" title="<?php echo esc_attr(get_bloginfo('name')); ?> - Về trang chủ"><?php echo $logo_img; ?></a>
                        <?php else :
                            the_custom_logo();
                        endif;
                    else : ?>
                        <a href="<?php echo $logo_url; ?>" rel="home" class="logo-link" title="<?php echo esc_attr(get_bloginfo('name')); ?> - Về trang chủ">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/Logo CDTTVN 1.png'); ?>" alt="Logo <?php bloginfo('name'); ?>">
                        </a>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="banner-header-section">
                    <div class="banner-header">
                        <?php
                        $banner_order = get_theme_mod('dnttvn_banner_order', '1,2,3,4,5');
                        $order_array  = array_map('trim', explode(',', $banner_order));
                        $first_banner_id = '';

                        foreach ($order_array as $id_num) {
                            $tmp_id = get_theme_mod('dnttvn_banner_' . $id_num, '');
                            if ($tmp_id) {
                                $first_banner_id = $tmp_id;
                                break;
                            }
                        }

                        if (!$first_banner_id) {
                            for ($i = 1; $i <= 5; $i++) {
                                $first_banner_id = get_theme_mod('dnttvn_banner_' . $i, '');
                                if ($first_banner_id) break;
                            }
                        }

                        $banner_mobile_id = get_theme_mod('dnttvn_banner_mobile', '');
                        $default_desktop_url = get_template_directory_uri() . '/BANNER HEADER  WEB 1920 X 400.png';
                        $default_mobile_url  = get_template_directory_uri() . '/BANNER HEADER  WEB 360 X 230.png';

                        $desktop_url = $first_banner_id ? wp_get_attachment_url($first_banner_id) : $default_desktop_url;
                        $desktop_alt = $first_banner_id ? get_post_meta($first_banner_id, '_wp_attachment_image_alt', true) : 'Banner Header';

                        if ($banner_mobile_id) {
                            $mobile_url = wp_get_attachment_url($banner_mobile_id);
                            $mobile_alt = get_post_meta($banner_mobile_id, '_wp_attachment_image_alt', true);
                            if (!$mobile_url) {
                                $mobile_url = $default_mobile_url;
                                $mobile_alt = 'Banner Header Mobile';
                            }
                        } else {
                            $mobile_url = $default_mobile_url;
                            $mobile_alt = 'Banner Header Mobile';
                        }

                        if (!$desktop_url) $desktop_url = $default_desktop_url;
                        if (!$mobile_url)  $mobile_url  = $default_mobile_url;

                        echo '<img class="banner-img-desktop" src="' . esc_url($desktop_url) . '" alt="' . esc_attr($desktop_alt) . '" style="width: 100%; height: 100%; object-fit: contain; object-position: center;">';
                        echo '<img class="banner-img-mobile" src="' . esc_url($mobile_url) . '" alt="' . esc_attr($mobile_alt) . '" style="width: 100%; height: 100%; object-fit: contain; object-position: center;">';
                        ?>
                    </div>
                </div>
                <div class="social-links-menu">
                    <h3>KÊNH LIÊN KẾT</h3>
                    <ul class="social-list">
                        <?php
                        $facebook_url = get_theme_mod('dnttvn_social_facebook_url', '');
                        $tiktok_url   = get_theme_mod('dnttvn_social_tiktok_url', '');
                        $zalo_url     = get_theme_mod('dnttvn_social_zalo_url', '');
                        $youtube_url  = get_theme_mod('dnttvn_social_youtube_url', '');
                        ?>
                        <?php if ($facebook_url) : ?>
                        <li><a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" title="Facebook"><svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg><span>Facebook</span></a></li>
                        <?php endif; ?>
                        <?php if ($tiktok_url) : ?>
                        <li><a href="<?php echo esc_url($tiktok_url); ?>" target="_blank" rel="noopener noreferrer" title="TikTok"><svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg><span>TikTok</span></a></li>
                        <?php endif; ?>
                        <?php if ($zalo_url) : ?>
                        <li><a href="<?php echo esc_url($zalo_url); ?>" target="_blank" rel="noopener noreferrer" title="Zalo"><svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3.5 6L12 10.5 8.5 8l1.5 4.5L7 15l4.5-1.5L12 18l.5-4.5L17 15l-3-2.5L15.5 8z"/></svg><span>Zalo</span></a></li>
                        <?php endif; ?>
                        <?php if ($youtube_url) : ?>
                        <li><a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer" title="YouTube"><svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg><span>YouTube</span></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="main-navigation">
        <div class="container">
            <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'menu',
                'container'      => false,
                'fallback_cb'    => 'dnttvn_default_menu',
            ));
            ?>
        </div>
    </nav>
