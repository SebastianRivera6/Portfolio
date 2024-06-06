<?php
session_start();
require_once('config.php');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="resources/css/bootstrap.min.css">
<link rel="stylesheet" href="resources/css/styles.css">
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<title>FTD Analyzer</title>
</head>
<body class="bg-light">
<header class="p-3 bg-dark text-white">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
      <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
      </a>

      <h2 style="margin:auto;">Home Page</h2>

<div class="tab">
<a href="index.php"><button type="button"  class="tablinks">FTD Analyzer</button></a>
<a href="favorites.php"><button type="button"  class="tablinks">Favorites</button></a>
<a href="changelog.php"><button type="button"  class="tablinks">Changelog</button></a>
<a href="data.php"><button type="button"  class="tablinks">Data Sources & FAQ </button></a>
<a href="setting.php"><button type="button"  class="tablinks">Settings </button></a>

</div>
<div class="text-end" style="margin:auto;">
    <?php
    error_log($_SESSION["valid"]);
    if ($_SESSION["valid"] === true)
    {
      $buttons = '<a href="https://elemental.ps.uci.edu/ftd-analyzer/login.php?logout=true"><button type="button"  class="btn btn-outline-light me-2">Logout</button></a>';
    }
    else
    {
      $buttons = '<a href="login.php"><button type="button"  class="btn btn-outline-light me-2">Login</button></a>
                  <a href="createaccount.php"><button type="button"  class="btn btn-warning">Create Account</button></a>';
    }
     echo $buttons;
    ?>
  </div>
</header>
<br />
<div class="container">
<form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" id="tickersearch" autocomplete="off">
        <input type="search" id = "tickersearchbox" onkeyup="updateTicker();" class="form-control" placeholder="Search for ticker...">
        <label for="startdate">Start Date</label>
        <input type="text" id="startdate">
        <label for="enddate">End Date</label>
        <input type="text" id="enddate">
        <button type="button" id="search" onclick="executesearch();" class="btn">Search</button>
</form>
</div>
<div id="charts">
<canvas id="ftd_chart" width="80%" height="80%" style="height:30%; width: 50%;"></canvas>
</div>
<?php
 echoScripts();
?>
<script>
  $( function() {
    $( "#startdate" ).datepicker({
      dateFormat: "yy-mm-dd"
    });
  } );
  $( function() {
    $( "#enddate" ).datepicker({
      dateFormat: "yy-mm-dd"
    });
  } );
  </script>
</body>
</html>
