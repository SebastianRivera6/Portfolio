<?php
//allows searching for tickers that match a partial.

//for database operations
require_once('dboperations.php');

//create database management object
$mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

//search term should be in GET
if(isset($_GET["partial"]) && $_GET["partial"] !== "")
{
    $partialTerm = $_GET["partial"];
    $result["tickers"] = $mydb->searchTicker($partialTerm);
    echo json_encode($result);
}