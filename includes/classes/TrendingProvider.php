<?php
class TrendingProvider {
    private $connection, $userLoggedInObj; 

    public function __construct($connection, $userLoggedInObj) {
        $this->connection = $connection;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array(); 

        $query = $this->connection->prepare("SELECT * FROM videos WHERE uploadDate >= now() - INTERVAL 7 DAY
                                            ORDER BY views DESC LIMIT 15"); 
        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->connection, $row, $this->userLoggedInObj); 
            array_push($videos, $video);
        }

        return $videos; 
    }
}
?> 