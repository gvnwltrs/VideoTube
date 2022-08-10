<?php

class CommentSection {
    private $connection, $video, $userLoggedInObj; 

    public function __construct($connection, $video, $userLoggedInObj) {
        $this->connection = $connection;
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create() {
        return $this->createCommentSection(); 
    }

    private function createCommentSection() {
        $numComments = $this->video->getNumberOfComments(); 
        $postedBy = $this->userLoggedInObj->getUsername();
        $videoId = $this->video->getVideoId();

        $profileButton = ButtonProvider::createUserProfileButton($this->connection, $postedBy);
        $commentAction = "postComment(this, \"$postedBy\", $videoId, 0, \"comments\")";
        $commentButton = ButtonProvider::createButton("COMMENT", null, $commentAction, "postComment");

        $comments = $this->video->getComments();
        $commentItems = "";
        foreach($comments as $comment) {
            $commentItems .= $comment->create(); 
        }

        return "<div class='commentSection'>
                    <div class='header'>
                        <span class='commentCount'>$numComments Comments</span>

                        <div class='commentForm'>
                            $profileButton
                            <textarea class='commentBodyClass' placeholder='Add a public comment'></textarea>
                            $commentButton
                        </div>
                    </div>

                    <div class='comments'>
                        $commentItems
                    </div>
                </div>";
    }
}
?>