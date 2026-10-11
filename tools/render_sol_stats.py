#!/usr/bin/env python3
"""Generate weekly PNG/JSON from SB Stats daily fixtures; Pillow required."""
import argparse,json
from pathlib import Path
from datetime import date,timedelta
from PIL import Image,ImageDraw,ImageFont,PngImagePlugin

def font(n):
    for name in ['/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf','/usr/share/fonts/truetype/liberation2/LiberationSans-Regular.ttf']:
        try:return ImageFont.truetype(name,n)
        except OSError:pass
    return ImageFont.load_default()

def write(path,value):
    path.parent.mkdir(parents=True,exist_ok=True)
    path.write_text(json.dumps(value,indent=2)+'\n')

def png(path,record):
    img=Image.new('RGB',(1050,410),'#fafbfd')
    p=ImageDraw.Draw(img)
    p.text((30,22),record['subject'].replace('-',' ').title()+' / '+record['week'],font=font(28),fill='#1c2b3c')
    p.text((30,65),record['start']+' to '+record['end']+' UTC / '+str(record['total'])+' sampled signatures',font=font(17),fill='#5d7083')
    left,right,top,bottom=80,1000,115,315
    peak=max(1,max(row['count'] for row in record['points']))
    for i in range(5):
        y=bottom-round((bottom-top)*i/4)
        p.line((left,y,right,y),fill='#dce3ec')
        p.text((27,y-9),str(round(peak*i/4)),font=font(14),fill='#5d7083')
    for i,row in enumerate(record['points']):
        center=left+(right-left)*(i+.5)/7
        y=bottom-round((bottom-top)*row['count']/peak)
        if row['count']:p.rectangle((center-27,y,center+27,bottom),fill='#24688f' if record['subject']=='get-money' else '#585596')
        p.text((center-16,max(91,y-21)),str(row['count']),font=font(15),fill='#1c2b3c')
        p.text((center-18,bottom+12),['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][i],font=font(15),fill='#1c2b3c')
        p.text((center-21,bottom+35),row['date'][5:],font=font(13),fill='#5d7083')
    p.text((30,385),'SAMPLE ONLY: top-20 token-account signatures, not verified token transfers',font=font(13),fill='#5d7083')
    meta=PngImagePlugin.PngInfo()
    meta.add_itxt('sbstats.json',json.dumps(record,separators=(',',':')),zip=True)
    path.parent.mkdir(parents=True,exist_ok=True)
    img.save(path,pnginfo=meta,optimize=True)

def run(source,output):
    data=json.loads(Path(source).read_text())
    catalog={'schema':'sbstats/catalog-v1','timezone':'UTC','weeks':[]}
    for week,subjects in sorted(data['weeks'].items()):
        year,w=week.split('-W')
        start=date.fromisocalendar(int(year),int(w),1)
        days=[(start+timedelta(days=i)).isoformat() for i in range(7)]
        location=Path(output)/year/('W'+w)
        item={'schema':'sbstats/week-v1','id':week,'timezone':'UTC','start':days[0],'end':days[-1],'metric':'sampled_distinct_signatures','subjects':{},'commentary':'commentary.json'}
        for token,counts in subjects.items():
            points=[{'date':day,'count':counts[i]} for i,day in enumerate(days)]
            obj={'schema':'sbstats/png-v1','week':week,'subject':token,'start':days[0],'end':days[-1],'timezone':'UTC','metric':'sampled_distinct_signatures','points':points,'total':sum(counts),'coverage':data['coverage'],'source':'examples/solana/data/sample-daily.json'}
            png(location/(token+'.png'),obj)
            item['subjects'][token]={'total':sum(counts),'chart':token+'.png','points':points}
        write(location/'week.json',item)
        if not (location/'commentary.json').exists():
            write(location/'commentary.json',{'schema':'sbstats/commentary-v1','week':week,'entries':[]})
        catalog['weeks'].append({'id':week,'path':year+'/W'+w+'/week.json','start':days[0],'end':days[-1]})
    write(Path(output)/'catalog.json',catalog)
    for y in sorted(set(w['id'][:4] for w in catalog['weeks'])):
        write(Path(output)/y/'year.json',{'schema':'sbstats/year-v1','iso_year':int(y),'weeks':[w['id'] for w in catalog['weeks'] if w['id'].startswith(y)]})

if __name__=='__main__':
    ap=argparse.ArgumentParser()
    ap.add_argument('--source',default='examples/solana/data/sample-daily.json')
    ap.add_argument('--output',default='examples/solana')
    ap.add_argument('--read',help='Recover embedded JSON data from a PNG')
    args=ap.parse_args()
    if args.read:
        with Image.open(args.read) as im:print(json.dumps(json.loads(im.info['sbstats.json']),indent=2))
    else:run(args.source,args.output)
