(() => {
  "use strict";

  const header = document.getElementById("siteHeader");
  const menuToggle = document.getElementById("menuToggle");
  const mainNav = document.getElementById("mainNav");

  /* Sticky glass header */
  const onScroll = () => {
    if (!header) return;
    header.classList.toggle("is-scrolled", window.scrollY > 24);
  };
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  const closeMobileNav = () => {
    if (!mainNav || !menuToggle) return;
    mainNav.classList.remove("is-open");
    menuToggle.classList.remove("is-open");
    menuToggle.setAttribute("aria-expanded", "false");
  };

  /* Mobile menu */
  if (menuToggle && mainNav) {
    menuToggle.addEventListener("click", () => {
      const open = mainNav.classList.toggle("is-open");
      menuToggle.classList.toggle("is-open", open);
      menuToggle.setAttribute("aria-expanded", open ? "true" : "false");
    });

    mainNav.querySelectorAll("a.nav__link").forEach((link) => {
      link.addEventListener("click", () => {
        closeMobileNav();
      });
    });
  }

  /* ===== Card sliders ===== */
  const sliderInstances = [];

  const getPerView = (el) => {
    const w = window.innerWidth;
    if (w <= 768) return Math.max(1, Number(el.dataset.mobile || 1));
    if (w <= 1200) return Math.max(1, Number(el.dataset.tablet || 2));
    return Math.max(1, Number(el.dataset.desktop || 3));
  };

  const enhanceSlider = (track) => {
    if (track.dataset.sliderReady === "1") return;
    track.dataset.sliderReady = "1";

    const autoplayMs = Number(track.dataset.autoplay || 4500);
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const root = document.createElement("div");
    root.className = "slider";
    const viewport = document.createElement("div");
    viewport.className = "slider__viewport";
    const nav = document.createElement("div");
    nav.className = "slider__nav";
    nav.innerHTML = `
      <button type="button" class="slider__btn slider__btn--prev" aria-label="Xem trước">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
      <button type="button" class="slider__btn slider__btn--next" aria-label="Xem tiếp">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
      </button>
      <div class="slider__dots" aria-hidden="true"></div>
    `;

    track.parentNode.insertBefore(root, track);
    viewport.appendChild(track);
    root.appendChild(viewport);
    root.appendChild(nav);
    track.classList.add("slider__track");

    const prevBtn = nav.querySelector(".slider__btn--prev");
    const nextBtn = nav.querySelector(".slider__btn--next");
    const dotsWrap = nav.querySelector(".slider__dots");
    const items = () => [...track.children];
    let index = 0;
    let timer = null;

    const maxIndex = (perView) => Math.max(0, items().length - perView);

    const stopAutoplay = () => {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    };

    const startAutoplay = () => {
      stopAutoplay();
      if (prefersReducedMotion) return;
      if (!autoplayMs || autoplayMs < 1000) return;
      if (!root.classList.contains("is-active")) return;
      timer = setInterval(() => {
        go(1, true);
      }, autoplayMs);
    };

    const renderDots = (perView) => {
      const pages = maxIndex(perView) + 1;
      dotsWrap.innerHTML = "";
      if (pages <= 1) return;
      for (let i = 0; i < pages; i += 1) {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "slider__dot" + (i === index ? " is-active" : "");
        dot.setAttribute("aria-label", `Trang ${i + 1}`);
        dot.addEventListener("click", () => {
          index = i;
          update();
          startAutoplay();
        });
        dotsWrap.appendChild(dot);
      }
    };

    const update = () => {
      const perView = getPerView(track);
      root.style.setProperty("--slider-per-view", String(perView));
      const total = items().length;
      const needsSlider = total > perView;
      root.classList.toggle("is-active", needsSlider);

      if (!needsSlider) {
        index = 0;
        track.style.transform = "";
        dotsWrap.innerHTML = "";
        stopAutoplay();
        return;
      }

      const max = maxIndex(perView);
      if (index > max) index = 0;
      if (index < 0) index = max;

      void track.offsetWidth;
      const first = items()[0];
      const gapPx = Number.parseFloat(getComputedStyle(track).gap) || 18;
      if (first) {
        const cardW = first.getBoundingClientRect().width;
        track.style.transform = `translateX(-${index * (cardW + gapPx)}px)`;
      }

      renderDots(perView);
    };

    const go = (step, fromAuto = false) => {
      const perView = getPerView(track);
      const max = maxIndex(perView);
      if (max <= 0) return;
      index += step;
      if (index > max) index = 0;
      if (index < 0) index = max;
      update();
      if (!fromAuto) startAutoplay();
    };

    prevBtn.addEventListener("click", () => go(-1));
    nextBtn.addEventListener("click", () => go(1));

    root.addEventListener("mouseenter", stopAutoplay);
    root.addEventListener("mouseleave", startAutoplay);
    root.addEventListener("focusin", stopAutoplay);
    root.addEventListener("focusout", (e) => {
      if (!root.contains(e.relatedTarget)) startAutoplay();
    });

    let ptrStartX = 0;
    let ptrDeltaX = 0;
    let ptrActive = false;
    let ptrDragging = false;
    let suppressImgClick = false;

    viewport.addEventListener(
      "pointerdown",
      (e) => {
        if (!root.classList.contains("is-active") || e.button !== 0) return;
        ptrActive = true;
        ptrDragging = false;
        suppressImgClick = false;
        ptrStartX = e.clientX;
        ptrDeltaX = 0;
        stopAutoplay();
      },
      { passive: true }
    );
    viewport.addEventListener(
      "pointermove",
      (e) => {
        if (!ptrActive) return;
        ptrDeltaX = e.clientX - ptrStartX;
        if (!ptrDragging && Math.abs(ptrDeltaX) > 10) {
          ptrDragging = true;
          try {
            viewport.setPointerCapture(e.pointerId);
          } catch (_err) {
            /* ignore */
          }
        }
      },
      { passive: true }
    );
    const endPointer = (e) => {
      if (!ptrActive) return;
      ptrActive = false;
      if (e?.pointerId != null) {
        try {
          viewport.releasePointerCapture(e.pointerId);
        } catch (_err) {
          /* ignore */
        }
      }
      if (ptrDragging && Math.abs(ptrDeltaX) > 50) {
        suppressImgClick = true;
        go(ptrDeltaX < 0 ? 1 : -1);
      } else {
        startAutoplay();
      }
      ptrDragging = false;
      ptrDeltaX = 0;
    };
    viewport.addEventListener("pointerup", endPointer, { passive: true });
    viewport.addEventListener("pointercancel", endPointer, { passive: true });

    const api = {
      update: () => {
        update();
        startAutoplay();
      },
      root,
      track,
      consumeSuppressClick: () => {
        if (!suppressImgClick) return false;
        suppressImgClick = false;
        return true;
      },
    };
    sliderInstances.push(api);
    update();
    startAutoplay();
  };

  document.querySelectorAll("[data-slider]").forEach(enhanceSlider);

  let sliderResizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(sliderResizeTimer);
    sliderResizeTimer = setTimeout(() => {
      sliderInstances.forEach((s) => s.update());
    }, 120);
  });

  /* ===== Lightbox for slider images (kitchen + doors) ===== */
  const lightbox = document.createElement("div");
  lightbox.className = "lightbox";
  lightbox.hidden = true;
  lightbox.setAttribute("role", "dialog");
  lightbox.setAttribute("aria-modal", "true");
  lightbox.setAttribute("aria-label", "Xem ảnh phóng to");
  lightbox.innerHTML = `
    <div class="lightbox__backdrop" data-lightbox-close></div>
    <div class="lightbox__panel">
      <button type="button" class="lightbox__close" data-lightbox-close aria-label="Đóng">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
      <button type="button" class="lightbox__nav lightbox__nav--prev" aria-label="Ảnh trước">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </button>
      <figure class="lightbox__figure">
        <img class="lightbox__img" alt="" />
        <figcaption class="lightbox__caption"></figcaption>
      </figure>
      <button type="button" class="lightbox__nav lightbox__nav--next" aria-label="Ảnh tiếp">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
      </button>
      <p class="lightbox__counter" aria-live="polite"></p>
    </div>
  `;
  document.body.appendChild(lightbox);

  const lbImg = lightbox.querySelector(".lightbox__img");
  const lbCaption = lightbox.querySelector(".lightbox__caption");
  const lbCounter = lightbox.querySelector(".lightbox__counter");
  const lbPrev = lightbox.querySelector(".lightbox__nav--prev");
  const lbNext = lightbox.querySelector(".lightbox__nav--next");
  const lbClose = lightbox.querySelector(".lightbox__close");

  let lbItems = [];
  let lbIndex = 0;
  let lbLastFocus = null;

  const renderLightbox = () => {
    const item = lbItems[lbIndex];
    if (!item) return;
    lbImg.src = item.src;
    lbImg.alt = item.alt || "";
    lbCaption.textContent = item.alt || "";
    lbCaption.hidden = !item.alt;
    lbCounter.textContent = `${lbIndex + 1} / ${lbItems.length}`;
    const multi = lbItems.length > 1;
    lbPrev.hidden = !multi;
    lbNext.hidden = !multi;
  };

  const closeLightbox = () => {
    if (lightbox.hidden) return;
    lightbox.hidden = true;
    lightbox.classList.remove("is-open");
    document.body.classList.remove("lightbox-open");
    lbImg.removeAttribute("src");
    if (lbLastFocus && typeof lbLastFocus.focus === "function") {
      lbLastFocus.focus();
    }
    lbLastFocus = null;
  };

  const openLightbox = (items, startIndex) => {
    if (!items.length) return;
    lbItems = items;
    lbIndex = Math.max(0, Math.min(startIndex, items.length - 1));
    lbLastFocus = document.activeElement;
    renderLightbox();
    lightbox.hidden = false;
    lightbox.classList.add("is-open");
    document.body.classList.add("lightbox-open");
    lbClose.focus();
  };

  const stepLightbox = (dir) => {
    if (lbItems.length <= 1) return;
    lbIndex = (lbIndex + dir + lbItems.length) % lbItems.length;
    renderLightbox();
  };

  lbPrev.addEventListener("click", () => stepLightbox(-1));
  lbNext.addEventListener("click", () => stepLightbox(1));
  lightbox.addEventListener("click", (e) => {
    if (e.target.closest("[data-lightbox-close]")) closeLightbox();
  });

  document.addEventListener("keydown", (e) => {
    if (lightbox.hidden) return;
    if (e.key === "Escape") {
      e.preventDefault();
      closeLightbox();
    } else if (e.key === "ArrowLeft") {
      e.preventDefault();
      stepLightbox(-1);
    } else if (e.key === "ArrowRight") {
      e.preventDefault();
      stepLightbox(1);
    }
  });

  let lbTouchX = 0;
  lightbox.querySelector(".lightbox__panel").addEventListener(
    "touchstart",
    (e) => {
      lbTouchX = e.changedTouches[0].clientX;
    },
    { passive: true }
  );
  lightbox.querySelector(".lightbox__panel").addEventListener(
    "touchend",
    (e) => {
      const dx = e.changedTouches[0].clientX - lbTouchX;
      if (Math.abs(dx) > 50) stepLightbox(dx < 0 ? 1 : -1);
    },
    { passive: true }
  );

  document.addEventListener("click", (e) => {
    const img = e.target.closest(".slider__track img, [data-slider] img");
    if (!img || lightbox.contains(img)) return;

    const track = img.closest(".slider__track, [data-slider]");
    if (!track) return;

    const instance = sliderInstances.find((s) => s.track === track);
    if (instance?.consumeSuppressClick?.()) return;

    const imgs = [...track.querySelectorAll("img")].filter((el) => el.src);
    if (!imgs.length) return;

    e.preventDefault();
    openLightbox(
      imgs.map((el) => ({ src: el.currentSrc || el.src, alt: el.alt || "" })),
      imgs.indexOf(img)
    );
  });

  const bindSliderLightbox = (instance) => {
    const { track } = instance;
    track.querySelectorAll("img").forEach((img) => {
      if (img.dataset.lightboxBound === "1") return;
      img.dataset.lightboxBound = "1";
      img.addEventListener("click", (e) => {
        if (instance.consumeSuppressClick()) return;
        const imgs = [...track.querySelectorAll("img")].filter((el) => el.src);
        if (!imgs.length) return;
        e.preventDefault();
        e.stopPropagation();
        openLightbox(
          imgs.map((el) => ({ src: el.currentSrc || el.src, alt: el.alt || "" })),
          imgs.indexOf(img)
        );
      });
    });
  };

  sliderInstances.forEach(bindSliderLightbox);

  /* ===== Kitchen / Doors landing switch (same page) ===== */
  const viewKitchen = document.getElementById("view-kitchen");
  const viewDoors = document.getElementById("view-doors");
  const headerQuoteBtn = document.getElementById("headerQuoteBtn");
  const brandTagline = document.querySelector("#brandHome .brand__tagline");
  const LINE = {
    kitchen: {
      view: viewKitchen,
      quoteHref: "#products",
      tagline: "TỦ BẾP CÔNG NGHỆ",
      hash: "",
      productRelated: new Set(["products", "lines", "aluminum"]),
    },
    doors: {
      view: viewDoors,
      quoteHref: "#d-products",
      tagline: "CỬA NHÔM KÍNH",
      hash: "cua",
      productRelated: new Set(["d-products", "d-lines", "d-aluminum"]),
    },
  };

  let currentLine = document.body.dataset.line === "doors" ? "doors" : "kitchen";

  const getSectionsForLine = (line) => {
    const view = LINE[line]?.view;
    if (!view) return [];
    return [...view.querySelectorAll("section[id]")].sort(
      (a, b) => a.offsetTop - b.offsetTop
    );
  };

  const setLine = (
    line,
    { scrollTarget, updateHash = true, replace = false, scroll = true } = {}
  ) => {
    if (!LINE[line]?.view) return;
    currentLine = line;
    document.body.dataset.line = line;

    Object.entries(LINE).forEach(([key, cfg]) => {
      if (!cfg.view) return;
      const active = key === line;
      cfg.view.hidden = !active;
      cfg.view.classList.toggle("is-active", active);
    });

    document.querySelectorAll("[data-line-link]").forEach((el) => {
      el.hidden = el.dataset.lineLink !== line;
    });
    document.querySelectorAll(".nav__switch[data-switch]").forEach((el) => {
      el.hidden = el.dataset.switch === line;
    });

    if (headerQuoteBtn) headerQuoteBtn.setAttribute("href", LINE[line].quoteHref);
    if (brandTagline) {
      brandTagline.innerHTML = `<i></i>${LINE[line].tagline}<i></i>`;
    }

    const brandHome = document.getElementById("brandHome");
    if (brandHome) {
      brandHome.setAttribute(
        "aria-label",
        line === "doors" ? "Phúc Đạt — Cửa Nhôm Kính" : "Phúc Đạt — Tủ Bếp Công Nghệ"
      );
    }

    document.querySelectorAll("a.brand[data-switch]").forEach((el) => {
      el.setAttribute("data-switch", line);
      el.setAttribute("href", line === "doors" ? "#d-hero" : "#hero");
    });

    if (updateHash) {
      // Prefer stable hashes; avoid breaking hosts (e.g. Ladipage) that sandbox history
      const hash =
        scrollTarget && scrollTarget !== "hero" && scrollTarget !== "d-hero"
          ? scrollTarget
          : LINE[line].hash || (line === "doors" ? "cua" : "");
      const url =
        window.location.pathname +
        window.location.search +
        (hash ? `#${hash}` : "");
      try {
        if (replace) history.replaceState({ line }, "", url);
        else history.pushState({ line }, "", url);
      } catch (_) {
        try {
          if (hash) window.location.hash = hash;
          else if (window.location.hash) {
            history.replaceState(null, "", window.location.pathname + window.location.search);
          }
        } catch (__) {
          /* ignore — view switch already applied */
        }
      }
    }

    requestAnimationFrame(() => {
      sliderInstances.forEach((s) => s.update());
      if (scroll) {
        if (scrollTarget) {
          const el = document.getElementById(scrollTarget);
          if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
          else window.scrollTo({ top: 0, behavior: "smooth" });
        } else {
          window.scrollTo({ top: 0, behavior: "smooth" });
        }
      }
      updateActiveNav();
    });
  };

  const lineFromHash = (hash) => {
    const id = (hash || "").replace(/^#/, "");
    if (!id) return "kitchen";
    if (id === "cua" || id.startsWith("d-")) return "doors";
    if (document.getElementById(id)?.closest("#view-doors")) return "doors";
    if (document.getElementById(id)?.closest("#view-kitchen")) return "kitchen";
    return "kitchen";
  };

  document.querySelectorAll("[data-switch]").forEach((el) => {
    el.addEventListener("click", (e) => {
      const line = el.dataset.switch;
      // Need both views in the same document (Ladipage = 1 page)
      if (!LINE[line]?.view) return;

      e.preventDefault();
      closeMobileNav();

      let target = "";
      const href = el.getAttribute("href") || "";
      if (href.startsWith("#")) target = href.slice(1);
      if (!target || target === "cua" || /\.html/i.test(href)) {
        target = line === "doors" ? "d-hero" : "hero";
      }

      // Same line: just scroll
      if (line === currentLine) {
        const elTarget = document.getElementById(target);
        if (elTarget) elTarget.scrollIntoView({ behavior: "smooth", block: "start" });
        else window.scrollTo({ top: 0, behavior: "smooth" });
        return;
      }
      setLine(line, { scrollTarget: target, updateHash: false });
    });
  });

  window.addEventListener("popstate", () => {
    const line = lineFromHash(window.location.hash);
    const id = (window.location.hash || "").replace(/^#/, "");
    const scrollTarget =
      id && id !== "cua" && document.getElementById(id) ? id : line === "doors" ? "d-hero" : "hero";
    setLine(line, { scrollTarget, updateHash: false });
  });

  /* Active nav on scroll (per active view) */
  const navLinks = document.querySelectorAll("a.nav__link");

  const updateActiveNav = () => {
    const cfg = LINE[currentLine];
    const sections = getSectionsForLine(currentLine);
    const offset = (header?.offsetHeight || 80) + 40;
    let current = sections[0]?.id;
    for (const section of sections) {
      if (section.getBoundingClientRect().top - offset <= 0) {
        current = section.id;
      }
    }

    navLinks.forEach((link) => {
      if (link.hidden || link.classList.contains("nav__switch")) {
        link.classList.remove("is-active");
        return;
      }
      const href = link.getAttribute("href") || "";
      const id = href.replace("#", "");
      let active = id === current;
      if (cfg?.productRelated?.has(current)) {
        if (currentLine === "kitchen" && id === "products") active = true;
        if (currentLine === "doors" && id === "d-products") active = true;
      }
      link.classList.toggle("is-active", active);
    });
  };
  window.addEventListener("scroll", updateActiveNav, { passive: true });

  /* Initial line from URL hash (fallback: body data-line) */
  {
    const hash = window.location.hash;
    const fromHash = hash ? lineFromHash(hash) : null;
    const fromBody = document.body.dataset.line === "doors" ? "doors" : "kitchen";
    const line = fromHash || fromBody;
    const id = hash.replace(/^#/, "");
    const scrollTarget =
      id && id !== "cua" && document.getElementById(id) ? id : undefined;
    setLine(line, {
      scrollTarget,
      updateHash: false,
      replace: true,
      scroll: Boolean(scrollTarget),
    });
  }
  updateActiveNav();

  /* Quote forms (front-end only) */
  const bindQuoteForm = (form) => {
    if (!form || form.dataset.quoteBound === "1") return;
    form.dataset.quoteBound = "1";
    const note = form.querySelector(".quote__note");
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = form.querySelector('[name="name"]');
      const phone = form.querySelector('[name="phone"]');
      const product = form.querySelector('[name="product"]');
      if (!name?.value.trim() || !phone?.value.trim()) {
        name?.focus();
        return;
      }
      if (!product?.value) {
        product?.focus();
        return;
      }
      form.reset();
      if (note) {
        note.hidden = false;
        setTimeout(() => {
          note.hidden = true;
        }, 5000);
      }
    });
  };

  document.querySelectorAll("#quoteForm, [data-quote-form]").forEach(bindQuoteForm);

  /* YouTube lite: thumbnail + soft play until click */
  const activateYouTube = (host, videoId, title) => {
    const iframe = document.createElement("iframe");
    iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
    iframe.title = title;
    iframe.allow =
      "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
    iframe.setAttribute("referrerpolicy", "strict-origin-when-cross-origin");
    iframe.allowFullscreen = true;
    host.replaceWith(iframe);
  };

  document.querySelectorAll('iframe[src*="youtube.com/embed/"]').forEach((iframe) => {
    const match = iframe.getAttribute("src")?.match(/embed\/([\w-]{11})/);
    if (!match) return;

    const videoId = match[1];
    const title = iframe.getAttribute("title") || "Phát video";
    const lite = document.createElement("button");
    lite.type = "button";
    lite.className = "yt-lite";
    lite.setAttribute("aria-label", `Phát video: ${title}`);

    const thumb = document.createElement("img");
    thumb.className = "yt-lite__thumb";
    thumb.src = `https://i.ytimg.com/vi/${videoId}/maxresdefault.jpg`;
    thumb.alt = "";
    thumb.loading = "lazy";
    thumb.width = 1280;
    thumb.height = 720;
    thumb.decoding = "async";
    thumb.addEventListener("error", () => {
      if (thumb.src.includes("maxresdefault")) {
        thumb.src = `https://i.ytimg.com/vi/${videoId}/sddefault.jpg`;
      } else if (thumb.src.includes("sddefault")) {
        thumb.src = `https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`;
      }
    });

    const play = document.createElement("span");
    play.className = "yt-lite__play";
    play.setAttribute("aria-hidden", "true");
    play.innerHTML =
      '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.2 5.4v13.2c0 .7.8 1.1 1.4.7l10.2-6.6c.6-.4.6-1.2 0-1.6L9.6 4.7c-.6-.4-1.4 0-1.4.7z"/></svg>';

    lite.append(thumb, play);
    iframe.replaceWith(lite);

    lite.addEventListener("click", () => {
      activateYouTube(lite, videoId, title);
    });
  });
})();
