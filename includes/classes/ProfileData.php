<?php
class ProfileData {

    private $connection, $profileUserObj;

    public function __construct($connection, $profileUsername) {
        
        $this->connection = $connection;
        $this->profileUserObj = new User($connection, $profileUsername);
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
        
    }

}
?>