/**
 * Estatein build
 *
 *   src/pages/*.html     -> static/*.html          (partials + image srcsets resolved)
 *   src/css/*.css        -> static/assets/css/main.css + main.min.css
 *   src/js/*.js          -> static/assets/js/main.js  + main.min.js
 *   static/assets/**     -> wordpress/estatein/assets/**  (theme uses the same assets)
 *
 * Page templates start with a JSON comment holding the page meta:
 *   <!-- {"title": "...", "description": "...", "nav": "home", "slug": "index"} -->
 *
 * Template syntax (kept deliberately tiny):
 *   <!-- @include header -->      inserts src/partials/header.html
 *   {{title}} / {{description}}   page meta values
 *   {{nav:home}}                  ' aria-current="page"' when the page's nav key is "home"
 *   {{src:name:width}}            path of a generated image width
 *   {{srcset:name}}               full srcset for an image from the manifest
 *
 * Usage: npm run build
 */
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const esbuild = require('esbuild');

const ROOT = path.resolve(__dirname, '..');
const SRC = path.join(ROOT, 'src');
const STATIC = path.join(ROOT, 'static');
const THEME = path.join(ROOT, 'wordpress', 'estatein');

// Public URL of the static site (canonical links, Open Graph, sitemap).
// Override when deploying elsewhere: SITE_URL=https://example.com npm run build
const SITE_URL = (process.env.SITE_URL || 'https://estatein-vinceramirez.vercel.app').replace(/\/+$/, '');

const read = file => fs.readFileSync(file, 'utf8');
const manifest = JSON.parse(read(path.join(STATIC, 'assets', 'images', 'manifest.json')));
const versions = {}; // asset path -> short content hash, filled in by build()

// CSS and JS are concatenated in this order (base layers first, page layers last).
const CSS_ORDER = ['tokens', 'base', 'layout', 'buttons', 'header', 'footer', 'components', 'forms', 'home', 'about', 'properties', 'property-details', 'services', 'contact', 'content', 'responsive'];
const JS_ORDER = ['banner', 'nav', 'carousel', 'gallery', 'tabs', 'forms', 'search', 'animations', 'main'];

function imagePath(name, width) {
  const img = manifest[name];
  if (!img) throw new Error(`Unknown image "${name}"`);
  const w = width ? Number(width) : img.widths[img.widths.length - 1];
  if (!img.widths.includes(w)) throw new Error(`Image "${name}" has no ${w}w file (${img.widths.join(', ')})`);
  return `assets/images/${name}-${w}.webp`;
}

function srcset(name) {
  const img = manifest[name];
  if (!img) throw new Error(`Unknown image "${name}"`);
  return img.widths.map(w => `assets/images/${name}-${w}.webp ${w}w`).join(', ');
}

/**
 * Page meta defaults: absolute URL, robots, and the JSON-LD graph (the agency
 * on every page, plus a RealEstateListing when the meta has a "listing").
 */
function pageMeta(file, meta) {
  const name = path.basename(file);
  const url = name === 'index.html' ? `${SITE_URL}/` : `${SITE_URL}/${name}`;
  const graph = [{
    '@type': 'RealEstateAgent',
    '@id': `${SITE_URL}/#organization`,
    name: 'Estatein',
    url: `${SITE_URL}/`,
    logo: `${SITE_URL}/assets/icons/favicon.svg`,
    email: 'info@estatein.com',
    telephone: '+1 (123) 456-7890',
  }];
  if (meta.listing) {
    graph.push({
      '@type': 'RealEstateListing',
      name: meta.listing.name,
      url,
      description: meta.description,
      image: `${SITE_URL}/${imagePath(meta.listing.image)}`,
      contentLocation: { '@type': 'Place', name: meta.listing.location },
      offers: { '@type': 'Offer', price: meta.listing.price, priceCurrency: 'USD' },
    });
  }
  const jsonld = JSON.stringify({ '@context': 'https://schema.org', '@graph': graph }).replace(/</g, '\\u003c');
  return { robots: 'index, follow', ...meta, url, site: SITE_URL, jsonld, file: name };
}

function renderPage(file) {
  let html = read(file);
  const metaMatch = html.match(/^<!--\s*(\{[\s\S]*?\})\s*-->\s*/);
  if (!metaMatch) throw new Error(`${file} is missing its meta comment`);
  const meta = pageMeta(file, JSON.parse(metaMatch[1]));
  html = html.slice(metaMatch[0].length);

  // Partials may include other partials, so resolve until none are left.
  for (let i = 0; i < 5 && html.includes('<!-- @include'); i++) {
    html = html.replace(/<!--\s*@include\s+([\w-]+)\s*-->/g, (_, name) => read(path.join(SRC, 'partials', `${name}.html`)));
  }

  html = html
    .replace(/\{\{v:([\w./-]+)\}\}/g, (_, asset) => versions[asset] || '')
    .replace(/\{\{nav:([\w-]+)\}\}/g, (_, key) => (key === meta.nav ? ' aria-current="page"' : ''))
    .replace(/\{\{src:([\w-]+)(?::(\d+))?\}\}/g, (_, name, w) => imagePath(name, w))
    .replace(/\{\{srcset:([\w-]+)\}\}/g, (_, name) => srcset(name))
    .replace(/\{\{(\w+)\}\}/g, (m, key) => (key in meta ? meta[key] : m));
  return { html, meta };
}

/**
 * fluid(<laptop>px, <desktop>px) -> clamp() that equals the Laptop design value
 * at 1440px wide, the Desktop value at 1920px, and interpolates in between.
 */
function expandFluid(css) {
  return css.replace(/fluid\(\s*(-?[\d.]+)px\s*,\s*(-?[\d.]+)px\s*\)/g, (_, a, b) => {
    const lap = Number(a);
    const desk = Number(b);
    if (lap === desk) return `${lap}px`;
    const slope = +((desk - lap) / 480).toFixed(6);
    const lo = Math.min(lap, desk);
    const hi = Math.max(lap, desk);
    return `clamp(${lo}px, calc(${lap}px + (100vw - 1440px) * ${slope}), ${hi}px)`;
  });
}

const hash = text => crypto.createHash('md5').update(text).digest('hex').slice(0, 8);

function concat(dir, order, ext) {
  return order
    .map(name => path.join(SRC, dir, `${name}.${ext}`))
    .filter(f => fs.existsSync(f))
    .map(f => read(f).trim())
    .join('\n\n') + '\n';
}

function copyDir(from, to) {
  fs.mkdirSync(to, { recursive: true });
  for (const entry of fs.readdirSync(from, { withFileTypes: true })) {
    const a = path.join(from, entry.name);
    const b = path.join(to, entry.name);
    if (entry.isDirectory()) copyDir(a, b);
    else fs.copyFileSync(a, b);
  }
}

// src/icons/<name>.svg -> <symbol id="<name>"> inside static/assets/icons/sprite.svg
function buildSprite() {
  const dir = path.join(SRC, 'icons');
  const symbols = fs.readdirSync(dir).filter(f => f.endsWith('.svg')).sort().map(file => {
    const svg = read(path.join(dir, file));
    const viewBox = (svg.match(/viewBox="([^"]+)"/) || [])[1];
    if (!viewBox) throw new Error(`${file} has no viewBox`);
    const inner = svg.replace(/^[\s\S]*?<svg[^>]*>/, '').replace(/<\/svg>\s*$/, '').trim();
    return `<symbol id="${path.basename(file, '.svg')}" viewBox="${viewBox}">${inner}</symbol>`;
  });
  const out = path.join(STATIC, 'assets', 'icons');
  fs.mkdirSync(out, { recursive: true });
  fs.writeFileSync(path.join(out, 'sprite.svg'), `<svg xmlns="http://www.w3.org/2000/svg">\n${symbols.join('\n')}\n</svg>\n`);
  console.log('icons  sprite.svg', symbols.length, 'symbols');
}

async function build() {
  buildSprite();

  // Styles
  const cssDir = path.join(STATIC, 'assets', 'css');
  fs.mkdirSync(cssDir, { recursive: true });
  const css = expandFluid(concat('css', CSS_ORDER, 'css'));
  fs.writeFileSync(path.join(cssDir, 'main.css'), css);
  const cssMin = await esbuild.transform(css, { loader: 'css', minify: true, target: ['chrome100', 'firefox100', 'safari15', 'edge100'] });
  fs.writeFileSync(path.join(cssDir, 'main.min.css'), cssMin.code);
  console.log('css    main.css', (css.length / 1024).toFixed(1) + 'KB ->', (cssMin.code.length / 1024).toFixed(1) + 'KB');

  // Scripts
  const jsDir = path.join(STATIC, 'assets', 'js');
  fs.mkdirSync(jsDir, { recursive: true });
  const js = `(function () {\n'use strict';\n\n${concat('js', JS_ORDER, 'js')}})();\n`;
  fs.writeFileSync(path.join(jsDir, 'main.js'), js);
  const jsMin = await esbuild.transform(js, { loader: 'js', minify: true, target: ['es2019'] });
  fs.writeFileSync(path.join(jsDir, 'main.min.js'), jsMin.code);
  console.log('js     main.js', (js.length / 1024).toFixed(1) + 'KB ->', (jsMin.code.length / 1024).toFixed(1) + 'KB');

  // Third-party libraries (GSAP) are copied as-is.
  copyDir(path.join(SRC, 'vendor'), path.join(jsDir, 'vendor'));

  // Cache-busting query strings (?v=hash) so the CSS/JS can be cached for a year.
  versions['css/main.min.css'] = hash(cssMin.code);
  versions['js/main.min.js'] = hash(jsMin.code);

  // Pages, then the sitemap of every indexable page.
  const pagesDir = path.join(SRC, 'pages');
  const indexable = [];
  const pageFiles = fs.readdirSync(pagesDir).filter(f => f.endsWith('.html')).sort((a, b) => (b === 'index.html') - (a === 'index.html') || a.localeCompare(b));
  for (const file of pageFiles) {
    const { html, meta } = renderPage(path.join(pagesDir, file));
    fs.writeFileSync(path.join(STATIC, file), html);
    if (!/noindex/.test(meta.robots)) indexable.push(meta.url);
    console.log('page  ', file);
  }
  const today = new Date().toISOString().slice(0, 10);
  fs.writeFileSync(path.join(STATIC, 'sitemap.xml'), `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${indexable.map(url => `  <url><loc>${url}</loc><lastmod>${today}</lastmod></url>`).join('\n')}
</urlset>
`);
  fs.writeFileSync(path.join(STATIC, 'robots.txt'), `User-agent: *\nAllow: /\n\nSitemap: ${SITE_URL}/sitemap.xml\n`);
  console.log('seo    sitemap.xml (' + indexable.length + ' urls), robots.txt');

  // The WordPress theme ships the exact same assets.
  if (fs.existsSync(THEME)) {
    for (const dir of ['css', 'js', 'fonts', 'icons', 'images']) {
      const from = path.join(STATIC, 'assets', dir);
      if (fs.existsSync(from)) copyDir(from, path.join(THEME, 'assets', dir));
    }
    console.log('theme  assets copied to wordpress/estatein/assets');
  }
}

build().catch(err => { console.error(err); process.exit(1); });
