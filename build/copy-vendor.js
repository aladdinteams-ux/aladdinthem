/**
 * Copies third-party font/icon assets into the theme so the theme never
 * depends on external CDNs (important for Iranian hosting / users).
 */
const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const nm = (p) => path.join(root, 'node_modules', p);
const out = (p) => path.join(root, 'assets', p);

function copy(src, dest) {
  fs.mkdirSync(path.dirname(dest), { recursive: true });
  fs.copyFileSync(src, dest);
  console.log('copied', path.relative(root, dest));
}

// Vazirmatn variable font.
copy(nm('vazirmatn/fonts/webfonts/Vazirmatn[wght].woff2'), out('fonts/vazirmatn/Vazirmatn-Variable.woff2'));
copy(nm('vazirmatn/OFL.txt'), out('fonts/vazirmatn/OFL.txt'));

// Bootstrap Icons (font + css).
copy(nm('bootstrap-icons/font/fonts/bootstrap-icons.woff2'), out('vendor/bootstrap-icons/fonts/bootstrap-icons.woff2'));
copy(nm('bootstrap-icons/font/fonts/bootstrap-icons.woff'), out('vendor/bootstrap-icons/fonts/bootstrap-icons.woff'));
copy(nm('bootstrap-icons/font/bootstrap-icons.min.css'), out('vendor/bootstrap-icons/bootstrap-icons.min.css'));
copy(nm('bootstrap-icons/LICENSE'), out('vendor/bootstrap-icons/LICENSE'));

// Icon list consumed by the Elementor icon picker (Icons_Manager additional tab).
const map = JSON.parse(fs.readFileSync(nm('bootstrap-icons/font/bootstrap-icons.json'), 'utf8'));
const json = { icons: Object.keys(map) };
fs.writeFileSync(out('vendor/bootstrap-icons/elementor-icons.json'), JSON.stringify(json));
console.log('wrote elementor-icons.json with', json.icons.length, 'icons');
