<?php
require_once("includes/header.php"); 
require_once("includes/classes/TrendingProvider.php"); 

if(!User::isLoggedIn()) {
    header("Location: signin.php");
}

$subscriptionsProvider = new SubscriptionsProvider($connection, $userLoggedInObj); 
$videos = $subscriptionsProvider->getVideos();

$videoGrid = new VideoGrid($connection, $userLoggedInObj); 
?> 

<div class="largeVideoGrid">
    <?php
        if(sizeof($videos) > 0) {
            echo $videoGrid->createLarge($videos, "New from your subscriptions", false); 
        }
        else {
            echo "No videos to show"; 
        }
    ?>
</div>