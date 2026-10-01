/**
 * Renders Figma frames from the decoded .fig to PNG (via Chrome), so the build
 * can be compared side by side with the design.
 * Usage: node render-reference.js "Home Page - Desktop" ["About Us Page - Desktop" ...]
 */
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const lib = require('./fig-lib');

const ROOT = path.resolve(__dirname, '..');
const OUT = path.join(ROOT, 'design', 'ref');
fs.mkdirSync(OUT, { recursive: true });
const L = lib.load(path.join(ROOT, 'design', 'fig', 'canvas.json'));
const names = process.argv.slice(2);

(async () => {
  const browser = await puppeteer.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: 'new', args: ['--allow-file-access-from-files'] });
  for (const name of names) {
    const frame = L.nodes.find(n => n.name === name && n.type === 'FRAME');
    if (!frame) { console.log('not found:', name); continue; }
    const { body, defs } = lib.renderSubtree(L, frame, { imageDir: '../fig/images', prefix: 'r' });
    const w = frame.size.x, h = frame.size.y;
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}" viewBox="0 0 ${w} ${h}"><defs>${defs}</defs>${body}</svg>`;
    const file = path.join(OUT, name.replace(/[^a-z0-9]+/gi, '-').toLowerCase() + '.svg');
    fs.writeFileSync(file, svg);
    const page = await browser.newPage();
    await page.setViewport({ width: Math.round(w), height: Math.round(h) });
    await page.goto(require('url').pathToFileURL(file).href, { waitUntil: 'networkidle0' });
    await page.screenshot({ path: file.replace(/\.svg$/, '.png') });
    await page.close();
    console.log('rendered', name, `${w}x${h}`);
  }
  await browser.close();
})();
