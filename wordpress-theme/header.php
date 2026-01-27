<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <!-- Header Section -->
    <header class="site-header">
        <div class="header-top-container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <div class="header-top">
                <div class="logo-section">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <img src="https://cvgbenhviendoanhnghiep.vn/wp-content/uploads/2023/08/Logo_cvgbenhviendoanhnghiep_benh-vien-doanh-nghiep-cvg-1-768x237.png" alt="Logo <?php bloginfo('name'); ?>">
                    <?php endif; ?>
                </div>
                <div class="title-section">
                    <h1 class="site-title"><?php bloginfo('name'); ?></h1>
                    <p class="site-domain">(Tên miền: <?php echo parse_url(home_url(), PHP_URL_HOST); ?>)</p>
                </div>
            </div>
            <div class="social-links-menu">
                <h3>KÊNH LIÊN KẾT</h3>
                <ul class="social-list">
                    <li>
                        <a href="#" target="_blank" title="Facebook">
                            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Facebook</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" target="_blank" title="TikTok">
                            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                            </svg>
                            <span>Tiktok</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" target="_blank" title="Zalo">
                            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3.5 6L12 10.5 8.5 8l1.5 4.5L7 15l4.5-1.5L12 18l.5-4.5L17 15l-3-2.5L15.5 8z"/>
                            </svg>
                            <span>Zalo</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" target="_blank" title="YouTube">
                            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            <span>Youtube</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="banner-section">
            <div class="banner-slider">
                <?php
                $banners = array();
                for ($i = 1; $i <= 5; $i++) {
                    $banner_id = get_theme_mod('dnttvn_banner_' . $i, '');
                    if ($banner_id) {
                        $banners[] = $banner_id;
                    }
                }
                
                if (!empty($banners)) :
                    $first = true;
                    foreach ($banners as $banner_id) :
                        $banner_url = wp_get_attachment_image_url($banner_id, 'full');
                        $banner_alt = get_post_meta($banner_id, '_wp_attachment_image_alt', true);
                        if ($banner_url) :
                            ?>
                            <div class="banner-slide <?php echo $first ? 'active' : ''; ?>">
                                <img src="<?php echo esc_url($banner_url); ?>" alt="<?php echo esc_attr($banner_alt); ?>" style="width: 100%; height: auto; display: block;">
                            </div>
                            <?php
                            $first = false;
                        endif;
                    endforeach;
                else :
                    // Fallback to default banners
                    ?>
                    <div class="banner-slide active">BANNER 1</div>
                    <div class="banner-slide">BANNER 2</div>
                    <div class="banner-slide">BANNER 3</div>
                    <div class="banner-slide">BANNER 4</div>
                    <div class="banner-slide">BANNER 5</div>
                    <?php
                endif;
                ?>
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
