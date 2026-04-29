
function initBannerSearchDropdowns() {
  if (typeof bootstrap === "undefined" || !bootstrap.Dropdown) return;
  document
    .querySelectorAll(
      ".banner-search .search-form > .search-field > .dropdown-trigger[data-bs-toggle=\"dropdown\"]"
    )
    .forEach((toggle) => {
      const existing = bootstrap.Dropdown.getInstance(toggle);
      if (existing) existing.dispose();
      new bootstrap.Dropdown(toggle, {
        popperConfig: {
          placement: "bottom-start",
          modifiers: [{ name: "flip", enabled: false }],
        },
      });
    });
}

// jQuery ready (not DOMContentLoaded alone): with defer, script may run after DOMContentLoaded fired
jQuery(function () {
  (function initLanguageSwitcher() {
    const root = document.documentElement;
    const labelEls = document.querySelectorAll("[data-lang-label]");
    const flagEls = document.querySelectorAll("[data-lang-flag]");
    const buttons = document.querySelectorAll("[data-lang-choice]");
    if (!buttons.length) return;

    function applyLang(lang) {
      const safeLang = lang === "ar" ? "ar" : "en";
      const dir = safeLang === "ar" ? "rtl" : "ltr";
      root.setAttribute("lang", safeLang);
      root.setAttribute("dir", dir);
      labelEls.forEach((el) => {
        el.textContent = safeLang === "ar" ? "AR" : "ENG";
      });
      flagEls.forEach((img) => {
        if (safeLang === "ar") {
          img.src = "public/images/arabic.jpg";
          img.alt = "Arabic flag";
        } else {
          img.src = "public/images/english.jpg";
          img.alt = "English flag";
        }
      });
      buttons.forEach((btn) => {
        const active = btn.getAttribute("data-lang-choice") === safeLang;
        btn.classList.toggle("active", active);
        btn.setAttribute("aria-pressed", active ? "true" : "false");
        btn.setAttribute("aria-current", active ? "true" : "false");
      });
      try {
        localStorage.setItem("dre_lang", safeLang);
      } catch (e) {}
    }

    const current = root.getAttribute("lang") || "en";
    applyLang(current);

    buttons.forEach((btn) => {
      btn.addEventListener("click", function () {
        applyLang(this.getAttribute("data-lang-choice"));
      });
    });
  })();

  const header = document.querySelector("header");
  if (header && document.querySelector(".banner--page")) {
    header.classList.add("header-white");
  }
  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });

  initBannerSearchDropdowns();

  // About page: when the timeline section is in view, use vertical wheel to scroll the
  // horizontal track first; only after it reaches the end (or start, when scrolling up) does
  // the default page scroll continue.
  (function initJourneyTimelineScrollLock() {
    const section = document.querySelector(".our-journey");
    const timeline = document.querySelector(".journey-timeline");
    if (!section || !timeline) return;

    const preferSmooth = !window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    // Raw wheel delta is small; boost so one tick reveals noticeably more of the timeline
    // Wheel delta is small; higher = more px per tick (keep in single digits to avoid runaway)
    const WHEEL_HORIZ_BOOST = 4;
    // rAF lerp: higher = display catches the target position faster
    const LERP = 1;
    const STOP_EPS = 0.6;

    let targetLeft = null;
    let rafId = 0;

    function maxHScroll() {
      return Math.max(0, Math.round(timeline.scrollWidth - timeline.clientWidth));
    }

    function sectionInView() {
      const r = section.getBoundingClientRect();
      return r.top < window.innerHeight && r.bottom > 0;
    }

    /** User has "reached" the section: top is in upper part of the viewport, or already past the top */
    function readyForHorizontalOnDown() {
      const r = section.getBoundingClientRect();
      return r.top < window.innerHeight * 0.6 || r.top < 0;
    }

    function step() {
      rafId = 0;
      if (targetLeft == null) return;
      const maxS = maxHScroll();
      const goal = Math.max(0, Math.min(maxS, targetLeft));
      const cur = timeline.scrollLeft;
      const diff = goal - cur;
      if (Math.abs(diff) < STOP_EPS) {
        timeline.scrollLeft = goal;
        targetLeft = null;
        return;
      }
      timeline.scrollLeft = cur + diff * LERP;
      rafId = requestAnimationFrame(step);
    }

    const mqHorizTimeline = window.matchMedia("(min-width: 768px)");
    function useHorizontalTimelineWheel() {
      return mqHorizTimeline.matches;
    }

    document.addEventListener(
      "wheel",
      function (e) {
        if (e.target.closest("input, textarea, select, [contenteditable='true']")) return;
        if (!useHorizontalTimelineWheel()) return;
        if (!sectionInView()) return;

        const maxS = maxHScroll();
        if (maxS <= 0) return;

        const sl = timeline.scrollLeft;
        const move = (e.deltaY + e.deltaX) * WHEEL_HORIZ_BOOST;
        if (move === 0) return;

        if (move > 0) {
          if (!readyForHorizontalOnDown()) return;
          if (sl >= maxS - 0.5) return;
        } else {
          if (sl <= 0.5) return;
        }

        e.preventDefault();

        const base = targetLeft != null ? targetLeft : sl;
        const next = Math.max(0, Math.min(maxS, base + move));

        if (!preferSmooth) {
          timeline.scrollLeft = next;
          targetLeft = null;
          if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = 0;
          }
          return;
        }

        targetLeft = next;
        if (!rafId) rafId = requestAnimationFrame(step);
      },
      { passive: false, capture: true }
    );
  })();

  const BANNER_AUTOPLAY_MS = 5000;
  const $bannerSlider = $(".banner-slider");

  function getBannerDirection(currentIndex, nextIndex, slideCount) {
    if (slideCount <= 1 || currentIndex === nextIndex) return "next";
    const lastIndex = slideCount - 1;
    if (currentIndex === lastIndex && nextIndex === 0) return "next";
    if (currentIndex === 0 && nextIndex === lastIndex) return "prev";
    return nextIndex > currentIndex ? "next" : "prev";
  }

  function syncBannerSlideState($slider, activeIndex) {
    $slider.find(".banner-slide").removeClass("is-active is-incoming is-outgoing");
    $slider
      .find(".banner-slide[data-slick-index='" + activeIndex + "']")
      .addClass("is-active");
  }

  function restartBannerDotProgress($slider) {
    $slider.find("li.slick-active .banner-dot__progress").each(function () {
      this.style.animation = "none";
      void this.offsetWidth;
      this.style.removeProperty("animation");
    });
  }

  if ($bannerSlider.length) {
    $bannerSlider.on("init", function (event, slick) {
      const el = this;
      if (el && el.style) el.style.setProperty("--banner-autoplay-ms", BANNER_AUTOPLAY_MS + "ms");
      $(el).find(".slick-dots button").each(function (i) {
        this.setAttribute("aria-label", "Go to slide " + (i + 1));
      });
      syncBannerSlideState($(el), slick.currentSlide || 0);
      restartBannerDotProgress($(el));
    });

    $bannerSlider.on("beforeChange", function (event, slick, currentSlide, nextSlide) {
      const $slider = $(this);
      const dir = getBannerDirection(currentSlide, nextSlide, slick.slideCount || 1);
      $slider.removeClass("is-next is-prev").addClass(dir === "prev" ? "is-prev" : "is-next");
      $slider.find(".banner-slide").removeClass("is-active is-incoming is-outgoing");
      $slider
        .find(".banner-slide[data-slick-index='" + currentSlide + "']")
        .addClass("is-outgoing");
      $slider
        .find(".banner-slide[data-slick-index='" + nextSlide + "']")
        .addClass("is-incoming");
    });

    $bannerSlider.on("afterChange", function (event, slick, currentSlide) {
      const $slider = $(this);
      syncBannerSlideState($slider, currentSlide || 0);
      restartBannerDotProgress($slider);
    });

    $bannerSlider.slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      dots: true,
      arrows: false,
      autoplay: true,
      autoplaySpeed: BANNER_AUTOPLAY_MS,
      fade: true,
      cssEase: "linear",
      speed: 1000,
      infinite: true,
      customPaging: function () {
        // viewBox 40×40 + stroke rings scale cleanly; r=14 → circumference ≈ 87.965 (sync with SCSS keyframes)
        return (
          '<span class="banner-dot" role="presentation">' +
          '<svg class="banner-dot__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="24" height="24" fill="none" focusable="false" aria-hidden="true" shape-rendering="geometricPrecision">' +
          '<circle class="banner-dot__track" cx="20" cy="20" r="14" fill="none" stroke="rgba(255,255,255,0.42)" stroke-width="1.25"/>' +
          '<circle class="banner-dot__progress" cx="20" cy="20" r="14" fill="none" stroke="#2A559C" stroke-width="2" stroke-linecap="round" transform="rotate(-90 20 20)" stroke-dasharray="87.965" stroke-dashoffset="87.965"/>' +
          '<circle class="banner-dot__center" cx="20" cy="20" r="5" fill="rgba(255,255,255,0.45)"/>' +
          "</svg>" +
          "</span>"
        );
      },
    });
  }

  const $propertySlider = $(".property-slider");
  if ($propertySlider.length) {
    const $wrap = $propertySlider.closest(".rental-properties");
    const $fill = $wrap.find(".property-slider__progress-fill");
    const $bar = $wrap.find(".property-slider__progress");

    function updatePropertyProgress(currentSlide, slideCount) {
      const n = slideCount || 1;
      const i = typeof currentSlide === "number" ? currentSlide : 0;
      const pct = n <= 1 ? 100 : ((i + 1) / n) * 100;
      $fill.css("width", pct + "%");
      if ($bar.length) $bar.attr("aria-valuenow", Math.round(pct));
    }

    function isThisPropertyRowSlick(slick) {
      return slick && slick.$slider && slick.$slider[0] === $propertySlider[0];
    }

    $propertySlider.on("init", function (event, slick) {
      if (!isThisPropertyRowSlick(slick)) return;
      updatePropertyProgress(slick.currentSlide, slick.slideCount);
    });
    $propertySlider.on("afterChange", function (event, slick, currentSlide) {
      if (!isThisPropertyRowSlick(slick)) return;
      updatePropertyProgress(currentSlide, slick.slideCount);
    });

    $propertySlider.slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      infinite: true,
      variableWidth: true,
      arrows: false,
      dots: false,
      autoplay: true,
      autoplaySpeed: 4000,
      speed: 450,
      centerMode: true,
      centerPadding: '0',
      responsive: [
        {
          breakpoint: 575,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            variableWidth: false  ,
          },
        },
      ],
    });

    $wrap.find(".property-slider__arrow--prev").on("click", function () {
      $propertySlider.slick("slickPrev");
    });
    $wrap.find(".property-slider__arrow--next").on("click", function () {
      $propertySlider.slick("slickNext");
    });
  }

  /* ------------------------------
     Dropdown Selection Logic
  ------------------------------ */
  const VISIBLE_PROPERTY_COUNT = 5;
  const propertyTypeField = document.getElementById('propertyTypeField');

  if (propertyTypeField) {
    const propertyDropdownMenu = propertyTypeField.querySelector('.dropdown-menu');
    if (propertyDropdownMenu) {
      propertyDropdownMenu.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    }

    const propertyOptionsWrap = propertyTypeField.querySelector('.option-buttons[data-type="property"]');
    const propertyOptions = propertyTypeField.querySelectorAll('.option-buttons[data-type="property"] button');
    const propertySelectedText = propertyTypeField.querySelector('.selected-text');
    const hiddenPropertyType = document.getElementById('hiddenPropertyType');
    const viewMoreBtn = propertyTypeField.querySelector('.view-more');

    propertyOptions.forEach((btn, index) => {
      if (index >= VISIBLE_PROPERTY_COUNT) {
        btn.classList.add('property-option--extra');
      }
    });

    if (propertyOptionsWrap && propertyOptions.length > VISIBLE_PROPERTY_COUNT) {
      propertyOptionsWrap.classList.add('property-options--collapsed');
    } else if (viewMoreBtn) {
      viewMoreBtn.hidden = true;
    }

    if (viewMoreBtn && propertyOptionsWrap) {
      viewMoreBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const collapsed = propertyOptionsWrap.classList.toggle('property-options--collapsed');
        viewMoreBtn.textContent = collapsed ? 'View more' : 'View less';
      });
    }

    if (propertyOptions.length > 0) {
      propertyOptions.forEach(btn => {
        btn.addEventListener('click', function () {
          propertyOptions.forEach(opt => opt.classList.remove('active'));
          this.classList.add('active');
          const value = this.getAttribute('data-value');
          if (propertySelectedText) propertySelectedText.textContent = value;
          if (hiddenPropertyType) hiddenPropertyType.value = value;
        });
      });
    }
  }

  // Beds & Baths Selection
  // Beds & Baths Selection
  const bedsBathsField = document.getElementById('bedsBathsField');
  if (bedsBathsField) {
    const bedsCheckboxes = Array.from(bedsBathsField.querySelectorAll('.bed-check'));
    const bathsCheckboxes = Array.from(bedsBathsField.querySelectorAll('.bath-check'));
    const bedsBathsSelectedText = bedsBathsField.querySelector('.selected-text');
    const hiddenBedrooms = document.getElementById('hiddenBedrooms');
    const hiddenBathrooms = document.getElementById('hiddenBathrooms');
    const clearFiltersBtn = bedsBathsField.querySelector('.clear-filters');

    // Prevent dropdown closing clicking inside
    const bedsBathsDropdownMenu = bedsBathsField.querySelector('.dropdown-menu');
    if (bedsBathsDropdownMenu) {
      bedsBathsDropdownMenu.addEventListener('click', function (e) {
        e.stopPropagation();
      });
    }

    function updateBedsBathsText() {
      const selectedBeds = bedsCheckboxes.filter(cb => cb.checked).map(cb => cb.value);
      const selectedBaths = bathsCheckboxes.filter(cb => cb.checked).map(cb => cb.value);

      let textParts = [];
      if (selectedBeds.length > 0) {
        let hasStudio = selectedBeds.includes('Studio');
        let nums = selectedBeds.filter(x => x !== 'Studio');
        let bedStr = '';
        if (nums.length > 0) {
          let isMultipleOrNotOne = nums.length > 1 || nums[0] !== '1';
          bedStr = nums.join(',') + ' Bed' + (isMultipleOrNotOne ? 's' : '');
        }
        if (hasStudio) bedStr = bedStr ? 'Studio, ' + bedStr : 'Studio';
        textParts.push(bedStr);
      }

      if (selectedBaths.length > 0) {
        let isMultipleOrNotOne = selectedBaths.length > 1 || selectedBaths[0] !== '1';
        textParts.push(selectedBaths.join(',') + ' Bath' + (isMultipleOrNotOne ? 's' : ''));
      }

      if (hiddenBedrooms) hiddenBedrooms.value = selectedBeds.join(',');
      if (hiddenBathrooms) hiddenBathrooms.value = selectedBaths.join(',');

      if (bedsBathsSelectedText) {
        bedsBathsSelectedText.textContent = textParts.length > 0 ? textParts.join(' & ') : 'Select Beds & Baths';
      }
    }

    [...bedsCheckboxes, ...bathsCheckboxes].forEach(cb => {
      cb.addEventListener('change', updateBedsBathsText);
    });

    if (clearFiltersBtn) {
      clearFiltersBtn.addEventListener('click', function () {
        bedsCheckboxes.forEach(cb => cb.checked = false);
        bathsCheckboxes.forEach(cb => cb.checked = false);
        updateBedsBathsText();
      });
    }
  }

  const categoriesField = document.getElementById("categoriesField");
  if (categoriesField) {
    const categoriesMenu = categoriesField.querySelector(".dropdown-menu");
    if (categoriesMenu) {
      categoriesMenu.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }
    const categoriesText = categoriesField.querySelector(".selected-text");
    const hiddenCategories = document.getElementById("hiddenCategories");
    categoriesField.querySelectorAll('.option-buttons button').forEach((btn) => {
      btn.addEventListener("click", function () {
        categoriesField.querySelectorAll(".option-buttons button").forEach((b) => b.classList.remove("active"));
        this.classList.add("active");
        const v = this.getAttribute("data-value");
        if (categoriesText) categoriesText.textContent = v;
        if (hiddenCategories) hiddenCategories.value = v;
      });
    });
  }

  const priceField = document.getElementById("priceField");
  if (priceField) {
    const priceMenu = priceField.querySelector(".dropdown-menu.price-range-dropdown--listing") || priceField.querySelector(".dropdown-menu");
    if (priceMenu) {
      priceMenu.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }
    const priceText = priceField.querySelector(".selected-text");
    const hiddenMinPrice = document.getElementById("hiddenMinPrice");
    const hiddenMaxPrice = document.getElementById("hiddenMaxPrice");
    const minLabelEl = priceField.querySelector(".js-price-min-label");
    const maxLabelEl = priceField.querySelector(".js-price-max-label");
    let minPriceVal = "";
    let maxPriceVal = "";

    function formatPillAmount(val) {
      if (!val) return "";
      return Number(val).toLocaleString("en-US");
    }

    function updatePriceLabel() {
      if (!priceText) return;
      const fmt = (n) => Number(n).toLocaleString("en-US") + " د.إ";
      if (minPriceVal && maxPriceVal) {
        priceText.textContent = fmt(minPriceVal) + " – " + fmt(maxPriceVal);
      } else if (minPriceVal) {
        priceText.textContent = "From " + fmt(minPriceVal);
      } else if (maxPriceVal) {
        priceText.textContent = "Up to " + fmt(maxPriceVal);
      } else {
        priceText.textContent = "Price";
      }
    }

    priceField.querySelectorAll(".js-price-min-opt").forEach((btn) => {
      btn.addEventListener("click", function () {
        minPriceVal = this.getAttribute("data-value") || "";
        if (hiddenMinPrice) hiddenMinPrice.value = minPriceVal;
        if (minLabelEl) minLabelEl.textContent = minPriceVal ? formatPillAmount(minPriceVal) : "No Min";
        updatePriceLabel();
      });
    });

    priceField.querySelectorAll(".js-price-max-opt").forEach((btn) => {
      btn.addEventListener("click", function () {
        maxPriceVal = this.getAttribute("data-value") || "";
        if (hiddenMaxPrice) hiddenMaxPrice.value = maxPriceVal;
        if (maxLabelEl) maxLabelEl.textContent = maxPriceVal ? formatPillAmount(maxPriceVal) : "No Max";
        updatePriceLabel();
      });
    });

    priceField.querySelectorAll(".price-range-pill-dd__toggle").forEach((t) => {
      t.addEventListener("click", (e) => e.stopPropagation());
    });
  }

  // --- Property card media inner slider ---
  // Prefer wrapped slides (detail similar / future markup); home & listing use bare <picture> siblings.
  $(".property-card__media").each(function () {
    const $this = $(this);
    let $slides = $this.children(".property-card__media-slide");
    let slideSelector = ".property-card__media-slide";
    if ($slides.length === 0) {
      $slides = $this.children("picture");
      slideSelector = "picture";
    }
    if ($slides.length > 1) {
      $this.slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true,
        autoplay: true,
        autoplaySpeed: 3500,
        fade: true,
        cssEase: 'linear',
        speed: 300,
        appendDots: $this.find('.property-card__dots').empty(),
        customPaging: function (slider, i) {
          return '<span></span>';
        },
        slide: slideSelector,
      });
    }
  });

  // --- Property card click-to-open ---
  (function initPropertyCardNavigation() {
    const DETAIL_URL = "properties-details.php";
    const INTERACTIVE_SELECTOR = [
      "a",
      "button",
      "input",
      "select",
      "textarea",
      "label",
      "[role='button']",
      "[data-bs-toggle]",
      ".slick-dots",
      ".property-card__dots",
    ].join(", ");

    let pointerStart = null;

    document.addEventListener("pointerdown", function (e) {
      const card = e.target && e.target.closest && e.target.closest(".property-card");
      pointerStart = card
        ? { x: e.clientX, y: e.clientY, card: card }
        : null;
    });

    document.addEventListener("click", function (e) {
      if (e.defaultPrevented || e.button !== 0) return;
      const target = e.target;
      if (!target || !target.closest) return;

      const card = target.closest(".property-card");
      if (!card) return;
      if (target.closest(INTERACTIVE_SELECTOR)) return;

      // Ignore click after a drag/swipe gesture on card content.
      if (pointerStart && pointerStart.card === card) {
        const dx = Math.abs(e.clientX - pointerStart.x);
        const dy = Math.abs(e.clientY - pointerStart.y);
        if (dx > 8 || dy > 8) return;
      }

      const selectedText = window.getSelection && window.getSelection().toString();
      if (selectedText) return;

      const destination =
        card.getAttribute("data-property-url") ||
        (card.querySelector("[data-property-url]") &&
          card.querySelector("[data-property-url]").getAttribute("data-property-url")) ||
        DETAIL_URL;

      window.location.href = destination;
    });
  })();

  // --- Property detail: similar listings row ---
  // Nested Slick (card media fade) + row Slick: inline heights on .slick-list / track / slides
  // can balloon; strip them so the row only wraps real content height.
  function clearSlickListTrackSlideHeights($sliderRoots) {
    $sliderRoots.each(function () {
      const list = this.querySelector(":scope > .slick-list");
      if (!list) return;
      list.style.removeProperty("height");
      list.style.removeProperty("min-height");
      const track = list.querySelector(":scope > .slick-track");
      if (!track) return;
      track.style.removeProperty("height");
      track.style.removeProperty("min-height");
      Array.prototype.forEach.call(track.children, function (child) {
        if (child.classList && child.classList.contains("slick-slide")) {
          child.style.removeProperty("height");
          child.style.removeProperty("min-height");
        }
      });
    });
  }

  function refreshSimilarListingsLayout($row, $section) {
    if (!$row.length || !$row.hasClass("slick-initialized")) return;
    requestAnimationFrame(function () {
      $section.find(".property-card__media.slick-initialized").each(function () {
        clearSlickListTrackSlideHeights($(this));
      });
      clearSlickListTrackSlideHeights($row);
      $row.slick("setPosition");
      requestAnimationFrame(function () {
        clearSlickListTrackSlideHeights($row);
      });
    });
  }

  const $similarDetailSlider = $(".js-property-detail-similar-slider");
  if ($similarDetailSlider.length) {
    const $similarSection = $similarDetailSlider.closest(".property-detail-similar");
    const $similarFill = $similarSection.find(".property-slider__progress-fill");
    const $similarBar = $similarSection.find(".property-slider__progress");

    function updateSimilarDetailProgress(currentSlide, slideCount) {
      const n = slideCount || 1;
      const i = typeof currentSlide === "number" ? currentSlide : 0;
      const pct = n <= 1 ? 100 : ((i + 1) / n) * 100;
      $similarFill.css("width", Math.max(0, Math.min(100, pct)) + "%");
      if ($similarBar.length) $similarBar.attr("aria-valuenow", Math.round(pct));
    }

    function isThisSimilarRowSlick(slick) {
      return slick && slick.$slider && slick.$slider[0] === $similarDetailSlider[0];
    }

    $similarDetailSlider.on("init reInit", function (event, slick) {
      if (isThisSimilarRowSlick(slick)) {
        updateSimilarDetailProgress(slick.currentSlide, slick.slideCount);
      }
      refreshSimilarListingsLayout($similarDetailSlider, $similarSection);
    });
    $similarDetailSlider.on("afterChange", function (event, slick, currentSlide) {
      if (!isThisSimilarRowSlick(slick)) return;
      updateSimilarDetailProgress(currentSlide, slick.slideCount);
    });

    var similarListingsResizeTimer;
    $(window).on("resize", function () {
      if (!$similarDetailSlider.hasClass("slick-initialized")) return;
      clearTimeout(similarListingsResizeTimer);
      similarListingsResizeTimer = setTimeout(function () {
        refreshSimilarListingsLayout($similarDetailSlider, $similarSection);
      }, 150);
    });

    $similarDetailSlider.slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      infinite: true,
      arrows: false,
      dots: false,
      autoplay: true,
      autoplaySpeed: 4000,
      speed: 450,
      responsive: [
        {
          breakpoint: 1400,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 576,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
      ],
    });

    $(window).on("load", function () {
      refreshSimilarListingsLayout($similarDetailSlider, $similarSection);
    });

    $similarSection.find(".property-slider__arrow--prev").on("click", function () {
      $similarDetailSlider.slick("slickPrev");
    });
    $similarSection.find(".property-slider__arrow--next").on("click", function () {
      $similarDetailSlider.slick("slickNext");
    });
  }

  // --- Enquiry Modal logic ---
  const enquiryButtons = document.querySelectorAll('.property-card__actions .property-btn--primary[data-bs-target="#siteEnquiryForm"]');
  const modalEl = document.getElementById('siteEnquiryForm');
  
  if (modalEl) {
    const locInput = modalEl.querySelector('input[name="location"]');
    const typeSelect = modalEl.querySelector('select[name="property_type"]');
    const sizeInput = modalEl.querySelector('input[name="property_size"]');
    
    enquiryButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const card = this.closest('.property-card__inner');
        if (!card) return;
        
        const titleEl = card.querySelector('.property-card__title');
        const locEl = card.querySelector('.property-card__location span');
        const typeEl = card.querySelector('.property-tag--fill');
        
        let sizeStr = '';
        const detailItems = card.querySelectorAll('.property-details__item span');
        detailItems.forEach(span => {
          if (span.textContent.includes('ft²')) {
            sizeStr = span.textContent.trim();
          }
        });

        if (locInput) {
          const title = titleEl ? titleEl.textContent.trim() : '';
          const loc = locEl ? locEl.textContent.trim() : '';
          locInput.value = title ? `${title}, ${loc}` : loc;
          locInput.disabled = true;
        }

        if (sizeInput) {
          sizeInput.value = sizeStr;
          sizeInput.disabled = true;
        }

        if (typeSelect) {
          const typeStr = typeEl ? typeEl.textContent.trim() : '';
          let matched = false;
          Array.from(typeSelect.options).forEach(opt => {
            if (opt.text.toLowerCase() === typeStr.toLowerCase()) {
              typeSelect.value = opt.value;
              matched = true;
            }
          });
          if (!matched && typeStr) {
            const newOpt = new Option(typeStr, typeStr.toLowerCase());
            typeSelect.add(newOpt);
            typeSelect.value = typeStr.toLowerCase();
          }
          typeSelect.disabled = true;
        }
      });
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
      if (locInput) {
        locInput.value = '';
        locInput.disabled = false;
      }
      if (sizeInput) {
        sizeInput.value = '';
        sizeInput.disabled = false;
      }
      if (typeSelect) {
        typeSelect.value = '';
        typeSelect.disabled = false;
      }
    });
  }

  // --- News Slider logic ---
  const $newsSlider = $(".news-card-slider");
  if ($newsSlider.length) {
    const $newsWrap = $newsSlider.closest(".news-slider-wrap");
    const $newsFill = $newsWrap.find(".property-slider__progress-fill");
    const $newsBar = $newsWrap.find(".property-slider__progress");

    function updateNewsProgress(currentSlide, slideCount) {
      const n = slideCount || 1;
      const i = typeof currentSlide === "number" ? currentSlide : 0;
      const pct = n <= 1 ? 100 : ((i + 1) / n) * 100;
      $newsFill.css("width", Math.max(0, Math.min(100, pct)) + "%");
      if ($newsBar.length) $newsBar.attr("aria-valuenow", Math.round(pct));
    }

    function isThisNewsRowSlick(slick) {
      return slick && slick.$slider && slick.$slider[0] === $newsSlider[0];
    }

    $newsSlider.on("init", function (event, slick) {
      if (!isThisNewsRowSlick(slick)) return;
      updateNewsProgress(slick.currentSlide, slick.slideCount);
    });
    $newsSlider.on("afterChange", function (event, slick, currentSlide) {
      if (!isThisNewsRowSlick(slick)) return;
      updateNewsProgress(currentSlide, slick.slideCount);
    });

    $newsSlider.slick({
      slidesToShow: 2,
      slidesToScroll: 1,
      infinite: true,
      arrows: false,
      dots: false,
      autoplay: true,
      autoplaySpeed: 4000,
      speed: 500,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
      ],
    });

    $newsWrap.find(".property-slider__arrow--prev").on("click", function () {
      $newsSlider.slick("slickPrev");
    });
    $newsWrap.find(".property-slider__arrow--next").on("click", function () {
      $newsSlider.slick("slickNext");
    });
  }

  // --- Sticky Share Button ---
  const stickyShareTrigger = document.getElementById('stickyShareTrigger');
  const stickySharePanel   = document.getElementById('stickySharePanel');
  const stickyShareClose   = document.getElementById('stickyShareClose');

  const listingGrid = document.querySelector(".properties-grid");
  const listingToggleBtns = document.querySelectorAll("[data-listing-view]");
  if (listingGrid && listingToggleBtns.length) {
    listingToggleBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        const v = btn.getAttribute("data-listing-view");
        listingToggleBtns.forEach((b) => {
          const on = b.getAttribute("data-listing-view") === v;
          b.classList.toggle("is-active", on);
          b.setAttribute("aria-pressed", on ? "true" : "false");
        });
        listingGrid.classList.toggle("properties-grid--list", v === "list");
      });
    });
  }

  const listingSearchOffcanvas = document.getElementById("listingSearchOffcanvas");
  const listingSearchHome = document.querySelector(".js-listing-search-home");
  const listingSearchPanel = document.querySelector(".js-listing-search-panel");
  const listingSearchOffcanvasMount = document.querySelector(".js-listing-search-offcanvas-mount");
  if (listingSearchOffcanvas && listingSearchHome && listingSearchPanel && listingSearchOffcanvasMount) {
    listingSearchOffcanvas.addEventListener("show.bs.offcanvas", () => {
      listingSearchOffcanvasMount.appendChild(listingSearchPanel);
    });
    listingSearchOffcanvas.addEventListener("shown.bs.offcanvas", () => {
      initBannerSearchDropdowns();
    });
    listingSearchOffcanvas.addEventListener("hidden.bs.offcanvas", () => {
      listingSearchHome.appendChild(listingSearchPanel);
      initBannerSearchDropdowns();
    });
  }

  document.querySelectorAll(".listing-sort-offcanvas__opt").forEach((opt) => {
    opt.addEventListener("click", () => {
      document.querySelectorAll(".listing-sort-offcanvas__opt").forEach((b) => b.classList.remove("is-active"));
      opt.classList.add("is-active");
    });
  });

  if (stickyShareTrigger && stickySharePanel) {
    // Inject current page URL into share links
    const pageUrl = encodeURIComponent(window.location.href);
    stickySharePanel.querySelectorAll('.sticky-share__icon[href]').forEach(function(a) {
      const href = a.getAttribute('href');
      if (href && (href.includes('facebook.com') || href.includes('twitter.com') || href.includes('linkedin.com'))) {
        a.setAttribute('href', href + pageUrl);
      }
    });

    function openSharePanel() {
      stickySharePanel.classList.add('is-open');
      stickyShareTrigger.setAttribute('aria-expanded', 'true');
    }
    function closeSharePanel() {
      stickySharePanel.classList.remove('is-open');
      stickyShareTrigger.setAttribute('aria-expanded', 'false');
    }

    stickyShareTrigger.addEventListener('click', function () {
      stickySharePanel.classList.contains('is-open') ? closeSharePanel() : openSharePanel();
    });

    if (stickyShareClose) {
      stickyShareClose.addEventListener('click', closeSharePanel);
    }

    document.addEventListener('click', function (e) {
      if (!e.target.closest('#stickyShare')) closeSharePanel();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeSharePanel();
    });
  }

  if (typeof jQuery !== 'undefined' && jQuery.fn.slick) {
    const $slider = jQuery('.js-property-detail-slider');
    if ($slider.length) {
      const sliderEl = $slider[0];
      $slider.slick({
        variableWidth: true,
        infinite: true,
        arrows: false,
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3500,
        speed: 400,
        centerMode: true,
        swipeToSlide: true,
        // Avoid aria-hidden vs focus on slides (Chrome warnings); custom arrows are outside slider
        accessibility: false,
        focusOnChange: false,
        pauseOnFocus: false,
      });
      const galleryRoot = sliderEl.closest('.property-detail-gallery');
      if (galleryRoot) {
        galleryRoot.addEventListener(
          'click',
          function (e) {
            const btn = e.target && e.target.closest && e.target.closest('[data-gallery-prev], [data-gallery-next]');
            if (!btn || !galleryRoot.contains(btn)) return;
            e.preventDefault();
            try {
              if (btn.hasAttribute('data-gallery-prev')) {
                jQuery(sliderEl).slick('slickPrev');
              } else {
                jQuery(sliderEl).slick('slickNext');
              }
            } catch (err) {
              /* noop */
            }
          },
          true
        );
      }
    }
  }

  (function propertyDetailGalleryFancybox() {
    if (typeof Fancybox === 'undefined') return;
    const btn = document.querySelector('.js-property-gallery-fancybox');
    const galleryRoot = btn && btn.closest('.property-detail-gallery');
    const slider = galleryRoot && galleryRoot.querySelector('.js-property-detail-slider');
    if (!btn || !slider) return;

    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const imgs = slider.querySelectorAll('.slick-slide:not(.slick-cloned) img');
      const slides = [];
      imgs.forEach(function (img) {
        const src = img.currentSrc || img.getAttribute('src');
        if (!src) return;
        const alt = (img.getAttribute('alt') || '').trim();
        const item = { src: src, type: 'image' };
        if (alt) item.caption = alt;
        slides.push(item);
      });
      if (!slides.length) return;
      let startIndex = 0;
      if (typeof jQuery !== 'undefined' && jQuery.fn.slick && jQuery(slider).hasClass('slick-initialized')) {
        try {
          startIndex = jQuery(slider).slick('slickCurrentSlide') || 0;
        } catch (err) {
          startIndex = 0;
        }
      }
      Fancybox.show(slides, {
        startIndex: startIndex,
        Carousel: { infinite: true },
      });
    });
  })();

  (function propertyDetailPage() {
    const calcRoot = document.querySelector('.js-payment-calc');
    if (!calcRoot) return;

    const priceEl = calcRoot.querySelector('[name="calc_price"]');
    const downPctEl = calcRoot.querySelector('[name="calc_down_pct"]');
    const downAmtEl = calcRoot.querySelector('[name="calc_down_amount"]');
    const rateEl = calcRoot.querySelector('[name="calc_rate"]');
    const yearsEl = calcRoot.querySelector('[name="calc_years"]');
    const taxEl = calcRoot.querySelector('[name="calc_tax"]');
    const hoaEl = calcRoot.querySelector('[name="calc_hoa"]');
    const outMonthly = calcRoot.querySelector('.js-calc-monthly');
    const piDisplay = calcRoot.querySelector('.js-calc-pi-monthly');
    const donut = calcRoot.querySelector('.js-calc-donut');
    const legPi = calcRoot.querySelector('.js-calc-legend-pi');
    const legTax = calcRoot.querySelector('.js-calc-legend-tax');
    const legHoa = calcRoot.querySelector('.js-calc-legend-hoa');

    let downSyncFrom = null;

    function fmtCurrency(n) {
      return (
        new Intl.NumberFormat('en-AE', { maximumFractionDigits: 2 }).format(Math.round(n * 100) / 100) +
        ' د.إ'
      );
    }

    function monthlyPayment(principal, annualRate, years) {
      const r = annualRate / 100 / 12;
      const n = Math.max(1, years) * 12;
      if (principal <= 0) return 0;
      if (r <= 0) return principal / n;
      return (principal * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
    }

    function update() {
      const price = parseFloat(priceEl && priceEl.value) || 0;
      let downPct = parseFloat(downPctEl && downPctEl.value) || 0;
      let down = 0;

      if (downSyncFrom === 'amount' && downAmtEl) {
        down = Math.min(Math.max(0, parseFloat(downAmtEl.value) || 0), price);
        downPct = price > 0 ? (down / price) * 100 : 0;
        if (downPctEl) downPctEl.value = (Math.round(downPct * 100) / 100).toFixed(2);
      } else {
        downPct = Math.min(Math.max(0, downPct), 100);
        if (downPctEl) downPctEl.value = (Math.round(downPct * 100) / 100).toString();
        down = price * (downPct / 100);
        if (downAmtEl) downAmtEl.value = String(Math.round(down));
      }

      downSyncFrom = null;

      const rate = parseFloat(rateEl && rateEl.value) || 0;
      const years = parseFloat(yearsEl && yearsEl.value) || 25;
      const taxAnnual = parseFloat(taxEl && taxEl.value) || 0;
      const hoaAnnual = parseFloat(hoaEl && hoaEl.value) || 0;

      const loan = Math.max(0, price - down);
      const pay = monthlyPayment(loan, rate, years);
      const taxMo = taxAnnual / 12;
      const hoaMo = hoaAnnual / 12;
      const total = pay + taxMo + hoaMo;

      if (outMonthly) outMonthly.textContent = price > 0 ? fmtCurrency(total) : '—';
      if (piDisplay) piDisplay.textContent = price > 0 ? fmtCurrency(pay) : '—';
      if (legPi) legPi.textContent = price > 0 ? fmtCurrency(pay) : '—';
      if (legTax) legTax.textContent = price > 0 ? fmtCurrency(taxMo) : '—';
      if (legHoa) legHoa.textContent = price > 0 ? fmtCurrency(hoaMo) : '—';

      if (donut && price > 0 && total > 0) {
        const d1 = (pay / total) * 360;
        const d2 = d1 + (taxMo / total) * 360;
        donut.style.background =
          'conic-gradient(#2A559C 0deg ' +
          d1 +
          'deg, #5BA3E8 ' +
          d1 +
          'deg ' +
          d2 +
          'deg, #DC2626 ' +
          d2 +
          'deg 360deg)';
      } else if (donut) {
        donut.style.background =
          'conic-gradient(#2A559C 0deg 120deg, #5BA3E8 120deg 240deg, #DC2626 240deg 360deg)';
      }
    }

    function onDownPctInput() {
      downSyncFrom = 'pct';
      update();
    }
    function onDownAmtInput() {
      downSyncFrom = 'amount';
      update();
    }

    [priceEl, rateEl, yearsEl, taxEl, hoaEl].forEach(function (el) {
      if (el) el.addEventListener('input', update);
    });
    if (downPctEl) downPctEl.addEventListener('input', onDownPctInput);
    if (downAmtEl) downAmtEl.addEventListener('input', onDownAmtInput);
    update();
  })();

  (function propertyDetailMapCard() {
    const tablist = document.querySelector('.js-property-map-tabs');
    const panelRoot = document.querySelector('.js-map-map-panels');
    const mapCard = document.querySelector('.property-detail-body__map .property-detail-map-card');

    if (tablist) {
      const tabs = tablist.querySelectorAll('[role="tab"]');

      function activateMapTab(btn) {
        if (!btn || !tablist.contains(btn)) return;
        const panelId = btn.getAttribute('aria-controls');
        tabs.forEach(function (t) {
          t.classList.remove('is-active');
          t.setAttribute('aria-selected', 'false');
          t.setAttribute('tabindex', '-1');
        });
        btn.classList.add('is-active');
        btn.setAttribute('aria-selected', 'true');
        btn.setAttribute('tabindex', '0');

        if (panelRoot && panelId) {
          const panels = panelRoot.querySelectorAll('.property-detail-map-card__map-panel');
          panels.forEach(function (panel) {
            if (panel.id === panelId) {
              panel.removeAttribute('hidden');
            } else {
              panel.setAttribute('hidden', '');
            }
          });
        }
      }

      tablist.addEventListener('click', function (e) {
        const btn = e.target && e.target.closest && e.target.closest('[role="tab"]');
        if (!btn || !tablist.contains(btn)) return;
        e.preventDefault();
        activateMapTab(btn);
      });

      const initial = tablist.querySelector('[role="tab"].is-active');
      if (initial) activateMapTab(initial);
    }

    if (mapCard) {
      mapCard.addEventListener('click', function (e) {
        const closeBtn = e.target && e.target.closest && e.target.closest('.js-map-popup-close');
        if (!closeBtn || !mapCard.contains(closeBtn)) return;
        const frame = closeBtn.closest('.property-detail-map-card__frame');
        const popup = frame && frame.querySelector('.js-map-popup');
        if (popup) popup.classList.add('is-hidden');
      });
    }
  })();

  (function initCareerAttachment() {
    const input = document.getElementById("career-attachment");
    const nameEl = document.getElementById("career-attachment-filename");
    if (!input || !nameEl) return;
    input.addEventListener("change", function () {
      const f = this.files && this.files[0];
      if (f) {
        nameEl.textContent = f.name;
        nameEl.removeAttribute("hidden");
      } else {
        nameEl.textContent = "";
        nameEl.setAttribute("hidden", "");
      }
    });
  })();

});



// ==========================contry dorp down=============================

function initializePhoneInput(selector) {
  const shippingFormWrapper = document.querySelector(selector + ' .phone_number');
  if (shippingFormWrapper !== null) {
    const phoneInput = window.intlTelInput(shippingFormWrapper, {
      preferredCountries: ["ae", "sa", "kw", "bh", "qa", "om"],
      excludeCountries: ["ru", "cu", "sy", "ir", "sd", "ss", "kp", "ye", "KR", "UA"],
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });
    $(selector + ' .phone_number').on('blur', function () {
      contactPhone(selector, phoneInput);
    });
  }
}

function contactPhone(selector, phoneInput) {
  let phoneNumber = phoneInput.getNumber(); // Get full international number

  if (phoneNumber.startsWith('+')) {
    let countryCode = phoneInput.getSelectedCountryData().dialCode; // Get country code only
    let localNumber = phoneNumber.replace('+' + countryCode, ''); // Remove country code from full number
    phoneNumber = `+${countryCode}-${localNumber}`; // Add separator
  }

  $(selector + ' .phone_number').val(phoneNumber);
}

initializePhoneInput("#careerApplyModal");
initializePhoneInput("#siteEnquiryForm");
initializePhoneInput(".contact-form");

