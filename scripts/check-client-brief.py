#!/usr/bin/env python3
"""Check the client's supplied copy, SEO fields, FAQs and safe preview forms."""
from pathlib import Path
from html.parser import HTMLParser
import html,json,re
ROOT=Path(__file__).resolve().parents[1]
class Page(HTMLParser):
    def __init__(self): super().__init__();self.text=[];self.skip=0;self.meta={};self.forms=[];self.fields=[];self.details=0
    def handle_starttag(self,tag,attrs):
        a=dict(attrs)
        if tag in ('p','h1','h2','h3','div','section','summary','br','title'): self.text.append(' ')
        if tag in ('style','script'): self.skip+=1
        if tag=='meta': self.meta[a.get('name')]=a.get('content')
        if tag=='form': self.forms.append(a)
        if tag in ('input','textarea','select'): self.fields.append(a)
        if tag=='details': self.details+=1
    def handle_endtag(self,tag):
        if tag in ('p','h1','h2','h3','div','section','summary','title'): self.text.append(' ')
        if tag in ('style','script'): self.skip-=1
    def handle_data(self,data):
        if not self.skip:self.text.append(data)
def plain(s): return html.unescape(re.sub('<[^>]*>','',s))
def norm(s):return re.sub(r'\s+',' ',re.sub(r'[^\w\s]','',plain(s))).strip().casefold()
ignored={'Services','Why us?','Contact us','Contact Us','Blogs','Get started','Submit','View all articles','Get Your Complimentary IT Consult Today!'}
missing=[];checks=0
for draft in json.loads((ROOT/'scripts/client-brief-content.json').read_text())['pages']:
    p=ROOT/'preview'/draft['path'].strip('/')/'index.html'; doc=Page();doc.feed(p.read_text());rendered=norm(''.join(doc.text))
    assert doc.meta['description']==draft['description'],draft['path']
    assert norm(draft['seo_title']) in rendered,draft['path']
    assert len(doc.forms)==1 and 'data-preview-form' in doc.forms[0],draft['path']
    assert not any('name' in field for field in doc.fields),draft['path']
    assert 'this form does not send messages' in rendered
    excluded=False;in_faq=False;faq_count=0
    for section in draft['sections']:
        heading=section['heading']
        if section['level']<=2:
            excluded=heading in ['Who Are Our Clients?','What Our Customers Say About Us','Suggested Blogs']
            in_faq=heading.startswith('Frequently Asked')
        elif in_faq:faq_count+=1
        if excluded: continue
        if not heading.startswith('Stuck with'):
            checks+=1
            if norm(heading) not in rendered: missing.append((draft['path'],'heading',heading))
        for item in section['items']:
            text=plain(item).strip()
            if text in ignored or text.startswith(('[','<BUTTON>','<ACCORDION>','<DROPDOWN>')):
                if not text.startswith('[ICON]'):continue
            text=re.sub(r'^(<ICON>|\[ICON\])\s*','',text)
            if not text:continue
            checks+=1
            if norm(text) not in rendered:missing.append((draft['path'],'copy',text))
    assert doc.details>=faq_count,(draft['path'],faq_count,doc.details)
    for marker in ['[CLIENT LOGOS]','[REVIEW MODULES]','[BLOG MODULES]','<BUTTON>','<ICON>','[CONTACT FORM]','H2 Our Scope']:
        assert marker not in ' '.join(doc.text),(draft['path'],marker)
assert not missing,json.dumps(missing,indent=2)
print(f'PASS: {checks} supplied headings and copy items, five SEO descriptions, FAQ dropdowns and five non-sending preview forms.')
