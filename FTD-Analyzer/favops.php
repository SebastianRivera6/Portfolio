<?php
//allows searching for fails

//for database operations
require_once('dboperations.php');

//create database management object
$mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

//if we are deleting
if(isset($_POST["delete"]) && isset($_POST["ticker"]) && isset($_POST["userid"]))
{
    $ticker = $_POST["ticker"];
    $uid = $_POST["userid"];
    $mydb->deleteFavorite($ticker, $uid);
}

//if we are adding
if(isset($_POST["add"]) && isset($_POST["ticker"]) && isset($_POST["userid"]))
{
    $ticker = $_POST["ticker"];
    $uid = $_POST["userid"];
    $mydb->addFavorite($ticker, $uid);
}