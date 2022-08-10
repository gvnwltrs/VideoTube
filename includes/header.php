<?php 
require_once("includes/config.php");
require_once("includes/classes/ButtonProvider.php"); 
require_once("includes/classes/User.php"); 
require_once("includes/classes/Video.php"); 
require_once("includes/classes/VideoGrid.php"); 
require_once("includes/classes/VideoGridItem.php"); 
require_once("includes/classes/SubscriptionsProvider.php"); 
require_once("includes/classes/NavigationMenuProvider.php"); 

$usernameLoggedIn = User::isLoggedIn() ? $_SESSION["userLoggedIn"] : "";
$userLoggedInObj = new User($connection, $usernameLoggedIn);  
?>

<!DOCTYPE html>
<html>
    <head>
        <title>VideoTube</title>

        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>

        <script src="assets/js/commonActions.js"></script>

        <script src="assets/js/userActions.js"></script>
    </head>

    <body>
            
           <div id="pageContainer">

                <div id="mastHeadContainer">
                    <button class="navShowHide">
                        <img src="assets/images/icons/menu.png">
                    </button>

                    <a class="logoContainer" href="index.php">
                        <img src="assets/images/icons/VideoTubeLogo.png" title="logo">
                    </a>

                    <div class="searchBarContainer">
                        <form action="search.php" method="GET">
                            <input type="text" class="searchBar" name="term" placeholder="Search..."> 
                            <button class="searchButton">
                                <img src="assets/images/icons/search.png">
                            </button>
                        </form>
                    </div>

                    <div class="rightIcons"> 
            
                        <a href="upload.php">
                            <img src="assets/images/icons/upload.png"> 
                        </a>
                        <?php echo ButtonProvider::createUserProfileNavigationButton($connection, $userLoggedInObj->getUsername()); ?>
                    </div>
                </div>

                <div id="sideNavContainer" style="display:none;">
                    <?php
                        $navigationProvider = new NavigationMenuProvider($connection, $userLoggedInObj); 
                        echo $navigationProvider->create(); 
                    ?>
                </div>

                <div id="mainSectionContainer">
                    <div id="mainContentContainer">