# Pixel diff of two screenshot folders: % of pixels whose RGB differs by > 24 (anti-aliasing tolerant).
import sys, os
from PIL import Image, ImageChops
a_dir, b_dir = sys.argv[1], sys.argv[2]
worst = 0
for f in sorted(os.listdir(a_dir)):
    pb = os.path.join(b_dir, f)
    if not f.endswith('.png') or not os.path.exists(pb):
        continue
    a = Image.open(os.path.join(a_dir, f)).convert('RGB'); b = Image.open(pb).convert('RGB')
    h = min(a.height, b.height); w = min(a.width, b.width)
    d = ImageChops.difference(a.crop((0, 0, w, h)), b.crop((0, 0, w, h))).convert('L').point(lambda v: 255 if v > 24 else 0)
    pct = 100.0 * sum(d.histogram()[255:]) / (w * h)
    worst = max(worst, pct)
    print(f"{f:28s} size {a.height}->{b.height}  diff {pct:6.3f}%")
print(f"WORST {worst:.3f}%")
