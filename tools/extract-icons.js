/**
 * Finds every icon-like node in the page frames of the decoded .fig, renders
 * each to SVG, de-duplicates them and writes a contact sheet + manifest so the
 * icons can be named. Usage: node extract-icons.js
 */
const fs = require('fs');
const path = require('path');
const sharp = require('sharp');
const lib = require('./fig-lib');

const OUT = path.join(__dirname, '..', 'design', 'icons');
fs.mkdirSync(OUT, { recursive: true });
const L = lib.load(path.join(__dirname, '..', 'design', 'fig', 'canvas.json'));

const PAGES = ['Home Page - Desktop', 'About Us Page - Desktop', 'Properties Page - Desktop', 'Property Details Page - Desktop', 'Services Page - Desktop', 'Contact Page - Desktop',
  'Home Page - Mobile', 'Home Page - Laptop'];

const isIcon = n => (n.type === 'FRAME' && /^(Icon|Logo|Symbol|Abstract Design|Frame 3186)$/.test(n.name)) || n.type === 'STAR';

function nearestText(node) {
  for (let p = node.parent; p; p = p.parent) {
    const texts = [];
    (function walk(n) { if (n === node) return; if (n.type === 'TEXT' && n.textData) texts.push(n.textData.characters); n.kids.forEach(walk); })(p);
    if (texts.length) return texts[0].slice(0, 40);
  }
  return '';
}

const unique = new Map();
for (const pageName of PAGES) {
  const page = L.nodes.find(n => n.name === pageName && n.type === 'FRAME');
  if (!page) continue;
  (function walk(n, trail) {
    if (n.visible === false) return;
    if (isIcon(n) && n !== page) {
      const { body, defs } = lib.renderSubtree(L, n, { prefix: n.id.replace(':', '_') });
      if (body) {
        const key = `${n.size.x}x${n.size.y}|${body.replace(/id="[^"]+"|url\(#[^)]+\)/g, '')}`;
        if (!unique.has(key)) unique.set(key, { id: n.id, name: n.name, w: n.size.x, h: n.size.y, body, defs, uses: [] });
        unique.get(key).uses.push(`${pageName.replace(/ Page - /, ':')} > ${trail.slice(-2).join(' > ')} :: "${nearestText(n)}"`);
      }
      if (n.name !== 'Abstract Design') return; // don't descend into icons
    }
    n.kids.forEach(k => walk(k, trail.concat(n.name)));
  })(page, []);
}

const list = [...unique.values()];
const manifest = list.map((ic, i) => ({ index: i, id: ic.id, name: ic.name, size: `${ic.w}x${ic.h}`, uses: ic.uses.length, examples: [...new Set(ic.uses)].slice(0, 3) }));
fs.writeFileSync(path.join(OUT, 'manifest.json'), JSON.stringify(manifest, null, 2));

(async () => {
  const cell = 110;
  const cols = 10;
  const rows = Math.ceil(list.length / cols);
  const comps = [];
  for (let i = 0; i < list.length; i++) {
    const ic = list[i];
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${ic.w} ${ic.h}" width="80" height="${Math.round(80 * ic.h / ic.w)}"><defs>${ic.defs}</defs>${ic.body}</svg>`;
    fs.writeFileSync(path.join(OUT, `icon-${i}.svg`), svg);
    let png;
    try {
      png = await sharp(Buffer.from(svg)).resize(80, 80, { fit: 'contain', background: { r: 0, g: 0, b: 0, alpha: 0 } }).png().toBuffer();
    } catch (e) { continue; }
    const x = (i % cols) * cell;
    const y = Math.floor(i / cols) * cell;
    comps.push({ input: png, left: x + 15, top: y + 5 });
    comps.push({ input: Buffer.from(`<svg width="${cell}" height="20"><text x="4" y="14" font-size="13" fill="#ff0" font-family="Arial">${i} ${ic.w}x${ic.h}</text></svg>`), left: x, top: y + 88 });
  }
  await sharp({ create: { width: cols * cell, height: rows * cell, channels: 3, background: '#3a3a3a' } }).composite(comps).png().toFile(path.join(OUT, 'sheet.png'));
  console.log('unique icons:', list.length);
})();
