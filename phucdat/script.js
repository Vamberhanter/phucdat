(() => {
  "use strict";

  const header = document.getElementById("siteHeader");
  const menuToggle = document.getElementById("menuToggle");
  const mainNav = document.getElementById("mainNav");

  const doorsMenu = document.getElementById("doorsMenu");
  const doorsTrigger = document.getElementById("doorsTrigger");
  const doorsPanel = document.getElementById("doorsPanel");

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

  const closeDoors = () => {
    if (!doorsMenu || !doorsTrigger || !doorsPanel) return;
    doorsMenu.classList.remove("is-open");
    doorsTrigger.setAttribute("aria-expanded", "false");
    doorsPanel.hidden = true;
  };

  const openDoors = () => {
    if (!doorsMenu || !doorsTrigger || !doorsPanel) return;
    doorsMenu.classList.add("is-open");
    doorsTrigger.setAttribute("aria-expanded", "true");
    doorsPanel.hidden = false;
  };

  /* Mobile menu */
  if (menuToggle && mainNav) {
    menuToggle.addEventListener("click", () => {
      const open = mainNav.classList.toggle("is-open");
      menuToggle.classList.toggle("is-open", open);
      menuToggle.setAttribute("aria-expanded", open ? "true" : "false");
      if (!open) closeDoors();
    });

    mainNav.querySelectorAll("a.nav__link").forEach((link) => {
      link.addEventListener("click", () => {
        closeDoors();
        closeMobileNav();
      });
    });
  }

  /* Doors dropdown */
  if (doorsTrigger && doorsPanel) {
    doorsTrigger.addEventListener("click", (e) => {
      e.stopPropagation();
      const isOpen = doorsTrigger.getAttribute("aria-expanded") === "true";
      if (isOpen) closeDoors();
      else openDoors();
    });

    doorsPanel.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        closeDoors();
        closeMobileNav();
      });
    });

    document.addEventListener("click", (e) => {
      if (!doorsMenu?.contains(e.target)) closeDoors();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeDoors();
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

    let startX = 0;
    let deltaX = 0;
    let dragging = false;
    viewport.addEventListener(
      "pointerdown",
      (e) => {
        if (!root.classList.contains("is-active")) return;
        dragging = true;
        startX = e.clientX;
        deltaX = 0;
        stopAutoplay();
        viewport.setPointerCapture?.(e.pointerId);
      },
      { passive: true }
    );
    viewport.addEventListener(
      "pointermove",
      (e) => {
        if (!dragging) return;
        deltaX = e.clientX - startX;
      },
      { passive: true }
    );
    viewport.addEventListener("pointerup", () => {
      if (!dragging) return;
      dragging = false;
      if (Math.abs(deltaX) > 50) {
        go(deltaX < 0 ? 1 : -1);
      } else {
        startAutoplay();
      }
      deltaX = 0;
    });

    const api = {
      update: () => {
        update();
        startAutoplay();
      },
      root,
      track,
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

  /* Active nav on scroll */
  const sectionIds = [
    "hero",
    "why",
    "lines",
    "products",
    "aluminum",
    "cua-di",
    "cua-so",
    "glass",
    "cua-phong",
    "projects",
    "video",
    "accessories",
    "partners",
    "certs",
    "faq",
    "news",
    "careers",
  ];
  const doorRelated = new Set(["glass", "cua-di", "cua-so", "cua-phong"]);
  const productRelated = new Set(["products", "lines", "aluminum"]);
  const sections = sectionIds
    .map((id) => document.getElementById(id))
    .filter(Boolean)
    .sort((a, b) => a.offsetTop - b.offsetTop);
  const navLinks = document.querySelectorAll("a.nav__link");

  const updateActiveNav = () => {
    const offset = (header?.offsetHeight || 80) + 40;
    let current = sections[0]?.id;
    for (const section of sections) {
      if (section.getBoundingClientRect().top - offset <= 0) {
        current = section.id;
      }
    }

    navLinks.forEach((link) => {
      const href = link.getAttribute("href") || "";
      const id = href.replace("#", "");
      let active = id === current;
      if (id === "products" && productRelated.has(current)) active = true;
      if (id === "glass" && doorRelated.has(current) && current !== "glass") {
        /* door anchors share gallery; keep Cửa active instead */
        active = false;
      }
      link.classList.toggle("is-active", active);
    });

    if (doorsTrigger) {
      doorsTrigger.classList.toggle("is-active", doorRelated.has(current) && current !== "glass");
    }
  };
  window.addEventListener("scroll", updateActiveNav, { passive: true });
  updateActiveNav();

  /* Quote form (front-end only) */
  const quoteForm = document.getElementById("quoteForm");
  const quoteNote = document.getElementById("quoteNote");
  if (quoteForm) {
    quoteForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = quoteForm.querySelector("#quoteName");
      const phone = quoteForm.querySelector("#quotePhone");
      const product = quoteForm.querySelector("#quoteProduct");
      if (!name?.value.trim() || !phone?.value.trim()) {
        name?.focus();
        return;
      }
      if (!product?.value) {
        product?.focus();
        return;
      }
      quoteForm.reset();
      if (quoteNote) {
        quoteNote.hidden = false;
        setTimeout(() => {
          quoteNote.hidden = true;
        }, 5000);
      }
    });
  }

})();
