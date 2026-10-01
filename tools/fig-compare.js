/**
 * Walks the Desktop / Laptop / Mobile frames of a page in parallel (matching
 * children by name + order) and prints auto-layout padding, gap, radius and
 * size for each matched node, so breakpoint values can be read side by side.
 * Usage: node fig-compare.js "Home Page"
 */
const path = require('path');
const lib = require('./fig-lib');
const L = lib.load(path.join(__dirname, '..', 'design', 'fig', 'canvas.json'));
const page = process.argv[2];
const frames = ['Desktop', 'Laptop', 'Mobile'].map(v => L.nodes.find(n => n.name === `${page} - ${v}` && n.type === 'FRAME'));

const r = v => Math.round(v * 10) / 10;
function desc(n) {
  if (!n) return '—';
  const parts = [`${r(n.size.x)}x${r(n.size.y)}`];
  if (n.stackMode && n.stackMode !== 'NONE') {
    const pl = n.stackHorizontalPadding ?? n.stackPadding ?? 0;
    const pt = n.stackVerticalPadding ?? n.stackPadding ?? 0;
    parts.push(`${n.stackMode[0]} g${r(n.stackSpacing || 0)} p${r(pt)}/${r(n.stackPaddingRight ?? 0)}/${r(n.stackPaddingBottom ?? 0)}/${r(pl)}`);
  }
  if (n.cornerRadius) parts.push('r' + r(n.cornerRadius));
  if (n.type === 'TEXT') parts.push(`${r(n.fontSize)}`);
  return parts.join(' ');
}
function firstText(n) {
  if (n.type === "TEXT") return n.textData.characters.slice(0, 20);
  for (const k of n.kids) { const t = firstText(k); if (t) return t; }
  return "";
}
function hasLayout(n) { return n && ((n.stackMode && n.stackMode !== 'NONE') || n.type === 'TEXT' || /Image|Profile|Icon|Logo|Shape/.test(n.name)); }

function walk(nodes, depth, label) {
  const [d] = nodes;
  if (!d || d.visible === false) return;
  if (depth > 0 && hasLayout(d)) {
    const name = d.type === 'TEXT' ? `"${d.textData.characters.slice(0, 22)}"` : d.name;
    console.log(`${'  '.repeat(depth)}${name.padEnd(30 - 2 * Math.min(depth, 10))} D ${desc(nodes[0]).padEnd(30)} L ${desc(nodes[1]).padEnd(30)} M ${desc(nodes[2])}`);
  }
  if (d.type === 'TEXT' || /Icon|Logo|Abstract Design/.test(d.name)) return;
  const groups = nodes.map(n => (n ? n.kids.filter(k => k.visible !== false) : []));
  // match children by name + the first text they contain, falling back to name order
  const used = groups.map(() => new Set());
  for (const child of groups[0]) {
    const sig = firstText(child);
    const sameBefore = groups[0].filter(k => k.name === child.name).indexOf(child);
    const matches = groups.map((g, i) => {
      if (i === 0) return child;
      const free = g.filter(k => !used[i].has(k) && k.name === child.name);
      const m = (sig && free.find(k => firstText(k) === sig)) || (!sig && free[0]) || g.filter(k => k.name === child.name)[sameBefore];
      if (m) used[i].add(m);
      return m;
    });
    walk(matches, depth + 1);
  }
}
walk(frames, 0);
