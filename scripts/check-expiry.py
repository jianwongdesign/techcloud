#!/usr/bin/env python3
"""Verify expiry boundary and ensure expired deployments contain no site assets."""
from datetime import timedelta
from pathlib import Path
import importlib.util
import tempfile
from preview_expiry import DEADLINE, expired, guard

spec = importlib.util.spec_from_file_location('pages_build', Path(__file__).with_name('build-pages.py'))
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)
assert not expired(DEADLINE - timedelta(microseconds=1))
assert expired(DEADLINE)
assert expired(DEADLINE + timedelta(days=1))
with tempfile.TemporaryDirectory() as folder:
    output = Path(folder) / 'site'
    assert module.build(output, DEADLINE - timedelta(seconds=1)) == 'active'
    assert (output / 'assets/hero-motion.mp4').exists()
    assert guard() in (output / 'index.html').read_text()
    # Rebuild the same directory to confirm old media really disappears.
    assert module.build(output, DEADLINE) == 'expired'
    pages = list(output.rglob('*.html'))
    assert len(pages) == 18
    assert not (output / 'assets').exists()
    assert all('This preview has expired.' in p.read_text() for p in pages)
    assert all('<script' not in p.read_text() and '<iframe' not in p.read_text() for p in pages)
print('PASS: exact deadline, active export, all 18 expired routes, and removal of published site assets.')
