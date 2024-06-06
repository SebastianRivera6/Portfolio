<?php
  session_start();

  //if we are already logged in
  if(isset($_SESSION["email"]) && isset($_SESSION["valid"]))
  {
    header("Location: https://elemental.ps.uci.edu/ftd-analyzer");
  }

  //if we are logging in
  if(isset($_POST["email"]) && isset($_POST["password"]) && !isset($_SESSION["valid"]))
  {
    //if valid

    //for database operations
    require_once('dboperations.php');

    //create database management object
    $mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

    if($mydb->verify_user($_POST["email"], $_POST["password"]))
    {
      error_log("Valid password");
      session_start();
      $_SESSION["email"] = $_POST["email"];
      $_SESSION["valid"] = true;

      header("Location: https://elemental.ps.uci.edu/ftd-analyzer");
    }
    else
    {
      echo "Invalid email or password";
    }
  }

  //if we are logging out
  if($_GET["logout"] === "true")
  {
    session_unset();
    session_destroy();
    error_log("logged out");
    header("Location: https://elemental.ps.uci.edu/ftd-analyzer");

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

      <h2 style="margin:auto;">Login</h2>

      <div class="tab">
      <a href="index.php"><button type="button"  class="tablinks">FTD Analyzer</button></a>
      <a href="favorites.php"><button type="button"  class="tablinks">Favorites</button></a>
      <a href="changelog.php"><button type="button"  class="tablinks">Changelog</button></a>
      <a href="data.php"><button type="button"  class="tablinks">Data Sources & FAQ </button></a>
      <a href="setting.php"><button type="button"  class="tablinks">Settings </button></a>

      </div>
      <div class="text-end" style="margin:auto;"> <a href="login.php">
          <a href="login.php"><button type="button"  class="btn btn-outline-light me-2">Login</button></a>
          <a href="createaccount.php"><button type="button"  class="btn btn-warning">Create Account</button></a>
        </div>
      </header>
      <br />

<div class="container">
<form action="login.php" method="post" class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" id="tickersearch">
        <p>Enter Email </p>
        <br />
        <input type="search" name="email" id="email" class="form-control" placeholder="Email" style="width: 500px">
        <br /><br />
        <p>Enter Password </p>
        <input type="search" name="password" id="password" class="form-control" placeholder="Enter Password" style="width: 500px">
        <br /><br />

        <button type="submit"  class="btn btn-success">Login </button>
        <a href="setting.php"><button type="button"  class="btn btn-warning"> Reset Password </button></a>
        <br /><br />
        <a href="createaccount.php"><button type="button"  class="btn btn-info">Create Account</button></a>



        <br /><br />

</form>
</div>

<script src="resources/js/bootstrap.bundle.min.js"></script>
</body>
</html>
