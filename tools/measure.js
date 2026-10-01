/**
 * Prints the bounding boxes of elements matching selectors on a page, so they
 * can be compared with the Figma frame coordinates.
 * Usage: node measure.js <page.html> <width> "<selector>" ["<selector>" ...]
 */
const puppeteer = require('puppeteer-core');
const CHROME = 'C:/Program Files/Google/Chrome/Application/chrome.exe';
(async () => {
  const [page, width, ...selectors] = process.argv.slice(2);
  const browser = await puppeteer.launch({ executablePath: CHROME, headless: 'new' });
  const tab = await browser.newPage();
  await tab.setViewport({ width: Number(width), height: 1000 });
  await tab.goto(`http://localhost:5173/${page}`, { waitUntil: 'networkidle0' });
  for (const sel of selectors) {
    const boxes = await tab.$$eval(sel, els => els.map(el => {
      const r = el.getBoundingClientRect();
      return `${Math.round(r.x)},${Math.round(r.y + scrollY)} ${Math.round(r.width)}x${Math.round(r.height)}`;
    }));
    console.log(sel.padEnd(40), boxes.slice(0, 6).join(' | '));
  }
  console.log('document height', await tab.evaluate(() => document.documentElement.scrollHeight));
  await browser.close();
})();
