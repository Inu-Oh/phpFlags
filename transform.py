import json

with open('json_output.json', 'r+') as f:
    data = json.load(f)

count = 0
for dict in data:
    dict['pk'] = count
    count+=1

with open('countries.json', 'w') as newf:
    json.dump(data, newf, indent=4)
