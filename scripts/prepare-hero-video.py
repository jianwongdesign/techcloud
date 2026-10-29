#!/usr/bin/env python3
"""Encode a silent hero loop with a short dissolve across its repeat boundary."""
import argparse
import io
import json
from pathlib import Path
import subprocess
from PIL import Image

parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('source', type=Path)
parser.add_argument('--output-dir', type=Path, default=Path(__file__).resolve().parents[1] / 'theme/techcloud/assets')
args = parser.parse_args()
source = args.source.resolve(strict=True)
probe = json.loads(subprocess.check_output(['ffprobe', '-v', 'error', '-show_entries', 'format=duration', '-of', 'json', str(source)]))
duration = float(probe['format']['duration'])
if duration < 2:
    raise SystemExit('The hero source must be at least two seconds long.')
overlap = 0.5
args.output_dir.mkdir(parents=True, exist_ok=True)
video = args.output_dir / 'hero-motion.mp4'
poster = args.output_dir / 'hero-motion-poster.webp'
if source == video.resolve():
    raise SystemExit('Use the downloaded source, not the output loop, as input.')
# Start after the first half-second. Blend the tail back into that first half-second,
# so the end flows into the start without reversing the camera movement.
filters = (
    '[0:v]fps=24,scale=1280:720:flags=lanczos,setsar=1,format=yuv420p,split[body][lead];'
    f'[body]trim=start={overlap},setpts=PTS-STARTPTS[body];'
    f'[lead]trim=duration={overlap},setpts=PTS-STARTPTS[lead];'
    f'[body][lead]xfade=transition=fade:duration={overlap}:offset={duration-2*overlap}[loop]'
)
subprocess.run(['ffmpeg', '-hide_banner', '-loglevel', 'error', '-y', '-i', str(source),
                '-filter_complex', filters, '-map', '[loop]', '-an', '-t', str(duration-overlap),
                '-c:v', 'libx264', '-preset', 'medium', '-crf', '24', '-pix_fmt', 'yuv420p',
                '-movflags', '+faststart', str(video)], check=True)
frame = subprocess.check_output(['ffmpeg', '-hide_banner', '-loglevel', 'error', '-i', str(video),
                                 '-frames:v', '1', '-f', 'image2pipe', '-c:v', 'png', '-'])
Image.open(io.BytesIO(frame)).save(poster, 'WEBP', quality=82)
print(f'Prepared {video.name}: {duration-overlap:.2f}s, {video.stat().st_size:,} bytes; {poster.name}')
