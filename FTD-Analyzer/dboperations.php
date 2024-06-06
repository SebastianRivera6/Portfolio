<?php

//This file will contain the database operations via the DBOP class

class DBManager{

    //-----------------
    //member data here
    //-----------------

    //database object
    protected $dbh;

    //--------------
    //methods below
    //---------------

    //Tables used will be (5):  tickers, users, favorite_stocks, price_history, failures_to_deliver
    //These will be created if they do not yet exist
    //constructor -- hostname, username, password, database name
    function __construct($dbhost, $dbuser, $dbpass, $dbname, $sql_file){

        //prepare to handle any connection failure
        try{
            //setup connection string with mysql type of driver
            $dsn = 'mysql:dbname='.$dbname.';host='.$dbhost.'';

            //attempt to connect to the database
            $this->dbh = new PDO($dsn, $dbuser, $dbpass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql_contents = file_get_contents($sql_file);
            //check to see if table exists and make it if not
            $stmt = $this->dbh->prepare($sql_contents);

            $stmt->execute();
        }

        //if we can't connect to the database, kill our program and whine.
        catch(PDOException $e) {
            die("Could not connect to the database $dbname on server $dbhost:" . $e->getMessage());
        }

    }

    //add user
    function addUser($useremail, $userpass){

        //hash the password
        $hashedpass = password_hash($userpass, PASSWORD_BCRYPT);
        $em = $useremail;
        $pw = $hashedpass;

        //enter error try block
        try{
            //verify inputs are sanitized
            $stmt = $this->dbh->prepare("INSERT INTO `users` (user_email, user_password, confirmed) VALUES (:username, :hashedpass, FALSE);");
            //prepare sql query to add to database
            $stmt->bindParam(':username', $em);
            $stmt->bindParam(':hashedpass', $userpass);

            //execute prepared statement
            $stmt->execute();
        }
        //if there was an error executing the statement die and give a message
        catch(PDOException $e) {
            die("Could not add entry $useremail to database $dbhost:" . $e->getMessage());
        }
    }

    

    //confirm user from email
    function confirmUser($useremail){

        //update confirmed to true
        //enter error try block
        try{
            //verify inputs are sanitized
            $stmt = $this->dbh->prepare("UPDATE users SET CONFRIMED = :conf WHERE user_email = :useremail;");
            //prepare sql query to add to database
            $stmt->bindParam(':useremail', $useremail, PDO::PARAM_STR, 250);
            $stmt->bindParam(':conf', "TRUE", PDO::PARAM_BOOL);

            //execute prepared statement
            $stmt->execute();
        }
        //if there was an error executing the statement die and give a message
        catch(PDOException $e) {
        die("Could not add entry $useremail to database $dbhost:" . $e->getMessage());
        }

    }

    function getPassHash($useremail){
        try{
            $stmt = $this->dbh->prepare('SELECT user_password FROM users WHERE user_email = :useremail;');
            $stmt->bindParam(':useremail', $useremail, PDO::PARAM_STR, 250);
            $stmt->execute();
        }
        catch(PDOException $e) {
            die("Could not retrieve entry $useremail from database:" . $e->getMessage());
        }
        //grab the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //if nothing
        if(!$result)
            return false;

        //otherwise return result
        return $result["user_password"];
    }

    function verify_user($useremail, $userpass)
    {
        $goodpass = false;
        if($userpass == $this->getPassHash($useremail))
        {
            $goodpass = true;
        }

        return $goodpass;
    }

    //remove user from database
    function deleteUser($useremail, $userpass){
        //check password
        $goodpass = false;
        if($userpass == $this->getPassHash($useremail))
        {
            $goodpass = true;
        }

        if($goodpass){
            //enter error try block
            try{

                //verify inputs are sanitized
                $stmt = $this->dbh->prepare("DELETE FROM users WHERE user_email = :useremail AND user_password = :hashedpass;");
                //prepare sql query to remove from database
                $stmt->bindParam(':useremail', $useremail, PDO::PARAM_STR, 250);
                $stmt->bindParam(':hashedpass', $userpass, PDO::PARAM_STR, 250);


                //execute prepared statement
                $stmt->execute();

                return true;
            }
            //if there was an error executing the statement die and give a message
            catch(PDOException $e) {
                die("Could not delete entry $useremail from database:" . $e->getMessage());

            }
        }

        return false;

    }

    function addTicker($dataArray){


        //enter error try block
        try{


            //verify inputs are sanitized
            $stmt = $this->dbh->prepare("INSERT INTO tickers (stock_ticker,ticker_description) VALUES (:tick, :dsc);");
            //prepare sql query to add to database
            $stmt->bindParam(':tick', $dataArray["ticker"]);
            $stmt->bindParam(':dsc', $dataArray["description"]);

            //execute prepared statement
            $stmt->execute();
        }
        //if there was an error executing the statement die and give a message
        catch(PDOException $e) {
            die("Could not add ticker to database $dbhost:" . $e->getMessage());
        }

    }

    function addTickerFTD($dataArray){


        //enter error try block
        try{

            $this->addTicker($dataArray);

            //verify inputs are sanitized
            $stmt = $this->dbh->prepare("INSERT INTO failures_to_deliver (hist_date,stock_ticker,failures_to_deliver) VALUES (:hstd, :tick, :ftd);");
            //prepare sql query to add to database
            $stmt->bindParam(':hstd', $dataArray["date"]);
            $stmt->bindParam(':tick', $dataArray["ticker"]);
            $stmt->bindParam(':ftd', $dataArray["fails_to_deliver"]);

            //execute prepared statement
            $stmt->execute();
        }
        //if there was an error executing the statement die and give a message
        catch(PDOException $e) {
            die("Could not add ticker to database $dbhost:" . $e->getMessage());
        }

    }



    function searchTicker($tickerPartial){
        try{

            $ticker = $tickerPartial.'%';

            //verify inputs are sanitized
            $stmt = $this->dbh->prepare("SELECT `stock_ticker` FROM `tickers` WHERE `stock_ticker` LIKE :part LIMIT 10;");
            //prepare sql query to add to database
            $stmt->bindParam(':part', $ticker);

            //execute prepared statement
            $stmt->execute();

            $results = $stmt->fetchAll();
            //return array of results
            return $results;

        }
        //if there was an error executing the statement die and give a message
        catch(PDOException $e) {
            die("Could search for ticker$dbhost:" . $e->getMessage());
        }




    }

    //retrieve all data on a ticker by way of join
    function retrieveTickerData($tickerName, $start, $end){

        try{

            $ticker = $tickerName;

            //verify inputs are sanitized
            $stmt = $this->dbh->prepare("SELECT UNIX_TIMESTAMP(hist_date) as epoch_time, hist_date, failures_to_deliver FROM `failures_to_deliver` WHERE `stock_ticker` = :part AND `hist_date` BETWEEN :strt AND :nd ;");
            //prepare sql query to add to database
            $stmt->bindParam(':part', $ticker);
            $stmt->bindParam(':strt', $start);
            $stmt->bindParam(':nd', $end);

            //execute prepared statement
            $stmt->execute();

            $results = $stmt->fetchAll();
            //return array of results
            return $results;

        }
        //if there was an error executing the statement die and give a message
        catch(PDOException $e) {
            die("Could search for ticker$dbhost:" . $e->getMessage());
        }

  }



  //Reset Password
  function resetPass($useremail, $userpass){

 
    try{

        $email = $useremail;
        //verify inputs are sanitized
        $stmt = $this->dbh->prepare("UPDATE users SET user_password = :userpass WHERE user_email = :useremail;");
        //prepare sql query to add to database
        $stmt->bindParam(':useremail', $email);
        $stmt->bindParam(':userpass', $userpass);

        //execute prepared statement
        $stmt->execute();


    }
    //if there was an error executing the statement die and give a message
    catch(PDOException $e) {
        die("Could search for ticker$dbhost:" . $e->getMessage());
    }
    
}




  function getFavorites($useremail){

    try{

        $email = $useremail;
        //verify inputs are sanitized
        $stmt = $this->dbh->prepare("SELECT F.favorite_ticker AS fav_ticker FROM favorite_stocks AS F INNER JOIN users AS U ON F.user_id = U.user_id WHERE U.user_email = :email ORDER BY fav_ticker;");
        //prepare sql query to add to database
        $stmt->bindParam(':email', $email);

        //execute prepared statement
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        //return array of results
        return $results;

    }
    //if there was an error executing the statement die and give a message
    catch(PDOException $e) {
        die("Could search for ticker$dbhost:" . $e->getMessage());
    }
  }

  
  function deleteFavorite($ticker, $userid){

    try{

        $id = $userid;
        $tick = $ticker;
        //verify inputs are sanitized
        $stmt = $this->dbh->prepare("DELETE FROM `favorite_stocks` WHERE user_id = :userd AND favorite_ticker = :favticker;");
        //prepare sql query to add to database
        $stmt->bindParam(':userd', $id, PDO::PARAM_INT);
        $stmt->bindParam(':favticker', $tick);

        //execute prepared statement
        $stmt->execute();


    }
    //if there was an error executing the statement die and give a message
    catch(PDOException $e) {
        die("Could search for ticker$dbhost:" . $e->getMessage());
    }
  }

  function addFavorite($ticker, $userid){

    try{

        $id = $userid;
        $tick = $ticker;
        //verify inputs are sanitized
        $stmt = $this->dbh->prepare("INSERT INTO `favorite_stocks` (user_id, favorite_ticker) VALUES (:userd, :favticker);");
        //prepare sql query to add to database
        $stmt->bindParam(':userd', $userid);
        $stmt->bindParam(':favticker', $tick);

        //execute prepared statement
        $stmt->execute();


    }
    //if there was an error executing the statement die and give a message
    catch(PDOException $e) {
        die("Could search for ticker$dbhost:" . $e->getMessage());
    }
  }

  function getID($useremail){
    try{
        $stmt = $this->dbh->prepare('SELECT `user_email`, `user_id` FROM `users` WHERE `user_email`= :useremail;');
        $stmt->bindParam(':useremail', $useremail, PDO::PARAM_STR, 250);
        $stmt->execute();
    }
    catch(PDOException $e) {
        die("Could not retrieve entry $useremail from database:" . $e->getMessage());
    }
    //grab the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //if nothing
    if(!$result)
        return false;

    //otherwise return result
    return $result["user_id"];
}

}
