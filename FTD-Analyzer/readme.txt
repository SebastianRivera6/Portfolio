This web application allows for the user to get detailed graphical and numerical information on failures to deliver for any stock ticker on US exchanges.

The user can add a ticker to favorites to view quick 2 and 5 month charts, or use the main search functionality on the homepage
to view failures between a desired date range. 

The data is updated every midnight by a script that downloads zip files
from https://www.sec.gov/data/foiadocsfailsdatahtm, then unzips them, parses them, and loads into the database.

The settings page allows the user to either change their password or delete their account.