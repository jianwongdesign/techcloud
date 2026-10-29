#!/usr/bin/env python3
"""Export the actual local WordPress render to a self-contained review site."""
from pathlib import Path
from urllib.request import urlopen
from urllib.error import HTTPError
from urllib.parse import urlsplit, urlunsplit
import html
import hashlib
import json
import os
import re
import shutil

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / 'preview'
BASE = 'http://localhost:8129'

def fetch(path):
    if os.environ.get('TC_FETCH_DIR'):
        cache = Path(os.environ['TC_FETCH_DIR'])
        name = 'pages.json' if path.startswith('/wp-json/wp/v2/pages') else 'cases.json' if path.startswith('/wp-json/wp/v2/tc_case') else path.strip('/').replace('/', '__') + '.html'
        source = (cache / name).read_text()
        if path == '/this-page-does-not-exist/':
            import io
            raise HTTPError(BASE + path, 404, 'Not Found', {}, io.BytesIO(source.encode()))
        return source
    with urlopen(BASE + path, timeout=20) as response:
        return response.read().decode()

def rewrite_url(value, current):
    value = html.unescape(value)
    if value.startswith(BASE):
        value = value[len(BASE):]
    if value.startswith('/wp-content/themes/techcloud/assets/'):
        value = '/assets/' + value.split('/assets/', 1)[1]
    if not value.startswith('/'):
        return value
    parts = urlsplit(value)
    target = parts.path.lstrip('/')
    relative = os.path.relpath(OUT / target, current)
    if parts.path.endswith('/'):
        relative += '/'
    return urlunsplit(('', '', relative, parts.query, parts.fragment))

pages = json.loads(fetch('/wp-json/wp/v2/pages?per_page=100'))
routes = ['/']
for page in pages:
    path = urlsplit(page['link']).path
    if page['slug'] == 'sample-page' or path == '/':
        continue
    routes.append(path)
routes.append('/case-studies/')
cases = json.loads(fetch('/wp-json/wp/v2/tc_case?per_page=100'))
routes += [urlsplit(case['link']).path for case in cases]
OUT.mkdir(exist_ok=True)
# This directory is generated only. Rebuild deterministically, including removals.
for child in OUT.iterdir():
    if child.is_dir():
        shutil.rmtree(child)
    else:
        child.unlink()
shutil.copytree(ROOT / 'theme/techcloud/assets', OUT / 'assets')

def export(route, source, destination):
    title = re.search(r'<title>(.*?)</title>', source, re.S).group(1)
    body = re.search(r'<body([^>]*)>(.*)</body>', source, re.S)
    content = re.sub(r'<script\b[^>]*>.*?</script>', '', body.group(2), flags=re.S)
    content = re.sub(r'<style\b[^>]*>.*?</style>', '', content, flags=re.S)
    # GitHub Pages has no WordPress backend. Keep a reviewable form, without
    # endpoints, tokens or named fields that could send enquiry data in a URL.
    def preview_form(match):
        form = match.group(0)
        form = re.sub(r'<input\b[^>]*type=["\']hidden["\'][^>]*>', '', form)
        form = re.sub(r'\sname=("|\').*?\1', '', form)
        form = re.sub(r'\saction=("|\').*?\1', ' action="#contact-form"', form, count=1)
        form = re.sub(r'\smethod=("|\').*?\1', ' method="get"', form, count=1)
        form = form.replace('data-contact-form', 'data-contact-form data-preview-form', 1)
        form = form.replace('<div class="form-fields">', '<p class="form-note preview-form-note">Design preview — this form does not send messages.</p><div class="form-fields">', 1)
        form = form.replace('>Send enquiry ', '>Preview enquiry ', 1)
        return form
    content = re.sub(r'<form\b[^>]*data-contact-form\b[^>]*>.*?</form>', preview_form, content, flags=re.S)
    content = re.sub(r'\b(href|src|poster|data-src)=("|\')(.*?)\2', lambda m: f'{m[1]}={m[2]}{html.escape(rewrite_url(m[3], destination.parent), quote=True)}{m[2]}', content)
    def asset(name):
        digest = hashlib.sha256((OUT / 'assets' / name).read_bytes()).hexdigest()[:12]
        return rewrite_url('/assets/' + name + '?v=' + digest, destination.parent)
    css = asset('site.css')
    rich_css = asset('rich.css')
    js = asset('site.js')
    notice = '<div class="preview-notice">TechCloud · Design preview · Content for review</div>'
    base_tag = '<base href="/techcloud/">' if route == '/404.html' else ''
    document = f'''<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">{base_tag}<title>{title} · Design preview</title><meta name="description" content="TechCloud custom WordPress website design preview."><link rel="stylesheet" href="{css}"><link rel="stylesheet" href="{rich_css}"><style>.preview-notice{{text-align:center;background:#172135;color:#e1e7f1;font:10px/1.6 Manrope,sans-serif;letter-spacing:.05em;padding:7px 16px}}</style></head>
<body{body.group(1)}>{notice}{content}<script src="{js}" defer></script></body></html>'''
    destination.parent.mkdir(parents=True, exist_ok=True)
    destination.write_text(re.sub(r'[ \t]+$', '', document, flags=re.M))
    print(route)

for route in sorted(set(routes)):
    export(route, fetch(route), OUT / route.strip('/') / 'index.html')
try:
    fetch('/this-page-does-not-exist/')
except HTTPError as error:
    if error.code != 404:
        raise
    export('/404.html', error.read().decode(), OUT / '404.html')
(OUT / '.nojekyll').touch()
(OUT / 'robots.txt').write_text('User-agent: *\nDisallow: /\n')
print(f'Exported {len(set(routes))} pages plus 404 to {OUT}')
