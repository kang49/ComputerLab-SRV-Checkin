import json
import pandas as pd

with open('data.json', 'rb') as file:
    data = file.read().decode('utf-8')
    data = json.loads(data)

with open('data.json', 'w', encoding='utf-8') as file:
    json.dump(data, file, ensure_ascii=False)

    df = pd.DataFrame(data)
    df.to_excel("data.xlsx", index=False)