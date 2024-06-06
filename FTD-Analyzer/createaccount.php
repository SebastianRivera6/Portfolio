<?php
//if we are making an account
if(isset($_POST["email"]) && isset($_POST["newpass1"]) && isset($_POST["newpass2"]))
{
  //validate email
  if(!filter_var( $_POST["email"], FILTER_VALIDATE_EMAIL))
  {
    header('Location: https://elemental.ps.uci.edu/ftd-analyzer/createaccount.php?emailerror="invalid email"');
    die();
  }
  if(!($_POST["newpass1"] === $_POST["newpass2"]))
  {
    header('Location: https://elemental.ps.uci.edu/ftd-analyzer/createaccount.php?passworderror="no match"');
    die();
  }
  else
  {
    //create the user
    //for database operations
    require_once('dboperations.php');


    $email = $_POST["email"];
    $pass = $_POST["newpass1"];

    //create database management object
    $mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");
    $mydb->addUser($email, $pass);

    error_log("password is: ".$pass);
    header('Location: https://elemental.ps.uci.edu/ftd-analyzer/login.php');

  }
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

      <h2 style="margin:auto;">Create Account</h2>

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
<form action="createaccount.php" method="post" class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" id="tickersearch">

          <p>Create a New Account </p>
          <input type="search" name="email" id="email" class="form-control" placeholder="Email" style="width: 500px">
                <br /><?php echo $_GET["emailerror"];?><br />
          <input type="search" name="newpass1" id="newpass1" class="form-control" placeholder="New Password" style="width: 500px">
          <br /><?php echo $_GET["passworderror"];?><br />
          <input type="search" name="newpass2" id="newpass2" class="form-control" placeholder="Confirm New Password" style="width: 500px">
          <br /><?php echo $_GET["passworderror"];?><br />
          <button type="submit"  class="btn btn-warning">Create Account</button>

</form>
</div>

<script src="resources/js/bootstrap.bundle.min.js"></script>


</body>
</html>
