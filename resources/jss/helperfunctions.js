//autocomplete for search

var availableTickers;

$(document).bind('keypress', function(e) {
    if(e.keyCode==13){
         $('#search').trigger('click');
     }
});

function updateTicker(){
    getStocks($( "#tickersearchbox" )[0].value);
    $( "#tickersearchbox" ).autocomplete({source: availableTickers});
}

function changepw(){
    var currentpass = $("#curr_pass")[0].value;
    var newpass1 = $("#newpass1")[0].value
    var newpass2 = $("#newpass2")[0].value
    $.ajax({
        url: "setting.php",
        data: { 
            "password": currentpass,
            "newpass1": newpass1,
            "newpass2": newpass2,
            "reset" : true
        },
        cache: false,
        type: "POST",
        success: function(response) {
            if(response == "changed"){
                alert("Password changed. Page will now reload.");
            }
            else{
                alert("Password not changed. Incorrect information entered.");
            }
            location.reload();
    }});

}

function deleteAccount(){
    var currentpass = $("#curr_pass")[0].value;

    var really = confirm("Do you really want to delete your account?");

    if(really == true){
        $.ajax({
            url: "setting.php",
            data: { 
                "password": currentpass,
                "delete" : true
            },
            cache: false,
            type: "POST",
            success: function(response) {
                if(response == "deleted"){
                    alert("Account deleted. Page will now reload.");
                }
                else{
                    alert("Account not deleted. Check your password.");
                }
                location.reload();
        }});
    }

}

function getStocks(partialTicker){
    availableTickers = [];
    partialTicker = partialTicker.toUpperCase();
    $.ajax({
        url: "search.php",
        dataType: 'JSON',
        data: { 
            "partial": partialTicker, 
        },
        cache: false,
        type: "GET",
        success: function(response) {
            Object.entries(response["tickers"]).forEach(entry => {
                const [key, value] = entry;
                availableTickers.push(value["stock_ticker"])
              });
        },
    });

    
    return availableTickers;
}

function addToFav(userid){
    var ticker = $( "#tickersearchbox" )[0].value;
    $.ajax({
        url: "favops.php",
        data: { 
            "ticker": ticker,
            "userid": userid,
            "add": true
        },
        cache: false,
        type: "POST",
        success: function(response) {
            location.reload();
    }});
}

function deleteFavorite(ticker, userid){
    $.ajax({
        url: "favops.php",
        data: { 
            "ticker": ticker,
            "userid": userid,
            "delete": true
        },
        cache: false,
        type: "POST",
        success: function(response) {
            location.reload();
    }});
}

function executesearch(){
    var ticker = $( "#tickersearchbox" )[0].value;
    ticker = ticker.toUpperCase();
    var startDate = $("#startdate").datepicker('getDate');
    startDate = startDate.toISOString().slice(0, 19).replace('T', ' ');
    var endDate = $("#enddate").datepicker('getDate');
    endDate = endDate.toISOString().slice(0, 19).replace('T', ' ');

    if(startDate != null && endDate !== null && ticker.length > 0){
        let dataSeries = [];
        $.ajax({
            url: "getfails.php",
            dataType: 'JSON',
            data: { 
                "ticker": ticker,
                "start": startDate,
                "end": endDate
            },
            cache: false,
            type: "GET",
            success: function(response) {
                if(Object.keys(response["tickers"]).length > 0){
                Object.entries(response["tickers"]).forEach(entry => {
                    const [key, value] = entry;
                    var newDate = new Date(value["epoch_time"] * 1000);
                    dataSeries.push({x: value["hist_date"], y: value["failures_to_deliver"]});
                  });

                  $("canvas#ftd_chart").remove();
                  $("#charts").append('<canvas id="ftd_chart"  height="150"></canvas>');
          
                  //construct chart
                  var ctx = document.getElementById('ftd_chart').getContext('2d');
                  var timeFormat = "YYYY-MM-DD";
                  var config = {
                      type:    'line',
                      data:    {
                          datasets: [
                              {
                                  label: "FTDs",
                                  data: dataSeries,
                                  fill: false,
                                  borderColor: 'red'
                              }
                          ]
                      },
                      options: {
                          responsive: true,
                          title:      {
                              display: true,
                              text:    "Failures to Deliver"
                          },
                          scales:     {
                              xAxes: [{
                                  type:       "time",
                                  time:       {
                                      format: timeFormat,
                                      tooltipFormat: 'YYYY MMM ll',
                                      unit: 'day'
                                  },
                                  scaleLabel: {
                                      display:     true,
                                      labelString: 'Date'
                                  }
                              }],
                              yAxes: [{
                                  scaleLabel: {
                                      display:     true,
                                      labelString: 'value'
                                  }
                              }]
                          }
                      }
                  };
                  window.myLine = new Chart(ctx, config);

                }
                else{
                    alert('No failures found for the ticker and date range.');
                }
            },
        });
    }
    else{
        alert("Enter a ticker, start date, and end date.");
    }
    

}


function makeChart(ticker, months){
    ticker = ticker.toUpperCase();
    var endDate = new Date().getDate();
    var startDate = moment(startDate); 
    endDate = moment(endDate);
    endDate = startDate.toISOString().slice(0, 19).replace('T', ' ');
    startDate.add(months, 'months');
    startDate = startDate.toISOString().slice(0, 19).replace('T', ' ');
    console.log("Start: "+startDate);
    console.log("End: " + endDate);
    console.log("ticker: " + ticker);
    if(startDate != null && endDate !== null && ticker.length > 0){
        let dataSeries = [];
        $.ajax({
            url: "getfails.php",
            dataType: 'JSON',
            data: { 
                "ticker": ticker,
                "start": startDate,
                "end": endDate
            },
            cache: false,
            type: "GET",
            success: function(response) {
                if(Object.keys(response["tickers"]).length > 0){
                Object.entries(response["tickers"]).forEach(entry => {
                    const [key, value] = entry;
                    var newDate = new Date(value["epoch_time"] * 1000);
                    dataSeries.push({x: value["hist_date"], y: value["failures_to_deliver"]});
                  });

                  $("#"+ticker+months).append('<div style="width:400px; height: 250px;"><canvas id="'+ticker+months+'_chart"  height="150"></canvas></div>');
          
                  //construct chart
                  var ctx = document.getElementById(ticker+months+'_chart').getContext('2d');
                  var timeFormat = "YYYY-MM-DD";
                  var config = {
                      type:    'line',
                      data:    {
                          datasets: [
                              {
                                  label: "FTDs",
                                  data: dataSeries,
                                  fill: false,
                                  borderColor: 'red'
                              }
                          ]
                      },
                      options: {
                          responsive: true,
                          maintainAspectRatio: false,
                          title:      {
                              display: true,
                              text:    "Failures to Deliver"
                          },
                          scales:     {
                              xAxes: [{
                                  type:       "time",
                                  time:       {
                                      format: timeFormat,
                                      tooltipFormat: 'YYYY MMM ll',
                                  },
                                  scaleLabel: {
                                      display:     true,
                                      labelString: 'Date'
                                  }
                              }],
                              yAxes: [{
                                  scaleLabel: {
                                      display:     true,
                                      labelString: 'value'
                                  }
                              }]
                          }
                      }
                  };
                  window.myLine = new Chart(ctx, config);

                }
                else{
                    alert('No failures found for the ticker and date range.');
                }
            },
        });
    }
    else{
        alert("Enter a ticker, start date, and end date.");
    }
    

}
