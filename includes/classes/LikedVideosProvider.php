<?php
class LikedVideosProvider {
    private $connection, $userLoggedInObj; 

    public function __construct($connection, $userLoggedInObj) {
        $this->connection = $connection;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array(); 
        $username = $this->userLoggedInObj->getUsername(); 

        $query = $this->connection->prepare("SELECT videoId FROM likes WHERE username=:username AND commentId=0 ORDER BY id DESC");
                                            
        $query->bindParam(":username", $username); 
        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->connection, $row["videoId"], $this->userLoggedInObj); 
            array_push($videos, $video);
        }

        return $videos; 
    }
}
?> 