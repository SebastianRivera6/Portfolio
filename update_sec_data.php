<?php

//for database operations
require_once('dboperations.php');

//This file contains the code to check the SEC FOIA website for updated data and uploads it into our database
//1. Check for updated data
//2. Download ANY new zip files
//3. Incrementally parse and bulk load the data into our database

//This file supports being run every X minutes via a cron job
/*
command to edit crontab: 
# crontab -u www-data -e

to run every hour on the first minute, contents to put in:
0  *  *  *  * php -f /var/www/nextcloud/cron.php
*/

//DEBUG flag
$DEBUG = true;


$webcontent = file_get_contents("https://www.sec.gov/data/foiadocsfailsdatahtm");

//holds each web link to download zip
$fileList = [];

//parse DOM
$dom = new DOMDocument;
$dom->loadHTML($webcontent);
$links = $dom->getElementsByTagName('a');
foreach ($links as $link) {
    //if the file contains a comma, we are looking for it
    if (strpos($link->nodeValue, ',') !== false)
    {
        $filename = explode('/',$link->getAttribute('href'))[4];
        $baseaddress = $link->getAttribute('href');
        //if the file is after 2009, save it to the list
        if (strpos($filename, 'cnsf') !== false)
        {
            if($DEBUG === true)
            {
                echo "Adding ", $filename, " to the list of possible additions", PHP_EOL;
            }
            //add to list of SEC FTD files fileList[https://.....] = cnsfaildata.zip
            $fileList[$filename] = "https://www.sec.gov".$baseaddress;
        }
    }
}
if($DEBUG === true){
    foreach ($fileList as $file){
        echo $file, PHP_EOL;
    }
}

//check to see if ftd_data_zips folder exists, if not then make it
$path = "./ftd_data_zips";
$unzip_path = "./ftd_data";
$done_path = "./ftd_data_done";
if (!file_exists($path)) {
    mkdir($path, 0777, true);
}
if (!file_exists($unzip_path)) {
    mkdir($unzip_path, 0777, true);
}
if (!file_exists($done_path)) {
    mkdir($done_path, 0777, true);
}

//download and unzip each file
foreach ($fileList as $file => $webaddress)
{
    if (!file_exists($path.'/'.$file)) 
    {   if($DEBUG === true)
            echo "Downloading ", $file, " from ", $webaddress, "..." ,PHP_EOL;
        if(file_put_contents($path.'/'.$file,file_get_contents($webaddress))) {
            echo "File downloaded successfully", PHP_EOL;
            //unzip file
            $zip = new ZipArchive;
            $res = $zip->open($path.'/'.$file);
            $filenozipname = explode(".",$file)[0];
            if ($res === TRUE) {
                $zip->extractTo($unzip_path.'/');
                $zip->close();
                echo "unzipped!", PHP_EOL;
            } else {
                echo 'unzip failed ', PHP_EOL;
            }
        }
        else {
            echo "File downloading failed.", PHP_EOL;
        }
              
    }
    else
    {
        echo "$file exists already", PHP_EOL;
    }
}


//create database management object
$mydb = new DBManager("localhost", "webuser", "cpsc431_webuser", "cpsc431", "createtables.sql");

//check to see if each text file is in the database, if not then add it
$textFileList = scandir($unzip_path.'/');
$doneFileList = scandir($done_path.'/');

foreach ($textFileList as $key => $textFile)
{
    //skip first 2 and make sure not done
    if($key >= 2 && !array_search($textFile, $doneFileList))
        {
        //get contents
        $textContents = file_get_contents($unzip_path.'/'.$textFile);

        //split by line
        $lines = explode(PHP_EOL, $textContents);
        $index = 0;
        $totalLines = count($lines);

        //data = ["ticker" => ticker,
        // "date" => date, "CUISP" => CUISP, "fails_to_deliver" => ftd_number, "Description" => desc, "price" => price]
 
        


        foreach ($lines as $line)
        {

            $lineData = explode("|", $lines[$index]);
            //check for valid lines
            if(isset($lineData[5]) && $index !== 0)
            {

                //massage date into an acceptable format
                $correctDate = date('Y-m-d',strtotime($lineData[0]));
                

                $data = ["date" => $correctDate,
                    "cuisp" => $lineData[1],
                    "ticker" => $lineData[2],
                    "fails_to_deliver" => $lineData[3],
                    "description" => $lineData[4],
                    "closing_price" => $lineData[5]];

                //enter into database
                $mydb->addTickerFTD($data);
            }
            //increase counter
            ++$index;
        }

        //move file to done folder
        rename($unzip_path.'/'.$textFile, $done_path.'/'.$textFile);
    }
}