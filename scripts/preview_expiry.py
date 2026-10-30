"""Shared fixed preview deadline and expiry screen."""
from datetime import datetime, timezone
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
POLICY = json.loads((ROOT / 'preview-policy.json').read_text())
EXPIRES_AT = POLICY['expires_at']
DEADLINE = datetime.fromisoformat(EXPIRES_AT.replace('Z', '+00:00'))
assert DEADLINE.tzinfo is not None
LOCK_MARKUP = '''<style>html,body{margin:0;min-height:100%;background:#f7f8fb;color:#202b3e;font-family:Arial,sans-serif}*{box-sizing:border-box}.preview-lock{min-height:100svh;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;max-width:720px;margin:auto;padding:48px 28px}.preview-lock p{font-size:17px;line-height:1.7;max-width:48ch}.preview-lock h1{font-size:clamp(34px,7vw,56px);line-height:1.08;letter-spacing:-.04em;margin:22px 0 8px}.preview-lock span{color:#33398d;font-size:12px;letter-spacing:.14em;font-weight:700}.preview-lock a{color:#33398d;font-weight:700;display:inline-block;padding:14px 0}</style><main class="preview-lock"><span>TECHCLOUD · DESIGN PREVIEW</span><h1>This preview has expired.</h1><p>The review period has ended. Please contact AdCendes to arrange access to the latest preview.</p><a href="https://adcendes.com.sg/">Contact AdCendes ↗</a></main>'''

def expired(now=None):
    return (now or datetime.now(timezone.utc)) >= DEADLINE

def guard():
    source = (ROOT / 'scripts/preview-expiry.js').read_text()
    return source.replace('__PREVIEW_EXPIRES_AT__', json.dumps(EXPIRES_AT)).replace('__PREVIEW_LOCK_MARKUP__', json.dumps(LOCK_MARKUP))

def locked_document():
    return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Preview expired · TechCloud</title></head><body>' + LOCK_MARKUP + '</body></html>'
