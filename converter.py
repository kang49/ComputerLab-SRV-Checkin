import json
import pandas as pd
import datetime

with open('/volume1/web/ComputerLab-SRV-Checkin/form.json', 'rb') as file:
    data = file.read().decode('utf-8')
    data = json.loads(data)

with open('/volume1/web/ComputerLab-SRV-Checkin/form.json', 'w', encoding='utf-8') as file:
    json.dump(data, file, ensure_ascii=False)

df = pd.DataFrame(data)
#Save for system
# df.to_excel("/volume1/web/ComputerLab-SRV-Checkin/form.xlsx", index=False)

#Save for user
current_month = datetime.datetime.now().strftime("%B")
current_year = datetime.datetime.now().strftime("%Y")
file_name = f"/volume1/web/ComputerLab-SRV-Checkin/formData/{current_month}_{current_year}.xlsx"
df.to_excel(file_name, index=False)