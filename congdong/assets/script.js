// Toggle Menu Function (legacy + modern header)
function toggleMenu() {
    var nav = document.getElementById('mainNav');
    var btn = document.getElementById('hamburgerBtn');
    if (nav) {
        nav.classList.toggle('nav--open');
        if (btn) {
            var open = nav.classList.contains('nav--open');
            btn.classList.toggle('hamburger--open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        return;
    }
    var menu = document.getElementById('mainMenu') || document.querySelector('.main-navigation .menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

/** Toast thông báo ngắn (thay alert khi có thể) */
function showCdToast(message) {
    var text = (message || '').toString().trim();
    if (!text) return;
    var el = document.querySelector('.cd-toast');
    if (!el) {
        el = document.createElement('div');
        el.className = 'cd-toast';
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        document.body.appendChild(el);
    }
    el.textContent = text;
    el.classList.add('cd-toast--show');
    clearTimeout(el._cdToastTimer);
    el._cdToastTimer = setTimeout(function () {
        el.classList.remove('cd-toast--show');
    }, 3400);
}

/** Alert "Đang cập nhật" — dùng chung cho Liên hệ, Xem video giới thiệu, ... */
function initDangCapNhatAlert() {
    document.addEventListener('click', function (e) {
        var link = e.target.closest('a, button');
        if (!link) return;

        var href = (link.getAttribute('href') || '').trim().toLowerCase();
        var text = (link.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
        var isUpdating =
            link.classList.contains('js-dang-cap-nhat') ||
            link.classList.contains('js-lien-he-updating') ||
            href === '#lien-he' ||
            href === '#lienhe' ||
            text === 'liên hệ' ||
            text.indexOf('xem video giới thiệu') !== -1;

        if (!isUpdating) return;

        e.preventDefault();
        e.stopPropagation();
        showCdToast(link.getAttribute('data-alert') || 'Đang cập nhật');

        var nav = document.getElementById('mainNav');
        var btn = document.getElementById('hamburgerBtn');
        if (nav && nav.classList.contains('nav--open')) {
            nav.classList.remove('nav--open');
            if (btn) {
                btn.classList.remove('hamburger--open');
                btn.setAttribute('aria-expanded', 'false');
            }
        }
    });
}

/**
 * Cuộn ngang bảng dùng chung:
 * - Bọc bảng trong nội dung nếu chưa có .dnttvn-table-scroll-wrapper
 * - Hiện hint khi bảng rộng hơn khung
 */
function initTableHorizontalScroll() {
    var roots = document.querySelectorAll(
        '.cd-main, .cd-detail__body, .content-column, .content-column-dang-ky, ' +
        '.entry-content, .structured-content-text, .accordion-section-content, ' +
        '.section-content, .content-item-display, .dang-ky-luat-choi-content'
    );

    roots.forEach(function (root) {
        root.querySelectorAll('table').forEach(function (table) {
            if (table.closest('.dnttvn-table-scroll-wrapper')) return;
            if (table.closest('.form-table, .widefat, .admin-table')) return;

            var wrap = document.createElement('div');
            wrap.className = 'dnttvn-table-scroll-wrapper';
            var hint = document.createElement('div');
            hint.className = 'dnttvn-scroll-hint';
            hint.setAttribute('aria-hidden', 'true');
            hint.textContent = '← Kéo ngang để xem thêm →';
            table.parentNode.insertBefore(wrap, table);
            wrap.appendChild(hint);
            wrap.appendChild(table);
        });
    });

    function updateScrollHints() {
        document.querySelectorAll('.dnttvn-table-scroll-wrapper').forEach(function (wrapper) {
            var table = wrapper.querySelector('table');
            if (!table) return;
            if (table.scrollWidth > wrapper.clientWidth + 8) {
                wrapper.classList.add('has-scroll');
            } else {
                wrapper.classList.remove('has-scroll');
            }
        });
    }

    updateScrollHints();
    window.addEventListener('resize', updateScrollHints);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDangCapNhatAlert);
    document.addEventListener('DOMContentLoaded', initTableHorizontalScroll);
} else {
    initDangCapNhatAlert();
    initTableHorizontalScroll();
}

// Responsive Banner Images
function initResponsiveBanner() {
    const bannerImages = document.querySelectorAll('.banner-slide img');
    if (!bannerImages.length) return;

    function updateBannerSrc() {
        const isMobile = window.innerWidth < 969;
        const bannerPath = isMobile
            ? '/wp-content/themes/congdong/BANNER HEADER  WEB 360 x230.png'
            : '/wp-content/themes/congdong/BANNER HEADER  WEB 1920 X 400.png';

        bannerImages.forEach(img => {
            if (img.src.includes('BANNER HEADER') || img.src.includes('Image')) {
                img.src = window.location.origin + bannerPath;
            }
        });
    }

    // Update on load and resize
    updateBannerSrc();
    window.addEventListener('resize', updateBannerSrc);
}

// Initialize responsive banner
initResponsiveBanner();

    // Mobile Accordion for Both Sidebars (event delegation)
    function initMobileSidebarToggle() {
        document.addEventListener('click', function(event) {
            var header = event.target.closest('.column-header.mobile-toggle');
            if (!header) return;
            event.preventDefault();
            event.stopPropagation();
            var content = header.nextElementSibling;
            header.classList.toggle('collapsed');
            if (content) {
                content.classList.toggle('mobile-collapsed');
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileSidebarToggle);
    } else {
        initMobileSidebarToggle();
    }

    // Accordion: Quy trình gia nhập / Hỏi đáp Cộng đồng (bấm vào khung bài → mở/đóng nội dung)
    // Dùng vanilla JS + event delegation (capture) để luôn hoạt động, không phụ thuộc jQuery
    function initAccordionQuyTrinh() {
        function toggleAccordion(header) {
            if (!header || !header.closest) return;
            var item = header.closest('.accordion-item');
            var body = item ? item.querySelector('.accordion-body') : null;
            if (!item || !body) return;
            var isOpen = item.classList.contains('is-open');
            item.classList.toggle('is-open', !isOpen);
            body.classList.toggle('accordion-body-open', !isOpen);
            body.hidden = isOpen;
            header.setAttribute('aria-expanded', !isOpen);
        }
        document.addEventListener('click', function(e) {
            var header = e.target.closest('.accordion-list .accordion-header');
            if (!header) return;
            e.preventDefault();
            e.stopPropagation();
            toggleAccordion(header);
        }, true);
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var header = e.target.closest('.accordion-list .accordion-header');
            if (!header) return;
            e.preventDefault();
            toggleAccordion(header);
        }, true);

        // Mặc định mở bài đầu tiên trong mỗi danh sách accordion
        document.querySelectorAll('.accordion-list').forEach(function(list) {
            var firstItem = list.querySelector('.accordion-item');
            if (!firstItem) return;
            if (firstItem.classList.contains('is-open')) return;
            var header = firstItem.querySelector('.accordion-header');
            var body   = firstItem.querySelector('.accordion-body');
            if (!header || !body) return;
            firstItem.classList.add('is-open');
            body.classList.add('accordion-body-open');
            body.hidden = false;
            header.setAttribute('aria-expanded', 'true');
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAccordionQuyTrinh);
    } else {
        initAccordionQuyTrinh();
    }

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

    // Structured Content Images Lightbox - lazy init: chỉ tạo khi mở lần đầu để tránh CLS
    (function initStructuredLightbox() {
        let lightbox = null;
        let backdrop, imgEl, btnClose, btnPrev, btnNext, captionEl;
        let currentImages = [];
        let currentIndex = 0;
        let bound = false;

        function createLightbox() {
            if (document.getElementById('structured-lightbox')) return document.getElementById('structured-lightbox');
            const lightboxHTML = `
                <div id="structured-lightbox" class="structured-lightbox" aria-hidden="true" style="position:fixed;top:0;right:0;bottom:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:999999;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;opacity:0;visibility:hidden;-webkit-transition:all 0.3s ease;transition:all 0.3s ease;pointer-events:none;">
                    <div class="structured-lightbox-backdrop" style="position:absolute;top:0;right:0;bottom:0;left:0;"></div>
                    <img class="structured-lightbox-image" src="" alt="" style="max-width:90%;max-height:90%;object-fit:contain;box-shadow:0 4px 20px rgba(0,0,0,0.5);border-radius:4px;">
                    <button type="button" class="structured-lightbox-close" aria-label="Đóng" style="position:absolute;top:20px;right:20px;background:rgba(0,0,0,0.5);color:white;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:20px;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;z-index:1000001;">×</button>
                    <button type="button" class="structured-lightbox-prev" aria-label="Ảnh trước" style="position:absolute;left:20px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:rgba(0,0,0,0.5);color:white;border:none;width:50px;height:50px;border-radius:50%;cursor:pointer;font-size:24px;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;z-index:1000001;">‹</button>
                    <button type="button" class="structured-lightbox-next" aria-label="Ảnh sau" style="position:absolute;right:20px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:rgba(0,0,0,0.5);color:white;border:none;width:50px;height:50px;border-radius:50%;cursor:pointer;font-size:24px;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;z-index:1000001;">›</button>
                    <div class="structured-lightbox-caption" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:white;background:rgba(0,0,0,0.7);padding:10px 20px;border-radius:20px;font-size:14px;max-width:80%;text-align:center;z-index:1000001;"></div>
                </div>`;
            document.body.insertAdjacentHTML('beforeend', lightboxHTML);
            return document.getElementById('structured-lightbox');
        }

        function bindOnce() {
            if (bound || !lightbox) return;
            bound = true;
            backdrop = lightbox.querySelector('.structured-lightbox-backdrop');
            imgEl = lightbox.querySelector('.structured-lightbox-image');
            btnClose = lightbox.querySelector('.structured-lightbox-close');
            btnPrev = lightbox.querySelector('.structured-lightbox-prev');
            btnNext = lightbox.querySelector('.structured-lightbox-next');
            captionEl = lightbox.querySelector('.structured-lightbox-caption');
            backdrop.addEventListener('click', closeLightbox);
            btnClose.addEventListener('click', closeLightbox);
            btnPrev.addEventListener('click', showPrev);
            btnNext.addEventListener('click', showNext);
            document.addEventListener('keydown', onKeydown);
        }

        function onKeydown(e) {
            if (!lightbox || lightbox.style.visibility !== 'visible') return;
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') showPrev();
            else if (e.key === 'ArrowRight') showNext();
        }

        function openLightbox(images, startIndex = 0) {
            if (!images || images.length === 0) return;
            lightbox = createLightbox();
            bindOnce();
            currentImages = images;
            currentIndex = startIndex;
            const imageData = currentImages[currentIndex];
            imgEl.src = imageData.src;
            imgEl.alt = imageData.alt || '';
            captionEl.textContent = imageData.caption || '';
            captionEl.style.display = imageData.caption ? 'block' : 'none';
            lightbox.style.pointerEvents = 'auto';
            lightbox.style.opacity = '1';
            lightbox.style.visibility = 'visible';
            lightbox.setAttribute('aria-hidden', 'false');
            btnPrev.style.display = currentImages.length > 1 ? 'flex' : 'none';
            btnNext.style.display = currentImages.length > 1 ? 'flex' : 'none';
        }

        function closeLightbox() {
            if (!lightbox) return;
            lightbox.style.pointerEvents = 'none';
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

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('structured-image-main') || e.target.classList.contains('structured-image-thumb')) {
                e.preventDefault();
                const structuredItem = e.target.closest('.structured-item-display');
                if (!structuredItem) return;
                const allImages = structuredItem.querySelectorAll('.structured-image-main, .structured-image-thumb');
                const imagesData = Array.from(allImages).map(img => ({
                    src: img.getAttribute('data-full-src') || img.src,
                    alt: img.alt,
                    caption: img.getAttribute('data-caption') || ''
                }));
                const clickedIndex = Array.from(allImages).indexOf(e.target);
                openLightbox(imagesData, clickedIndex);
            }
        });

        window.initStructuredLightbox = function() { /* lazy init on first image click */ };
    })();

    // Lightbox for hình phụ gallery (ảnh + video, bấm xem to hơn)
    window.openLightbox = function(src, type, indexOrMime) {
        const lightbox = document.createElement('div');
        lightbox.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.92);display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;z-index:10000;cursor:pointer;';

        const closeLightbox = function() {
            if (lightbox.parentNode) {
                document.body.removeChild(lightbox);
            }
        };

        lightbox.onclick = function(e) {
            if (e.target === lightbox) closeLightbox();
        };

        if (type === 'video') {
            const mimeType = (typeof indexOrMime === 'string') ? indexOrMime : 'video/mp4';
            const video = document.createElement('video');
            video.controls = true;
            video.autoplay = true;
            video.style.cssText = 'max-width: 90%; max-height: 90%; object-fit: contain; cursor: default;';
            const source = document.createElement('source');
            source.src = src;
            source.type = mimeType;
            video.appendChild(source);
            video.onclick = function(e) { e.stopPropagation(); };
            lightbox.appendChild(video);
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '×';
            closeBtn.setAttribute('type', 'button');
            closeBtn.style.cssText = 'position: absolute; top: 15px; right: 20px; background: rgba(255,255,255,0.2); color: #fff; border: none; font-size: 28px; cursor: pointer; width: 44px; height: 44px; border-radius: 50%; line-height: 1;';
            closeBtn.onclick = function(e) { e.stopPropagation(); closeLightbox(); };
            lightbox.appendChild(closeBtn);
        } else {
            const img = document.createElement('img');
            img.src = src;
            img.style.cssText = 'max-width: 90%; max-height: 90%; object-fit: contain; cursor: default;';
            img.onclick = function(e) { e.stopPropagation(); };
            lightbox.appendChild(img);
        }

        document.body.appendChild(lightbox);
    };

    // Hình chính + thumbnail: bấm thumbnail thay hình chính, bấm hình chính mở lightbox
    document.addEventListener('click', function(e) {
        const layout = e.target.closest('.hinh-phu-main-thumb-layout');
        if (!layout) return;

        const mainEl = layout.querySelector('.hinh-phu-main');
        const thumb = e.target.closest('.hinh-phu-thumb');
        if (thumb && layout.contains(thumb)) {
            e.preventDefault();
            const src = thumb.getAttribute('data-src');
            const type = thumb.getAttribute('data-type') || 'image';
            const mime = thumb.getAttribute('data-mime') || '';
            if (!src || !mainEl) return;
            mainEl.setAttribute('data-current-src', src);
            mainEl.setAttribute('data-current-type', type);
            mainEl.setAttribute('data-current-mime', mime);
            const media = mainEl.querySelector('.hinh-phu-main-media');
            if (media) {
                if (type === 'video') {
                    const video = document.createElement('video');
                    video.className = 'hinh-phu-main-media';
                    video.controls = true;
                    const source = document.createElement('source');
                    source.src = src;
                    source.type = mime || 'video/mp4';
                    video.appendChild(source);
                    mainEl.replaceChild(video, media);
                } else {
                    if (media.tagName.toLowerCase() === 'img') {
                        media.src = src;
                    } else {
                        const img = document.createElement('img');
                        img.className = 'hinh-phu-main-media';
                        img.src = src;
                        img.alt = '';
                        mainEl.replaceChild(img, media);
                    }
                }
            }
            layout.querySelectorAll('.hinh-phu-thumb').forEach(function(t) { t.classList.remove('active'); });
            thumb.classList.add('active');
            return;
        }
        if (e.target.closest('.hinh-phu-main') === mainEl) {
            e.preventDefault();
            const src = mainEl.getAttribute('data-current-src');
            const type = mainEl.getAttribute('data-current-type') || 'image';
            const mime = mainEl.getAttribute('data-current-mime') || '';
            if (src && typeof window.openLightbox === 'function') {
                window.openLightbox(src, type, mime);
            }
        }
    });

    // Lightbox for structured content gallery
    window.openStructuredGallery = function(src, index, caption, imagesArray, captionsArray) {
        const lightbox = document.createElement('div');
        lightbox.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;z-index:10001;';

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = 'position:absolute;top:20px;right:30px;background:rgba(255,255,255,0.2);color:white;border:none;font-size:30px;cursor:pointer;width:50px;height:50px;border-radius:50%;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;';

        const content = document.createElement('div');
        content.style.cssText = 'position:relative;max-width:90%;max-height:90%;text-align:center;';

        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '‹';
        prevBtn.style.cssText = 'position:absolute;left:-60px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:rgba(255,255,255,0.2);color:white;border:none;font-size:40px;cursor:pointer;width:50px;height:50px;border-radius:50%;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;';

        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '›';
        nextBtn.style.cssText = 'position:absolute;right:-60px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:rgba(255,255,255,0.2);color:white;border:none;font-size:40px;cursor:pointer;width:50px;height:50px;border-radius:50%;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;';

        const mediaContainer = document.createElement('div');
        mediaContainer.style.cssText = 'margin-bottom:20px;';

        const captionEl = document.createElement('div');
        captionEl.style.cssText = 'color:white;font-size:16px;margin-top:10px;max-width:600px;margin-left:auto;margin-right:auto;';

        let currentIndex = index;

        function showMedia(index) {
            mediaContainer.innerHTML = '';
            captionEl.textContent = '';

            if (imagesArray && imagesArray[index]) {
                const attachmentId = imagesArray[index];
                // For demo purposes, we'll use the src passed in
                // In real implementation, you'd fetch the full URL based on attachment ID

                const img = document.createElement('img');
                img.src = src;
                img.style.cssText = 'max-width:100%;max-height:80vh;-o-object-fit:contain;object-fit:contain;';
                mediaContainer.appendChild(img);

                if (captionsArray && captionsArray[index]) {
                    captionEl.textContent = captionsArray[index];
                }
            }
        }

        // Event listeners
        closeBtn.onclick = function() {
            document.body.removeChild(lightbox);
        };

        prevBtn.onclick = function() {
            currentIndex = (currentIndex - 1 + imagesArray.length) % imagesArray.length;
            showMedia(currentIndex);
        };

        nextBtn.onclick = function() {
            currentIndex = (currentIndex + 1) % imagesArray.length;
            showMedia(currentIndex);
        };

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.body.removeChild(lightbox);
            } else if (e.key === 'ArrowLeft') {
                prevBtn.click();
            } else if (e.key === 'ArrowRight') {
                nextBtn.click();
            }
        });

        // Assemble lightbox
        content.appendChild(mediaContainer);
        content.appendChild(captionEl);
        lightbox.appendChild(closeBtn);
        lightbox.appendChild(prevBtn);
        lightbox.appendChild(nextBtn);
        lightbox.appendChild(content);

        // Close when clicking background
        lightbox.onclick = function(e) {
            if (e.target === lightbox) {
                document.body.removeChild(lightbox);
            }
        };

        document.body.appendChild(lightbox);
        showMedia(currentIndex);
    };

    // Gallery thống nhất (hình chính + hình phụ): mũi tên qua lại, thumb chọn, bấm hình chính mở lightbox
    function handleDetailGalleryClick(e) {
        const unified = e.target.closest('.detail-gallery-unified');
        if (!unified) return;

        const mainEl = unified.querySelector('.detail-gallery-main');
        const thumbs = unified.querySelectorAll('.detail-gallery-thumb');
        const prevBtn = unified.querySelector('.detail-gallery-prev');
        const nextBtn = unified.querySelector('.detail-gallery-next');
        let items = [];
        var jsonScript = unified.querySelector('script.detail-gallery-json');
        if (jsonScript && jsonScript.textContent) {
            try { items = JSON.parse(jsonScript.textContent.trim()); } catch (err) {}
        }
        if (!items.length) {
            var raw = unified.getAttribute('data-detail-gallery-items');
            if (raw) {
                raw = raw.replace(/&quot;/g, '"').replace(/&#034;/g, '"').replace(/&#039;/g, "'");
                try { items = JSON.parse(raw); } catch (err2) {}
            }
        }
        if (!items.length || !mainEl) return;

        let currentIndex = parseInt(unified.getAttribute('data-detail-current') || '0', 10);
        if (currentIndex >= items.length) currentIndex = 0;

        function showDetailIndex(index) {
            currentIndex = (index + items.length) % items.length;
            unified.setAttribute('data-detail-current', String(currentIndex));
            const item = items[currentIndex];
            const media = mainEl.querySelector('.detail-gallery-media');
            if (!media || !item) return;
            const isPdf = !!item.is_pdf || (item.mime && String(item.mime).toLowerCase() === 'application/pdf');
            if (item.is_video) {
                const video = document.createElement('video');
                video.className = 'detail-gallery-media';
                video.controls = true;
                const src = document.createElement('source');
                src.src = item.url;
                src.type = item.mime || 'video/mp4';
                video.appendChild(src);
                mainEl.replaceChild(video, media);
            } else if (isPdf) {
                const iframe = document.createElement('iframe');
                iframe.className = 'detail-gallery-media detail-gallery-pdf';
                iframe.src = item.url;
                iframe.title = 'PDF';
                iframe.loading = 'lazy';
                mainEl.replaceChild(iframe, media);
            } else {
                if (media.tagName.toLowerCase() === 'img') {
                    media.src = item.url;
                } else {
                    const img = document.createElement('img');
                    img.className = 'detail-gallery-media';
                    img.src = item.url;
                    img.alt = '';
                    mainEl.replaceChild(img, media);
                }
            }
            thumbs.forEach(function(t, i) { t.classList.toggle('active', i === currentIndex); });
        }

        const thumb = e.target.closest('.detail-gallery-thumb');
        if (thumb && unified.contains(thumb)) {
            e.preventDefault();
            e.stopPropagation();
            const idx = parseInt(thumb.getAttribute('data-index'), 10);
            if (!isNaN(idx)) showDetailIndex(idx);
            return;
        }
        if (prevBtn && prevBtn.contains(e.target)) {
            e.preventDefault();
            e.stopPropagation();
            showDetailIndex(currentIndex - 1);
            return;
        }
        if (nextBtn && nextBtn.contains(e.target)) {
            e.preventDefault();
            e.stopPropagation();
            showDetailIndex(currentIndex + 1);
            return;
        }
        if (e.target.closest('.detail-gallery-main') === mainEl) {
            e.preventDefault();
            e.stopPropagation();
            var urls = items.map(function(it) { return { src: it.url, caption: (it.caption || ''), isVideo: it.is_video, isPdf: !!it.is_pdf || (it.mime && String(it.mime).toLowerCase() === 'application/pdf'), mime: it.mime }; });
            if (typeof window.openStructuredLightboxWithItems === 'function') {
                window.openStructuredLightboxWithItems(urls, currentIndex);
            } else if (typeof window.openLightbox === 'function') {
                var isPdf = !!items[currentIndex].is_pdf || (items[currentIndex].mime && String(items[currentIndex].mime).toLowerCase() === 'application/pdf');
                if (isPdf) {
                    window.open(items[currentIndex].url, '_blank');
                } else {
                    window.openLightbox(items[currentIndex].url, items[currentIndex].is_video ? 'video' : 'image', items[currentIndex].mime || '');
                }
            }
        }
    }
    document.addEventListener('click', handleDetailGalleryClick, true);
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var mainEl = e.target.closest('.detail-gallery-main');
        if (!mainEl) return;
        var unified = mainEl.closest('.detail-gallery-unified');
        if (!unified) return;
        e.preventDefault();
        var jsonScript = unified.querySelector('script.detail-gallery-json');
        var items = [];
        if (jsonScript && jsonScript.textContent) { try { items = JSON.parse(jsonScript.textContent.trim()); } catch (err) {} }
        if (!items.length) { var raw = unified.getAttribute('data-detail-gallery-items'); if (raw) { raw = raw.replace(/&quot;/g, '"').replace(/&#034;/g, '"'); try { items = JSON.parse(raw); } catch (err2) {} } }
        if (!items.length || typeof window.openStructuredLightboxWithItems !== 'function') return;
        var idx = parseInt(unified.getAttribute('data-detail-current') || '0', 10);
        if (idx >= items.length) idx = 0;
        var urls = items.map(function(it) { return { src: it.url, caption: (it.caption || ''), isVideo: it.is_video, isPdf: !!it.is_pdf || (it.mime && String(it.mime).toLowerCase() === 'application/pdf'), mime: it.mime }; });
        window.openStructuredLightboxWithItems(urls, idx);
    }, true);

    // Lightbox cho ảnh mục nội dung / gallery thống nhất: nhấn vào cho to lên (tạo overlay riêng để không xung đột)
    window.openStructuredLightboxWithItems = function(itemsArray, startIndex) {
        if (!itemsArray || !itemsArray.length) return;
        var items = itemsArray;
        var current = startIndex || 0;
        if (current >= items.length) current = 0;

        var lb = document.createElement('div');
        lb.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.92);z-index:100002;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;';
        var content = document.createElement('div');
        content.style.cssText = 'position:relative;max-width:90%;max-height:90%;text-align:center;';
        var mediaWrap = document.createElement('div');
        mediaWrap.style.cssText = 'margin-bottom:12px;';
        var closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = 'position:absolute;top:12px;right:12px;background:rgba(255,255,255,0.2);color:#fff;border:none;font-size:28px;cursor:pointer;width:44px;height:44px;border-radius:50%;';
        var prevBtn = document.createElement('button');
        prevBtn.innerHTML = '‹';
        prevBtn.style.cssText = 'position:absolute;left:12px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:rgba(255,255,255,0.2);color:#fff;border:none;font-size:32px;cursor:pointer;width:48px;height:48px;border-radius:50%;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;';
        var nextBtn = document.createElement('button');
        nextBtn.innerHTML = '›';
        nextBtn.style.cssText = 'position:absolute;right:12px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:rgba(255,255,255,0.2);color:#fff;border:none;font-size:32px;cursor:pointer;width:48px;height:48px;border-radius:50%;display:-webkit-flex;display:flex;-webkit-align-items:center;align-items:center;-webkit-justify-content:center;justify-content:center;';
        var capEl = document.createElement('div');
        capEl.style.cssText = 'color:#fff;font-size:14px;margin-top:8px;';

        function showAt(i) {
            current = (i + items.length) % items.length;
            var it = items[current];
            mediaWrap.innerHTML = '';
            capEl.textContent = it.caption || '';
            var isPdf = !!it.isPdf || (it.mime && String(it.mime).toLowerCase() === 'application/pdf');
            if (it.isVideo) {
                var vid = document.createElement('video');
                vid.controls = true;
                vid.style.maxWidth = '90vw';
                vid.style.maxHeight = '85vh';
                var src = document.createElement('source');
                src.src = it.src;
                src.type = it.mime || 'video/mp4';
                vid.appendChild(src);
                mediaWrap.appendChild(vid);
            } else if (isPdf) {
                var iframe = document.createElement('iframe');
                iframe.src = it.src;
                iframe.title = it.caption || 'PDF';
                iframe.style.width = '90vw';
                iframe.style.height = '85vh';
                iframe.style.border = '0';
                iframe.style.background = '#fff';
                mediaWrap.appendChild(iframe);
            } else {
                var img = document.createElement('img');
                img.src = it.src;
                img.alt = it.caption || '';
                img.style.maxWidth = '90vw';
                img.style.maxHeight = '85vh';
                img.style.objectFit = 'contain';
                mediaWrap.appendChild(img);
            }
            prevBtn.style.display = items.length > 1 ? 'flex' : 'none';
            nextBtn.style.display = items.length > 1 ? 'flex' : 'none';
        }
        function closeLb() {
            if (lb.parentNode) document.body.removeChild(lb);
        }
        closeBtn.onclick = closeLb;
        prevBtn.onclick = function() { showAt(current - 1); };
        nextBtn.onclick = function() { showAt(current + 1); };
        lb.onclick = function(e) { if (e.target === lb) closeLb(); };
        content.appendChild(closeBtn);
        content.appendChild(prevBtn);
        content.appendChild(nextBtn);
        content.appendChild(mediaWrap);
        content.appendChild(capEl);
        lb.appendChild(content);
        document.body.appendChild(lb);
        showAt(current);
    };

    // Click ảnh/video trong mục nội dung (structured / content-items) mở lightbox to lên
    document.addEventListener('click', function(e) {
        var target = e.target;
        if (target.nodeName !== 'IMG' && target.nodeName !== 'VIDEO') return;
        var gallery = target.closest('.structured-images-gallery, .content-item-images-gallery');
        if (!gallery || target.closest('.detail-gallery-unified')) return;
        e.preventDefault();
        e.stopPropagation();
        var urls = [];
        var clickIdx = -1;
        var imgs = gallery.querySelectorAll('img[src]');
        var vids = gallery.querySelectorAll('video');
        imgs.forEach(function(im, i) {
            var src = im.src || im.getAttribute('data-full-src') || '';
            if (!src) return;
            if (im === target) clickIdx = urls.length;
            urls.push({ src: src, caption: im.alt || '', isVideo: false, mime: 'image/jpeg' });
        });
        vids.forEach(function(vid) {
            var srcEl = vid.querySelector('source[src]');
            var src = srcEl ? (srcEl.src || srcEl.getAttribute('src') || '') : '';
            var mime = srcEl && srcEl.type ? srcEl.type : 'video/mp4';
            if (!src) return;
            if (vid === target) clickIdx = urls.length;
            urls.push({ src: src, caption: '', isVideo: true, mime: mime });
        });
        if (!urls.length || clickIdx < 0) return;
        if (typeof window.openStructuredLightboxWithItems === 'function') {
            window.openStructuredLightboxWithItems(urls, clickIdx);
        }
    });
