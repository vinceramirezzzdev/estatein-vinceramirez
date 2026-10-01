/**
 * Puts the Figma reference render and the build screenshot side by side,
 * sliced into vertical segments, for visual comparison.
 * Usage: node sidebyside.js <ref.png> <shot.png> <outPrefix> [segmentHeight]
 */
const sharp = require('sharp');
(async () => {
  const [refFile, shotFile, prefix, segArg] = process.argv.slice(2);
  const seg = Number(segArg || 1400);
  const [ref, shot] = await Promise.all([refFile, shotFile].map(f => sharp(f).metadata()));
  const w = ref.width;
  const total = Math.max(ref.height, shot.height);
  let i = 0;
  for (let y = 0; y < total; y += seg, i++) {
    const parts = [];
    for (const [file, meta, left] of [[refFile, ref, 0], [shotFile, shot, w + 20]]) {
      const h = Math.min(seg, meta.height - y);
      if (h > 0) parts.push({ input: await sharp(file).extract({ left: 0, top: y, width: Math.min(w, meta.width), height: h }).toBuffer(), left, top: 0 });
    }
    await sharp({ create: { width: w * 2 + 20, height: seg, channels: 3, background: '#ff00ff' } }).composite(parts).png().toFile(`${prefix}-${i}.png`);
  }
  console.log('segments', i);
})();
