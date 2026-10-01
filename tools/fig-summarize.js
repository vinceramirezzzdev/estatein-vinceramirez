/**
 * Compact layout dump of a frame in the decoded .fig (positions relative to
 * the frame, fills, strokes, auto-layout, text styles), for reading the
 * Laptop/Mobile designs. Usage: node fig-summarize.js "Home Page - Mobile" > out.txt
 */
const path = require('path');
const lib = require('./fig-lib');
const L = lib.load(path.join(__dirname, '..', 'design', 'fig', 'canvas.json'));

const frame = L.nodes.find(n => n.name === process.argv[2] && n.type === 'FRAME');
if (!frame) { console.error('frame not found'); process.exit(1); }

const r = v => Math.round(v * 10) / 10;
const paint = p => {
  if (!p || p.visible === false) return null;
  if (p.type === 'SOLID') return lib.hex(p.color) + ((p.opacity ?? 1) < 1 ? '*' + r(p.opacity) : '') + (p.color.a < 1 ? '@' + r(p.color.a) : '');
  if (p.type === 'IMAGE') return 'IMG:' + Buffer.from(p.image.hash.__b64, 'base64').toString('hex').slice(0, 8);
  return p.type.replace('GRADIENT_', 'G-');
};
const isDecor = n => !hasContent(n);
function hasContent(n) {
  if (n.type === 'TEXT') return true;
  if ((n.fillPaints || []).some(p => p.type === 'IMAGE' && p.visible !== false)) return true;
  if (n.stackMode && n.stackMode !== 'NONE') return true;
  return n.kids.some(hasContent);
}
function countShapes(n) { return n.kids.reduce((a, k) => a + countShapes(k), n.type === 'FRAME' ? 0 : 1); }

function line(n, d) {
  const b = lib.box(n, frame);
  let s = '  '.repeat(d) + n.type.slice(0, 4) + ' "' + n.name + '" @' + Math.round(b.x) + ',' + Math.round(b.y) + ' ' + Math.round(b.w) + 'x' + Math.round(b.h);
  if (n.visible === false) s += ' HIDDEN';
  const fills = (lib.resolvePaints(L, n, 'fill') || []).map(paint).filter(Boolean);
  if (fills.length && n.type !== 'TEXT') s += ' fill=' + fills.join('|');
  const strokes = (lib.resolvePaints(L, n, 'stroke') || []).map(paint).filter(Boolean);
  if (strokes.length) {
    s += ' stroke=' + strokes.join('|') + '/' + r(n.strokeWeight) + (n.strokeAlign || '')[0];
    if (n.borderStrokeWeightsIndependent) s += `[t${n.borderTopWeight || 0} r${n.borderRightWeight || 0} b${n.borderBottomWeight || 0} l${n.borderLeftWeight || 0}]`;
  }
  if (n.cornerRadius) s += ' r=' + r(n.cornerRadius);
  if (n.rectangleCornerRadiiIndependent) s += ` r=${r(n.rectangleTopLeftCornerRadius || 0)}/${r(n.rectangleTopRightCornerRadius || 0)}/${r(n.rectangleBottomRightCornerRadius || 0)}/${r(n.rectangleBottomLeftCornerRadius || 0)}`;
  if (n.opacity !== undefined && n.opacity < 1) s += ' op=' + r(n.opacity);
  if (n.stackMode && n.stackMode !== 'NONE') {
    const pl = n.stackHorizontalPadding ?? n.stackPadding ?? 0;
    const pt = n.stackVerticalPadding ?? n.stackPadding ?? 0;
    const pr = n.stackPaddingRight ?? 0;
    const pb = n.stackPaddingBottom ?? 0;
    s += ` AL:${n.stackMode[0]} gap=${r(n.stackSpacing || 0)} pad=${r(pt)}/${r(pr)}/${r(pb)}/${r(pl)}`;
    if (n.stackPrimaryAlignItems) s += ' main=' + n.stackPrimaryAlignItems;
    if (n.stackCounterAlignItems) s += ' cross=' + n.stackCounterAlignItems;
    if (n.stackWrap === 'WRAP') s += ' wrap';
  }
  if (n.effects && n.effects.length) s += ' fx=' + n.effects.filter(e => e.visible !== false).map(e => e.type + ' spread=' + (e.spread || 0)).join(',');
  if (n.type === 'TEXT') {
    const lh = n.lineHeight ? (n.lineHeight.units === 'RAW' ? r(n.lineHeight.value * n.fontSize) : n.lineHeight.units === 'PERCENT' ? r(n.lineHeight.value / 100 * n.fontSize) : r(n.lineHeight.value)) : '';
    const ls = n.letterSpacing && n.letterSpacing.value ? (n.letterSpacing.units === 'PERCENT' ? ' ls=' + r(n.letterSpacing.value) + '%' : ' ls=' + r(n.letterSpacing.value) + 'px') : '';
    s += ` {${n.fontName.style} ${r(n.fontSize)}/${lh}${ls}${n.textAlignHorizontal && n.textAlignHorizontal !== 'LEFT' ? ' ' + n.textAlignHorizontal : ''} ${fills.join('|')}} "${n.textData.characters.replace(/\n/g, '\\n').slice(0, 90)}"`;
  }
  return s;
}

(function walk(n, d) {
  if (d > 0 && n.kids.length && isDecor(n)) { console.log(line(n, d) + ` [decor/icon: ${countShapes(n)} shapes]`); return; }
  console.log(line(n, d));
  n.kids.forEach(k => walk(k, d + 1));
})(frame, 0);
