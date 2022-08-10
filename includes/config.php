<?php
// show errors 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); //Turns on output buffering for
session_start(); 

date_default_timezone_set("America/Denver"); 

try {
    $connection = new PDO("mysql:dbname=VideoTube;host=localhost", "root", "");  
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 
}
catch (PDOException $e) {
    echo "Connection failed: ". $e->getMessage(); 
}
?>