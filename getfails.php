<?php
//allows searching for fails

//for database operations
require_once('dboperations.php');

//create database management object
$mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

//search term should be in GET
if(isset($_GET["ticker"]) && $_GET["ticker"] !== "" && isset($_GET["start"]) && isset($_GET["end"]))
{
    $ticker = $_GET["ticker"];
    $start = $_GET["start"];
    $end = $_GET["end"];
    $result["tickers"] = $mydb->retrieveTickerData($ticker, $start, $end);
    echo json_encode($result);
}