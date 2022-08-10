<?php 
require_once("includes/header.php"); 
require_once("includes/classes/VideoPlayer.php"); 
require_once("includes/classes/VideoInfoSection.php"); 
require_once("includes/classes/CommentSection.php"); 
require_once("includes/classes/Comment.php");
// session_destroy(); 
if(!isset($_GET["id"])) {
    echo "No url passed into page"; 
    exit();
}

$video = new Video($connection, $_GET["id"], $userLoggedInObj);
$video->incrementViews(); 
?> 

<script src="assets/js/videoPlayerActions.js"></script>
<script src="assets/js/commentActions.js"></script>

<div class="watchLeftColumn">

    <?php
        $videoPlayer = new VideoPlayer($video);
        echo $videoPlayer->create(true);

        $videoPlayer = new VideoInfoSection($connection, $video, $userLoggedInObj);
        echo $videoPlayer->create();

        $commentSection = new CommentSection($connection, $video, $userLoggedInObj);
        echo $commentSection->create();
    ?>

</div>

<div class="suggestions">

    <?php
    $videoGrid = new VideoGrid($connection, $userLoggedInObj);
    echo $videoGrid->create(null, null, false);
    ?>

</div>
                        
<?php require_once("includes/footer.php"); ?> 