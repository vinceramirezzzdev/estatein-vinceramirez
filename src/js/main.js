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
