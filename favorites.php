<?php
session_start();
require_once('config.php');
if(!$_SESSION["valid"] === true)
{
  header("Location: https://elemental.ps.uci.edu/ftd-analyzer/login.php");
  die();
}
else{
  //for database operations
  require_once('dboperations.php');

  //create database management object
  $mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

  $userid = $mydb->getID($_SESSION["email"]);
}
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

      <h2 style="margin:auto;">Login Page</h2>

      <div class="tab">
      <a href="index.php"><button type="button"  class="tablinks">FTD Analyzer</button></a>
      <a href="favorites.php"><button type="button"  class="tablinks">Favorites</button></a>
      <a href="changelog.php"><button type="button"  class="tablinks">Changelog</button></a>
      <a href="data.php"><button type="button"  class="tablinks">Data Sources & FAQ </button></a>
      <a href="setting.php"><button type="button"  class="tablinks">Settings </button></a>

      </div>
      <div class="text-end" style="margin:auto;"> <a href="login.php">
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

<form >
  <label for="tickersearchox">Add Ticker <br></label>
  <input type="search" id = "tickersearchbox" onkeyup="updateTicker();" class="form-control" placeholder="Search for ticker...">
  <button type="button" id="search" onclick="addToFav(<?PHP echo $userid ?>);" class="btn">Add to favorites</button>
</form>
<br />

<table id="favorite_list">
  <tr>
    <th>Favorite Tickers</th>
    <th>2 Month FTD Chart</th>
    <th>5 Month FTD Chart</th>
    <td>Delete</th>
  </tr>
  <?php
  //echo out for each favorite
  /*
  <tr>
    <td class="favticker">TICKER NAME</td>
    <td class="favtickerchart"><CHART></td>
  </tr>
  */
  //get favorites
  $favorites = $mydb->getFavorites($_SESSION["email"]);

  foreach($favorites as $ticker)
  {
    echo
    '<tr>
      <td class="favticker">'.$ticker.'</td>
      <td ticker="'.$ticker.'" id="'.$ticker.'-2" name="favtickerchart2m"></td>
      <td ticker="'.$ticker.'" id="'.$ticker.'-5" name="favtickerchart5m"></td>
      <td><button onclick="deleteFavorite(\''.$ticker.'\','.$userid.');" class="btn btn-warning ">Delete</button></td>
     
    </tr>';
  }
  ?>
</table>
</body>
<?php
 echoScripts(); 
$chartstuff = <<<STUFF
 <script> 
  window.onload = function fillcharts() {
  var tds = document.getElementsByName("favtickerchart2m")
  for (td of tds) {
      var ticker = td.getAttribute("ticker")
      makeChart(ticker, -2);
  }

  var tds = document.getElementsByName("favtickerchart5m")
  for (td of tds) {
      var ticker = td.getAttribute("ticker")
      makeChart(ticker, -5);
  }

}
</script>
STUFF;
echo $chartstuff;
?>
</html>
