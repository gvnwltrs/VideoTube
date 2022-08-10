<?php
class SubscriptionsProvider {
    private $connection, $userLoggedInObj; 

    public function __construct($connection, $userLoggedInObj) {
        $this->connection = $connection;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array(); 
        $subscriptions = $this->userLoggedInObj->getSubscriptions();

        if(sizeof($subscriptions) > 0) {
            // user1, user2, user3
            //SELECT * FROM videos WHERE uploadedBy = ? OR uploadedBy = ? AND uploadedBy = ?
            // $query->bindParam(1, "user1"); //number '1' corresponds to the first '?' and so on
            // $query->bindParam(2, "user2");
            // $query->bindParam(3, "user3");


            $condition = "";
            $i = 0; 

            while($i < sizeof($subscriptions)) {
                if($i == 0) {
                    $condition .= "WHERE uploadedBy = ?"; 
                }
                else {
                    $condition .= " OR uploadedBy = ?"; 
                }
                $i++;
            }

            $videoSql = "SELECT * FROM videos $condition ORDER BY uploadDate DESC";
            $videoQuery = $this->connection->prepare($videoSql); 

            $i = 1; 

            foreach($subscriptions as $sub) {
                $subUsername = $sub->getUsername(); 
                $videoQuery->bindValue($i, $subUsername); 
                $i++;
            }

            $videoQuery->execute();
            while($row = $videoQuery->fetch(PDO::FETCH_ASSOC)) {
                $video = new Video($this->connection, $row, $this->userLoggedInObj);
                array_push($videos, $video);
            }
        }

        return $videos; 

    }


}
?>