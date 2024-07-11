import json
import requests
import pandas as pd
import jsonschema
from HelperFunctions import *
import openpyxl
import win32com.client


class GlobalVariables:
    EntryID = ''
    TermID = '103'
    RoomSpaceID =''
    Description =''
    TimeStamp = ""
    Billing = ''
    Objects_list = []
    LockoutID = ''
    Processed = ''
    Count = 0
    amount = 1
    BookingID = 0
    TransactionID = 0
    TermSessionID = 0

def SendRequest(json_file_path, state):
    # Read JSON file
    print(json_file_path)
    with open(json_file_path, 'r') as json_file:
        request_data = json.load(json_file)

    # Extract request details from JSON
    uri = request_data.get('uri')
    method = request_data.get('method')
    headers = request_data.get('headers', {})
    authentication = request_data.get("authentication", {})
    body = request_data.get('body', {})

    # Prepare the request headers
    #headers["Content-Type"] = "application/json"

    # Authenticate if authentication details are provided
    username = authentication.get('username')
    password = authentication.get('password')
    auth = requests.auth.HTTPBasicAuth(username, password)

    if state == 1:
        body = body.replace('{variable1}', str(GlobalVariables.EntryID))
        body = body.replace('{variable2}', str(GlobalVariables.TermID))
    elif state == 2:
        body = body.replace('{variable1}', str(GlobalVariables.EntryID))
        body = body.replace('{variable2}', str(GlobalVariables.RoomSpaceID))
    elif state == 3:
        body = json.dumps(body)
        body = body.replace('{variable1}', str(GlobalVariables.EntryID))
        body = body.replace('{variable2}', str(GlobalVariables.amount))
        body = body.replace('{variable3}', str(GlobalVariables.Description))
        body = body.replace('{variable4}', str(GlobalVariables.TimeStamp))
        body = body.replace('{variable5}', str(GlobalVariables.TermSessionID))
        body = body.replace('{variable6}', str(GlobalVariables.BookingID))
        body = body.replace('{variable7}', str(GlobalVariables.LockoutID))
        body = body.replace('{variable8}', str(GlobalVariables.RoomSpaceID))
    elif state == 4:
        body = json.dumps(body)
        uri = uri.replace('{variable1}', str(GlobalVariables.LockoutID))
        body = body.replace('{variable2}', str(GlobalVariables.TransactionID))
    else:
        print('Retrieving Lockouts...\n')

    #Send POST request with JSON data
    response = requests.request(
        method,
        uri,
        headers=headers,
        auth=(username, password),
        data=body
   )

    if state == 0:
        json_response = response.json()
        if isinstance(json_response, list):
            # Store each object and its attributes in a list of dictionaries
            for obj in json_response:
                object_data = {
                    'EntryID': obj.get('EntryID'),
                    'LockoutID': obj.get('LockoutID'),
                    'Processed': obj.get('Processed'),
                    'Billing': obj.get('Billing'),
                    'TermID': obj.get('TermID'),
                    'Timestamp': obj.get('Timestamp'),
                    'RoomSpaceID': obj.get('RoomSpaceID')
                }
                GlobalVariables.Objects_list.append(object_data)


    elif state in [1, 2, 3]:
        # Process response based on state
        json_response = response.json()
        if state == 3:
            # Print debugging information
            print('State:', state)
            print('Request URI:', uri)
            print('Request Body:', body)
            print('Response Status Code:', response.status_code)
            print('Response Content:', response.content)
            print('Response Headers:', response.headers)

            # Handle response based on state
            if response.status_code == 400:
                print("Bad Request - Server could not process the request")
            else:
                print("Request was successful!\n\n")
            GlobalVariables.TransactionID = json_response.get("TransactionID")
        if state == 4:
            # Print debugging information
            print('State:', state)
            print('Request URI:', uri)
            print('Request Body:', body)
            print('Response Status Code:', response.status_code)
            print('Response Content:', response.content)
            print('Response Headers:', response.headers)

            # Handle response based on state
            if response.status_code == 400:
                print("Bad Request - Server could not process the request")
            else:
                print("Request was successful!\n\n")
        for entry in json_response:
            if state == 1:
                GlobalVariables.Count = entry.get("Count")
            elif state == 2:
                GlobalVariables.BookingID = entry.get("BookingID")
                GlobalVariables.RoomSpaceID = entry.get("RoomSpaceID")
                GlobalVariables.Description = entry.get("Room")
                GlobalVariables.TermSessionID = entry.get("TermSessionID")


def ChargeDriver():
    SendRequest('jsons/GetLockouts.json', 0)
    with open('Log.txt', 'w') as file:
        for obj in GlobalVariables.Objects_list:
            GlobalVariables.EntryID = obj['EntryID']
            GlobalVariables.LockoutID = obj['LockoutID']
            GlobalVariables.Processed = obj['Processed']
            GlobalVariables.Billing = obj['Billing']
            GlobalVariables.TermID = obj['TermID']
            GlobalVariables.Timestamp = obj['Timestamp']
            GlobalVariables.RoomSpaceID = obj['RoomSpaceID']

            if GlobalVariables.Billing == 'standard':
                # GlobalVariables.amount = 10
                print('would have been $10 charge\n')
            else:
                # GlobalVariables.amount = 20
                print('would have been charged $20\n')

            SendRequest('jsons/GetLockoutCount.json', 1)
            SendRequest('jsons/GetBookingCharge.json', 2)
            if GlobalVariables.Count >= 2:
                SendRequest('jsons/AddCharge.json', 3)
            SendRequest('jsons/ProcessedTrue.json', 4)


        print("Lockouts Processed\n")
        file.write(str(GlobalVariables.EntryID)+'\n')
        file.write(str(GlobalVariables.RoomSpaceID)+'\n')
        file.write(str(GlobalVariables.Description)+'\n')
        file.write("-------------------------\n")

