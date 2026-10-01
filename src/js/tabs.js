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
