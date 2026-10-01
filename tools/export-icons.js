/**
 * Exports the design's icons and background patterns from the decoded .fig:
 *   icons    -> src/icons/<name>.svg   (combined into sprite.svg by build.js)
 *   patterns -> static/assets/icons/<name>.svg  (used as CSS backgrounds)
 * The name -> Figma node id mapping lives in icons.config.json.
 * Usage: node export-icons.js
 */
const fs = require('fs');
const path = require('path');
const lib = require('./fig-lib');

const ROOT = path.resolve(__dirname, '..');
const config = JSON.parse(fs.readFileSync(path.join(__dirname, 'icons.config.json'), 'utf8'));
const L = lib.load(path.join(ROOT, 'design', 'fig', 'canvas.json'));

// Icons whose colour changes with state (disabled arrows, hover) use currentColor.
const CURRENT_COLOR = ['arrow-left', 'arrow-right', 'tile-arrow', 'chevron-down'];

const num = v => +v.toFixed(3);

function render(name, nodeId, opts) {
  const node = L.byId.get(nodeId);
  if (!node) throw new Error(`${name}: node ${nodeId} not found`);
  let { body, defs } = lib.renderSubtree(L, node, { prefix: name.replace(/[^a-z0-9]/gi, ''), ...opts });
  if (opts.round !== undefined) body = body.replace(/-?\d+\.\d+/g, m => String(+(+m).toFixed(opts.round)));
  const fills = [...new Set([...body.matchAll(/fill="(#[0-9a-f]{6})"/g)].map(m => m[1]))];
  return { node, body, defs, fills };
}

const iconDir = path.join(ROOT, 'src', 'icons');
fs.mkdirSync(iconDir, { recursive: true });
for (const [name, id] of Object.entries(config.icons)) {
  const { node, body, defs, fills } = render(name, id, {});
  let svgBody = body;
  if (CURRENT_COLOR.includes(name)) {
    svgBody = svgBody.replace(/fill="#[0-9a-f]{6}"/g, 'fill="currentColor"');
    console.log(`${name}: currentColor (design colour ${fills.join(', ')})`);
  }
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${num(node.size.x)} ${num(node.size.y)}">${defs ? `<defs>${defs}</defs>` : ''}${svgBody}</svg>\n`;
  fs.writeFileSync(path.join(iconDir, `${name}.svg`), svg);
}

const patternDir = path.join(ROOT, 'static', 'assets', 'icons');
fs.mkdirSync(patternDir, { recursive: true });
for (const [name, id] of Object.entries(config.patterns)) {
  const { node, body, defs } = render(name, id, { round: 1 });
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${num(node.size.x)} ${num(node.size.y)}" preserveAspectRatio="xMidYMid slice">${defs ? `<defs>${defs}</defs>` : ''}${body}</svg>\n`;
  fs.writeFileSync(path.join(patternDir, `${name}.svg`), svg);
  console.log(`${name}: ${node.size.x}x${node.size.y}, ${(svg.length / 1024).toFixed(0)}KB`);
}
console.log(`exported ${Object.keys(config.icons).length} icons, ${Object.keys(config.patterns).length} patterns`);
