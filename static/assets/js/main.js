(function () {
'use strict';

/* --------------------------------------------------------------------------
   Announcement banner — the close button hides it for the rest of the visit.
   -------------------------------------------------------------------------- */
const BANNER_KEY = 'estatein:banner-dismissed';

function initBanner() {
  const banner = document.querySelector('[data-banner]');
  if (!banner) return;

  try {
    if (sessionStorage.getItem(BANNER_KEY) === '1') banner.hidden = true;
  } catch (e) { /* storage unavailable: keep the banner visible */ }

  const close = banner.querySelector('[data-banner-close]');
  if (!close) return;
  close.addEventListener('click', () => {
    banner.hidden = true;
    try { sessionStorage.setItem(BANNER_KEY, '1'); } catch (e) { /* ignore */ }
  });
}

/* --------------------------------------------------------------------------
   Mobile navigation toggle (the menu is always visible on desktop).
   -------------------------------------------------------------------------- */
function initNav() {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = toggle && document.getElementById(toggle.getAttribute('aria-controls'));
  if (!toggle || !nav) return;

  const setOpen = open => {
    toggle.setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('nav-open', open);
  };

  toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
  nav.addEventListener('click', e => { if (e.target.closest('a')) setOpen(false); });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
      setOpen(false);
      toggle.focus();
    }
  });
}

/* --------------------------------------------------------------------------
   Carousel — a native scroll-snap track driven by the arrow buttons, with the
   "01 of 10" counter from the design.

   data-carousel-total="10"  The design shows a fixed total ("01 of 10",
                             "01 of 60") while only three items are drawn, so
                             the authored slides are repeated up to that total.
   -------------------------------------------------------------------------- */
const pad2 = n => String(n).padStart(2, '0');

class Carousel {
  constructor(root) {
    this.root = root;
    this.track = root.querySelector('[data-carousel-track]');
    this.prev = root.querySelector('[data-carousel-prev]');
    this.next = root.querySelector('[data-carousel-next]');
    this.current = root.querySelector('[data-carousel-current]');
    this.count = root.querySelector('[data-carousel-count]');
    this.footer = root.querySelector('.carousel__footer');
    this.originals = Array.from(this.track.children);
    this.total = Number(root.dataset.carouselTotal) || this.originals.length;
    this.filter = null;

    this.render();

    this.prev && this.prev.addEventListener('click', () => this.go(-1));
    this.next && this.next.addEventListener('click', () => this.go(1));

    let frame = 0;
    const schedule = () => {
      cancelAnimationFrame(frame);
      frame = requestAnimationFrame(() => this.update());
    };
    this.track.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule);
  }

  /** Rebuild the track from the original slides (optionally filtered). */
  render() {
    const items = this.filter ? this.originals.filter(this.filter) : this.originals;
    // Only pad up to the design total when nothing is filtered out.
    const target = this.filter ? items.length : Math.max(this.total, items.length);

    this.track.replaceChildren(...items);
    for (let i = items.length; i < target && items.length; i++) {
      const clone = items[i % items.length].cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      clone.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));
      clone.querySelectorAll('a, button, input, select, textarea').forEach(el => el.setAttribute('tabindex', '-1'));
      this.track.appendChild(clone);
    }

    this.track.scrollLeft = 0;
    this.size = target;
    // No results: hide the "01 of 00" counter and arrows.
    if (this.footer) this.footer.hidden = target === 0;
    this.update();
    return items.length;
  }

  setFilter(fn) {
    this.filter = fn;
    return this.render();
  }

  step() {
    const slide = this.track.children[0];
    if (!slide) return 0;
    const gap = parseFloat(getComputedStyle(this.track).columnGap) || 0;
    return slide.getBoundingClientRect().width + gap;
  }

  go(direction) {
    this.track.scrollBy({ left: direction * this.step(), behavior: 'smooth' });
  }

  update() {
    const step = this.step();
    const max = this.track.scrollWidth - this.track.clientWidth;
    const index = step ? Math.round(this.track.scrollLeft / step) : 0;

    if (this.current) this.current.textContent = pad2(Math.min(index + 1, Math.max(this.size, 1)));
    if (this.count) this.count.textContent = pad2(this.size);
    if (this.prev) this.prev.disabled = this.track.scrollLeft <= 1;
    if (this.next) this.next.disabled = this.track.scrollLeft >= max - 1;
  }
}

function initCarousels() {
  document.querySelectorAll('[data-carousel]').forEach(root => {
    root.carousel = new Carousel(root);
  });
}

/* --------------------------------------------------------------------------
   Property gallery — thumbnails, arrows and progress dashes all drive the same
   scroll-snap viewport.
   -------------------------------------------------------------------------- */
function initGallery() {
  const gallery = document.querySelector('[data-gallery]');
  if (!gallery) return;

  const track = gallery.querySelector('[data-gallery-track]');
  const images = Array.from(track.children);
  const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
  const prev = gallery.querySelector('[data-gallery-prev]');
  const next = gallery.querySelector('[data-gallery-next]');
  const dotsBox = gallery.querySelector('[data-gallery-dots]');

  const step = () => {
    const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
    return images[0].getBoundingClientRect().width + gap;
  };
  const perView = () => Math.max(1, Math.round(track.clientWidth / step()));
  const positions = () => Math.max(1, images.length - perView() + 1);
  const index = () => Math.round(track.scrollLeft / step());

  const goTo = i => {
    const target = Math.max(0, Math.min(i, positions() - 1));
    track.scrollTo({ left: target * step(), behavior: 'smooth' });
  };

  let dots = [];
  // The design always shows six progress dashes, whatever the photo count.
  const DOTS = Number(dotsBox && dotsBox.dataset.galleryDots) || 6;
  const buildDots = () => {
    if (!dotsBox) return;
    const count = DOTS;
    if (dots.length === count) return;
    dots = Array.from({ length: count }, () => {
      const dot = document.createElement('span');
      dot.className = 'gallery__dot';
      return dot;
    });
    dotsBox.replaceChildren(...dots);
  };

  const update = () => {
    buildDots();
    const i = index();
    const active = positions() > 1 ? Math.round(i / (positions() - 1) * (DOTS - 1)) : 0;
    dots.forEach((dot, n) => { dot.dataset.active = String(n === active); });
    thumbs.forEach((thumb, n) => {
      if (n === i) thumb.setAttribute('aria-current', 'true');
      else thumb.removeAttribute('aria-current');
    });
    if (prev) prev.disabled = i <= 0;
    if (next) next.disabled = i >= positions() - 1;
  };

  thumbs.forEach(thumb => thumb.addEventListener('click', () => goTo(Number(thumb.dataset.galleryThumb))));
  prev && prev.addEventListener('click', () => goTo(index() - 1));
  next && next.addEventListener('click', () => goTo(index() + 1));
  track.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') { e.preventDefault(); goTo(index() + 1); }
    if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(index() - 1); }
  });

  let frame = 0;
  const schedule = () => { cancelAnimationFrame(frame); frame = requestAnimationFrame(update); };
  track.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('resize', schedule);
  update();
}

/* --------------------------------------------------------------------------
   Office tabs (All / Regional / International) — filters the office cards.
   -------------------------------------------------------------------------- */
function initTabs() {
  document.querySelectorAll('[data-tabs]').forEach(root => {
    const tabs = Array.from(root.querySelectorAll('[data-tab]'));
    const items = Array.from(root.querySelectorAll('[data-tab-item]'));
    const empty = root.querySelector('[data-tab-empty]');

    const select = tab => {
      const key = tab.dataset.tab;
      tabs.forEach(t => {
        const on = t === tab;
        t.setAttribute('aria-selected', String(on));
        t.tabIndex = on ? 0 : -1;
      });
      let shown = 0;
      items.forEach(item => {
        const match = item.dataset.tabItem.split(' ').includes(key);
        item.hidden = !match;
        if (match) shown++;
      });
      if (empty) empty.hidden = shown > 0;
    };

    tabs.forEach((tab, i) => {
      tab.tabIndex = tab.getAttribute('aria-selected') === 'true' ? 0 : -1;
      tab.addEventListener('click', () => select(tab));
      tab.addEventListener('keydown', e => {
        const delta = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
        if (!delta) return;
        e.preventDefault();
        const nextTab = tabs[(i + delta + tabs.length) % tabs.length];
        nextTab.focus();
        select(nextTab);
      });
    });
  });
}

/* --------------------------------------------------------------------------
   Forms — inline validation + submission.

   WordPress renders each form with data-endpoint (admin-ajax.php) and a nonce,
   so it is sent with fetch and the reply is shown inline. Without JavaScript
   the same form posts to admin-post.php. The static preview has no backend,
   so it only validates and says so.
   -------------------------------------------------------------------------- */
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

function fieldIsValid(field) {
  if (field.type === 'checkbox') return !field.required || field.checked;
  const value = field.value.trim();
  if (field.required && !value) return false;
  if (field.type === 'email' && value && !EMAIL_RE.test(value)) return false;
  return true;
}

function setStatus(form, message, state) {
  const status = form.querySelector('.form-status');
  if (!status) return;
  status.textContent = message;
  status.dataset.state = state || '';
}

function initForms() {
  document.querySelectorAll('form[data-form]').forEach(form => {
    const fields = Array.from(form.querySelectorAll('input, select, textarea')).filter(f => f.type !== 'hidden');

    fields.forEach(field => {
      const clear = () => {
        if (field.getAttribute('aria-invalid') === 'true' && fieldIsValid(field)) field.removeAttribute('aria-invalid');
      };
      field.addEventListener('input', clear);
      field.addEventListener('change', clear);
    });

    form.addEventListener('submit', async event => {
      event.preventDefault();

      const invalid = fields.filter(field => !fieldIsValid(field));
      fields.forEach(field => field.removeAttribute('aria-invalid'));
      invalid.forEach(field => field.setAttribute('aria-invalid', 'true'));
      if (invalid.length) {
        const agree = invalid.find(f => f.type === 'checkbox');
        setStatus(form, agree && invalid.length === 1
          ? 'Please agree to the Terms of Use and Privacy Policy.'
          : 'Please check the highlighted fields.', 'error');
        invalid[0].focus();
        return;
      }

      const endpoint = form.dataset.endpoint;
      if (!endpoint) {
        setStatus(form, 'Thanks! This static preview does not send messages — the WordPress site delivers them to the Estatein team.', 'success');
        form.reset();
        return;
      }

      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
      setStatus(form, 'Sending…');
      try {
        const response = await fetch(endpoint, { method: 'POST', body: new FormData(form), credentials: 'same-origin' });
        const json = await response.json();
        if (!json.success) {
          // Highlight the fields the server rejected.
          ((json.data && json.data.fields) || []).forEach(name => {
            const field = form.elements[name];
            if (field && field.setAttribute) field.setAttribute('aria-invalid', 'true');
          });
          throw new Error((json.data && json.data.message) || 'Something went wrong.');
        }
        setStatus(form, json.data.message, 'success');
        form.reset();
      } catch (error) {
        setStatus(form, error.message || 'Something went wrong. Please try again.', 'error');
      } finally {
        if (submit) submit.disabled = false;
      }
    });
  });
}

/* --------------------------------------------------------------------------
   Property search — filters the "Discover a World of Possibilities" cards by
   keyword, location, type, price and size (data-* attributes on each slide).
   -------------------------------------------------------------------------- */
function inRange(value, range) {
  if (!range) return true;
  if (!value) return false;
  const [min, max] = range.split('-').map(n => (n === '' ? null : Number(n)));
  return (min === null || value >= min) && (max === null || value <= max);
}

function initPropertySearch() {
  const form = document.querySelector('[data-property-search]');
  const results = document.querySelector('[data-property-results]');
  // WordPress filters on the server (data-property-search="server"); the static build filters here.
  if (!form || form.dataset.propertySearch === 'server' || !results || !results.carousel) return;

  const empty = results.querySelector('[data-property-empty]');

  const apply = () => {
    const data = new FormData(form);
    const keyword = String(data.get('keyword') || '').trim().toLowerCase();
    const location = data.get('location');
    const type = data.get('type');
    const price = data.get('price');
    const size = data.get('size');
    const year = data.get('year');
    const active = keyword || location || type || price || size || year;

    const shown = results.carousel.setFilter(active ? slide => {
      const d = slide.dataset;
      return (!keyword || slide.textContent.toLowerCase().includes(keyword))
        && (!location || d.location === location)
        && (!type || d.type === type)
        && inRange(Number(d.price), price)
        && inRange(Number(d.size), size)
        && (!year || d.year === year);
    } : null);

    if (empty) empty.hidden = shown > 0;
  };

  form.addEventListener('submit', e => {
    e.preventDefault();
    apply();
    results.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  form.querySelectorAll('select').forEach(select => select.addEventListener('change', apply));

  // Support links like properties.html?keyword=villa
  const params = new URLSearchParams(location.search);
  let prefilled = false;
  params.forEach((value, key) => {
    const field = form.elements[key];
    if (field && value) { field.value = value; prefilled = true; }
  });
  if (prefilled) apply();
}

/* --------------------------------------------------------------------------
   Motion (GSAP + ScrollTrigger, self-hosted and deferred).

   - Sections and cards fade up as they scroll into view; only elements below
     the first screen are animated, so nothing above the fold (the LCP hero)
     is ever hidden or delayed.
   - Stat numbers ("200+", "10k+", "16+") count up once.
   - The hero badge ring turns slowly.
   - Every animation ends on the exact layout from the design (props cleared).
   - Skipped entirely for prefers-reduced-motion or when GSAP did not load.
   -------------------------------------------------------------------------- */
const REVEAL_SELECTORS = [
  '.section-head', '.journey__text', '.journey__media', '.values__text', '.values__grid',
  '.investments__intro', '.investments__grid', '.inquire__text', '.office-gallery',
  '.pricing__note', '.pricing__listing', '.cost-card', '.panel', '.gallery', '.carousel',
  '.form-card', '.property-search', '.tabs', '.cta__inner', '.site-footer__main',
].join(',');

const STAGGER_GROUPS = [
  ['.feature-strip', '.feature-tile'],
  ['.stats', '.stat'],
  ['.achievements', '.achievement'],
  ['.steps', '.step'],
  ['.team', '.team-card'],
  ['.service-grid', '.service-card, .promo-card'],
  ['.offices__grid', '.office-card'],
  ['.card-grid', ':scope > *'],
];

function initAnimations() {
  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  if (!gsap || !ScrollTrigger || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  gsap.registerPlugin(ScrollTrigger);

  const fold = window.innerHeight;
  const belowFold = el => el.getBoundingClientRect().top > fold * 0.9;
  const done = { clearProps: 'opacity,transform' };

  // Single blocks fade up.
  gsap.utils.toArray(REVEAL_SELECTORS).filter(belowFold).forEach(el => {
    gsap.from(el, {
      opacity: 0,
      y: 40,
      duration: 0.8,
      ease: 'power3.out',
      ...done,
      scrollTrigger: { trigger: el, start: 'top 88%', once: true },
    });
  });

  // Card groups fade up one after another.
  STAGGER_GROUPS.forEach(([groupSel, itemSel]) => {
    document.querySelectorAll(groupSel).forEach(group => {
      if (!belowFold(group)) return;
      const items = group.querySelectorAll(itemSel);
      if (!items.length) return;
      gsap.from(items, {
        opacity: 0,
        y: 32,
        duration: 0.7,
        ease: 'power3.out',
        stagger: 0.08,
        ...done,
        scrollTrigger: { trigger: group, start: 'top 85%', once: true },
      });
    });
  });

  // Count-up for the stat numbers (keeps the "+" / "k+" suffix).
  document.querySelectorAll('.stat__value').forEach(el => {
    const match = el.textContent.trim().match(/^(\d+)(.*)$/);
    if (!match) return;
    const target = Number(match[1]);
    const suffix = match[2];
    const counter = { value: 0 };
    gsap.to(counter, {
      value: target,
      duration: 1.6,
      ease: 'power2.out',
      onUpdate: () => { el.textContent = Math.round(counter.value) + suffix; },
      onComplete: () => { el.textContent = target + suffix; },
      scrollTrigger: { trigger: el, start: 'top 95%', once: true },
    });
  });

  // Slow turn of the circular hero badge text.
  const ring = document.querySelector('.hero-badge__ring');
  if (ring) {
    gsap.to(ring, { rotation: 360, duration: 24, ease: 'none', repeat: -1, transformOrigin: '50% 50%' });
  }
}

/* --------------------------------------------------------------------------
   Boot
   -------------------------------------------------------------------------- */
function boot() {
  initBanner();
  initNav();
  initCarousels();
  initGallery();
  initTabs();
  initForms();
  initPropertySearch();
  initAnimations();
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
else boot();
})();
