<?php 
require_once("includes/config.php"); 
require_once("includes/classes/Constants.php"); 
require_once("includes/classes/Account.php");
require_once("includes/classes/FormSanitizer.php");

$account = new Account($connection); 

if(isset($_POST["submitButton"])) {

    $username = FormSanitizer::sanitizeFormUsername($_POST["username"]);  
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]); 

    $wasSuccessful = $account->login($username, $password); 

    if($wasSuccessful) {
        $_SESSION["userLoggedIn"] = $username; 
        header("Location: index.php"); 
    }
}

function getInputValue($name) {
    if(isset($_POST[$name])) {
        echo $_POST[$name]; 
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Sign In</title>

        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>

    </head>

    <body>

        <div class="signInContainer">

            <div class="column">

                <div class="header">
                    <img src="assets/images/icons/VideoTubeLogo.png" title="logo" alt="Site logo">
                    <h3>Sign In</h3>
                    <span>to continue to VideoTube</span>
                </div>

                <div class="loginForm">
                    <form action="signin.php" method="POST">

                        <?php echo $account->getError(Constants::$loginFailed); ?>
                        <input type="text" name="username" placeholder="Username" required autocomplete="off" value="<?php getInputValue('username')?>">
                        <input type="password" name="password" placeholder="Password" required value="<?php getInputValue('password')?>">
                        <input type="submit" name="submitButton" value="SUBMIT">

                    </form>
                </div>

                <a class="signInMessage" href="signUp.php">Need an account? Sign up here!</a>

            </div>

        </div>

    </body>

</html>