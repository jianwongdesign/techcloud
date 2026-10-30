#!/usr/bin/env python3
"""Extract the five supplied website drafts without publishing the client DOCX."""
from pathlib import Path
from zipfile import ZipFile
from xml.etree import ElementTree as E
from urllib.parse import urlsplit
import html, json, re, sys, hashlib
ROOT = Path(__file__).resolve().parents[1]
source = Path(sys.argv[1]) if len(sys.argv)>1 else next(ROOT.glob('FPD*.docx'))
W='{http://schemas.openxmlformats.org/wordprocessingml/2006/main}'
R='{http://schemas.openxmlformats.org/officeDocument/2006/relationships}'
paths={'/services/managed-services':'/services/managed-it-services/', '/services/end-user-support':'/services/end-user-it-support/', '/services/installation-and-provisioning':'/services/it-installation-infrastructure/'}
def plain(s): return html.unescape(re.sub('<[^>]+>','',s))
def clean(s):
    for old,new in [('IR Installation','IT Installation'),('SIngapore','Singapore'),('What Are IT Staffing?','What Is IT Staffing?'),('demeanor','demeanour')]: s=s.replace(old,new)
    return s.replace(' (done)','')
def link(url):
    u=urlsplit(url)
    if u.netloc=='techcloud.sg':
        if u.fragment.lower()=='contact': return '/contact/#contact-form'
        if u.fragment.lower()=='services': return '/services/'
        path=paths.get(u.path,u.path.rstrip('/')+'/')
        return path+('#how' if u.fragment=='how' else '')
    return url
with ZipFile(source) as z:
    root=E.fromstring(z.read('word/document.xml'))
    rels={e.get('Id'):e.get('Target') for e in E.fromstring(z.read('word/_rels/document.xml.rels'))}
    def render(node):
        if node.tag==W+'t': return html.escape(node.text or '')
        if node.tag in [W+'br',W+'cr']: return '\n'
        if node.tag==W+'tab': return ' '
        if node.tag in [W+'del',W+'pPr',W+'rPr']: return ''
        s=''.join(render(x) for x in node)
        if node.tag==W+'hyperlink': return '<a href="'+html.escape(link(rels.get(node.get(R+'id'),'')),quote=True)+'">'+s+'</a>'
        return s
    lines=[clean(line.strip()) for p in root.findall('.//'+W+'body//'+W+'p') for line in render(p).split('\n') if line.strip()]
starts=[i for i,l in enumerate(lines) if re.match(r'^\d\. Page URL',plain(l))]
pages=[]
for i,start in enumerate(starts):
    lineset=lines[start:starts[i+1] if i+1<len(starts) else len(lines)]
    url=plain(lineset[0]).split('Page URL: ')[1]
    data={'source_url':url,'path':link(url),'sections':[]}
    for key,prefix in [('seo_title','Recommended Page Title: '),('description','Recommended Meta Description: ')]:
        data[key]=next(plain(l)[len(prefix):] for l in lineset if plain(l).startswith(prefix))
    if data['description'].startswith('['):
        data['description']='Proactive managed IT services in Singapore, including monitoring, maintenance, security and tailored support. Book a free consultation with TechCloud.'
        data['description_note']='Written from supplied scope because the recommended description was a placeholder.'
    content_start=next(j for j,l in enumerate(lineset) if l.startswith('H1 '))
    data['heading']=plain(lineset[content_start])[3:]
    current={'level':1,'heading':data['heading'],'items':[]}
    data['sections'].append(current)
    for l in lineset[content_start+1:]:
        p=plain(l)
        if p.startswith('Checklist'): break
        l=re.sub(r'^&lt;(DROPDOWN|ACCORDION)&gt;','',l)
        match=re.match(r'^H([23]) (.*)',l)
        if match:
            current={'level':int(match[1]),'heading':plain(match[2]).rstrip(':'),'items':[]}; data['sections'].append(current)
        else: current['items'].append(l)
    pages.append(data)
assert len(pages)==5
output={'source_file':source.name,'source_sha256':hashlib.sha256(source.read_bytes()).hexdigest(),'pages':pages}
(ROOT/'scripts/client-brief-content.json').write_text(json.dumps(output,ensure_ascii=False,indent=2)+'\n')
print('Extracted five pages, preserving supplied text and internal links.')
