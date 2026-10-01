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
