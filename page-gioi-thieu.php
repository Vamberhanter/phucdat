<?php
/**
 * Template Name: Trang Giới thiệu
 * 
 * Template cho trang giới thiệu của Cộng đồng Doanh nhân Trí tuệ Việt Nam
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Header Section */
        .site-header {
            background: #06202e;
            padding: 20px 0 40px;
            border-bottom: none;
            position: relative;
        }

        .header-top {
            text-align: center;
            margin-bottom: 20px;
        }

        .site-title {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }

        .site-domain {
            color: #fff;
            font-size: 14px;
            opacity: 0.9;
        }

        .header-content {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 20px;
        }

        .social-links {
            flex: 0 0 180px;
            order: 1;
        }

        .logo-section {
            flex: 0 0 200px;
            order: 2;
        }

        .logo-section img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .banner-section {
            flex: 1;
            height: 200px;
            position: relative;
            border-radius: 5px;
            overflow: hidden;
            order: 3;
        }

        .banner-slider {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .banner-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .banner-slide:nth-child(1) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .banner-slide:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .banner-slide:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .banner-slide:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .banner-slide:nth-child(5) {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .banner-slide.active {
            opacity: 1;
            z-index: 1;
        }

        .social-links h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #fff;
            text-align: left;
        }

        .social-list {
            list-style: none;
        }

        .social-list li {
            margin-bottom: 10px;
        }

        .social-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .social-list a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            transition: all 0.3s;
            width: 100%;
        }

        .social-list a:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: scale(1.05);
        }

        .social-icon {
            width: 24px;
            height: 24px;
            margin-right: 8px;
            fill: currentColor;
        }

        .social-list a span {
            font-size: 14px;
        }

        /* Navigation Menu */
        .main-navigation {
            background: white;
            padding: 20px 0;
            margin: -30px auto 0;
            max-width: 1200px;
            width: calc(100% - 40px);
            border-radius: 15px 15px 0 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            z-index: 100;
        }

        .main-navigation .container {
            text-align: center;
            max-width: 100%;
            padding: 0 20px;
        }

        .main-navigation .menu {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 0;
            padding: 0;
        }

        .main-navigation .menu li a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            display: block;
            transition: color 0.3s;
            font-size: 15px;
        }

        .main-navigation .menu li a:hover {
            color: #ff9800;
        }

        /* Main Content Area */
        .main-content {
            padding: 0;
            display: flex;
            min-height: calc(100vh - 400px);
        }

        .sidebar-column {
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 10;
        }

        .sidebar-column:first-child {
            width: 250px;
        }

        .sidebar-column:last-child {
            width: 300px;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }

        .main-center {
            flex: 1;
            padding: 40px;
            background: #f5f5f5;
        }

        .content-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 30px;
        }

        .content-column {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .business-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            min-height: 400px;
            display: flex;
            gap: 20px;
            border: 1px solid #e0e0e0;
        }

        .business-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .business-card-left {
            flex: 0 0 200px;
            width: 200px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .business-card-image {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .business-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .business-card-info-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .business-card-info-section h4 {
            font-size: 18px;
            color: #06202e;
            margin: 0;
            font-weight: bold;
            line-height: 1.4;
        }

        .business-card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .business-card-small-image {
            width: 100%;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
        }

        .business-card-small-image-slider {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .business-card-small-image-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .business-card-small-image-slide.active {
            opacity: 1;
            z-index: 1;
        }

        .business-card-small-image-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .business-card-description {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .business-card-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .business-card-info-icon {
            width: 16px;
            height: 16px;
            fill: #ff9800;
            flex-shrink: 0;
        }

        .business-card-gallery {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .business-card-gallery img {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .business-card-gallery img:hover {
            transform: scale(1.1);
        }

        .column-header {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            position: relative;
        }

        .column-header.mobile-toggle {
            cursor: pointer;
            user-select: none;
        }

        .column-header.mobile-toggle::before {
            content: "▶";
            position: absolute;
            left: 20px;
            transition: transform 0.3s;
            font-size: 14px;
        }

        .column-header.mobile-toggle.collapsed::before {
            transform: rotate(-90deg);
        }

        .column-content {
            padding: 25px;
        }

        .column-content.mobile-collapsed {
            display: none;
        }

        /* Left Column - About Community */
        .about-list {
            list-style: none;
        }

        .about-list li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            padding-left: 25px;
            position: relative;
        }

        .about-list li:before {
            content: "▶";
            position: absolute;
            left: 0;
            color: #ff9800;
        }

        .about-list li:last-child {
            border-bottom: none;
        }

        .about-list li a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }

        .about-list li a:hover {
            color: #667eea;
        }

        .highlight-item {
            background: #fff3e0;
            padding: 15px;
            border-left: 4px solid #ff9800;
            margin: 10px 0;
            font-weight: 600;
        }

        /* Top Search Section */
        .top-search-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .top-search-section h3 {
            font-size: 20px;
            color: #06202e;
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        /* Middle Column - Search and Filters */
        .search-section {
            margin-bottom: 30px;
        }

        .search-block {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }

        .search-block h4 {
            font-size: 16px;
            font-weight: bold;
            color: #06202e;
            margin-bottom: 15px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff9800;
        }

        .search-button {
            padding: 12px 30px;
            background: #ff9800;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            white-space: nowrap;
            height: fit-content;
        }

        .search-button:hover {
            background: #f57c00;
        }

        /* Right Column - Advertising by Industry */
        .ad-section {
            margin-bottom: 20px;
        }

        .column-content {
            padding: 15px;
        }

        .ad-block {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
        }

        .ad-block.vvip {
            border-color: #d4af37;
            background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
        }

        .ad-block.vip {
            border-color: #c0c0c0;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        }

        .ad-block.standard {
            border-color: #cd7f32;
            background: linear-gradient(135deg, #faf5f0 0%, #f0e6d2 100%);
        }

        .ad-block h4 {
            font-size: 14px;
            font-weight: bold;
            color: #06202e;
            margin-bottom: 8px;
            text-align: center;
            line-height: 1.3;
        }

        .ad-block .ad-type {
            font-size: 13px;
            color: #666;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .ad-block .ad-description {
            font-size: 12px;
            color: #555;
            text-align: center;
            line-height: 1.4;
            margin-bottom: 10px;
        }

        .ad-placeholder {
            width: 100%;
            height: 120px;
            background: #e0e0e0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
        }

        .ad-block-mobile {
            display: none;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
        }

        .ad-block-mobile.vvip {
            border-color: #d4af37;
            background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
        }

        .ad-block-mobile.vip {
            border-color: #c0c0c0;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        }

        .ad-block-mobile.standard {
            border-color: #cd7f32;
            background: linear-gradient(135deg, #faf5f0 0%, #f0e6d2 100%);
        }

        .ad-block-mobile h4 {
            font-size: 14px;
            font-weight: bold;
            color: #06202e;
            margin-bottom: 8px;
            text-align: center;
            line-height: 1.3;
        }

        .ad-block-mobile .ad-type {
            font-size: 13px;
            color: #666;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .ad-block-mobile .ad-description {
            font-size: 12px;
            color: #555;
            text-align: center;
            line-height: 1.4;
            margin-bottom: 10px;
        }

        /* Footer */
        .site-footer {
            background: #333;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        .footer-content {
            text-align: center;
        }

        .footer-content p {
            margin-bottom: 10px;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .main-content {
                flex-direction: column;
            }

            .sidebar-column {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar-column:first-child .column-header {
                text-align: left;
                padding-left: 50px;
            }

            .sidebar-column:first-child .column-header.mobile-toggle::before {
                display: block;
            }

            .sidebar-column:last-child {
                display: none;
            }

            .main-center {
                order: 1;
            }

            .ad-block-mobile {
                display: block;
                margin-bottom: 20px;
            }

            .column-header.mobile-toggle::before {
                display: block;
            }

            .content-columns {
                grid-template-columns: 1fr;
            }

            .business-card {
                min-height: auto;
                flex-direction: column;
            }

            .business-card-left {
                width: 100%;
                flex: none;
            }

            .business-card-image {
                width: 100%;
                height: 200px;
            }

            .business-card-content {
                width: 100%;
            }

            .business-card-small-image {
                width: 100%;
                height: 200px;
            }

            .content-columns {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .search-form-row {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
            }

            .logo-section,
            .banner-section,
            .social-links {
                width: 100%;
            }

            .main-navigation {
                width: calc(100% - 20px);
                margin: -20px auto 0;
            }

            .main-navigation .menu {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
    <script>
        // Banner Carousel Auto-play
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.banner-slide');
            if (slides.length > 0) {
                let currentSlide = 0;

                function showSlide(index) {
                    slides.forEach((slide, i) => {
                        slide.classList.remove('active');
                        if (i === index) {
                            slide.classList.add('active');
                        }
                    });
                }

                function nextSlide() {
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }

                // Auto-play every 5 seconds
                setInterval(nextSlide, 5000);
            }

            // Small Image Carousel Auto-play for each business card
            const smallImageSliders = document.querySelectorAll('.business-card-small-image-slider');
            smallImageSliders.forEach(function(slider) {
                const slides = slider.querySelectorAll('.business-card-small-image-slide');
                if (slides.length > 0) {
                    let currentSlide = 0;

                    function showSlide(index) {
                        slides.forEach((slide, i) => {
                            slide.classList.remove('active');
                            if (i === index) {
                                slide.classList.add('active');
                            }
                        });
                    }

                    function nextSlide() {
                        currentSlide = (currentSlide + 1) % slides.length;
                        showSlide(currentSlide);
                    }

                    // Auto-play every 5 seconds
                    setInterval(nextSlide, 5000);
                }
            });

            // Mobile Accordion for Left Sidebar
            const mobileToggle = document.querySelector('.column-header.mobile-toggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    this.classList.toggle('collapsed');
                    if (content) {
                        content.classList.toggle('mobile-collapsed');
                    }
                });
            }
        });

        // Toggle Menu Function
        function toggleMenu() {
            const menu = document.getElementById('mainMenu');
            if (menu) {
                menu.classList.toggle('active');
            }
        }
    </script>
</head>
<body <?php body_class(); ?>>
    <!-- Header Section -->
    <header class="site-header">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <div class="header-top">
                <h1 class="site-title">CỘNG ĐỒNG DOANH NHÂN TRÍ TUỆ VIỆT NAM</h1>
                <p class="site-domain">(Tên miền: congdongdoanhnhantrituevietnam.vn)</p>
            </div>
            <div class="header-content">
                <div class="social-links">
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
                <div class="logo-section">
                    <img src="https://cvgbenhviendoanhnghiep.vn/wp-content/uploads/2023/08/Logo_cvgbenhviendoanhnghiep_benh-vien-doanh-nghiep-cvg-1-768x237.png" alt="Logo Cộng đồng Doanh nhân Trí tuệ Việt Nam">
                </div>
                <div class="banner-section">
                    <div class="banner-slider">
                        <div class="banner-slide active">BANNER 1</div>
                        <div class="banner-slide">BANNER 2</div>
                        <div class="banner-slide">BANNER 3</div>
                        <div class="banner-slide">BANNER 4</div>
                        <div class="banner-slide">BANNER 5</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="main-navigation">
        <div class="container">
            <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
            <ul class="menu" id="mainMenu">
                <li><a href="#">Trang chủ</a></li>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Thành viên</a></li>
                <li><a href="#">Sự kiện</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
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
            <!-- Top Search Section -->
            <div class="top-search-section">
                <h3>Tìm kiếm Doanh nghiệp</h3>
                <form>
                    <div class="search-form-row">
                        <div class="form-group">
                            <label>* Tên Doanh nghiệp</label>
                            <input type="text" placeholder="Nhập tên doanh nghiệp">
                        </div>
                        <div class="form-group">
                            <label>* Khu vực</label>
                            <select>
                                <option value="">Chọn khu vực</option>
                                <option value="hanoi">Hà Nội</option>
                                <option value="hcm">TP. Hồ Chí Minh</option>
                                <option value="danang">Đà Nẵng</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>* Ngành hàng</label>
                            <select>
                                <option value="">Chọn ngành hàng</option>
                                <option value="tech">Công nghệ</option>
                                <option value="finance">Tài chính</option>
                                <option value="retail">Bán lẻ</option>
                                <option value="manufacturing">Sản xuất</option>
                                <option value="service">Dịch vụ</option>
                            </select>
                        </div>
                        <button type="submit" class="search-button">Tìm kiếm</button>
                    </div>
                </form>
            </div>

            <!-- Business Cards Grid (8 cards in 2 columns) -->
            <div class="content-columns">
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=DN1" alt="Doanh nghiệp 1">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 1</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Công nghệ</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> Hà Nội</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/764ba2/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/f093fb/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/4facfe/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/00f2fe/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Chuyên về phát triển phần mềm và giải pháp công nghệ. Chúng tôi cung cấp các dịch vụ phát triển ứng dụng web, mobile, hệ thống quản lý doanh nghiệp (ERP), và các giải pháp công nghệ thông tin chuyên nghiệp. Với đội ngũ kỹ sư giàu kinh nghiệm, chúng tôi cam kết mang lại những sản phẩm chất lượng cao, đáp ứng mọi nhu cầu của khách hàng.</p>
                        </div>
                    </div>
                </div>
                <!-- Ad Block Mobile - VVIP (chèn sau thẻ 1) -->
                <div class="ad-block-mobile vvip">
                    <h4>Video quảng cáo hoặc banner: VVIP</h4>
                    <div class="ad-type">VVIP</div>
                    <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                    <div class="ad-placeholder">Banner/Video VVIP</div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/f5576c/ffffff?text=DN2" alt="Doanh nghiệp 2">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 2</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Tài chính</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/f5576c/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/f093fb/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/fa709a/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/fee140/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/ff6b6b/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Dịch vụ tài chính và đầu tư chuyên nghiệp. Chúng tôi cung cấp các dịch vụ tư vấn tài chính, quản lý đầu tư, tư vấn cổ phần hóa, và các giải pháp tài chính doanh nghiệp. Với nhiều năm kinh nghiệm trong lĩnh vực tài chính, chúng tôi đã hỗ trợ hàng trăm doanh nghiệp phát triển bền vững và đạt được các mục tiêu tài chính của mình.</p>
                        </div>
                    </div>
                </div>
                <!-- Ad Block Mobile - VIP (chèn sau thẻ 2) -->
                <div class="ad-block-mobile vip">
                    <h4>Video quảng cáo hoặc banner: VIP</h4>
                    <div class="ad-type">VIP</div>
                    <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                    <div class="ad-placeholder">Banner/Video VIP</div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/4facfe/ffffff?text=DN3" alt="Doanh nghiệp 3">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 3</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Bán lẻ</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> Đà Nẵng</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/4facfe/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/00f2fe/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/43e97b/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/38f9d7/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Chuỗi cửa hàng bán lẻ hiện đại với hệ thống phân phối rộng khắp. Chúng tôi chuyên cung cấp các sản phẩm tiêu dùng chất lượng cao, từ thực phẩm, đồ uống đến các mặt hàng gia dụng. Với mạng lưới cửa hàng trải dài trên toàn quốc, chúng tôi cam kết mang đến cho khách hàng trải nghiệm mua sắm tiện lợi và giá cả hợp lý nhất.</p>
                        </div>
                    </div>
                </div>
                <!-- Ad Block Mobile - Standard (chèn sau thẻ 3) -->
                <div class="ad-block-mobile standard">
                    <h4>Video quảng cáo hoặc banner: Standard</h4>
                    <div class="ad-type">Standard</div>
                    <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                    <div class="ad-placeholder">Banner/Video Standard</div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/43e97b/ffffff?text=DN4" alt="Doanh nghiệp 4">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 4</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Sản xuất</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> Hà Nội</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/43e97b/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/38f9d7/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/4facfe/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/00f2fe/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Sản xuất và chế biến công nghiệp với công nghệ hiện đại. Chúng tôi chuyên sản xuất các sản phẩm công nghiệp, chế biến nguyên liệu, và cung cấp các giải pháp sản xuất tự động hóa. Với nhà máy được trang bị máy móc tiên tiến và đội ngũ công nhân lành nghề, chúng tôi đảm bảo chất lượng sản phẩm đạt tiêu chuẩn quốc tế.</p>
                        </div>
                    </div>
                </div>
                <!-- Ad Block Mobile - VIP (chèn sau thẻ 4) -->
                <div class="ad-block-mobile vip">
                    <h4>Video quảng cáo hoặc banner: VIP</h4>
                    <div class="ad-type">VIP</div>
                    <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                    <div class="ad-placeholder">Banner/Video VIP</div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/fa709a/ffffff?text=DN5" alt="Doanh nghiệp 5">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 5</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Dịch vụ</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/fa709a/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/fee140/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/764ba2/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/f093fb/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Cung cấp dịch vụ tư vấn và hỗ trợ doanh nghiệp toàn diện. Chúng tôi chuyên tư vấn chiến lược kinh doanh, quản trị nhân sự, marketing, và các dịch vụ hỗ trợ pháp lý cho doanh nghiệp. Với đội ngũ chuyên gia giàu kinh nghiệm, chúng tôi giúp các doanh nghiệp tối ưu hóa hoạt động và phát triển bền vững trong môi trường cạnh tranh hiện đại.</p>
                        </div>
                    </div>
                </div>
                <!-- Ad Block Mobile - VVIP (chèn sau thẻ 5) -->
                <div class="ad-block-mobile vvip">
                    <h4>Video quảng cáo hoặc banner: VVIP</h4>
                    <div class="ad-type">VVIP</div>
                    <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                    <div class="ad-placeholder">Banner/Video VVIP</div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/667eea/ffffff?text=DN6" alt="Doanh nghiệp 6">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 6</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Công nghệ</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> Hà Nội</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/764ba2/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/f093fb/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/4facfe/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/00f2fe/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Phát triển ứng dụng di động và web chuyên nghiệp. Chúng tôi chuyên thiết kế và phát triển các ứng dụng mobile (iOS, Android), website responsive, và các nền tảng thương mại điện tử. Với công nghệ hiện đại và phương pháp phát triển Agile, chúng tôi đảm bảo giao sản phẩm đúng tiến độ và đáp ứng mọi yêu cầu của khách hàng.</p>
                        </div>
                    </div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/f5576c/ffffff?text=DN7" alt="Doanh nghiệp 7">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 7</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Tài chính</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/f5576c/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/f093fb/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/4facfe/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/00f2fe/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/43e97b/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Ngân hàng và dịch vụ tài chính uy tín với nhiều năm kinh nghiệm. Chúng tôi cung cấp đầy đủ các dịch vụ ngân hàng như tiết kiệm, cho vay, đầu tư, bảo hiểm, và các sản phẩm tài chính khác. Với hệ thống công nghệ hiện đại và đội ngũ nhân viên chuyên nghiệp, chúng tôi cam kết mang đến dịch vụ tốt nhất cho khách hàng.</p>
                        </div>
                    </div>
                </div>
                <div class="business-card">
                    <div class="business-card-left">
                        <div class="business-card-image">
                            <img src="https://via.placeholder.com/200x200/4facfe/ffffff?text=DN8" alt="Doanh nghiệp 8">
                        </div>
                        <div class="business-card-info-section">
                            <h4>Doanh nghiệp 8</h4>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <p><strong>Ngành hàng:</strong> Bán lẻ</p>
                            </div>
                            <div class="business-card-info">
                                <svg class="business-card-info-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p><strong>Khu vực:</strong> Đà Nẵng</p>
                            </div>
                        </div>
                    </div>
                    <div class="business-card-content">
                        <div class="business-card-small-image">
                            <div class="business-card-small-image-slider">
                                <div class="business-card-small-image-slide active">
                                    <img src="https://via.placeholder.com/150x150/4facfe/ffffff?text=1" alt="Hình 1">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/00f2fe/ffffff?text=2" alt="Hình 2">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/43e97b/ffffff?text=3" alt="Hình 3">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/38f9d7/ffffff?text=4" alt="Hình 4">
                                </div>
                                <div class="business-card-small-image-slide">
                                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text=5" alt="Hình 5">
                                </div>
                            </div>
                        </div>
                        <div class="business-card-description">
                            <p><strong>Mô tả:</strong> Thương mại điện tử và bán lẻ trực tuyến hàng đầu. Chúng tôi vận hành nền tảng thương mại điện tử hiện đại, cung cấp đa dạng sản phẩm từ thời trang, điện tử, đến đồ gia dụng. Với hệ thống logistics chuyên nghiệp và dịch vụ chăm sóc khách hàng 24/7, chúng tôi đảm bảo trải nghiệm mua sắm trực tuyến tốt nhất cho người tiêu dùng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="sidebar-column">
            <div class="column-header">Theo ngành hàng</div>
            <div class="column-content">
                <div class="ad-section">
                    <!-- VVIP Ad Block -->
                    <div class="ad-block vvip">
                        <h4>Video quảng cáo hoặc banner: VVIP</h4>
                        <div class="ad-type">VVIP</div>
                        <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                        <div class="ad-placeholder">Banner/Video VVIP</div>
                    </div>

                    <!-- VIP Ad Block -->
                    <div class="ad-block vip">
                        <h4>Video quảng cáo hoặc banner: VIP</h4>
                        <div class="ad-type">VIP</div>
                        <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                        <div class="ad-placeholder">Banner/Video VIP</div>
                    </div>

                    <!-- Standard Ad Block -->
                    <div class="ad-block standard">
                        <h4>Video quảng cáo hoặc banner: Standard</h4>
                        <div class="ad-type">Standard</div>
                        <div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>
                        <div class="ad-placeholder">Banner/Video Standard</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <div class="footer-content">
                <p><strong>Thông tin chân trang</strong></p>
                <p>Cộng đồng Doanh nhân Trí tuệ Việt Nam</p>
                <p>Địa chỉ: [Địa chỉ liên hệ] | Email: [Email liên hệ] | Hotline: [Số điện thoại]</p>
                <p>&copy; <?php echo date('Y'); ?> - Bản quyền thuộc về Cộng đồng Doanh nhân Trí tuệ Việt Nam</p>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
