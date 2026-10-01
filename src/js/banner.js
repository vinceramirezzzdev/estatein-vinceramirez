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
