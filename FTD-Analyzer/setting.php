<?php
  session_start();
  require_once('config.php');
  //if we are logging in
  if(isset($_SESSION["email"]) && isset($_SESSION["valid"]))
  {
    //if valid

    //for database operations
    require_once('dboperations.php');

    //create database management object
    $mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

    //if we are resetting
    if(isset($_POST["reset"]))
    {

      //check for old pass match
      if($mydb->verify_user($_SESSION["email"], $_POST["password"]))
      {
        //if both new passwords match
        if($_POST["newpass1"] === $_POST["newpass2"])
        {
          $mydb->resetPass($_SESSION["email"], $_POST["newpass1"]);
          echo "changed";
          die();
        }
        else
        {
          echo "Passwords do not match.";
        }        
      }

      }
      //if we are deleting
    else if(isset($_POST["delete"]))
    {
      //check for old pass match
      if($mydb->verify_user($_SESSION["email"], $_POST["password"]))
      {
        $mydb->deleteUser($_SESSION["email"], $_POST["password"]);
        echo "deleted";


        session_unset();  
        session_destroy();
        die();
      }
        
    }
  }
  else
  {
    header("Location: https://elemental.ps.uci.edu/ftd-analyzer/login.php");
    die();
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

      <h2 style="margin:auto;">Settings</h2>

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
<div class="container">
<form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" id="tickersearch">
        <p> Change Password </p>
        <input type="search" id="curr_pass" class="form-control" placeholder="Current Password" style="width: 500px">
        <br /><br />
        <input type="search" id="newpass1" class="form-control" placeholder="New Password" style="width: 500px">
        <br /><br />
        <input type="search" id="newpass2" class="form-control" placeholder="Confirm New Password" style="width: 500px">
        <br /><br />
        <button type="button" onclick="changepw();" class="btn btn-warning">Change Password</button>
        <br /><br />
        <p> Delete your account. This is not reversable </p>
        <button type="button" onclick="deleteAccount();"  class="btn btn-danger">Delete Your Account</button>
</form>
</div>
<?php
 echoScripts();
?>
</body>
</html>
