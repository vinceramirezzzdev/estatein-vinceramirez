/**
 * Helpers for reading a decoded .fig (see decode-fig.js): node tree, absolute
 * transforms, vector path decoding and SVG rendering of any subtree.
 */
const fs = require('fs');

function load(file) {
  const msg = JSON.parse(fs.readFileSync(file, 'utf8'));
  const blobs = msg.blobs.map(b => Buffer.from(b.bytes.__b64, 'base64'));
  const byId = new Map();
  const children = new Map();
  const id = g => `${g.sessionID}:${g.localID}`;
  for (const n of msg.nodeChanges) {
    n.id = id(n.guid);
    byId.set(n.id, n);
  }
  for (const n of msg.nodeChanges) {
    if (!n.parentIndex) continue;
    const pid = id(n.parentIndex.guid);
    if (!children.has(pid)) children.set(pid, []);
    children.get(pid).push(n);
  }
  // Fractional-index positions sort by raw string comparison.
  for (const list of children.values()) list.sort((a, b) => (a.parentIndex.position < b.parentIndex.position ? -1 : a.parentIndex.position > b.parentIndex.position ? 1 : 0));
  for (const n of msg.nodeChanges) n.kids = children.get(n.id) || [];
  for (const n of msg.nodeChanges) for (const k of n.kids) k.parent = n;
  return { msg, blobs, byId, nodes: msg.nodeChanges };
}

/** commandsBlob -> SVG path data (0=Z, 1=M, 2=L, 3=Q, 4=C; little-endian float32). */
function pathFromBlob(blob, round = 3) {
  const f = v => +v.toFixed(round);
  let i = 0;
  let d = '';
  const num = () => { const v = blob.readFloatLE(i); i += 4; return f(v); };
  while (i < blob.length) {
    const cmd = blob[i++];
    if (cmd === 0) { if (d && !d.endsWith('Z')) d += 'Z'; } // a path may not start with Z
    else if (cmd === 1) d += `M${num()} ${num()}`;
    else if (cmd === 2) d += `L${num()} ${num()}`;
    else if (cmd === 3) d += `Q${num()} ${num()} ${num()} ${num()}`;
    else if (cmd === 4) d += `C${num()} ${num()} ${num()} ${num()} ${num()} ${num()}`;
    else throw new Error('Unknown path command ' + cmd);
  }
  return d;
}

const mul = (a, b) => ({
  m00: a.m00 * b.m00 + a.m01 * b.m10, m01: a.m00 * b.m01 + a.m01 * b.m11, m02: a.m00 * b.m02 + a.m01 * b.m12 + a.m02,
  m10: a.m10 * b.m00 + a.m11 * b.m10, m11: a.m10 * b.m01 + a.m11 * b.m11, m12: a.m10 * b.m02 + a.m11 * b.m12 + a.m12,
});
const IDENTITY = { m00: 1, m01: 0, m02: 0, m10: 0, m11: 1, m12: 0 };

/** Transform of node relative to `root` (an ancestor). */
function relTransform(node, root) {
  const chain = [];
  for (let n = node; n && n !== root; n = n.parent) chain.unshift(n);
  return chain.reduce((acc, n) => mul(acc, n.transform || IDENTITY), IDENTITY);
}

function box(node, root) {
  const t = relTransform(node, root);
  const w = node.size ? node.size.x : 0;
  const h = node.size ? node.size.y : 0;
  const pts = [[0, 0], [w, 0], [0, h], [w, h]].map(([x, y]) => [t.m00 * x + t.m01 * y + t.m02, t.m10 * x + t.m11 * y + t.m12]);
  const xs = pts.map(p => p[0]);
  const ys = pts.map(p => p[1]);
  return { x: Math.min(...xs), y: Math.min(...ys), w: Math.max(...xs) - Math.min(...xs), h: Math.max(...ys) - Math.min(...ys) };
}

const hex = c => '#' + [c.r, c.g, c.b].map(v => Math.round(v * 255).toString(16).padStart(2, '0')).join('');
const matrix = t => `matrix(${[t.m00, t.m10, t.m01, t.m11, t.m02, t.m12].map(v => +v.toFixed(4)).join(' ')})`;

function invert(t) {
  const det = t.m00 * t.m11 - t.m01 * t.m10;
  return {
    m00: t.m11 / det, m01: -t.m01 / det, m02: (t.m01 * t.m12 - t.m11 * t.m02) / det,
    m10: -t.m10 / det, m11: t.m00 / det, m12: (t.m10 * t.m02 - t.m00 * t.m12) / det,
  };
}

let gradientId = 0;

/** Paint -> { fill, defs } for SVG. Gradients use the node size (userSpaceOnUse). */
function paintToSvg(paint, size) {
  if (!paint || paint.visible === false) return null;
  const alpha = (paint.opacity ?? 1) * (paint.color ? paint.color.a : 1);
  if (paint.type === 'SOLID') {
    return { fill: hex(paint.color), opacity: +alpha.toFixed(3), defs: '' };
  }
  if (paint.type === 'GRADIENT_LINEAR' || paint.type === 'GRADIENT_RADIAL') {
    // paint.transform maps node-normalised space -> gradient space; invert to get handles.
    const inv = invert(paint.transform || IDENTITY);
    const ap = (x, y) => [(inv.m00 * x + inv.m01 * y + inv.m02) * size.x, (inv.m10 * x + inv.m11 * y + inv.m12) * size.y];
    const idName = `g${++gradientId}`;
    const stops = paint.stops.map(s => `<stop offset="${+s.position.toFixed(4)}" stop-color="${hex(s.color)}" stop-opacity="${+(s.color.a).toFixed(3)}"/>`).join('');
    let defs;
    if (paint.type === 'GRADIENT_LINEAR') {
      const [x1, y1] = ap(0, 0.5);
      const [x2, y2] = ap(1, 0.5);
      defs = `<linearGradient id="${idName}" gradientUnits="userSpaceOnUse" x1="${+x1.toFixed(2)}" y1="${+y1.toFixed(2)}" x2="${+x2.toFixed(2)}" y2="${+y2.toFixed(2)}">${stops}</linearGradient>`;
    } else {
      const [cx, cy] = ap(0.5, 0.5);
      const [ex, ey] = ap(1, 0.5);
      const r = Math.hypot(ex - cx, ey - cy);
      defs = `<radialGradient id="${idName}" gradientUnits="userSpaceOnUse" cx="${+cx.toFixed(2)}" cy="${+cy.toFixed(2)}" r="${+r.toFixed(2)}">${stops}</radialGradient>`;
    }
    return { fill: `url(#${idName})`, opacity: +(paint.opacity ?? 1).toFixed(3), defs };
  }
  return null; // images etc. are not rendered here
}

/**
 * Render a subtree as SVG markup in `root`'s local coordinates.
 * opts.currentColor: replace this hex colour with currentColor (for icons).
 */
/** Paints of a node, resolving shared colour styles (local paints can be stale). */
function resolvePaints(lib, node, kind) {
  const ref = kind === "stroke" ? node.styleIdForStrokeFill : node.styleIdForFill;
  if (ref && ref.guid) {
    const style = lib.byId.get(`${ref.guid.sessionID}:${ref.guid.localID}`);
    if (style && style.fillPaints) return style.fillPaints;
  }
  return kind === "stroke" ? node.strokePaints : node.fillPaints;
}

function renderSubtree(lib, root, opts = {}) {
  const defs = [];
  let clipId = 0;
  const colour = (fill) => (opts.currentColor && fill && opts.currentColor.includes(fill.toLowerCase()) ? 'currentColor' : fill);

  function paintPaths(node, geometry, paints) {
    let out = '';
    for (const paint of (paints || [])) {
      if (paint.type === "IMAGE" && opts.imageDir && paint.visible !== false && geometry && geometry.length) {
        // Image fill clipped to the node geometry (used for reference renders only).
        const hash = Buffer.from(paint.image.hash.__b64 || paint.image.hash, "base64").toString("hex");
        const cid = `i${opts.prefix || ""}${++clipId}`;
        defs.push(`<clipPath id="${cid}">${geometry.map(g => `<path d="${pathFromBlob(lib.blobs[g.commandsBlob])}"/>`).join("")}</clipPath>`);
        const fit = paint.imageScaleMode === "FIT" ? "xMidYMid meet" : "xMidYMid slice";
        out += `<image href="${opts.imageDir}/${hash}" width="${node.size.x}" height="${node.size.y}" preserveAspectRatio="${fit}" clip-path="url(#${cid})"${(paint.opacity ?? 1) < 1 ? ` opacity="${paint.opacity}"` : ""}/>`;
        continue;
      }
      const p = paintToSvg(paint, node.size || { x: 0, y: 0 });
      if (!p) continue;
      if (p.defs) defs.push(p.defs);
      for (const geo of (geometry || [])) {
        const d = pathFromBlob(lib.blobs[geo.commandsBlob]);
        if (!d) continue;
        const rule = geo.windingRule === 'EVENODD' || geo.windingRule === 'ODD' ? ' fill-rule="evenodd"' : '';
        const op = p.opacity < 1 ? ` fill-opacity="${p.opacity}"` : '';
        out += `<path d="${d}" fill="${colour(p.fill)}"${op}${rule}/>`;
      }
    }
    return out;
  }

  function walk(node, isRoot) {
    if (node.visible === false) return '';
    let inner = '';
    const isText = node.type === 'TEXT';
    if (isText && node.derivedTextData && node.derivedTextData.glyphs) {
      // Outline text using the glyph paths Figma stores (em units, y-down after flip).
      for (const paint of resolvePaints(lib, node, "fill") || []) {
        const p = paintToSvg(paint, node.size);
        if (!p) continue;
        if (p.defs) defs.push(p.defs);
        for (const g of node.derivedTextData.glyphs) {
          if (g.commandsBlob === undefined) continue;
          const d = pathFromBlob(lib.blobs[g.commandsBlob], 5);
          if (!d) continue;
          const s = g.fontSize;
          inner += `<path transform="matrix(${s} 0 0 ${-s} ${+g.position.x.toFixed(3)} ${+g.position.y.toFixed(3)})" d="${d}" fill="${colour(p.fill)}"${p.opacity < 1 ? ` fill-opacity="${p.opacity}"` : ''}/>`;
        }
      }
    } else {
      inner += paintPaths(node, node.fillGeometry, resolvePaints(lib, node, "fill"));
      inner += paintPaths(node, node.strokeGeometry, resolvePaints(lib, node, "stroke"));
    }
    // Boolean operations already carry the merged geometry; their children are operands.
    if (node.type !== 'BOOLEAN_OPERATION') for (const k of node.kids) inner += walk(k, false);
    if (!inner) return '';

    const clips = node.type === 'FRAME' && node.frameMaskDisabled !== true && !isRoot;
    if (clips) {
      const cid = `c${opts.prefix || ''}${++clipId}`;
      defs.push(`<clipPath id="${cid}"><rect width="${node.size.x}" height="${node.size.y}"/></clipPath>`);
      inner = `<g clip-path="url(#${cid})">${inner}</g>`;
    }
    const attrs = [];
    if (!isRoot && node.transform) attrs.push(`transform="${matrix(node.transform)}"`);
    if (node.opacity !== undefined && node.opacity < 1) attrs.push(`opacity="${+node.opacity.toFixed(3)}"`);
    if (opts.blend && node.blendMode && !['NORMAL', 'PASS_THROUGH'].includes(node.blendMode)) attrs.push(`style="mix-blend-mode:${node.blendMode.toLowerCase().replace('_', '-')}"`);
    return attrs.length ? `<g ${attrs.join(' ')}>${inner}</g>` : inner;
  }

  const body = walk(root, true);
  return { body, defs: defs.join('') };
}

module.exports = { resolvePaints, load, pathFromBlob, relTransform, box, hex, renderSubtree, paintToSvg };
