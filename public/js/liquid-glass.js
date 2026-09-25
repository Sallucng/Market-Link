/*!
 * liquid-glass.js — Pure iOS 27 Liquid Glass Refraction Engine
 * Inspired by Apple VisionOS / iOS Liquid Glass and open-source GitHub implementations:
 * https://github.com/deepika-builds/liquid-glass & https://github.com/ybouane/liquidglass
 *
 * Provides real-time optical refraction, chromatic aberration (RGB prism fringe),
 * cursor-tracked specular glare, and graceful frosted-glass fallback.
 */
(function (global) {
  "use strict";

  const SVG_NS = "http://www.w3.org/2000/svg";
  let uid = 0;
  let svgDefs = null;

  // Check browser support for SVG backdrop filters (Chromium vs Safari/Firefox fallback)
  const isChromiumSupported = (() => {
    if (typeof window === "undefined" || !window.navigator) return false;
    const ua = navigator.userAgent;
    const isSafari = /Safari/.test(ua) && !/Chrome|Chromium|Edg/.test(ua);
    const isFirefox = /Firefox/.test(ua);
    if (isSafari || isFirefox) return false;
    if (typeof CSS !== "undefined" && !CSS.supports("backdrop-filter", "url(#lg)")) return false;
    try {
      const c = document.createElement("canvas");
      c.width = c.height = 4;
      c.getContext("2d").getImageData(0, 0, 1, 1);
      return true;
    } catch (_) {
      return false;
    }
  })();

  function ensureDefs() {
    if (svgDefs && svgDefs.parentNode) return svgDefs;
    const svg = document.createElementNS(SVG_NS, "svg");
    svg.setAttribute("width", "0");
    svg.setAttribute("height", "0");
    svg.setAttribute("aria-hidden", "true");
    svg.style.position = "absolute";
    svg.style.pointerEvents = "none";
    svg.style.overflow = "hidden";
    svgDefs = document.createElementNS(SVG_NS, "defs");
    svg.appendChild(svgDefs);
    document.body.appendChild(svg);
    return svgDefs;
  }

  /**
   * Generates a dynamic 2-channel displacement map using canvas gradient difference.
   * A red ramp encodes X displacement, a blue ramp encodes Y displacement.
   * An inset neutral-gray rounded rect neutralizes the interior so text remains 100% legible.
   */
  function makeDisplacementMap(w, h, radius, border, mapBlur) {
    const canvas = document.createElement("canvas");
    canvas.width = Math.max(8, Math.round(w));
    canvas.height = Math.max(8, Math.round(h));
    const ctx = canvas.getContext("2d");

    // X displacement ramp (Red)
    const gx = ctx.createLinearGradient(0, 0, canvas.width, 0);
    gx.addColorStop(0, "rgb(0,0,0)");
    gx.addColorStop(1, "rgb(255,0,0)");
    ctx.fillStyle = gx;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Y displacement ramp (Blue) via difference blending
    const gy = ctx.createLinearGradient(0, 0, 0, canvas.height);
    gy.addColorStop(0, "rgb(0,0,0)");
    gy.addColorStop(1, "rgb(0,0,255)");
    ctx.globalCompositeOperation = "difference";
    ctx.fillStyle = gy;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Neutral interior mask in 50% neutral gray (RGB 128,128,128)
    ctx.globalCompositeOperation = "source-over";
    const inset = Math.max(4, border * Math.min(canvas.width, canvas.height));
    ctx.filter = `blur(${mapBlur}px)`;
    ctx.fillStyle = "rgba(128,128,128,0.94)";
    ctx.beginPath();
    const r = Math.max(radius - inset, 4);
    if (typeof ctx.roundRect === "function") {
      ctx.roundRect(inset, inset, canvas.width - inset * 2, canvas.height - inset * 2, r);
    } else {
      ctx.rect(inset, inset, canvas.width - inset * 2, canvas.height - inset * 2);
    }
    ctx.fill();
    ctx.filter = "none";

    return canvas.toDataURL();
  }

  /**
   * Constructs the 3-pass chromatic aberration SVG filter
   */
  function buildLiquidFilter(id, scales) {
    const filter = document.createElementNS(SVG_NS, "filter");
    filter.setAttribute("id", id);
    filter.setAttribute("x", "0");
    filter.setAttribute("y", "0");
    filter.setAttribute("width", "100%");
    filter.setAttribute("height", "100%");
    filter.setAttribute("color-interpolation-filters", "sRGB");

    const feImage = document.createElementNS(SVG_NS, "feImage");
    feImage.setAttribute("x", "0");
    feImage.setAttribute("y", "0");
    feImage.setAttribute("result", "map");
    feImage.setAttribute("preserveAspectRatio", "none");
    filter.appendChild(feImage);

    const keepMatrices = [
      "1 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 1 0", // Red
      "0 0 0 0 0  0 1 0 0 0  0 0 0 0 0  0 0 0 1 0", // Green
      "0 0 0 0 0  0 0 0 0 0  0 0 1 0 0  0 0 0 1 0", // Blue
    ];

    const channels = [];
    for (let i = 0; i < 3; i++) {
      const disp = document.createElementNS(SVG_NS, "feDisplacementMap");
      disp.setAttribute("in", "SourceGraphic");
      disp.setAttribute("in2", "map");
      disp.setAttribute("scale", scales[i]);
      disp.setAttribute("xChannelSelector", "R");
      disp.setAttribute("yChannelSelector", "B");
      disp.setAttribute("result", "d" + i);
      filter.appendChild(disp);

      const cm = document.createElementNS(SVG_NS, "feColorMatrix");
      cm.setAttribute("in", "d" + i);
      cm.setAttribute("type", "matrix");
      cm.setAttribute("values", keepMatrices[i]);
      cm.setAttribute("result", "c" + i);
      filter.appendChild(cm);
      channels.push("c" + i);
    }

    const blend1 = document.createElementNS(SVG_NS, "feBlend");
    blend1.setAttribute("in", channels[0]);
    blend1.setAttribute("in2", channels[1]);
    blend1.setAttribute("mode", "screen");
    blend1.setAttribute("result", "c01");
    filter.appendChild(blend1);

    const blend2 = document.createElementNS(SVG_NS, "feBlend");
    blend2.setAttribute("in", "c01");
    blend2.setAttribute("in2", channels[2]);
    blend2.setAttribute("mode", "screen");
    blend2.setAttribute("result", "c012");
    filter.appendChild(blend2);

    ensureDefs().appendChild(filter);
    return { filter, feImage };
  }

  function resolveBorderRadius(el, w, h, override) {
    if (override != null) return override;
    const raw = getComputedStyle(el).borderTopLeftRadius || "20px";
    const v = parseFloat(raw) || 20;
    return raw.trim().endsWith("%") ? (v / 100) * Math.min(w, h) : v;
  }

  /**
   * Applies the iOS 27 Liquid Glass optical refraction and reactive physics to an element.
   */
  function liquidGlass(el, opts) {
    if (!el || el.__liquidGlassActive) return el.__liquidGlassActive;

    const o = Object.assign(
      {
        scale: -90,          // Displacement strength: negative bends outward into a magnifying liquid lens
        chroma: 8,           // Prism rainbow fringe on rims
        border: 0.08,        // Neutral interior boundary
        mapBlur: 14,         // Curvature softness
        blur: 16,            // Frosted blur inside glass
        saturate: 1.85,      // Vibrant iOS saturation boost
        contrast: 1.05,
        radius: null,
        fallbackBlur: 20,
        tilt: true,          // 3D tilt perspective
        specularTracker: true // Real-time pointer tracking
      },
      opts
    );

    el.classList.add("ios27-liquid-glass");

    // Tracking pointer for dynamic specular glare
    if (o.specularTracker) {
      el.addEventListener("pointermove", (e) => {
        const rect = el.getBoundingClientRect();
        const gx = ((e.clientX - rect.left) / rect.width) * 100;
        const gy = ((e.clientY - rect.top) / rect.height) * 100;
        el.style.setProperty("--gx", `${gx.toFixed(1)}%`);
        el.style.setProperty("--gy", `${gy.toFixed(1)}%`);

        if (o.tilt) {
          const tiltX = ((gy - 50) / 50) * -6; // max 6deg tilt
          const tiltY = ((gx - 50) / 50) * 6;
          el.style.setProperty("--tilt-x", `${tiltX.toFixed(2)}deg`);
          el.style.setProperty("--tilt-y", `${tiltY.toFixed(2)}deg`);
        }
      });

      el.addEventListener("pointerleave", () => {
        el.style.setProperty("--gx", "50%");
        el.style.setProperty("--gy", "20%");
        el.style.setProperty("--tilt-x", "0deg");
        el.style.setProperty("--tilt-y", "0deg");
      });
    }

    if (!isChromiumSupported) {
      const fallbackStyle = `blur(${o.fallbackBlur}px) saturate(${o.saturate}) contrast(${o.contrast})`;
      el.style.backdropFilter = fallbackStyle;
      el.style.webkitBackdropFilter = fallbackStyle;
      el.classList.add("lg-fallback-mode");

      const handle = {
        supported: false,
        refresh: () => {},
        destroy: () => {
          el.style.backdropFilter = "";
          el.style.webkitBackdropFilter = "";
          el.classList.remove("lg-fallback-mode");
          delete el.__liquidGlassActive;
        }
      };
      el.__liquidGlassActive = handle;
      return handle;
    }

    const id = `lg-ios27-${++uid}`;
    const scales = [o.scale, o.scale + o.chroma, o.scale + 2 * o.chroma];
    const parts = buildLiquidFilter(id, scales);

    function refresh() {
      const w = el.offsetWidth;
      const h = el.offsetHeight;
      if (!w || !h) return;
      const radius = resolveBorderRadius(el, w, h, o.radius);
      const mapUri = makeDisplacementMap(w, h, radius, o.border, o.mapBlur);
      parts.feImage.setAttribute("href", mapUri);
      parts.feImage.setAttribute("width", w);
      parts.feImage.setAttribute("height", h);
    }

    refresh();
    el.style.backdropFilter = `url(#${id}) blur(${o.blur}px) saturate(${o.saturate}) contrast(${o.contrast})`;
    el.style.webkitBackdropFilter = `url(#${id}) blur(${o.blur}px) saturate(${o.saturate}) contrast(${o.contrast})`;

    let resizeTimer = null;
    const ro = new ResizeObserver(() => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(refresh, 100);
    });
    ro.observe(el);

    const handle = {
      supported: true,
      id,
      refresh,
      destroy: () => {
        ro.disconnect();
        clearTimeout(resizeTimer);
        parts.filter.remove();
        el.style.backdropFilter = "";
        el.style.webkitBackdropFilter = "";
        delete el.__liquidGlassActive;
      }
    };
    el.__liquidGlassActive = handle;
    return handle;
  }

  /**
   * Draggable iOS 27 Liquid Glass Dynamic Island Widget
   */
  class IOS27GlassIsland {
    constructor(el, opts = {}) {
      this.el = el;
      if (!this.el) return;
      this.opts = Object.assign({ stiffness: 0.14, damping: 0.76 }, opts);
      this.measure();

      // Initial placement at bottom-right or center
      const startX = window.innerWidth - this.w - 24;
      const startY = window.innerHeight - this.h - 96;
      this.x = startX;
      this.y = startY;
      this.tx = startX;
      this.ty = startY;
      this.vx = 0;
      this.vy = 0;
      this.dragging = false;
      this.grabDX = 0;
      this.grabDY = 0;

      this.el.addEventListener("pointerdown", (e) => this.onDown(e));
      window.addEventListener("pointermove", (e) => this.onMove(e));
      window.addEventListener("pointerup", (e) => this.onUp(e));
      window.addEventListener("pointercancel", (e) => this.onUp(e));
      window.addEventListener("resize", () => this.onResize());

      this.clamp();
      this.render();
      requestAnimationFrame(() => this.tick());

      // Apply liquid glass optics
      this.glassEffect = liquidGlass(this.el, { scale: -120, chroma: 10, blur: 18 });
    }

    measure() {
      this.w = this.el.offsetWidth || 280;
      this.h = this.el.offsetHeight || 60;
    }

    onDown(e) {
      if (e.target.closest("a, button, input, select, textarea")) return;
      this.dragging = true;
      this.el.classList.add("is-dragging");
      this.grabDX = e.clientX - this.tx;
      this.grabDY = e.clientY - this.ty;
      try {
        this.el.setPointerCapture(e.pointerId);
      } catch (_) {}
    }

    onMove(e) {
      if (!this.dragging) return;
      this.tx = e.clientX - this.grabDX;
      this.ty = e.clientY - this.grabDY;
      this.clamp();
    }

    onUp(e) {
      this.dragging = false;
      this.el.classList.remove("is-dragging");
      try {
        if (this.el.hasPointerCapture(e.pointerId)) {
          this.el.releasePointerCapture(e.pointerId);
        }
      } catch (_) {}
    }

    onResize() {
      this.measure();
      this.clamp();
    }

    clamp() {
      const margin = 16;
      this.tx = Math.min(Math.max(this.tx, margin), window.innerWidth - this.w - margin);
      this.ty = Math.min(Math.max(this.ty, margin), window.innerHeight - this.h - margin);
    }

    tick() {
      this.vx = (this.vx + (this.tx - this.x) * this.opts.stiffness) * this.opts.damping;
      this.vy = (this.vy + (this.ty - this.y) * this.opts.stiffness) * this.opts.damping;
      this.x += this.vx;
      this.y += this.vy;
      this.render();
      requestAnimationFrame(() => this.tick());
    }

    render() {
      const speed = Math.hypot(this.vx, this.vy);
      const squash = Math.min(speed / 90, 0.09);
      const angle = Math.atan2(this.vy, this.vx);
      this.el.style.transform = `translate3d(${this.x}px, ${this.y}px, 0) rotate(${angle * 0.25}rad) scale(${1 + squash}, ${1 - squash * 0.8})`;
    }
  }

  // Auto-initialize all liquid glass elements on DOM ready
  function initAutoLiquidGlass() {
    const targets = document.querySelectorAll(".liquid-glass, .liquid-glass-card, .liquid-glass-nav, .ios27-glass");
    targets.forEach((el) => {
      liquidGlass(el);
    });

    const islandEl = document.getElementById("ios27-liquid-island");
    if (islandEl) {
      new IOS27GlassIsland(islandEl);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAutoLiquidGlass);
  } else {
    initAutoLiquidGlass();
  }

  global.liquidGlass = liquidGlass;
  global.IOS27GlassIsland = IOS27GlassIsland;
})(typeof window !== "undefined" ? window : this);
