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
