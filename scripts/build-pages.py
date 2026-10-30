#!/usr/bin/env python3
"""Publish full review before expiry; publish only lock screens afterwards."""
from pathlib import Path
import shutil
from preview_expiry import ROOT, expired, locked_document

def build(output, now=None):
    source = ROOT / 'preview'
    if output.exists():
        shutil.rmtree(output)
    if not expired(now):
        shutil.copytree(source, output)
        return 'active'
    for page in source.rglob('*.html'):
        target = output / page.relative_to(source)
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_text(locked_document())
    (output / '.nojekyll').touch()
    (output / 'robots.txt').write_text('User-agent: *\nDisallow: /\n')
    return 'expired'

if __name__ == '__main__':
    print('Pages build:', build(ROOT / '_site'))
