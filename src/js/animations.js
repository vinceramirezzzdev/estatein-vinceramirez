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
