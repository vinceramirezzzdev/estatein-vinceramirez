/**
 * Full-page screenshots of the local build at a given width.
 * Usage: node shoot.js <width> page1 [page2 ...]   (pages without .html)
 */
const path = require('path');
const puppeteer = require('puppeteer-core');
(async () => {
  const [width, ...pages] = process.argv.slice(2);
  const out = path.join(__dirname, '..', 'design', 'shots');
  require('fs').mkdirSync(out, { recursive: true });
  const browser = await puppeteer.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: 'new' });
  const tab = await browser.newPage();
  await tab.setViewport({ width: Number(width), height: 900 });
  await tab.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'reduce' }]);
  for (const p of pages) {
    await tab.goto(`http://localhost:5173/${p}.html`, { waitUntil: 'networkidle0' });
    await tab.evaluate(() => { document.querySelectorAll('img[loading="lazy"]').forEach(i => { i.loading = 'eager'; }); return document.fonts.ready; });
    await tab.waitForNetworkIdle({ idleTime: 300 });
    const h = await tab.evaluate(() => document.documentElement.scrollHeight);
    await tab.screenshot({ path: path.join(out, `${p}-${width}.png`), fullPage: true });
    console.log(p, width, 'height', h);
  }
  await browser.close();
})();
