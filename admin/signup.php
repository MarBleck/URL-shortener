<?php 
    session_start();
    // Include general config
    require_once '../inc/config.inc.php';
    // Check is the registration is disabled and send the user to the login page
    if ($allow_registration == false) {
        header("location: /admin/login.php?error=nosignup");
        exit();
    } else {
        
    }
    // Forward when the user is logged in 
    if (isset($_SESSION['username'])) {
        header("location: /admin/index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup URL shortener</title>
    <link rel="stylesheet" href="/admin/css/main.php">
</head>
<body>
    <?php
        if (isset($_POST['submit'])) {
            // Get $_POST parameters
            $uid = $_POST['username'];
            $pwd = $_POST['pwd'];
            $pwdr = $_POST['pwd2'];
            $token = $_POST['token'];

            // Include MySQL PDO config
            require_once '../inc/pdo.mysql.php';
            // Check username
            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user"); 
            $stmt->bindParam(":user", $uid);
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count == 0) {
                // Username isn't taken

                // Check if registration with tokens required
                if ($allow_registration_with_token == true) {
                    // Check if the Token in the array
                    if (in_array($token, $registrations_token)) {
                        // If the token in the array, then do nothing
                    } else {
                        // If the token not in the array, then abort the registration and give the user the error that the token doesn't exist
                        header("location: /admin/signup.php?error=token&username=".$uid."");
                        exit();
                    }
                } else {
                    // If the registration allowed without tokens then do nothing
                }
                
                // Check if the passwords the same
                if ($pwd == $pwdr) {
                    // Create user

                    // Standard permission
                    $stp = "user";

                    // Hash password
                    $hashedpwd = password_hash($pwd, PASSWORD_BCRYPT);

                    // Insert the user data into the database and create the account
                    $stmt = $mysql->prepare("INSERT INTO accounts (username, password, permissions) VALUES (:user, :pwd, :permissions)");
                    // Binds username and password
                    $stmt->bindParam(":user", $uid);
                    $stmt->bindParam(":pwd", $hashedpwd);
                    $stmt->bindParam(":permissions", $stp);
                    // Execute the statement
                    $stmt->execute();
                    header("location: /admin/login.php?success=signup");


                } else {
                    header("location: /admin/signup.php?error=pwdns&username=".$uid."");
                }

            } else {
                header("location: /admin/signup.php?error=username_taken");
            }
        }
    ?>
    <div class="loginsignupcontainer">
        <h1 class="loginsignuph1">Signup URL shortener</h1>
        <?php 
            // Displays errors
            if (isset($_GET['error'])) {
                if ($_GET['error'] == "username_taken") {
                    // Display that the username is taken
                    echo "<h2>The username is already in use</h2>";
                } elseif ($_GET['error'] == "pwdns") {
                    // Display that the passwords don't match
                    echo "<h2>The passwords don't match</h2>";
                }
            }
        ?>
        <div class="spacer"></div>
        <form action="signup.php" method="post" class="signuploginform">
            <input type="text" name="username" id="username" placeholder="Username" required <?php if (isset($_GET['username'])) {echo 'value="'.$_GET['username'].'"';} ?> class="loginsignupinput">
            <input type="password" name="pwd" id="password" placeholder="Password" required class="loginsignupinput">
            <input type="password" name="pwd2" id="password" placeholder="Password repeat" required class="loginsignupinput">
            <?php 
            // Check if the registration allowed
            if ($allow_registration == true) {
                // Check if the registration with token required
                if ($allow_registration_with_token == true) {
                    if ($allow_token_show == true) {
                        // Display the input with the type text to see the token
                        echo '<input type="text" name="token" id="token" placeholder="Registration token" required class="loginsignupinput">';
                    }
                    if ($allow_token_show == false) {
                        // Display the input with the type password to write it securely into the input
                        echo '<input type="password" name="token" id="token" placeholder="Registration token" required class="loginsignupinput">';
                    }
                }
            }
            ?>
            <button type="submit" name="submit" class="loginsignupinput loginsignupinputbutton">Signup</button>
        </form>
        <br><p class="loginsignupoth"><a href="/admin/login.php">Login</a></p>
    </div>
    <?php
        include_once '../inc/footer-ls.php';
    ?>
</body>
</html>