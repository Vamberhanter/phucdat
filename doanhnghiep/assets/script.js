// Toggle Menu Function
function toggleMenu() {
    const menu = document.getElementById('mainMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
    const nav = document.getElementById('dnMainNav');
    const btn = document.getElementById('dnHamburger');
    if (nav) {
        nav.classList.toggle('is-open');
    }
    if (btn) {
        var open = nav && nav.classList.contains('is-open');
        btn.classList.toggle('is-open', !!open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
}

function initDnHeroNav() {
    var btn = document.getElementById('dnHamburger');
    var nav = document.getElementById('dnMainNav');
    if (!btn || !nav) return;
    btn.addEventListener('click', function () {
        var open = !nav.classList.contains('is-open');
        nav.classList.toggle('is-open', open);
        btn.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDnHeroNav);
} else {
    initDnHeroNav();
}

function initDangCapNhatNotice() {
    document.addEventListener('click', function (event) {
        var link = event.target.closest('.js-dang-cap-nhat');
        if (!link) return;
        event.preventDefault();
        var msg = link.getAttribute('data-alert') || '\u0110ang c\u1eadp nh\u1eadt';
        window.alert(msg);
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDangCapNhatNotice);
} else {
    initDangCapNhatNotice();
}

// Banner responsive logic removed as it's now handled by CSS classes .banner-img-desktop and .banner-img-mobile

// Mobile Accordion for Both Sidebars (event delegation)
function initMobileSidebarToggle() {
    document.addEventListener('click', function (event) {
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

/**
 * Ảnh chỉ thu nhỏ khi đã “vừa khít” khung (kích thước hiển thị ≈ vùng nội dung khung).
 * Ảnh nhỏ hơn khung (còn viền trống) không gắn .dn-img-snug-fit — giữ nguyên.
 */
(function initDnImgSnugFit() {
    var TOL = 2.5;

    function contentInnerSize(el) {
        if (!el || !el.getBoundingClientRect) return { w: 0, h: 0 };
        var cs = window.getComputedStyle(el);
        var pl = parseFloat(cs.paddingLeft) || 0;
        var pr = parseFloat(cs.paddingRight) || 0;
        var pt = parseFloat(cs.paddingTop) || 0;
        var pb = parseFloat(cs.paddingBottom) || 0;
        return {
            w: Math.max(0, el.clientWidth - pl - pr),
            h: Math.max(0, el.clientHeight - pt - pb),
        };
    }

    /** Vùng thật sự dành cho ảnh (slide có caption bên dưới thì không lấy hết chiều cao slide). */
    function boxInnerForImg(img, box) {
        var inner = contentInnerSize(box);
        if (
            box.classList &&
            box.classList.contains('business-card-small-image-slide')
        ) {
            var cap = box.querySelector('.dn-ci-slide-caption');
            if (cap) {
                var cs = window.getComputedStyle(box);
                var pt = parseFloat(cs.paddingTop) || 0;
                inner.h = Math.max(4, cap.offsetTop - pt - 1);
            }
        }
        return inner;
    }

    function findSnugBox(img) {
        if (!img || !img.closest) return null;
        var slide = img.closest('.business-card-small-image-slide');
        if (slide) return slide;
        var mainBox = img.closest('.business-card-image');
        if (mainBox) return mainBox;
        var yp = img.closest('.business-card-yp-logo');
        if (yp) return yp;
        var single = img.closest('.dn-ci-images--single');
        if (single) return single;
        var wrap = img.closest('.business-card-small-image');
        if (wrap && img.classList && img.classList.contains('business-small-image')) return wrap;
        return null;
    }

    function updateOne(img) {
        if (!(img instanceof HTMLImageElement)) return;
        var box = findSnugBox(img);
        if (!box) return;
        if (!img.complete || !img.naturalWidth) {
            img.addEventListener(
                'load',
                function onImgLoad() {
                    img.removeEventListener('load', onImgLoad);
                    applySnug(img, findSnugBox(img));
                },
                { once: true }
            );
            return;
        }
        applySnug(img, box);
    }

    function applySnug(img, box) {
        if (!(img instanceof HTMLImageElement) || !box) return;
        img.classList.remove('dn-img-snug-fit');
        void img.offsetWidth;
        var inner = boxInnerForImg(img, box);
        if (inner.w < 4 || inner.h < 4) return;
        var iw = img.clientWidth;
        var ih = img.clientHeight;
        if (iw < 2 || ih < 2) return;
        if (
            Math.abs(iw - inner.w) <= TOL &&
            Math.abs(ih - inner.h) <= TOL
        ) {
            img.classList.add('dn-img-snug-fit');
        }
    }

    function refreshAll() {
        var sel = [
            '.business-card-image img',
            '.business-card-image .business-main-image',
            '.business-card-yp-logo .business-main-image',
            '.business-card-small-image-slide img',
            '.dn-ci-images-slider .business-card-small-image-slide img',
            '.dn-ci-images-slider img.business-small-image',
            '.dn-ci-images--single img',
            '.dn-noi-dung-slider .business-card-small-image-slide img',
            '.dn-noi-dung-slider img.business-small-image',
        ].join(',');
        document.querySelectorAll(sel).forEach(updateOne);
    }

    var debTimer;
    function debouncedRefresh() {
        clearTimeout(debTimer);
        debTimer = setTimeout(refreshAll, 140);
    }

    window.dnttvnRefreshDnImgSnugFit = refreshAll;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            refreshAll();
            window.addEventListener('resize', debouncedRefresh);
        });
    } else {
        refreshAll();
        window.addEventListener('resize', debouncedRefresh);
    }
})();

// Slider ảnh phụ + thumbnail + lightbox (Doanh nghiệp — theo từng khối .business-card-content)
(function initBusinessGalleryAndLightbox() {
    function qa(sel, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(sel));
    }

    var lightbox = document.getElementById('business-lightbox');
    var imgEl = lightbox ? lightbox.querySelector('.business-lightbox-image') : null;
    var backdrop = lightbox ? lightbox.querySelector('.business-lightbox-backdrop') : null;
    var btnClose = lightbox ? lightbox.querySelector('.business-lightbox-close') : null;
    var btnPrev = lightbox ? lightbox.querySelector('.business-lightbox-prev') : null;
    var btnNext = lightbox ? lightbox.querySelector('.business-lightbox-next') : null;

    var activeSlides = [];
    var currentLightboxIndex = 0;

    function openLightbox(slideList, index) {
        if (!lightbox || !imgEl || !slideList || !slideList.length) return;
        if (index < 0 || index >= slideList.length) return;
        activeSlides = slideList;
        currentLightboxIndex = index;
        var slide = activeSlides[currentLightboxIndex];
        var fullUrl = slide.getAttribute('data-full');
        if (!fullUrl) return;
        var alt = slide.getAttribute('data-alt') || '';
        imgEl.src = fullUrl;
        imgEl.alt = alt;
        lightbox.classList.add('active');
        lightbox.setAttribute('aria-hidden', 'false');
    }

    function closeLightbox() {
        if (!lightbox || !imgEl) return;
        lightbox.classList.remove('active');
        lightbox.setAttribute('aria-hidden', 'true');
        imgEl.src = '';
        imgEl.alt = '';
        activeSlides = [];
    }

    function lightboxPrev() {
        if (!activeSlides.length) return;
        var n = (currentLightboxIndex - 1 + activeSlides.length) % activeSlides.length;
        openLightbox(activeSlides, n);
    }

    function lightboxNext() {
        if (!activeSlides.length) return;
        var n = (currentLightboxIndex + 1) % activeSlides.length;
        openLightbox(activeSlides, n);
    }

    qa('.business-card-small-image').forEach(function (wrap) {
        var slider = wrap.querySelector('.business-card-small-image-slider');
        if (!slider) return;
        var slidesArr = qa('.business-card-small-image-slide[data-full]', slider);
        if (!slidesArr.length) return;

        var galleryRow = wrap.nextElementSibling;
        if (!galleryRow || !galleryRow.classList || !galleryRow.classList.contains('business-card-gallery')) {
            galleryRow = wrap.parentElement ? wrap.parentElement.querySelector('.business-card-gallery') : null;
        }

        var current = 0;
        function showSlide(index) {
            if (!slidesArr.length) return;
            if (index < 0) index = slidesArr.length - 1;
            if (index >= slidesArr.length) index = 0;
            current = index;
            slidesArr.forEach(function (s, idx) {
                s.classList.toggle('active', idx === current);
            });
            if (galleryRow) {
                qa('.business-gallery-thumb', galleryRow).forEach(function (t, idx) {
                    t.classList.toggle('is-thumb-active', idx === current);
                });
            }
            if (window.dnttvnRefreshDnImgSnugFit) {
                window.requestAnimationFrame(function () {
                    window.dnttvnRefreshDnImgSnugFit();
                });
            }
        }

        slidesArr.forEach(function (slide) {
            slide.style.cursor = 'pointer';
        });

        slidesArr.forEach(function (slide, index) {
            slide.addEventListener('click', function () {
                openLightbox(slidesArr, index);
            });
        });

        if (galleryRow) {
            qa('.business-gallery-thumb[data-full]', galleryRow).forEach(function (thumb, index) {
                thumb.style.cursor = 'pointer';
                thumb.addEventListener('click', function () {
                    showSlide(index);
                    openLightbox(slidesArr, index);
                });
            });
        }

        var prevBtn = wrap.querySelector('.business-card-small-image-prev');
        var nextBtn = wrap.querySelector('.business-card-small-image-next');
        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                showSlide(current - 1);
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                showSlide(current + 1);
            });
        }

        showSlide(0);
    });

    if (!lightbox || !imgEl) return;

    var mainImage = document.querySelector('.business-card-image img');
    if (mainImage) {
        mainImage.style.cursor = 'pointer';
        mainImage.addEventListener('click', function () {
            var fullUrl = mainImage.getAttribute('data-full') || mainImage.src;
            var alt = mainImage.getAttribute('alt') || '';
            imgEl.src = fullUrl;
            imgEl.alt = alt;
            activeSlides = [];
            lightbox.classList.add('active');
            lightbox.setAttribute('aria-hidden', 'false');
        });
    }

    if (btnClose) btnClose.addEventListener('click', closeLightbox);
    if (backdrop) backdrop.addEventListener('click', closeLightbox);
    if (btnPrev) {
        btnPrev.addEventListener('click', function (e) {
            e.stopPropagation();
            lightboxPrev();
        });
    }
    if (btnNext) {
        btnNext.addEventListener('click', function (e) {
            e.stopPropagation();
            lightboxNext();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        else if (e.key === 'ArrowLeft') lightboxPrev();
        else if (e.key === 'ArrowRight') lightboxNext();
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
    document.addEventListener('click', function (e) {
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

// Lightbox for hình phụ gallery (ảnh + video, bấm xem to hơn)
window.openLightbox = function (src, type, indexOrMime) {
    const lightbox = document.createElement('div');
    lightbox.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            cursor: pointer;
        `;

    const closeLightbox = function () {
        if (lightbox.parentNode) {
            document.body.removeChild(lightbox);
        }
    };

    lightbox.onclick = function (e) {
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
        video.onclick = function (e) { e.stopPropagation(); };
        lightbox.appendChild(video);
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.setAttribute('type', 'button');
        closeBtn.style.cssText = 'position: absolute; top: 15px; right: 20px; background: rgba(255,255,255,0.2); color: #fff; border: none; font-size: 28px; cursor: pointer; width: 44px; height: 44px; border-radius: 50%; line-height: 1;';
        closeBtn.onclick = function (e) { e.stopPropagation(); closeLightbox(); };
        lightbox.appendChild(closeBtn);
    } else {
        const img = document.createElement('img');
        img.src = src;
        img.style.cssText = 'max-width: 90%; max-height: 90%; object-fit: contain; cursor: default;';
        img.onclick = function (e) { e.stopPropagation(); };
        lightbox.appendChild(img);
    }

    document.body.appendChild(lightbox);
};

// Hình chính + thumbnail: bấm thumbnail thay hình chính, bấm hình chính mở lightbox
document.addEventListener('click', function (e) {
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
        layout.querySelectorAll('.hinh-phu-thumb').forEach(function (t) { t.classList.remove('active'); });
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
window.openStructuredGallery = function (src, index, caption, imagesArray, captionsArray) {
    // Create gallery lightbox
    const lightbox = document.createElement('div');
    lightbox.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
        `;

    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '×';
    closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 30px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            font-size: 30px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

    // Content container
    const content = document.createElement('div');
    content.style.cssText = `
            position: relative;
            max-width: 90%;
            max-height: 90%;
            text-align: center;
        `;

    // Navigation buttons
    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = '‹';
    prevBtn.style.cssText = `
            position: absolute;
            left: -60px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            font-size: 40px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = '›';
    nextBtn.style.cssText = prevBtn.style.cssText;
    nextBtn.style.left = 'auto';
    nextBtn.style.right = '-60px';

    // Media container
    const mediaContainer = document.createElement('div');
    mediaContainer.style.cssText = `
            margin-bottom: 20px;
        `;

    // Caption
    const captionEl = document.createElement('div');
    captionEl.style.cssText = `
            color: white;
            font-size: 16px;
            margin-top: 10px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        `;

    let currentIndex = index;

    function showMedia(index) {
        mediaContainer.innerHTML = '';
        captionEl.textContent = '';

        if (imagesArray && imagesArray[index]) {
            const attachmentId = imagesArray[index];
            // For demo purposes, we'll use the src passed in
            // In real implementation, you'd fetch the full URL based on attachment ID

            const img = document.createElement('img');
            img.src = src; // This should be updated to get the correct URL for the current index
            img.style.cssText = `
                    max-width: 100%;
                    max-height: 80vh;
                    object-fit: contain;
                `;
            mediaContainer.appendChild(img);

            if (captionsArray && captionsArray[index]) {
                captionEl.textContent = captionsArray[index];
            }
        }
    }

    // Event listeners
    closeBtn.onclick = function () {
        document.body.removeChild(lightbox);
    };

    prevBtn.onclick = function () {
        currentIndex = (currentIndex - 1 + imagesArray.length) % imagesArray.length;
        showMedia(currentIndex);
    };

    nextBtn.onclick = function () {
        currentIndex = (currentIndex + 1) % imagesArray.length;
        showMedia(currentIndex);
    };

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
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
    lightbox.onclick = function (e) {
        if (e.target === lightbox) {
            document.body.removeChild(lightbox);
        }
    };

    document.body.appendChild(lightbox);
    showMedia(currentIndex);
};

/**
 * Banner quảng cáo cột phải: xoay nhiều slide trong cùng nhóm VVIP/VIP/Standard.
 * Markup: .ad-block-rotator[data-rotate-interval] > .ad-block-rotator__slides > .ad-block-rotator__slide
 * ≤768px: hiện tất cả slide xếp dọc, không tự chuyển (đúng mô tả admin).
 */
function initDnttvnAdBlockRotators() {
    var mq = window.matchMedia ? window.matchMedia('(max-width: 768px)') : null;
    if (mq && mq.matches) {
        document.querySelectorAll('.ad-block-rotator').forEach(function (rot) {
            var slides = rot.querySelectorAll('.ad-block-rotator__slide');
            slides.forEach(function (sl, idx) {
                sl.removeAttribute('hidden');
                sl.classList.toggle('is-active', idx === 0);
                sl.querySelectorAll('video').forEach(function (v) {
                    if (idx === 0) {
                        v.play().catch(function () {});
                    } else {
                        v.pause();
                    }
                });
            });
        });
        return;
    }

    document.querySelectorAll('.ad-block-rotator').forEach(function (rot) {
        var sec = parseInt(rot.getAttribute('data-rotate-interval'), 10);
        if (!sec || sec < 3) {
            sec = 8;
        }
        var slides = rot.querySelectorAll('.ad-block-rotator__slide');
        if (slides.length <= 1) {
            return;
        }

        function pauseAllVideos(root) {
            root.querySelectorAll('video').forEach(function (v) {
                v.pause();
                try {
                    v.currentTime = 0;
                } catch (e) {}
            });
        }

        function goTo(idx) {
            var n = slides.length;
            var i = ((idx % n) + n) % n;
            slides.forEach(function (sl, j) {
                var active = j === i;
                sl.classList.toggle('is-active', active);
                if (active) {
                    sl.removeAttribute('hidden');
                } else {
                    sl.setAttribute('hidden', '');
                }
            });
            pauseAllVideos(rot);
            slides[i].querySelectorAll('video').forEach(function (v) {
                v.play().catch(function () {});
            });
        }

        var cur = 0;
        goTo(0);
        setInterval(function () {
            cur = (cur + 1) % slides.length;
            goTo(cur);
        }, sec * 1000);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDnttvnAdBlockRotators);
} else {
    initDnttvnAdBlockRotators();
}
