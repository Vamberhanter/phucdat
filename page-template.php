<?php
/**
 * Template Name: Trang Cộng đồng DNTTVN
 * 
 * Template cho trang chủ của Cộng đồng Doanh nhân Trí tuệ Việt Nam
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header Section */
        .site-header {
            background: #06202e;
            padding: 20px 0;
            border-bottom: 2px solid #e0e0e0;
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
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo-section {
            flex: 0 0 200px;
        }

        .logo-section img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .logo-placeholder {
            width: 150px;
            height: 80px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
        }

        .banner-section {
            flex: 1;
            height: 200px;
            position: relative;
            border-radius: 5px;
            overflow: hidden;
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

        .social-links {
            flex: 0 0 200px;
        }

        .social-links h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #fff;
            text-align: center;
        }

        .social-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .social-list li {
            margin-bottom: 0;
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
            background: #333;
            padding: 15px 0;
        }

        .main-navigation .container {
            text-align: center;
        }

        .main-navigation .menu {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .main-navigation .menu li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            display: block;
            transition: color 0.3s;
        }

        .main-navigation .menu li a:hover {
            color: #667eea;
        }

        /* Main Content Area */
        .main-content {
            padding: 40px 0;
        }

        .content-columns {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        .content-column {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .column-header {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .column-content {
            padding: 25px;
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

        /* Middle Column - News */
        .news-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-item h4 {
            margin-bottom: 8px;
            color: #333;
        }

        .news-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .news-item a:hover {
            text-decoration: underline;
        }

        .news-item .news-date {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        /* Right Column - Linked Websites */
        .linked-websites {
            list-style: none;
        }

        .linked-websites li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .linked-websites li:last-child {
            border-bottom: none;
        }

        .linked-websites a {
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }

        .linked-websites a:hover {
            color: #667eea;
        }

        .linked-websites a:before {
            content: "🔗";
            margin-right: 10px;
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
            .content-columns {
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
        });
    </script>
</head>
<body <?php body_class(); ?>>
    <!-- Header Section -->
    <header class="site-header">
        <div class="container">
            <div class="header-top">
                <h1 class="site-title">CỘNG ĐỒNG DOANH NHÂN TRÍ TUỆ VIỆT NAM</h1>
                <p class="site-domain">(Tên miền: congdongdoanhnhantrituevietnam.vn)</p>
            </div>
            <div class="header-content">
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
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
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
        </div>
    </header>

    <!-- Navigation Menu -->
    <nav class="main-navigation">
        <div class="container">
            <ul class="menu">
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
        <div class="container">
            <div class="content-columns">
                <!-- Left Column: About Community -->
                <div class="content-column">
                    <div class="column-header">Về Cộng đồng DNTTVN</div>
                    <div class="column-content">
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

                <!-- Middle Column: Community News -->
                <div class="content-column">
                    <div class="column-header">Tin tức Cộng đồng</div>
                    <div class="column-content">
                        <div class="news-item">
                            <h4><a href="#">Tin 1: Ra mắt Cộng đồng Doanh nhân Trí tuệ Việt Nam</a></h4>
                            <p class="news-date">26/01/2026</p>
                            <p>Cộng đồng chính thức được thành lập với sứ mệnh kết nối và phát triển doanh nhân trí tuệ...</p>
                        </div>
                        <div class="news-item">
                            <h4><a href="#">Tin 2: Chương trình đào tạo doanh nhân khởi nghiệp</a></h4>
                            <p class="news-date">25/01/2026</p>
                            <p>Khóa học đầu tiên về quản trị doanh nghiệp và phát triển bền vững...</p>
                        </div>
                        <div class="news-item">
                            <h4><a href="#">Tin 3: Hội thảo kết nối doanh nghiệp</a></h4>
                            <p class="news-date">24/01/2026</p>
                            <p>Sự kiện quy tụ hàng trăm doanh nhân và nhà đầu tư...</p>
                        </div>
                        <div class="news-item">
                            <h4><a href="#">Tin 4: Tầm nhìn và sứ mệnh của Cộng đồng</a></h4>
                            <p class="news-date">23/01/2026</p>
                            <p>Định hướng phát triển từ 2024 đến 2028...</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Linked Websites -->
                <div class="content-column">
                    <div class="column-header">Website liên kết</div>
                    <div class="column-content">
                        <h4 style="margin-bottom: 15px; color: #333;">Danh sách Doanh nghiệp</h4>
                        <ul class="linked-websites">
                            <li><a href="#">Doanh nghiệp thành viên A</a></li>
                            <li><a href="#">Doanh nghiệp thành viên B</a></li>
                            <li><a href="#">Doanh nghiệp thành viên C</a></li>
                            <li><a href="#">Doanh nghiệp thành viên D</a></li>
                        </ul>
                        <h4 style="margin-top: 25px; margin-bottom: 15px; color: #333;">Cộng đồng</h4>
                        <ul class="linked-websites">
                            <li><a href="#">Cộng đồng Doanh nhân Trẻ</a></li>
                            <li><a href="#">Cộng đồng Khởi nghiệp</a></li>
                            <li><a href="#">Cộng đồng Đầu tư</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
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
