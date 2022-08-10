<?php 
require_once("includes/config.php"); 
require_once("includes/classes/Constants.php"); 
require_once("includes/classes/Account.php");
require_once("includes/classes/FormSanitizer.php");


$account = new Account($connection); 

if(isset($_POST["submitButton"])) {
    $firstName = FormSanitizer::sanitizeFormString($_POST["firstName"]); 
    $lastName = FormSanitizer::sanitizeFormString($_POST["lastName"]); 
    
    $username = FormSanitizer::sanitizeFormUsername($_POST["username"]); 
    
    $email = FormSanitizer::sanitizeFormEmail($_POST["email"]); 
    $email2 = FormSanitizer::sanitizeFormEmail($_POST["email2"]); 
    
    $password = FormSanitizer::sanitizeFormPassword($_POST["password"]); 
    $password2 = FormSanitizer::sanitizeFormPassword($_POST["password2"]); 

    $wasSuccessful = $account->register($firstName, $lastName, $username, 
                        $email, $email2, $password, $password2);

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
                    <h3>Sign Up</h3>
                    <span>to continue to VideoTube</span>
                </div>

                <div class="loginForm">
                    <form action="signUp.php" method="POST">

                        <?php echo $account->getError(Constants::$firstNameCharacters); ?>
                        <input type="text" name="firstName" placeholder="First Name" autocomplete="off" required value="<?php getInputValue('firstName')?>">
                        
                        <?php echo $account->getError(Constants::$lastNameCharacters); ?>
                        <input type="text" name="lastName" placeholder="Last Name" autocomplete="off" required value="<?php getInputValue('lastName')?>">

                        <?php echo $account->getError(Constants::$usernameCharacters); ?>
                        <?php echo $account->getError(Constants::$usernameTaken); ?>
                        <input type="text" name="username" placeholder="Username" autocomplete="off" required value="<?php getInputValue('username')?>">
                        
                        <?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
                        <?php echo $account->getError(Constants::$emailInvalid); ?>
                        <?php echo $account->getError(Constants::$emailTaken); ?>
                        <input type="email" name="email" placeholder="email" autocomplete="off" required value="<?php getInputValue('email')?>">
                        <input type="email" name="email2" placeholder="Confirm email" autocomplete="off" required value="<?php getInputValue('email2')?>">
                        
                        <?php echo $account->getError(Constants::$passwordsDoNotMatch); ?>
                        <?php echo $account->getError(Constants::$passwordsNotAlphanumeric); ?>
                        <?php echo $account->getError(Constants::$passwordLength); ?>
                        <input type="password" name="password" placeholder="Password" autocomplete="off" required value="<?php getInputValue('password')?>">
                        <input type="password" name="password2" placeholder="Confirm password" autocomplete="off" required value="<?php getInputValue('password2')?>">
                        
                        <input type="submit" name="submitButton" value="SUBMIT"> 

                    </form>
                </div>

                <a class="signInMessage" href="signin.php">Already have an account? Sign in Here!</a>

            </div>

        </div>

    </body>

</html>