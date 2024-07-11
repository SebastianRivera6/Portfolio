import json
import requests
import pandas as pd
import jsonschema
from HelperFunctions import *
import openpyxl
import win32com.client

class GlobalVariables:
    CWID = ''
    EntryID = ''
    TermID = '103'
    RoomSpaceID =''
    Description =''
    FormatDate = ""
    Billing = 'standard'
    excel_file_name = 'Lockouts.xlsx'

def SendRequest(json_file_path, state):
    # Read JSON file
    with open(json_file_path, 'r') as json_file:
        request_data = json.load(json_file)

    # Extract request details from JSON
    uri = request_data.get('uri')
    method = request_data.get('method')
    headers = request_data.get('headers', {})
    authentication = request_data.get("authentication", {})
    body = request_data.get('body', {})

    # Prepare the request headers
    headers["Content-Type"] = "application/json"

    # Authenticate if authentication details are provided
    username = authentication.get('username')
    password = authentication.get('password')
    auth = requests.auth.HTTPBasicAuth(username, password)

    if state == 0:
        body = body.replace('{variable}', str(GlobalVariables.CWID))
    if state == 1:
        body = body.replace('{variable}', str(GlobalVariables.EntryID))
    if state == 2:
        body = json.dumps(body)
        body = body.replace('{variable1}', str(GlobalVariables.EntryID))
        body = body.replace('{variable2}', str(GlobalVariables.FormatDate))
        body = body.replace('{variable3}', str(GlobalVariables.RoomSpaceID))
        body = body.replace('{variable4}', GlobalVariables.TermID)
        body = body.replace('{variable5}',  GlobalVariables.Billing)




    #Send POST request with JSON data
    response = requests.request(
        method,
        uri,
        headers=headers,
        auth=(username, password),
        data=body
   )

    print(response)
    print(state)
    print("hi")
    for entry in response.json():
        if state == 0:
            GlobalVariables.EntryID = entry.get("EntryID")
            print('hi')
        elif state == 1:
            GlobalVariables.RoomSpaceID = entry.get("RoomSpaceID")
            GlobalVariables.Description = entry.get("Description")


def GenericDataDriver():
    excel_app, ws = refresh_excel_connections(GlobalVariables.excel_file_name)
    last_row_column_A = excel_app.Cells(excel_app.Rows.Count, "A").End(-4162).Row
    wss = excel_app.ActiveSheet

    # Loop through each row
    with open('Log.txt', 'w') as file:
        for row in range(last_row_column_A, 0, -1):
            if wss.Cells(row, 'F').Value is None:
                wss.Cells(row, 'F').Value = 'yes'
            else:
                break
            
            GlobalVariables.CWID = wss.Cells(row, "A")  # Get value from Column A
            GlobalVariables.FormatDate = wss.Cells(row, "B").Text
            process_datetime(GlobalVariables)
            SendRequest('jsons/GetEntryID.json', 0)
            SendRequest('jsons/GetBooking.json', 1)
            #SendRequest('jsons/AddGenericData.json', 2)
            file.write(str(GlobalVariables.CWID) + '\n' + str(GlobalVariables.EntryID) + '\n' + str(GlobalVariables.RoomSpaceID) + '\n' + GlobalVariables.Description + '\n')
            file.write("-------------------------\n")
    print("All new Lockouts Loggeddddddd\n")
    # Save the changes
    ws.Save()
    # Close the workbook
    ws.Close()
    # Quit Excel application
    excel_app.Quit()