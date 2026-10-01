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
