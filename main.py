#!/usr/bin/python3

import json

with open('rank-table.csv','r') as table:
    data=[]
    item=None
    for row in table.readlines():
        row=row.split(',')
        if item==None:
            item={}
            item['name']=row[0]
            item['rank']=[]
            item['rank'].append(float(row[2].strip()))
        else:
            if item['name']==row[0]:
                item['rank'].append(float(row[2].strip()))
            else:
                item['rank'].sort()
                data.append(item)
                item={}
                item['name']=row[0]
                item['rank']=[]
                item['rank'].append(float(row[2].strip()))
    data.append(item)
    with open('db.json','w') as db:
        db.write(json.dumps(data))
