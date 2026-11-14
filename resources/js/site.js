/* ============================================================
   Webveo — main.js  (vanilla, no dependencies)
   ============================================================ */
(function () {
  "use strict";

  /* ---------- Inline SVG icon set ---------- */
  var P = 'stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"';
  var ICONS = {
    arrow: '<path d="M5 12h14M13 6l6 6-6 6" ' + P + '/>',
    "arrow-ur": '<path d="M7 17 17 7M8 7h9v9" ' + P + '/>',
    check: '<path d="M20 6 9 17l-5-5" ' + P + '/>',
    code: '<path d="m16 18 6-6-6-6M8 6l-6 6 6 6" ' + P + '/>',
    search: '<circle cx="11" cy="11" r="7" ' + P + '/><path d="m21 21-4.3-4.3" ' + P + '/>',
    share: '<circle cx="18" cy="5" r="3" ' + P + '/><circle cx="6" cy="12" r="3" ' + P + '/><circle cx="18" cy="19" r="3" ' + P + '/><path d="m8.6 13.5 6.8 4M15.4 6.5 8.6 10.5" ' + P + '/>',
    map: '<path d="M9 20 3 17V4l6 3 6-3 6 3v13l-6-3-6 3zM9 7v13M15 4v13" ' + P + '/>',
    video: '<rect x="2" y="6" width="14" height="12" rx="2" ' + P + '/><path d="m22 8-6 4 6 4V8z" ' + P + '/>',
    mobile: '<rect x="7" y="2" width="10" height="20" rx="2.5" ' + P + '/><path d="M11 18h2" ' + P + '/>',
    brush: '<path d="M9.5 14.5 3 21M14 4l6 6-8.5 6.5a3 3 0 0 1-4-4L14 4z" ' + P + '/>',
    phone: '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z" ' + P + '/>',
    mail: '<rect x="2" y="4" width="20" height="16" rx="2" ' + P + '/><path d="m2 7 10 6 10-6" ' + P + '/>',
    whatsapp: '<path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2z" ' + P + '/><path d="M8.5 7.5c-.3 0-.6.1-.8.4-.3.3-1 1-1 2.3 0 1.4 1 2.7 1.2 2.9.1.2 2 3 4.8 4.1 2.4 1 2.9.8 3.4.7.5 0 1.6-.6 1.8-1.3.2-.6.2-1.2.2-1.3-.1-.1-.3-.2-.6-.3l-2-1c-.3-.1-.5-.1-.7.1l-.7.9c-.1.2-.3.2-.5.1-.7-.3-1.5-.7-2.3-1.6-.6-.7-1-1.4-1.1-1.7 0-.2 0-.3.1-.4l.5-.6c.2-.2.2-.3.3-.5 0-.2 0-.4-.1-.5l-.8-2c-.2-.5-.4-.5-.6-.5z" fill="currentColor"/>',
    menu: '<path d="M4 6h16M4 12h16M4 18h16" ' + P + '/>',
    plus: '<path d="M12 5v14M5 12h14" ' + P + '/>',
    bolt: '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z" ' + P + '/>',
    gauge: '<path d="M12 14 16 9M3.3 17a9 9 0 1 1 17.4 0" ' + P + '/><circle cx="12" cy="14" r="1.4" fill="currentColor" stroke="none"/>',
    cursor: '<path d="m4 4 7 16 2.5-6.5L20 11 4 4z" ' + P + '/>',
    target: '<circle cx="12" cy="12" r="9" ' + P + '/><circle cx="12" cy="12" r="5" ' + P + '/><circle cx="12" cy="12" r="1" fill="currentColor" stroke="none"/>',
    rocket: '<path d="M5 15c-1.5 1.5-2 5-2 5s3.5-.5 5-2a3 3 0 0 0-3-3z" ' + P + '/><path d="M9 11a15 15 0 0 1 8-8c2 0 3 1 3 3a15 15 0 0 1-8 8l-3-3z" ' + P + '/><circle cx="15" cy="9" r="1.4" ' + P + '/>',
    trending: '<path d="m3 17 6-6 4 4 8-8M15 7h6v6" ' + P + '/>',
    shield: '<path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3z" ' + P + '/>',
    clock: '<circle cx="12" cy="12" r="9" ' + P + '/><path d="M12 7v5l3 2" ' + P + '/>',
    layout: '<rect x="3" y="3" width="18" height="18" rx="2" ' + P + '/><path d="M3 9h18M9 21V9" ' + P + '/>',
    location: '<path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z" ' + P + '/><circle cx="12" cy="10" r="2.6" ' + P + '/>',
    star: '<path d="m12 2 3 6.5 7 .8-5 4.8 1.3 7-6.3-3.4L5.7 21 7 14.1 2 9.3l7-.8L12 2z" fill="currentColor" stroke="none"/>',
    facebook: '<path d="M14 9V7c0-1 .5-1.5 1.7-1.5H17V2.5h-2.4C11.8 2.5 11 4.2 11 6.3V9H8.5v3H11v9h3v-9h2.3l.4-3H14z" fill="currentColor" stroke="none"/>',
    instagram: '<rect x="3" y="3" width="18" height="18" rx="5" ' + P + '/><circle cx="12" cy="12" r="4" ' + P + '/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>',
    linkedin: '<rect x="3" y="3" width="18" height="18" rx="2" ' + P + '/><path d="M7 10v7M7 7v.01M11 17v-4a2 2 0 0 1 4 0v4M11 10v7" ' + P + '/>',
    x: '<path d="M4 4l16 16M20 4 4 20" ' + P + '/>',
    youtube: '<rect x="2.5" y="6" width="19" height="12" rx="3.5" ' + P + '/><path d="m10 9 5 3-5 3V9z" fill="currentColor" stroke="none"/>',
    telegram: '<path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z" ' + P + '/>',
    briefcase: '<rect x="2.5" y="7" width="19" height="13" rx="2" ' + P + '/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M2.5 12h19" ' + P + '/>',
    users: '<circle cx="9" cy="8" r="3.4" ' + P + '/><path d="M3 20a6 6 0 0 1 12 0M16 5.5a3 3 0 0 1 0 5.6M21 20a6 6 0 0 0-4-5.7" ' + P + '/>'
  };

  function renderIcons(root) {
    (root || document).querySelectorAll("[data-icon]").forEach(function (el) {
      if (el.__done) return;
      var name = el.getAttribute("data-icon");
      if (!ICONS[name]) return;
      var size = el.getAttribute("data-size") || 22;
      el.innerHTML = '<svg viewBox="0 0 24 24" width="' + size + '" height="' + size + '" aria-hidden="true">' + ICONS[name] + "</svg>";
      el.__done = true;
    });
  }

  /* ---------- Header scroll ---------- */
  function initHeader() {
    var header = document.querySelector(".site-header");
    if (!header) return;
    var onScroll = function () { header.classList.toggle("scrolled", window.scrollY > 20); };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ---------- Mobile drawer ---------- */
  function initDrawer() {
    var toggle = document.querySelector(".nav-toggle");
    var drawer = document.querySelector(".drawer");
    var backdrop = document.querySelector(".drawer-backdrop");
    var close = document.querySelector(".drawer-close");
    if (!toggle || !drawer) return;
    var open = function () { drawer.classList.add("open"); backdrop.classList.add("open"); document.body.style.overflow = "hidden"; };
    var shut = function () { drawer.classList.remove("open"); backdrop.classList.remove("open"); document.body.style.overflow = ""; };
    toggle.addEventListener("click", open);
    if (close) close.addEventListener("click", shut);
    if (backdrop) backdrop.addEventListener("click", shut);
    drawer.querySelectorAll("a").forEach(function (a) { a.addEventListener("click", shut); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") shut(); });
  }

  /* ---------- Reveal on scroll ---------- */
  function initReveal() {
    var els = document.querySelectorAll("[data-reveal]");
    if (!("IntersectionObserver" in window) || !els.length) { els.forEach(function (e) { e.classList.add("in"); }); return; }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); } });
    }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
    els.forEach(function (e) { io.observe(e); });
  }

  /* ---------- Counter animation ---------- */
  function initCounters() {
    var nums = document.querySelectorAll("[data-count]");
    if (!nums.length) return;
    var run = function (el) {
      var target = parseFloat(el.getAttribute("data-count"));
      var dur = 1400, start = null;
      var step = function (ts) {
        if (!start) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased);
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = target;
      };
      requestAnimationFrame(step);
    };
    if (!("IntersectionObserver" in window)) { nums.forEach(run); return; }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { run(en.target); io.unobserve(en.target); } });
    }, { threshold: 0.5 });
    nums.forEach(function (n) { io.observe(n); });
  }

  /* ---------- FAQ accordion ---------- */
  function initFaq() {
    document.querySelectorAll(".faq-item").forEach(function (item) {
      var q = item.querySelector(".faq-q");
      var a = item.querySelector(".faq-a");
      if (!q || !a) return;
      q.setAttribute("aria-expanded", "false");
      q.addEventListener("click", function () {
        var isOpen = item.classList.contains("open");
        document.querySelectorAll(".faq-item.open").forEach(function (o) {
          o.classList.remove("open"); o.querySelector(".faq-a").style.maxHeight = null;
          o.querySelector(".faq-q").setAttribute("aria-expanded", "false");
        });
        if (!isOpen) { item.classList.add("open"); a.style.maxHeight = a.scrollHeight + "px"; q.setAttribute("aria-expanded", "true"); }
      });
    });
  }

  /* ---------- Forms (client-side only, no backend) ---------- */
  function initForms() {
    document.querySelectorAll("form[data-demo-form]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!form.checkValidity()) { form.reportValidity(); return; }
        var ok = form.querySelector(".form-success");
        if (ok) { ok.classList.add("show"); ok.textContent = "✓ Thank you! Your request has been captured. (Demo form — connect to your backend or email service to receive submissions.)"; }
        form.querySelectorAll("input, textarea, select").forEach(function (f) { if (f.type !== "checkbox") f.value = ""; });
        if (ok) ok.scrollIntoView({ behavior: "smooth", block: "center" });
      });
    });
  }

  /* ---------- Footer year ---------- */
  function initYear() {
    document.querySelectorAll("[data-year]").forEach(function (el) { el.textContent = new Date().getFullYear(); });
  }

  /* ---------- Audit demo scanner ---------- */
  function initAudit() {
    var form = document.querySelector("form[data-audit-form]");
    if (!form) return;
    var progress = document.querySelector(".audit-progress");
    var results = document.querySelector(".audit-results");
    var bar = document.querySelector("[data-audit-bar]");
    var statusEl = document.querySelector("[data-audit-status]");
    var btn = document.querySelector("[data-audit-btn]");
    var steps = ["Fetching page & resources…", "Measuring Core Web Vitals…", "Analyzing on-page SEO…", "Checking mobile & schema…", "Compiling your roadmap…"];
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.reportValidity(); return; }
      if (results) results.hidden = true;
      if (progress) progress.hidden = false;
      if (btn) { btn.disabled = true; btn.style.opacity = ".6"; }
      var p = 0, i = 0;
      var t = setInterval(function () {
        p = Math.min(p + Math.random() * 16 + 6, 100);
        if (bar) bar.style.width = p + "%";
        if (statusEl && steps[i]) statusEl.textContent = steps[i];
        i++;
        if (p >= 100) {
          clearInterval(t);
          setTimeout(showResults, 450);
        }
      }, 550);
    });
    function rnd(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }
    function showResults() {
      if (progress) progress.hidden = true;
      if (results) results.hidden = false;
      if (btn) { btn.disabled = false; btn.style.opacity = ""; }
      var score = rnd(58, 86);
      var grades = { 58: "C", 70: "B", 80: "A" };
      var grade = score >= 80 ? "A" : score >= 68 ? "B" : "C";
      var sEl = document.querySelector("[data-audit-score]");
      var gEl = document.querySelector("[data-audit-grade]");
      if (sEl) sEl.textContent = score + "/100";
      if (gEl) gEl.textContent = grade;
      ["m1", "m2", "m3", "m4"].forEach(function (k) {
        var el = document.querySelector("[data-audit-" + k + "]");
        if (el) setTimeout(function () { el.style.width = rnd(52, 96) + "%"; }, 150);
      });
      if (results) results.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }

  /* ---------- 3D tilt on cards (desktop pointers only) ---------- */
  function initTilt() {
    var fine = window.matchMedia && window.matchMedia("(hover:hover) and (pointer:fine)").matches;
    var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!fine || reduce) return;
    var cards = document.querySelectorAll(".feature, .pstep, .testi");
    cards.forEach(function (card) {
      card.addEventListener("mousemove", function (e) {
        var r = card.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width - 0.5;
        var py = (e.clientY - r.top) / r.height - 0.5;
        card.classList.add("tilt-active");
        card.style.transform = "perspective(760px) rotateX(" + (-py * 6).toFixed(2) +
          "deg) rotateY(" + (px * 7).toFixed(2) + "deg) translateY(-6px)";
      });
      card.addEventListener("mouseleave", function () {
        card.style.transform = "";
        setTimeout(function () { card.classList.remove("tilt-active"); }, 200);
      });
    });
  }

  /* ---------- Init ---------- */
  function init() {
    renderIcons();
    initHeader();
    initDrawer();
    initReveal();
    initCounters();
    initFaq();
    initForms();
    initAudit();
    initTilt();
    initYear();
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();
})();
