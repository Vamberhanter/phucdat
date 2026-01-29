// Toggle Menu Function
function toggleMenu() {
    const menu = document.getElementById('mainMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Banner Carousel Auto-play
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.banner-slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        let slideTimeout = null;

        function clearSlideTimeout() {
            if (slideTimeout) {
                clearTimeout(slideTimeout);
                slideTimeout = null;
            }
        }

        function showSlide(index) {
            clearSlideTimeout();

            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                const video = slide.querySelector('video');
                if (video) {
                    // Dừng và reset tất cả video không hiển thị
                    video.pause();
                    if (i !== index) {
                        try {
                            video.currentTime = 0;
                        } catch (e) {}
                    }
                    // Xóa handler cũ nếu có
                    video.onended = null;
                }
            });

            currentSlide = index;
            const current = slides[currentSlide];
            current.classList.add('active');

            const video = current.querySelector('video');
            if (video) {
                // Video: phát và chuyển slide khi chạy hết
                try {
                    video.currentTime = 0;
                } catch (e) {}
                const playPromise = video.play();
                if (playPromise && typeof playPromise.then === 'function') {
                    playPromise.catch(function () {
                        // Trình duyệt chặn autoplay, fallback sang chuyển slide sau 5s
                        slideTimeout = setTimeout(nextSlide, 5000);
                    });
                }
                video.onended = function () {
                    video.onended = null;
                    nextSlide();
                };
            } else {
                // Ảnh: tự động chuyển sau 5s
                slideTimeout = setTimeout(nextSlide, 5000);
            }
        }

        function nextSlide() {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }

        // Bắt đầu với slide đầu tiên
        showSlide(0);
    }

    // Mobile Accordion for Both Sidebars
    const mobileToggles = document.querySelectorAll('.column-header.mobile-toggle');
    mobileToggles.forEach(function(mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            this.classList.toggle('collapsed');
            if (content) {
                content.classList.toggle('mobile-collapsed');
            }
        });
    });

    // Business gallery lightbox (Doanh nghiệp)
    (function initBusinessLightbox() {
        const lightbox = document.getElementById('business-lightbox');
        if (!lightbox) return;

        const backdrop = lightbox.querySelector('.business-lightbox-backdrop');
        const imgEl    = lightbox.querySelector('.business-lightbox-image');
        const btnClose = lightbox.querySelector('.business-lightbox-close');
        const btnPrev  = lightbox.querySelector('.business-lightbox-prev');
        const btnNext  = lightbox.querySelector('.business-lightbox-next');

        const slides = Array.from(document.querySelectorAll('.business-card-small-image-slide[data-full]'));
        if (!slides.length) return;

        let currentIndex = 0;

        function openLightbox(index) {
            if (index < 0 || index >= slides.length) return;
            currentIndex = index;
            const slide   = slides[currentIndex];
            const fullUrl = slide.getAttribute('data-full');
            const alt     = slide.getAttribute('data-alt') || '';

            imgEl.src = fullUrl;
            imgEl.alt = alt;

            lightbox.classList.add('active');
            lightbox.setAttribute('aria-hidden', 'false');
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            lightbox.setAttribute('aria-hidden', 'true');
            imgEl.src = '';
            imgEl.alt = '';
        }

        function showPrev() {
            const nextIndex = (currentIndex - 1 + slides.length) % slides.length;
            openLightbox(nextIndex);
        }

        function showNext() {
            const nextIndex = (currentIndex + 1) % slides.length;
            openLightbox(nextIndex);
        }

        slides.forEach((slide, index) => {
            slide.style.cursor = 'pointer';
            slide.addEventListener('click', function () {
                openLightbox(index);
            });
        });

        // Gắn lightbox cho thumbnail gallery (nếu có)
        const thumbs = Array.from(document.querySelectorAll('.business-gallery-thumb[data-full]'));
        thumbs.forEach((thumb) => {
            thumb.style.cursor = 'pointer';
            thumb.addEventListener('click', function () {
                const fullUrl = thumb.getAttribute('data-full');
                const alt     = thumb.getAttribute('alt') || '';

                // Tìm index tương ứng trong slides (nếu cùng full URL)
                const idx = slides.findIndex(function (slide) {
                    return slide.getAttribute('data-full') === fullUrl;
                });
                if (idx >= 0) {
                    openLightbox(idx);
                } else {
                    // Nếu không tìm được trong slider, vẫn mở riêng hình này
                    imgEl.src = fullUrl;
                    imgEl.alt = alt;
                    lightbox.classList.add('active');
                    lightbox.setAttribute('aria-hidden', 'false');
                }
            });
        });

        // Gắn lightbox cho Hình chính (Featured Image)
        const mainImage = document.querySelector('.business-card-image img');
        if (mainImage) {
            mainImage.style.cursor = 'pointer';
            mainImage.addEventListener('click', function () {
                const fullUrl = mainImage.getAttribute('data-full') || mainImage.src;
                const alt     = mainImage.getAttribute('alt') || '';
                imgEl.src = fullUrl;
                imgEl.alt = alt;
                lightbox.classList.add('active');
                lightbox.setAttribute('aria-hidden', 'false');
            });
        }

        if (btnClose) {
            btnClose.addEventListener('click', closeLightbox);
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeLightbox);
        }
        if (btnPrev) {
            btnPrev.addEventListener('click', function (e) {
                e.stopPropagation();
                showPrev();
            });
        }
        if (btnNext) {
            btnNext.addEventListener('click', function (e) {
                e.stopPropagation();
                showNext();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                showPrev();
            } else if (e.key === 'ArrowRight') {
                showNext();
            }
        });
    })();

    // Structured Content Images Lightbox
    (function initStructuredLightbox() {
        // Create lightbox HTML if it doesn't exist
        if (!document.getElementById('structured-lightbox')) {
            const lightboxHTML = `
                <div id="structured-lightbox" class="structured-lightbox" aria-hidden="true" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.9);
                    z-index: 999999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                ">
                    <div class="structured-lightbox-backdrop" style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                    "></div>
                    <img class="structured-lightbox-image" src="" alt="" style="
                        max-width: 90%;
                        max-height: 90%;
                        object-fit: contain;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
                        border-radius: 4px;
                    ">
                    <button class="structured-lightbox-close" style="
                        position: absolute;
                        top: 20px;
                        right: 20px;
                        background: rgba(0, 0, 0, 0.5);
                        color: white;
                        border: none;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 1000001;
                    ">×</button>
                    <button class="structured-lightbox-prev" style="
                        position: absolute;
                        left: 20px;
                        top: 50%;
                        transform: translateY(-50%);
                        background: rgba(0, 0, 0, 0.5);
                        color: white;
                        border: none;
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 24px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 1000001;
                    ">‹</button>
                    <button class="structured-lightbox-next" style="
                        position: absolute;
                        right: 20px;
                        top: 50%;
                        transform: translateY(-50%);
                        background: rgba(0, 0, 0, 0.5);
                        color: white;
                        border: none;
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        cursor: pointer;
                        font-size: 24px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 1000001;
                    ">›</button>
                    <div class="structured-lightbox-caption" style="
                        position: absolute;
                        bottom: 20px;
                        left: 50%;
                        transform: translateX(-50%);
                        color: white;
                        background: rgba(0, 0, 0, 0.7);
                        padding: 10px 20px;
                        border-radius: 20px;
                        font-size: 14px;
                        max-width: 80%;
                        text-align: center;
                        z-index: 1000001;
                    "></div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', lightboxHTML);
        }

        const lightbox = document.getElementById('structured-lightbox');
        if (!lightbox) return;

        const backdrop = lightbox.querySelector('.structured-lightbox-backdrop');
        const imgEl = lightbox.querySelector('.structured-lightbox-image');
        const btnClose = lightbox.querySelector('.structured-lightbox-close');
        const btnPrev = lightbox.querySelector('.structured-lightbox-prev');
        const btnNext = lightbox.querySelector('.structured-lightbox-next');
        const captionEl = lightbox.querySelector('.structured-lightbox-caption');

        let currentImages = [];
        let currentIndex = 0;

        function openLightbox(images, startIndex = 0) {
            if (!images || images.length === 0) return;

            currentImages = images;
            currentIndex = startIndex;

            const imageData = currentImages[currentIndex];
            imgEl.src = imageData.src;
            imgEl.alt = imageData.alt || '';
            captionEl.textContent = imageData.caption || '';
            captionEl.style.display = imageData.caption ? 'block' : 'none';

            lightbox.style.opacity = '1';
            lightbox.style.visibility = 'visible';
            lightbox.setAttribute('aria-hidden', 'false');

            // Update navigation buttons
            btnPrev.style.display = currentImages.length > 1 ? 'flex' : 'none';
            btnNext.style.display = currentImages.length > 1 ? 'flex' : 'none';
        }

        function closeLightbox() {
            lightbox.style.opacity = '0';
            lightbox.style.visibility = 'hidden';
            lightbox.setAttribute('aria-hidden', 'true');
            imgEl.src = '';
            imgEl.alt = '';
            captionEl.textContent = '';
        }

        function showPrev() {
            if (currentImages.length <= 1) return;
            currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
            const imageData = currentImages[currentIndex];
            imgEl.src = imageData.src;
            imgEl.alt = imageData.alt || '';
            captionEl.textContent = imageData.caption || '';
            captionEl.style.display = imageData.caption ? 'block' : 'none';
        }

        function showNext() {
            if (currentImages.length <= 1) return;
            currentIndex = (currentIndex + 1) % currentImages.length;
            const imageData = currentImages[currentIndex];
            imgEl.src = imageData.src;
            imgEl.alt = imageData.alt || '';
            captionEl.textContent = imageData.caption || '';
            captionEl.style.display = imageData.caption ? 'block' : 'none';
        }

        // Event listeners
        backdrop.addEventListener('click', closeLightbox);
        btnClose.addEventListener('click', closeLightbox);
        btnPrev.addEventListener('click', showPrev);
        btnNext.addEventListener('click', showNext);

        // Keyboard navigation
        document.addEventListener('keydown', function (e) {
            if (lightbox.style.visibility !== 'visible') return;
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                showPrev();
            } else if (e.key === 'ArrowRight') {
                showNext();
            }
        });

        // Attach click handlers to structured images
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('structured-image-main') || e.target.classList.contains('structured-image-thumb')) {
                e.preventDefault();

                // Find all images in the same structured item
                const structuredItem = e.target.closest('.structured-item-display');
                if (!structuredItem) return;

                const allImages = structuredItem.querySelectorAll('.structured-image-main, .structured-image-thumb');
                const imagesData = Array.from(allImages).map(img => ({
                    src: img.getAttribute('data-full-src') || img.src,
                    alt: img.alt,
                    caption: img.getAttribute('data-caption') || ''
                }));

                // Find the clicked image index
                const clickedIndex = Array.from(allImages).indexOf(e.target);

                openLightbox(imagesData, clickedIndex);
            }
        });

        // Make function globally available for dynamic content
        window.initStructuredLightbox = initStructuredLightbox;
    })();
});
