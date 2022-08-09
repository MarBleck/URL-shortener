<?php 
    session_start();
    // Include general config
    require_once '../inc/config.inc.php';
    // Check is the login is disabled, exit the program and show a blank page
    if ($allow_login == false) {
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
    <title>Login URL shortener</title>
    <link rel="stylesheet" href="/admin/css/main.php">
</head>
<body>
    <?php 
        if (isset($_POST['submit'])) {
            // Get $_POST parameters
            $uid = $_POST['username'];
            $pwd = $_POST['pwd'];

            // Include MySQL PDO config
            require_once '../inc/pdo.mysql.php';
            // Check username
            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user");
            $stmt->bindParam(":user", $uid);
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count == 1) {
                
                // Fetches user data from the database
                $row = $stmt->fetch();
                // Check if the password is the same as in the database
                if (password_verify($pwd, $row['password'])) {
                    
                    // Start session and give it the username 
                    // session_start();
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    // Redirect to index.php after login
                    header("location: /admin/index.php");

                } else {
                    header("location: /admin/login.php?error=fail");
                }

            } else {
                header("location: /admin/login.php?error=fail");
            }
        }
    ?>
    <div class="loginsignupcontainer">
        <h1 class="loginsignuph1">Login URL shortener</h1>
        <?php 
            // Displays errors
            if (isset($_GET['error'])) {
                if ($_GET['error'] == "nosignup") {
                    // Display that registration is disabled
                    echo "<h2>Registration was disabled</h2>";
                } elseif ($_GET['error'] == "fail") {
                    // Display that login was failed
                    echo "<h2>This login was failed</h2>";
                }
            }
            // Displays success message
            if (isset($_GET['success'])) {
                if ($_GET['success'] == "signup") {
                    // Display that the user have registered successfully
                    echo "<h2>You have registered</h2>";
                }
            }
            // Displays delete message
            if (isset($_GET['delete'])) {
                if ($_GET['delete'] == "acc") {
                    // Display that the user have registered successfully
                    echo "<h2>You have successfully deleted your account</h2>";
                }
            }
        ?>
        <div class="spacer"></div>
        <form action="login.php" method="post" class="signuploginform">
            <input type="text" name="username" id="username" placeholder="Username" required class="loginsignupinput">
            <input type="password" name="pwd" id="pwd" placeholder="Password" required class="loginsignupinput">
            <button type="submit" name="submit" class="loginsignupinput loginsignupinputbutton">Login</button>
        </form>
        <?php
        // Show a link to signup page is registration enabled
        if ($allow_registration == true) {
            echo '<br><p class="loginsignupoth"><a href="/admin/signup.php">Signup</a></p>';
        } else {}
    ?>
    </div>
    <?php
        include_once '../inc/footer-ls.php';
    ?>
</body>
</html>