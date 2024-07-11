from datetime import datetime
import win32com.client
import os
import time

def refresh_excel_connections(file_name):
    # Get the current working directory
    current_directory = os.getcwd()
    # Construct the full path to the Excel file
    excel_file_path = os.path.join(current_directory, file_name)
    # Create Excel Application object
    excel_app = win32com.client.Dispatch("Excel.Application")
    # Open the Excel workbook
    ws = excel_app.Workbooks.Open(excel_file_path)
    # Refresh all connections in the workbook
    #ws.RefreshAll()
    time.sleep(10)
    return excel_app, ws

def process_datetime(global_variables):

    # Parse FormatDate string into a datetime object
    format_date = datetime.strptime(global_variables.FormatDate, '%m/%d/%Y %H:%M')

    # Set seconds component to zero
    format_date = format_date.replace(second=0)

    if 0 <= format_date.weekday() <= 4 and 8 <= format_date.hour < 17:
        global_variables.Billing = 'standard'
    else:
        global_variables.Billing = 'outside hours'

    # Format the DateTime value as required
    global_variables.FormatDate = format_date.strftime('%Y/%m/%dT%H:%M:%S')
