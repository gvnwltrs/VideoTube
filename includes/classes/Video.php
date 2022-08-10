<?php
class Video {
    
    private $connection, $sqlData, $userLoggedInObj; 

    public function __construct($connection, $input, $userLoggedInObj) {
        $this->connection = $connection;
        $this->userLoggedInObj = $userLoggedInObj;

        if(is_array($input)) {
            $this->sqlData = $input; 
        }
        else {
            $query = $this->connection->prepare("SELECT * FROM videos WHERE id = :id"); 
            $query->bindParam(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

        
    }

    public function getVideoId() {
        return $this->sqlData["id"]; 
    }

    public function getUploadedBy() {
        return $this->sqlData["uploadedBy"]; 
    }

    public function getTitle() {
        return $this->sqlData["title"]; 
    }

    public function getDescription() {
        return $this->sqlData["description"]; 
    }

    public function getPrivacy() {
        return $this->sqlData["privacy"]; 
    }

    public function getFilePath() {
        return $this->sqlData["filePath"]; 
    }
    
    public function getCategory() {
        return $this->sqlData["category"]; 
    }

    public function getUploadDate() {
        $date = $this->sqlData["uploadDate"]; 
        return date("M j, Y", strtotime($date)); 
    }

    public function getTimestamp() {
        $date = $this->sqlData["uploadDate"]; 
        return date("M jS, Y", strtotime($date)); 
    }

    public function getViews() {
        return $this->sqlData["views"]; 
    }

    public function getDuration() {
        return $this->sqlData["duration"]; 
    }

    public function incrementViews() {
        $videoId = $this->getVideoId(); 
        $query = $this->connection->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
        $query->bindParam(":id", $videoId); 

        $query->execute(); 

        $this->sqlData["views"] = $this->sqlData["views"] + 1; 
    }

    public function getLikes() {
        $videoId = $this->getVideoId();
        $query = $this->connection->prepare("SELECT count(*) as 'count' FROM likes WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        
        $query->execute(); 

        $data = $query->fetch(PDO::FETCH_ASSOC); 

        return $data["count"];
    }

    public function getDislikes() {
        $videoId = $this->getVideoId();
        $query = $this->connection->prepare("SELECT count(*) as 'count' FROM dislikes WHERE videoId = :videoId");
        $query->bindParam(":videoId", $videoId);
        
        $query->execute(); 

        $data = $query->fetch(PDO::FETCH_ASSOC); 

        return $data["count"];
    }

    public function like() {
        $username = $this->userLoggedInObj->getUsername(); 
        $videoId = $this->getVideoId(); 

        // check if liked before
        if($this->wasLikedBy()) {
            // user has already liked -- Remove Like
            $query = $this->connection->prepare("DELETE FROM likes WHERE username=:username AND videoId=:videoId"); 
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);

            $query->execute(); 

            $results = array(
                "likes" => -1,
                "dislikes" => 0
            );

            return json_encode($results); 
        }
        else {
            // user has not liked before -- Add Like and Remove dislike 
            $query = $this->connection->prepare("DELETE FROM dislikes WHERE username=:username AND videoId=:videoId"); 
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute(); 
            $count = $query->rowCount(); 
            
            $query = $this->connection->prepare("INSERT INTO likes(username, videoId) VALUES(:username, :videoId)"); 
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId); 
            $query->execute(); 

            $results = array(
                "likes" => 1,
                "dislikes" => 0 - $count
            );

            // return json_encoder($results); 
            return json_encode($results); 

        }
    }

    public function dislike() {
        $username = $this->userLoggedInObj->getUsername(); 
        $videoId = $this->getVideoId(); 

        // check if liked before
        if($this->wasDislikedBy()) {
            // user has already disliked -- Remove Like
            $query = $this->connection->prepare("DELETE FROM dislikes WHERE username=:username AND videoId=:videoId"); 
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);

            $query->execute(); 

            $results = array(
                "likes" => 0,
                "dislikes" => -1
            );

            return json_encode($results); 
        }
        else {
            // user has not disliked before -- Add Like and Remove dislike 
            $query = $this->connection->prepare("DELETE FROM likes WHERE username=:username AND videoId=:videoId"); 
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId);
            $query->execute(); 
            $count = $query->rowCount(); 
            
            $query = $this->connection->prepare("INSERT INTO dislikes(username, videoId) VALUES(:username, :videoId)"); 
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $videoId); 
            $query->execute(); 

            $results = array(
                "likes" => 0 - $count,
                "dislikes" => 1
            );

            // return json_encoder($results); 
            return json_encode($results); 

        }
    }

    public function wasLikedBy() {
        $username = $this->userLoggedInObj->getUsername(); 
        $videoId = $this->getVideoId(); 

        $query = $this->connection->prepare("SELECT * FROM likes WHERE username=:username AND videoId=:videoId"); 
        $query->bindParam(":username", $username); 
        $query->bindParam(":videoId", $videoId); 

        $query->execute(); 

        return $query->rowCount() > 0; 
    }

    public function wasDislikedBy() {
        $username = $this->userLoggedInObj->getUsername(); 
        $videoId = $this->getVideoId(); 

        $query = $this->connection->prepare("SELECT * FROM dislikes WHERE username=:username AND videoId=:videoId"); 
        $query->bindParam(":username", $username); 
        $query->bindParam(":videoId", $videoId); 

        $query->execute(); 

        return $query->rowCount() > 0; 
    }

    public function getNumberOfComments() {
        $videoId = $this->getVideoId(); 
        $query = $this->connection->prepare("SELECT * FROM comments WHERE videoId=:videoId");
        $query->bindParam(":videoId", $videoId); 
        $query->execute(); 

        return $query->rowCount(); 
    }

    public function getComments() {
        $videoId = $this->getVideoId(); 
        $query = $this->connection->prepare("SELECT * FROM comments WHERE videoId=:videoId AND responseTo=0 ORDER BY datePosted DESC");
        $query->bindParam(":videoId", $videoId); 
        $query->execute(); 

        $comments = array(); 

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $comment = new Comment($this->connection, $row, $this->userLoggedInObj, $videoId); 
            array_push($comments, $comment); 
        }

        return $comments; 
    }

    public function getThumbnail() {
        $videoId = $this->getVideoId();
        $query = $this->connection->prepare("SELECT filePath FROM thumbnails WHERE videoId=:videoId AND selected=1"); 
        $query->bindParam(":videoId", $videoId); 

        $query->execute();

        return $query->fetchColumn(); 
    }
}
?> 