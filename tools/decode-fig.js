/**
 * Decodes a Figma .fig "canvas.fig" (fig-kiwi container) into JSON:
 *   header "fig-kiwi" + u32 version, then u32-length-prefixed chunks:
 *   chunk 0 = compressed kiwi schema, chunk 1 = compressed message.
 * Usage: node decode-fig.js <canvas.fig> <out.json>
 */
const fs = require('fs');
const zlib = require('zlib');
const kiwi = require('kiwi-schema');

const [input, output] = process.argv.slice(2);
const buf = fs.readFileSync(input);
const magic = buf.slice(0, 8).toString('latin1');
const version = buf.readUInt32LE(8);
console.log('magic', magic, 'version', version);

let offset = 12;
const chunks = [];
while (offset < buf.length) {
  const len = buf.readUInt32LE(offset);
  chunks.push(buf.slice(offset + 4, offset + 4 + len));
  offset += 4 + len;
}
console.log('chunks', chunks.map(c => c.length));

function decompress(data) {
  if (data[0] === 0x28 && data[1] === 0xb5 && data[2] === 0x2f && data[3] === 0xfd) return zlib.zstdDecompressSync(data);
  try { return zlib.inflateRawSync(data); } catch (e) { return zlib.inflateSync(data); }
}

const schema = kiwi.decodeBinarySchema(decompress(chunks[0]));
const compiled = kiwi.compileSchema(schema);
const message = compiled.decodeMessage(decompress(chunks[1]));
console.log('message keys', Object.keys(message));
console.log('nodeChanges', message.nodeChanges && message.nodeChanges.length, 'blobs', message.blobs && message.blobs.length);

// Blobs are Uint8Arrays; store as base64 so the JSON stays usable.
const json = JSON.stringify(message, (k, v) => (v instanceof Uint8Array ? { __b64: Buffer.from(v).toString('base64') } : typeof v === 'bigint' ? Number(v) : v));
fs.writeFileSync(output, json);
console.log('wrote', output, (json.length / 1e6).toFixed(1) + 'MB');
