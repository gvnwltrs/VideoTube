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
        $coverPhotoSrc = $this->profileData->getCoverPhoto(); 
        $name = $this->profileData->getProfileUserFullName();
        return "<div class='coverPhotoContainer'>
                    <img src='$coverPhotoSrc' class='coverPhoto'>
                    <div class='channelName'>$name</div>
                </div>";
    }

    public function createHeaderSection() {
        $profileImage = $this->profileData->getProfilePicture(); 
        $name = $this->profileData->getProfileUserFullName(); 
        $subCount = $this->profileData->getSubscriberCount();

        $button = $this->createHeaderButton();

        return "<div class='profileHeader'>
                    <div class='userInfoContainer'>
                        <img src='$profileImage' class='profileImage'>
                        <div class='userInfo'>
                            <span class='title'>$name</span>
                            <span class='subscriber'>$subCount subscribers</span>
                        </div>
                    </div>

                    <div class='buttonContainer'>
                        <div class='buttonItem'>
                        $button
                        </div>
                    </div>
                </div>";
    }

    public function createTabSection() {
        return "<nav>
                    <div class='nav nav-tabs' id='nav-tab' role='tablist'>
                    <button class='nav-link active' id='videos-tab' data-bs-toggle='tab' 
                    data-bs-target='#videos' type='button' role='tab' aria-controls='videos' 
                    aria-selected='true'>
                        VIDEOS
                    </button>
                    <button class='nav-link' id='about-tab' data-bs-toggle='tab' 
                    data-bs-target='#about' type='button' role='tab' 
                    aria-controls='about' aria-selected='false'>
                        ABOUT
                    </button>
                    </div>
                </nav>";
    }

    public function createContentSection() {

        $videos = $this->profileData->getUserVideos();

        if(sizeof($videos) > 0) {
            $videosGrid = new VideoGrid($this->connection, $this->userLoggedInObj); 
            $videosGridHtml = $videosGrid->create($videos, null, false); 

        }
        else {
            $videosGridHtml = "<span>This user has no vidoes</span>";
        }

        $aboutSection = $this->createAboutSection();

        return "<div class='tab-content channelContent' id='nav-tabContent'>
                    <div class='tab-pane fade show active' id='videos' role='tabpanel' 
                    aria-labelledby='videos-tab' tabindex='0'>
                        $videosGridHtml
                    </div>
                    <div class='tab-pane fade' id='about' role='tabpanel' 
                    aria-labelledby='about-tab' tabindex='0'>
                        $aboutSection
                    </div>
                </div>";
    }

    private function createHeaderButton() {
        if($this->userLoggedInObj->getUsername() == $this->profileData->getProfileUsername()) {
            return "";
        }
        else {
            return ButtonProvider::createSubscribeButton(
                                                        $this->connection, 
                                                        $this->profileData->getProfileUserObj(), 
                                                        $this->userLoggedInObj); 
        }
    }

    private function createAboutSection() {
        $html = "<div class='section'>
                    <div class='title'>
                        <span>Details</span>
                    </div>
                    <div class='values'>";

        $details = $this->profileData->getAllUserDetails();
        foreach($details as $key => $value) {
            $html .= "<span>$key: $value</span>";
        }

        $html .= "</div></div>";

        return $html;
    }
}
?>