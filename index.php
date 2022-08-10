<?php require_once("includes/header.php"); ?> 



<div class="videoSection">
    <?php
        $subscriptionsProvider = new SubscriptionsProvider($connection, $userLoggedInObj);
        $subscriptionVideos = $subscriptionsProvider->getVideos(); 

        $videoGrid = new VideoGrid($connection, $userLoggedInObj->getUsername()); 

        if(User::isLoggedIn() && sizeof($subscriptionVideos) > 0 ) {
            echo $videoGrid->create($subscriptionVideos, "Subscriptions", false); 
        }

        echo $videoGrid->create(null, "Recommended", false); 
    ?>
</div>
                        
<?php require_once("includes/footer.php"); ?> 