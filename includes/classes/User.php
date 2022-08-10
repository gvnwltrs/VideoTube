<?php
class User {
    // userTo = who are subscribing to

    // userFrom = the current user/subscriber


    private $connection, $sqlData; 

    public function __construct($connection, $username) {
        $this->connection = $connection;

        $query = $this->connection->prepare("SELECT * FROM users WHERE username = :username"); 
        $query->bindParam(":username", $username);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public static function isLoggedIn() {
        return isset($_SESSION["userLoggedIn"]);
    }

    public function getUsername() {
        // check if logged in first or else database will throw a hissy-fit if you try to query 'nothing' 
        return User::isLoggedIn() && is_array($this->sqlData) ? $this->sqlData["username"] : ""; 
    }

    public function getName() {
        return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"]; 
    }

    public function getFirstName() {
        return $this->sqlData["firstName"]; 
    }

    public function getLastName() {
        return $this->sqlData["lastName"]; 
    }

    public function getEmail() {
        return $this->sqlData["email"]; 
    }

    public function getProfilePicture() {
        // return User::isLoggedIn() && is_array($this->sqlData) ? $this->sqlData["profilePicture"] : ""; 
        return $this->sqlData["profilePicture"];
    }

    public function getSignUpDate() {
        return $this->sqlData["signUpDate"]; 
    }

    public function isSubscribedTo($userTo) {
        $username = $this->getUsername();
        
        $query = $this->connection->prepare("SELECT * FROM subscribers WHERE userTo=:userTo AND userFrom=:userFrom"); 
        $query->bindParam(":userTo", $userTo); 
        $query->bindParam(":userFrom", $username); 

        $query->execute(); 

        return $query->rowCount() > 0; 
    
    }

    public function getSubscriberCount() {
        $username = $this->getUsername();
        
        $query = $this->connection->prepare("SELECT * FROM subscribers WHERE userTo=:userTo"); 
        $query->bindParam(":userTo", $username); 

        $query->execute(); 

        return $query->rowCount(); 
    
    }

    public function getSubscriptions() {
        $query = $this->connection->prepare("SELECT userTo FROM subscribers WHERE userFrom=:userFrom"); 
        $username = $this->getUsername(); 
        $query->bindParam(":userFrom", $username);

        $query->execute(); 

        $subscriptions = array(); 

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($this->connection, $row["userTo"]); 
            array_push($subscriptions, $user); 
        }

        return $subscriptions;
    }
}
?>  