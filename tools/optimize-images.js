/**
 * Converts the raw Figma image fills (design/source-images/<imageRef>) into
 * responsive WebP files under static/assets/images/.
 *
 * Every image gets one file per width in its `widths` list (capped at the
 * source width), named `<name>-<width>.webp`, so the HTML can use srcset.
 *
 * Usage: npm run images
 */
const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const ROOT = path.resolve(__dirname, '..');
const SRC = path.join(ROOT, 'design', 'source-images');
const OUT = path.join(ROOT, 'static', 'assets', 'images');

// imageRef (from the Figma file) -> output name + widths needed by the layout.
// Widths cover 1x/2x of the desktop, laptop and mobile display sizes.
const IMAGES = [
  { ref: '4cac57dce13185e5e5e6946611ce05ec70271908', name: 'hero-buildings', widths: [720, 1080, 1440, 1840] },
  { ref: '597f545bcb329f29999c8c335bec608731f34db8', name: 'seaside-serenity-villa', widths: [360, 480, 720, 1060] },
  { ref: 'a2b4e0260c692d688fb8fd238642558f0aee6697', name: 'metropolitan-haven', widths: [360, 480, 626] },
  { ref: '788f8cf7af7e05c7ae4a90ebd4dcf750b626e1f4', name: 'rustic-retreat-cottage', widths: [360, 480, 740] },
  { ref: '1dda9e286768bcc08f8c496bce3bb27566805e08', name: 'client-wade-warren', widths: [120, 240] },
  { ref: '5f5676c1845a6997318911013f36a692735c3ad1', name: 'client-emelie-thomson', widths: [120, 240] },
  { ref: 'd75502f71b1b8c139adf2f732b1fe5a854a8e672', name: 'client-john-mans', widths: [120, 240] },
  { ref: '493db206ad49649fccdfce1e8a3d58bffa111a0d', name: 'about-journey', widths: [480, 760, 1140, 1570] },
  { ref: 'e8477d9a1abbc65bb511c1d84b2a56ef165d6f2e', name: 'team-max-mitchell', widths: [360, 640, 1060] },
  { ref: 'e90148adebe8a557c4665cccf79840be94475065', name: 'team-sarah-johnson', widths: [360, 635] },
  { ref: '2c0806f9794ef35a53faf7d4444a6c0f082fa16b', name: 'team-david-brown', widths: [360, 626] },
  { ref: '79ceba09cbc6b2bc1a8ca25fdc7f99824ba4c255', name: 'team-michael-turner', widths: [360, 634] },
  { ref: '87fe0a7aeb054fbd88c44bbb3da756276d229ba2', name: 'villa-gallery-01', widths: [320, 480, 760, 1060] },
  { ref: 'd5eb949ec34e6367417f7283a0534730129af9ea', name: 'villa-gallery-02', widths: [320, 480, 760, 1060] },
  { ref: '4c69c331ae9ef333c0e2d7287f6d29426ff57b27', name: 'villa-gallery-03', widths: [320, 480, 760, 1060] },
  { ref: 'e32849b76a81ebf64ec3e8cf572251fb7eb89902', name: 'villa-gallery-04', widths: [320, 480, 760, 1060] },
  { ref: '9d215a0c1a1fd47ad163e62310e497b7a0f30470', name: 'villa-gallery-05', widths: [320, 480, 626] },
  { ref: 'f516e52bd167b08945642c9c50e6fef40a2a9b19', name: 'villa-gallery-06', widths: [320, 480, 626] },
  { ref: 'bc7dcf9bdfa4fb9bab4390d28f8f89bbfb1ac510', name: 'villa-gallery-07', widths: [320, 480, 626] },
  { ref: '6fd6381336d09396560fa7eb122adae84b3db23e', name: 'villa-gallery-08', widths: [320, 480, 626] },
  { ref: '3abcea88736c22d5b501dfd29ec51b259321ef35', name: 'villa-gallery-09', widths: [320, 480, 626] },
  { ref: '22be0450ebdbc0bcc4a6f34cd7b83c757bcea5c5', name: 'office-gallery-01', widths: [480, 760, 1140, 1577] },
  { ref: 'e0682926f998ebd8113ac977afdffb4293fa2573', name: 'office-gallery-02', widths: [480, 760, 1140, 1577] },
  { ref: 'df9a8ef2f716acce45da7d72c298f040caf7ace5', name: 'office-gallery-03', widths: [480, 760, 1140, 1578] },
  { ref: '4ea684067ff4aaeb5bf5310407755e3554743530', name: 'office-gallery-04', widths: [360, 769] },
  { ref: '0f92809831e48d24dda1db4a6f98c790d6d6cd8f', name: 'office-gallery-05', widths: [360, 770] },
  { ref: 'e68f0435ec2f92d786e418597af5875416078007', name: 'office-gallery-06', widths: [480, 760, 1140, 1576] },
];

async function run() {
  fs.mkdirSync(OUT, { recursive: true });
  const manifest = {};
  for (const img of IMAGES) {
    const input = path.join(SRC, img.ref);
    const meta = await sharp(input).metadata();
    const widths = [...new Set(img.widths.map(w => Math.min(w, meta.width)))].sort((a, b) => a - b);
    manifest[img.name] = { width: meta.width, height: meta.height, widths };
    for (const w of widths) {
      const file = path.join(OUT, `${img.name}-${w}.webp`);
      await sharp(input).resize({ width: w }).webp({ quality: 78, effort: 6 }).toFile(file);
    }
    console.log(img.name, widths.join(', '));
  }
  fs.writeFileSync(path.join(OUT, 'manifest.json'), JSON.stringify(manifest, null, 2));
}

run().catch(err => { console.error(err); process.exit(1); });
