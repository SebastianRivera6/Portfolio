import json
import  jsonschema
import requests
import openpyxl
from datetime import datetime



class GlobalVariables:
    CWID = ''
    EntryID=''
    NamePreferred = ''
    NameFirst = 'test'
    NameLast = ''
    EntryID = ''
    RoomSpaceID = ''
    Description = ''
    cwid_list = []
    time_list = []
    index = 0
    TermID = 103
    LockoutCountBody = "SELECT COUNT(EntryID) FROM Lockouts WHERE Processed=1 AND EntryID={variable} AND TermID={variabletwo}"
    bookingDataURI = "https://fullerton.starrezhousing.com/StarRezREST/services/query"
    bookingDataBody = "SELECT TOP 1 BookingID, TermSessionID, RoomSpaceID, [RoomSpace].Description FROM Booking WHERE EntryStatusEnum IN (2,5) AND EntryID'{variable}'  AND RoomSpaceID ='{variable1}' ORDER BY TermSessionID DESC"
    CreateTransactionURI = "https://fullerton.starrezhousing.com/StarRezREST/services/create/transaction"
    SetProcessed = "https://fullerton.starrezhousing.com/StarRezREST/services/update/lockouts/{variable}"

    data_json = '''
        {
            "uri": "https://fullerton.starrezhousing.com/StarRezREST/services/query",
            "method": "POST",
            "headers": {
                "Accept": "application/json"
            },
            "authentication": {
                "username": "*****",
                "password": "*****",
                "type": "Basic"
            },
            "body": "SELECT TOP 50 DISTINCT EntryID, LockoutID, Processed, Billing, TermID, Timestamp, RoomSpaceID FROM Lockouts WHERE Processed=0 AND Billing!="" ORDER BY Timestamp ASC"
        }
        '''


#***HELPER FUNCTIONS***

#----------------------------------------------------------------------------------------------------------------

def process_entry(entry,state):
    if state == 1:
        GlobalVariables.RoomSpaceID = entry.get("RoomSpaceID")
        GlobalVariables.Description = entry.get("Description")


#----------------------------------------------------------------------------------------------------------------

def send_post_request(request_data, state, index):
    # Replace the variable in the body with the most recent entry from the Excel file
    print(request_data)
    try:
        # Parse the JSON request data
        request_json = json.loads(request_data)

        # Extract parameters from the JSON data
        uri = request_json.get("uri")
        method = request_json.get("method")
        headers = request_json.get("headers", {})
        authentication = request_json.get("authentication", {})
        body = request_json.get("body", "")

        # Prepare the authentication details
        auth_type = authentication.get("type", "")
        username = authentication.get("username", "")
        password = authentication.get("password", "")

        # Prepare the request headers
        headers["Content-Type"] = "application/json"

        # Prepare the request
        response = requests.request(
            method,
            uri,
            headers=headers,
            auth=(username, password) if auth_type.lower() == "basic" else None,
            data=body if state == 0 else body.replace('{variable}', str(variable_value)) if body else None
            )

        print(response.text)
        # Store the received data as a JSON object
        received_data = response.json()
        #parse json

        for entry in response.json():
            process_entry(entry,state)


    except json.JSONDecodeError as e:
        print("Error decoding JSON:", str(e))
    except Exception as e:
        print("An error occurred:", str(e))

#----------------------------------------------------------------------------------------------------------------

#***DRIVER FUNCTIONS***

#this function facilitates looking up students data from CWID and adding a lockout
#record to generic Data


def AddGenericData():
    GlobalVariables()
    state = 0

    #retrieve list of unprocessed cwids
    #loop that tracks state of the process
    send_post_request(GlobalVariables.data_json, state, GlobalVariables.index)



            # The file is automatically closed after the 'with' block


