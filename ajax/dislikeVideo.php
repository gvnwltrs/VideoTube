<?php
require_once("../includes/config.php");
require_once("../includes/classes/Video.php");
require_once("../includes/classes/User.php");

$username = $_SESSION["userLoggedIn"]; 
$videoId = $_POST["videoId"];

$userLoggedInObj = new User($connection, $username); 
$video = new Video($connection, $videoId, $userLoggedInObj, $videoId); 

echo $video->dislike(); 
?>