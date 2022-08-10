<?php
require_once("../includes/config.php");    
require_once("../includes/classes/User.php");    
require_once("../includes/classes/Comment.php");    

if(isset($_POST['commentText']) && isset($_POST['postedBy']) && isset($_POST['videoId'])) {
   $userLoggedInObj = new User($connection, $_SESSION["userLoggedIn"]);    
   $postedBy = $_POST['postedBy']; 
   $videoId = $_POST['videoId']; 
   $responseTo = isset($_POST['responseTo']) ? $_POST['responseTo'] : 0; 
   $commentText = $_POST['commentText']; 
   
   $query = $connection->prepare("INSERT INTO comments(postedBy, videoId, responseTo, body)
                                    VALUES(:postedBy, :videoId, :responseTo, :body)");

    $query->bindParam(':postedBy', $postedBy);
    $query->bindParam(':videoId', $videoId);
    $query->bindParam(':responseTo', $responseTo);
    $query->bindParam(':body', $commentText);

    $query->execute();

    $newComment = new Comment($connection, $connection->lastInsertId(), $userLoggedInObj, $videoId);
    echo $newComment->create();
}
else {
    echo "One or more parameters are not passed into subscribe.php the file"; 
}

?>