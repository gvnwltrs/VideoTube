<?php
require_once("ProfileData.php"); 

class ProfileGenerator {

    private $connection, $userLoggedInObj, $profileData;

    public function __construct($connection, $userLoggedInObj, $profileUsername) {
        
        $this->connection = $connection;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->profileData = new ProfileData($connection, $profileUsername);
    }

    public function create() {
        $profileUsername = $this->profileData->getProfileUsername(); 

        if(!$this->profileData->userExists()) {
            return "User does not exist"; 
        }

        $coverPhotoSection = $this->createCoverPhotoSection(); 
        $headerSection = $this->createHeaderSection(); 
        $tabSection = $this->createTabSection(); 
        $contentSection = $this->createContentSection(); 

        return "<div class='profileContainer'>
                $coverPhotoSection
                $headerSection
                $tabSection
                $contentSection
                </div>";
    }

    public function createCoverPhotoSection() {

    }

    public function createHeaderSection() {
        
    }

    public function createTabSection() {
        
    }

    public function createContentSection() {
        
    }
}
?>