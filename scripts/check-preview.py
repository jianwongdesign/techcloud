#!/usr/bin/env python3
"""Check every exported local link, asset, anchor, heading and preview boundary."""
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urlsplit, unquote
import re

ROOT = Path(__file__).resolve().parents[1] / 'preview'
class Page(HTMLParser):
    def __init__(self):
        super().__init__(); self.links=[]; self.ids=set(); self.headings=0; self.images=[]; self.videos=[]; self.forms=[]; self.fields=[]; self.maps=[]
    def handle_starttag(self, tag, attributes):
        attrs=dict(attributes)
        if tag=='base':
            assert attrs.get('href') == '/techcloud/', 'Unexpected preview base URL'
            return
        if tag=='h1': self.headings+=1
        if 'id' in attrs: self.ids.add(attrs['id'])
        for key in ('href','src','poster','data-src'):
            if key in attrs: self.links.append(attrs[key])
        if tag=='img': self.images.append(attrs)
        if tag=='video': self.videos.append(attrs)
        if tag=='form': self.forms.append(attrs)
        if tag in ('input', 'select', 'textarea'): self.fields.append(attrs)
        if tag=='iframe': self.maps.append(attrs)

errors=[]; pages={}
for path in ROOT.rglob('*.html'):
    parser=Page(); text=path.read_text(); parser.feed(text); pages[path.resolve()]=parser
    if parser.headings!=1: errors.append(f'{path}: expected one h1, found {parser.headings}')
    for forbidden in ['localhost:', '127.0.0.1', 'wp-admin', 'wp-json', 'wp-content', 'elementor']:
        if forbidden in text: errors.append(f'{path}: leaked {forbidden}')
    if 'noindex,nofollow' not in text or 'Design preview' not in text: errors.append(f'{path}: preview guard missing')
    for img in parser.images:
        if 'alt' not in img: errors.append(f'{path}: missing image alt')
    if len(parser.maps) != 1 or not parser.maps[0].get('title'): errors.append(f'{path}: missing labelled footer map')
    if path == ROOT/'contact/index.html':
        if len(parser.forms) != 1 or 'data-preview-form' not in parser.forms[0]: errors.append(f'{path}: preview form missing')
        if any('name' in field for field in parser.fields): errors.append(f'{path}: preview form could transmit named fields')
        if any(token in text for token in ['tc_nonce', 'submission_token', 'tc_send_enquiry']): errors.append(f'{path}: backend form token leaked')
        if 'this form does not send messages' not in text: errors.append(f'{path}: preview form disclosure missing')
    expected_videos = 1 if path == ROOT/'index.html' else 0
    if len(parser.videos) != expected_videos: errors.append(f'{path}: expected {expected_videos} hero videos, found {len(parser.videos)}')
    for video in parser.videos:
        if not all(key in video for key in ('muted', 'loop', 'playsinline', 'poster', 'data-src')): errors.append(f'{path}: hero video missing playback attributes or fallback')
for path, page in pages.items():
    for link in page.links:
        parts=urlsplit(link)
        if parts.scheme or parts.netloc: continue
        if parts.path.startswith('/'): errors.append(f'{path}: absolute local path {link}'); continue
        target=(path.parent / unquote(parts.path)).resolve() if parts.path else path
        if target.is_dir(): target=target/'index.html'
        if not target.exists(): errors.append(f'{path}: broken link {link}')
        elif parts.fragment and target in pages and unquote(parts.fragment) not in pages[target].ids: errors.append(f'{path}: missing anchor {link}')
for css in (ROOT/'assets').glob('*.css'):
    for link in re.findall(r'url\([\'"]?([^\)\'\"]+)',css.read_text()):
        if not urlsplit(link).scheme and not (css.parent/link).exists(): errors.append(f'{css}: missing CSS asset {link}')
assert not errors, '\n'.join(errors)
print(f'PASS: {len(pages)} HTML pages; all internal links, anchors and assets; one h1 per page; preview guards; no local URLs or page-builder dependencies.')
