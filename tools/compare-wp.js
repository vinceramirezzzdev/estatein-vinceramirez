/**
 * Screenshots the static build and the local WordPress theme at the same width
 * and reports page heights plus the share of differing pixels.
 * Usage: node compare-wp.js <width> [pageKey ...]
 */
const path = require('path');
const sharp = require('sharp');
const puppeteer = require('puppeteer-core');

const PAGES = {
  home: ['index.html', '/'],
  about: ['about.html', '/about-us/'],
  properties: ['properties.html', '/properties/'],
  details: ['property-details.html', '/property/seaside-serenity-villa/'],
  services: ['services.html', '/services/'],
  contact: ['contact.html', '/contact-us/'],
  testimonials: ['testimonials.html', '/testimonials/'],
  faqs: ['faqs.html', '/faqs/'],
  terms: ['terms.html', '/terms-and-conditions/'],
};
const OUT = path.join(__dirname, '..', 'design', 'wpcmp');
require('fs').mkdirSync(OUT, { recursive: true });

async function shoot(tab, url, file, width) {
  await tab.setViewport({ width, height: 900 });
  await tab.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'reduce' }]);
  await tab.goto(url, { waitUntil: 'networkidle0' });
  await tab.evaluate(() => { document.querySelectorAll('img[loading="lazy"]').forEach(i => { i.loading = 'eager'; }); return document.fonts.ready; });
  await tab.waitForNetworkIdle({ idleTime: 300 });
  await tab.evaluate(() => window.scrollTo(0, 0));
  const h = await tab.evaluate(() => document.documentElement.scrollHeight);
  await tab.screenshot({ path: file, fullPage: true });
  return h;
}

(async () => {
  const [w, ...keys] = process.argv.slice(2);
  const width = Number(w);
  const browser = await puppeteer.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: 'new' });
  const tab = await browser.newPage();
  for (const key of keys.length ? keys : Object.keys(PAGES)) {
    const [staticPage, wpPath] = PAGES[key];
    const a = path.join(OUT, `${key}-${width}-static.png`);
    const b = path.join(OUT, `${key}-${width}-wp.png`);
    const ha = await shoot(tab, `http://localhost:5173/${staticPage}`, a, width);
    const hb = await shoot(tab, `http://localhost:8090${wpPath}`, b, width);
    const h = Math.min(ha, hb);
    const [ra, rb] = await Promise.all([a, b].map(f => sharp(f).extract({ left: 0, top: 0, width, height: h }).raw().toBuffer()));
    let diff = 0;
    for (let i = 0; i < ra.length; i += 3) {
      if (Math.abs(ra[i] - rb[i]) + Math.abs(ra[i + 1] - rb[i + 1]) + Math.abs(ra[i + 2] - rb[i + 2]) > 30) diff++;
    }
    console.log(`${key.padEnd(11)} static ${ha}  wp ${hb}  differing pixels ${(diff / (ra.length / 3) * 100).toFixed(2)}%`);
  }
  await browser.close();
})();
