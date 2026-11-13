#!/usr/bin/env python3
"""Verify real WordPress blog routes and generated review navigation."""
import json
import re
from html.parser import HTMLParser
from pathlib import Path
from urllib.request import urlopen
from urllib.error import HTTPError

BASE = 'http://localhost:8129'
ROOT = Path(__file__).resolve().parents[1]

class Page(HTMLParser):
    def __init__(self, source):
        super().__init__()
        self.cards = 0
        self.h1s = 0
        self.links = []
        self.current = []
        self.images = []
        self.feed(source)
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'h1': self.h1s += 1
        if tag == 'article' and 'blog-card' in attrs.get('class', '').split(): self.cards += 1
        if tag == 'a': self.links.append(attrs.get('href', ''))
        if attrs.get('aria-current') == 'page': self.current.append(attrs)
        if tag == 'img': self.images.append(attrs)

def fetch(path):
    with urlopen(BASE + path, timeout=20) as response:
        assert response.status == 200
        return response.read().decode()

first = fetch('/blog/')
second = fetch('/blog/page/2/')
assert Page(first).cards == 6
assert Page(second).cards == 1
assert BASE + '/blog/page/2/' in Page(first).links
assert BASE + '/blog/' in Page(second).links
assert 'aria-current="page"' in first and 'aria-current="page"' in second
assert not set(re.findall(r'<a class="blog-card-link" href="([^"]+)"', first)) & set(re.findall(r'<a class="blog-card-link" href="([^"]+)"', second))
for source in [first, second]:
    assert Page(source).h1s == 1
    assert 'type="search"' not in source and '<select' not in source
    assert 'blog.css' in source
try:
    fetch('/blog/page/999/')
    raise AssertionError('Out-of-range pagination must be 404')
except HTTPError as error:
    assert error.code == 404

posts = json.loads(fetch('/wp-json/wp/v2/posts?per_page=100'))
for post in posts:
    route = '/blog/' + post['slug'] + '/'
    assert post['link'] == BASE + route
    source = fetch(route)
    page = Page(source)
    assert page.h1s == 1 and page.cards == 3
    assert 'Related Blogs' in source
    assert all(term not in source for term in ['Share Post', 'Related Resources', 'All Resources', 'contact-banner'])
    cards = re.findall(r'<a class="blog-card-link" href="([^"]+)"', source)
    assert len(set(cards)) == 3 and post['link'] not in cards
    assert all('alt' in image for image in page.images)
    assert 'Sample article for design review' in source
    assert 'class="blog-body"' in source and '<aside' not in source
    exported = (ROOT / 'preview' / route.strip('/') / 'index.html').read_text()
    assert 'blog.css' in exported and 'localhost:' not in exported
    assert 'wp-content/uploads' not in exported
    for srcset in re.findall(r'srcset="([^"]+)"', exported):
        for candidate in srcset.split(','):
            url = candidate.strip().split()[0]
            assert ((ROOT / 'preview' / route.strip('/')) / url).resolve().exists()

for route in ['/', '/services/', '/case-studies/', '/contact/']:
    source = fetch(route)
    assert re.search(r'<nav aria-label="Footer navigation">.*?>Blog</a>', source)
print(f'PASS: {len(posts)} article routes; six/one pagination without duplicates; page 999 is 404; three distinct related posts excluding current; footer Blog links; exported responsive images.')
