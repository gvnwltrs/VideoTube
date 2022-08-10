<?php
class SearchResultsProvider {

    private $connection, $userLoggedInObj; 

    public function __construct($connection, $userLoggedInObj) {
        $this->connection = $connection;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos($term, $orderBy) {
        $query = $this->connection->prepare("SELECT * FROM videos WHERE title LIKE CONCAT('%', :term, '%')
                                            OR uploadedBy LIKE CONCAT('%', :term, '%') ORDER BY $orderBy DESC"); 

        $query->bindParam(":term", $term); 
        $query->execute(); 

        $videos = array(); 
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $video = new Video($this->connection, $row, $this->userLoggedInObj); 
            array_push($videos, $video); 
        }

        return $videos; 
    }
}
?>