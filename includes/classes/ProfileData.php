<?php
class ProfileData {

    private $connection, $profileUserObj;

    public function __construct($connection, $profileUsername) {
        
        $this->connection = $connection;
        $this->profileUserObj = new User($connection, $profileUsername);
    }

    public function getProfileUserObj() {
        return $this->profileUserObj;
    }

    public function getProfileUsername() {
        return $this->profileUserObj->getUsername(); 
    }

    public function userExists() {
        $profileUsername = $this->getProfileUsername(); 
        $query = $this->connection->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username", $profileUsername); 

        $query->execute(); 

        return $query->rowCount() != 0;  
    }

    public function getCoverPhoto() {
        return "assets/images/coverPhotos/default-cover-photo.jpg";
    }

    public function getProfileUserFullName() {
        return $this->profileUserObj->getName(); 
    }

    public function getProfilePicture() {
        return $this->profileUserObj->getProfilePicture();
    }

    public function getSubscriberCount() {
        return $this->profileUserObj->getSubscriberCount();
    }

    public function getUserVideos() {
        $username = $this->getProfileUsername();
        $query = $this->connection->prepare("SELECT * FROM videos WHERE uploadedBy=:uploadedBy ORDER BY uploadDate DESC"); 
        $query->bindParam(":uploadedBy", $username); 
        $query->execute(); 

        $videos = array(); 
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $videos[] = new Video($this->connection, $row, $this->profileUserObj->getUsername()); 
        }

        return $videos; 
    }

}
?>