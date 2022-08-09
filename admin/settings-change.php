<?php 
// Start session
session_start();
// Check if the parameter username in $_SESSION
if (isset($_SESSION['username'])) {
    // Include configs
    include_once '../inc/config.inc.php';
    include_once '../inc/oth.inc.php';

    // Check if the parameter changeuid in $_POST
    if (isset($_POST['changeuid'])) {
        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Username
        $useruid = $_POST['uid'];

        // Displays an interface to change the username
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Change username / URL shortener</title>
            <link rel="stylesheet" href="/admin/css/main.php">
        </head>
        <body>
            <div class="loginsignupcontainer">
            <h1 class="loginsignuph1">Change username</h1>
            <div class="spacer"></div>
            <p style="color:red;">After you changed your username, you have to log in again</a>
            <form action="/admin/settings-change.php" method="post" class="signuploginform">
                <input type="text" name="username" id="username" placeholder="Username" required class="loginsignupinput">
                <input type="text" name="username2" id="username2" placeholder="Username repeat" required class="loginsignupinput">
                <input type="hidden" name="userid" value="'.$userid.'">
                <button type="submit" name="changeuidf" class="loginsignupinput loginsignupinputbutton">Change username</button>
            </form>
            </div>
            ';
                include_once '../inc/footer.php';
            echo '
        </body>
        </html>';
    }
    // Check if the parameter changeuidf in $_POST
    elseif (isset($_POST['changeuidf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameter out of $_SESSION
        $oldusername = $_SESSION['username'];

        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // New username
        $newusername = $_POST['username'];
        // New username2
        $newusernamer = $_POST['username2'];

        if ($newusername == $newusernamer) {
            // Starts a query to look up that the username exist
            $check = $conn->query("SELECT * FROM accounts WHERE username = '".$newusername."'");

            // If it doesn't exist, starts the change of the URL
            if($check->num_rows == 0) {
                
                // Prepare MySQL statement
                $sql = "UPDATE accounts SET username='".$newusername."' WHERE id='".$userid."'";
                // Execute MySQL statement
                $run = mysqli_query($conn, $sql);

                // Get data from MySQL for the short URL user from the database
                // Prepare MySQL statement
                $sql_url = "SELECT * FROM url";
                // Execute MySQL statement
                $result_url = mysqli_query($conn, $sql_url);
                $queryResult_url = mysqli_num_rows($result_url);
                // Check if the result bigger than 0
                if ($queryResult_url > 0) {
                    // Start while loop and get data from MySQL
                    while ($row_url = mysqli_fetch_assoc($result_url)) {
                        // Check if the username in the entry, the old username is
                        if ($row_url['url_user'] === "".$oldusername."") {
                            // Change the old username to the new username
                            // Prepare MySQL statement
                            $sql_url = "UPDATE url SET url_user='".$newusername."' WHERE url_id='".$row_url['url_id']."'";
                            // Execute MySQL statement
                            $run_url = mysqli_query($conn, $sql_url);
                        }
                    }
                }

                // Redirect user to the logout page
                header("location: ".$nourl."logout.php");
                exit();

            } else {
                // Redirect the user with an error message to the settings page
                header("location: /admin/settings.php?username=theusernameexist");
                exit();
            }
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?username=errordontmatch");
            exit();
        }

    }
    // Check if the parameter changepwd in $_POST
    elseif (isset($_POST['changepwd'])) {
        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Username
        $useruid = $_POST['uid'];

        // Displays an interface to change the password
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Change password / URL shortener</title>
            <link rel="stylesheet" href="/admin/css/main.php">
        </head>
        <body>
            <div class="loginsignupcontainer">
            <h1 class="loginsignuph1">Change password</h1>
            <div class="spacer"></div>
            <form action="/admin/settings-change.php" method="post" class="signuploginform">
                <input type="password" name="pwd" id="pwd" placeholder="Current password" required class="loginsignupinput">
                <input type="password" name="newpwd" id="newpwd" placeholder="New password" required class="loginsignupinput">
                <input type="password" name="newpwdr" id="newpwdr" placeholder="New password repeat" required class="loginsignupinput">
                <input type="hidden" name="userid" value="'.$userid.'">
                <button type="submit" name="changepwdf" class="loginsignupinput loginsignupinputbutton">Change password</button>
            </form>
            </div>
            ';
                include_once '../inc/footer.php';
            echo '
        </body>
        </html>';
    }
    // Check if the parameter changepwdf in $_POST
    elseif (isset($_POST['changepwdf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Old password
        $pwd = $_POST['pwd'];
        // New password
        $newpwd = $_POST['newpwd'];
        // New password repeat
        $newpwd2 = $_POST['newpwdr'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE id = :id");
        $stmt->bindParam(":id", $userid);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Verify the password from the variable is the same as in the database
        if (password_verify($pwd, $row['password'])) {
            // Check if the two passwords are the same
            if ($newpwd == $newpwd2) {
                
                // Hash the new password
                $newhashedpwd = password_hash($newpwd, PASSWORD_BCRYPT);
    
                // Prepare MySQL statement
                $sql = "UPDATE accounts SET password='".$newhashedpwd."' WHERE id='".$userid."'";
                // Execute MySQL statement
                $run = mysqli_query($conn, $sql);
    
                // Redirect user to the settings page
                header("location: ".$nourl."settings.php?password=success");
                exit();
            } else {
                // Redirect the user with an error message to the settings page
                header("location: /admin/settings.php?password=newpassworddontmatch");
                exit();
            }
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?password=currentpassworddontmatch");
            exit();
        }

    }
    // Check if the parameter changepermissions in $_POST
    elseif (isset($_POST['changepermissions'])) {
        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Username
        $useruid = $_POST['uid'];
        
        // Check if the feature to change the permissions with a token is active
        if ($enable_change_permission_with_tokens == true) {
            // Displays an interface to change the change permissions
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Change permissions / URL shortener</title>
                <link rel="stylesheet" href="/admin/css/main.php">
            </head>
            <body>
                <div class="loginsignupcontainer">
                <h1 class="loginsignuph1">Change permissions with token</h1>
                <div class="spacer"></div>
                <form action="/admin/settings-change.php" method="post" class="signuploginform">
                    '; 
                    // Check if the feature to see the token when typing is active or disabled
                    if ($allow_token_show_permissions == true) {
                        echo '<input type="text" name="token" id="token" placeholder="Permission token" required class="loginsignupinput">';
                    } elseif ($allow_token_show_permissions == false) {
                        echo '<input type="password" name="token" id="token" placeholder="Permission token" required class="loginsignupinput">';
                    } echo '
                    <input type="hidden" name="userid" value="'.$userid.'">
                    <button type="submit" name="changepermissionsf" class="loginsignupinput loginsignupinputbutton">Change permissions</button>
                </form>
                </div>
                ';
                    include_once '../inc/footer.php';
                echo '
            </body>
            </html>';
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?permissions=fatalerror");
            exit();
        }
    }
    // Check if the parameter changepermissionsf in $_POST 
    elseif (isset($_POST['changepermissionsf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Token
        $permission_token = $_POST['token'];

        // Check if the feature to change the permissions with a token is active 
        if ($enable_change_permission_with_tokens == true) {
            
            // Check whether the permission change token is present in the permission token array
            if (in_array($permission_token, $enable_permission_tokens)) {
                
                // Prepare MySQL statement
                $sql = "UPDATE accounts SET permissions='admin' WHERE username='".$_SESSION['username']."'";
                // Execute MySQL statement
                $sql_run = mysqli_query($conn, $sql);
                // Redirect user to the settings page
                header("location: /admin/settings.php?permissions=success");
                exit();

            } else {
                // If the token not in the array, then abort the change permissions script
                header("location: /admin/settings.php?permissions=failtoken");
                exit();
            }
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?permissions=fatalerror");
            exit();
        }

    }
    // Check if the parameter changepermissionstouser in $_POST
    elseif (isset($_POST['changepermissionstouser'])) {
        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Username
        $useruid = $_POST['uid'];
        
        // Check if the feature to change the permissions with a token is active 
        if ($enable_change_permission_with_tokens == true) {
            // Displays an interface to change the change permissions to user
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Change permissions / URL shortener</title>
                <link rel="stylesheet" href="/admin/css/main.php">
            </head>
            <body>
                <div class="loginsignupcontainer">
                <h1 class="loginsignuph1">Change permissions to user</h1>
                <div class="spacer"></div>
                <form action="/admin/settings-change.php" method="post" class="signuploginform">
                    <p style="color:red;">If you do this, your account will be converted into a normal user account and will no longer have admin rights. This action cannot be undone by you, but can be done by an administrator unless you have a token to do so!</p>
                    <button type="submit" name="changepermissionstouserf" class="loginsignupinput loginsignupinputbutton">Change permissions to user</button>
                </form>
                </div>
                ';
                    include_once '../inc/footer.php';
                echo '
            </body>
            </html>';
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?permissions=fatalerror");
            exit();
        }
    }
    // Check if the parameter changepermissionstouserf in $_POST 
    elseif (isset($_POST['changepermissionstouserf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];

        // Check if the feature to change the permissions with a token is active 
        if ($enable_change_permission_with_tokens == true) {
            
            // Prepare MySQL statement
            $sql = "UPDATE accounts SET permissions='user' WHERE username='".$_SESSION['username']."'";
            // Execute MySQL statement
            $sql_run = mysqli_query($conn, $sql);
            // Redirect user to the settings page
            header("location: /admin/settings.php?permissions=success");
            exit();

        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?permissions=fatalerror");
            exit();
        }

    }
    // Check if the parameter deleteaccount in $_POST
    elseif (isset($_POST['deleteaccount'])) {
        // Get parameters out of $_POST
        // Users ID
        $userid = $_POST['userid'];
        // Username
        $useruid = $_POST['uid'];
        
        // Check if the feature to self delete own account is active
        if ($enable_user_can_delete_account == true) {
            // Displays an interface to delete the account
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Delete account / URL shortener</title>
                <link rel="stylesheet" href="/admin/css/main.php">
            </head>
            <body>
                <div class="loginsignupcontainer">
                <h1 class="loginsignuph1">Delete this account ['.$_SESSION['username'].']</h1>
                <div class="spacer"></div>
                <p style="color:red;">If you delete this account, the data will be deleted instantly and cannot be recovered.<br> Your created short URL/s are no longer available on this instance when you delete your account.<br> Please write your password down in the password field to verification that you delete your account and not another person.</p>
                <form action="/admin/settings-change.php" method="post" class="signuploginform">
                    <input type="password" name="pwd" id="pwd" placeholder="Current password to verification" required class="loginsignupinput">
                    <button type="submit" name="deleteaccountf" class="loginsignupinput loginsignupinputbutton">Delete this account</button>
                </form>
                </div>
                ';
                    include_once '../inc/footer.php';
                echo '
            </body>
            </html>';
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?delete=fatalerror");
            exit();
        }
    }
    // Check if the parameter deleteaccountf in $_POST 
    elseif (isset($_POST['deleteaccountf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST and $_SESSION
        // Users ID
        $userid = $_SESSION['username'];
        // Password
        $pwd = $_POST['pwd'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $userid);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the feature to self delete own account is active
        if ($enable_user_can_delete_account == true) {
            
            // Verify the password from the variable is the same as in the database
            if (password_verify($pwd, $row['password'])) {

                // Get data from MySQL for the short URL user from the database
                // Prepare MySQL statement
                $sql_url = "SELECT * FROM url";
                // Execute MySQL statement
                $result_url = mysqli_query($conn, $sql_url);
                $queryResult_url = mysqli_num_rows($result_url);
                // Check if the result bigger than 0
                if ($queryResult_url > 0) {
                    // Start while loop and get data from MySQL
                    while ($row_url = mysqli_fetch_assoc($result_url)) {
                        // Check if the username in the entry
                        if ($row_url['url_user'] === "".$row['username']."") {
                            // Delete the URL's
                            // Prepare MySQL statement
                            $sql_url = "DELETE FROM url WHERE url_id=".$row_url['url_id']."";
                            // Execute MySQL statement
                            $run_url = mysqli_query($conn, $sql_url);
                        }
                    }
                }
            
                // Delete the user and logout the user
                // Prepare MySQL statement
                $sql = "DELETE FROM accounts WHERE id=".$row['id']."";
                // Execute MySQL statement
                $run = mysqli_query($conn, $sql);

                // Redirect user to the logout page
                header("location: ".$nourl."logout.php?rd=deleteacc");
                exit();

            } else {
                // Redirect the user with an error message to the settings page
                header("location: /admin/settings.php?delete=passwordincorrect");
                exit();
            }

        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?delete=fatalerror");
            exit();
        }

    }
    // Check if the parameter createnewuseradmin in $_POST
    elseif (isset($_POST['createnewuseradmin'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST and $_SESSION
        // Users ID
        $userid = $_SESSION['username'];
        // Username
        $useruid = $_POST['uid'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $userid);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the user have admin permissions
        if ($row['permissions'] == "admin") {
            // Displays an interface to create an account
            echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Create account / URL shortener</title>
                    <link rel="stylesheet" href="/admin/css/main.php">
                </head>
                <body>
                    <div class="loginsignupcontainer">
                    <h1 class="loginsignuph1">Create account</h1>
                    <div class="spacer"></div>
                    <form action="/admin/settings-change.php" method="post" class="signuploginform">
                        <input type="text" name="newuid" id="newuid" placeholder="Username" required class="loginsignupinput">
                        <input type="password" name="pwd" id="pwd" placeholder="Password" required class="loginsignupinput">
                        <input type="password" name="pwdr" id="pwdr" placeholder="Password repeat" required class="loginsignupinput">
                        <select name="permissions" id="permissions" required class="loginsignupinput">
                            <option value="user">User without administrator rights</option>
                            <option value="admin">User with administrator rights</option>
                        </select>
                        <button type="submit" name="createnewuseradminf" class="loginsignupinput loginsignupinputbutton">Create new account</button>
                    </form>
                    </div>
                    ';
                        include_once '../inc/footer.php';
                    echo '
                </body>
                </html>';
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?error=nopermission");
            exit();
        }
    }
    // Check if the parameter createnewuseradminf in $_POST 
    elseif (isset($_POST['createnewuseradminf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST
        // New username
        $newuid = $_POST['newuid'];
        // New user's password
        $newpwd = $_POST['pwd'];
        $newpwdr = $_POST['pwdr'];
        // Permissions
        $newpermissions = $_POST['permissions'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $_SESSION['username']);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the user have admin permissions
        if ($row['permissions'] == "admin") {

            // Get data from MySQL for user from the database
            $stmt1 = $mysql->prepare("SELECT * FROM accounts WHERE username = :user"); 
            $stmt1->bindParam(":user", $newuid);
            $stmt1->execute();
            $count1 = $stmt1->rowCount();
            // Check if the username isn't taken
            if ($count1 == 0) {
                // Username isn't taken
                // Check if the passwords the same
                if ($newpwd == $newpwdr) {
                    // Create user

                    // Hash password
                    $hashedpwd = password_hash($newpwd, PASSWORD_BCRYPT);

                    // Insert the user data into the database and create the account
                    $stmt2 = $mysql->prepare("INSERT INTO accounts (username, password, permissions) VALUES (:user, :pwd, :permissions)");
                    // Binds username, password, and permissions level
                    $stmt2->bindParam(":user", $newuid);
                    $stmt2->bindParam(":pwd", $hashedpwd);
                    $stmt2->bindParam(":permissions", $newpermissions);
                    // Execute the statement
                    $stmt2->execute();
                    // Redirect user to the settings page
                    header("location: /admin/settings.php?newacc=success");
                    exit();

                } else {
                    // Redirect the user with an error message to the settings page
                    header("location: /admin/settings.php?newacc=errorpwdr");
                    exit();
                }
            } else {
                // Redirect the user with an error message to the settings page
                header("location: /admin/settings.php?newacc=usernametaken");
                exit();
            }

        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?error=nopermission");
            exit();
        }

    }
    // Check if the parameter deleteuseradmin in $_POST
    elseif (isset($_POST['deleteuseradmin'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';
        
        // Get parameters out of $_POST and $_SESSION
        // Users ID
        $userid = $_SESSION['username'];
        // Username
        $useruid = $_POST['uid'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $userid);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the user have admin permissions
        if ($row['permissions'] == "admin") {
            // Displays an interface to delete an account
            echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Delete account / URL shortener</title>
                    <link rel="stylesheet" href="/admin/css/main.php">
                </head>
                <body>
                    <div class="loginsignupcontainer">
                    <h1 class="loginsignuph1">Delete account</h1>
                    <div class="spacer"></div>
                    <p style="color:red;">If you delete this account, the data will be deleted instantly and cannot be recovered.<br> Your created short URL/s are no longer available on this instance when you delete your account.<br> Please write your password down in the password field to verification that you delete your account and not another person.</p>
                    <form action="/admin/settings-change.php" method="post" class="signuploginform">
                        <input type="text" name="delid" id="delid" placeholder="ID from the account" required class="loginsignupinput">
                        <input type="text" name="deluid" id="deluid" placeholder="Username from the account" required class="loginsignupinput">
                        <input type="password" name="adminpwd" id="adminpwd" placeholder="Your admin password" required class="loginsignupinput">
                        <button type="submit" name="deleteuseradminf" class="loginsignupinput loginsignupinputbutton">Delete this account</button>
                    </form>
                    </div>
                    ';
                        include_once '../inc/footer.php';
                    echo '
                </body>
                </html>';
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?error=nopermission");
            exit();
        }
    }
    // Check if the parameter deleteuseradminf in $_POST 
    elseif (isset($_POST['deleteuseradminf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST
        // Account ID
        $delid = $_POST['delid'];
        // Account username
        $deluid = $_POST['deluid'];
        // Admin password
        $adminpwd = $_POST['adminpwd'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $_SESSION['username']);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the user have admin permissions
        if ($row['permissions'] == "admin") {

            // Verify the password from the variable is the same as in the database
            if (password_verify($adminpwd, $row['password'])) {

                // Get data from MySQL for user from the database
                $stmt1 = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
                $stmt1->bindParam(":id", $deluid);
                $stmt1->execute();
                $count = $stmt1->rowCount();
                $row_del = $stmt1->fetch();

                // Check if the ID from the database as the same as from the input
                if ($row_del['id'] == $delid) {
                    // Get data from MySQL for the short URL user from the database
                    // Prepare MySQL statement
                    $sql_url = "SELECT * FROM url";
                    // Execute MySQL statement
                    $result_url = mysqli_query($conn, $sql_url);
                    $queryResult_url = mysqli_num_rows($result_url);
                    // Check if the result bigger than 0
                    if ($queryResult_url > 0) {
                        // Start while loop and get data from MySQL
                        while ($row_url = mysqli_fetch_assoc($result_url)) {
                            // Check if the username in the entry
                            if ($row_url['url_user'] === "".$row_del['username']."") {
                                // Delete the URL's
                                // Prepare MySQL statement
                                $sql_url = "DELETE FROM url WHERE url_id=".$row_url['url_id']."";
                                // Execute MySQL statement
                                $run_url = mysqli_query($conn, $sql_url);
                            }
                        }
                    }

                    // Delete the user account
                    // Prepare MySQL statement
                    $sql = "DELETE FROM accounts WHERE id=".$delid."";
                    // Execute MySQL statement
                    $run = mysqli_query($conn, $sql);

                    // Redirect user to the admin page
                    header("location: /admin/settings.php?delacc=success");
                    exit();
                } else {
                    // Redirect the user with an error message to the settings page
                    header("location: /admin/settings.php?delacc=delidandacciddontmatch");
                    exit();
                }
            } else {
                // Redirect the user with an error message to the settings page
                header("location: /admin/settings.php?delacc=adminpwdincorrect");
                exit();
            }
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?error=nopermission");
            exit();
        }
    }
    // Check if the parameter changepermissionsotu in $_POST
    elseif (isset($_POST['changepermissionsotu'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';
        
        // Get parameters out of $_POST and $_SESSION
        // Users ID
        $userid = $_SESSION['username'];
        // Username
        $useruid = $_POST['uid'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $userid);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the user have admin permissions
        if ($row['permissions'] == "admin") {
            // Displays an interface to change permissions from another user
            echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Change permissions from another user / URL shortener</title>
                    <link rel="stylesheet" href="/admin/css/main.php">
                </head>
                <body>
                    <div class="loginsignupcontainer">
                    <h1 class="loginsignuph1">Change permissions from another user</h1>
                    <div class="spacer"></div>
                    <form action="/admin/settings-change.php" method="post" class="signuploginform">
                        <input type="text" name="changeuserid" id="changeuserid" placeholder="ID from the account" required class="loginsignupinput">
                        <input type="text" name="changeuseruid" id="changeuseruid" placeholder="Username from the account" required class="loginsignupinput">
                        <input type="password" name="adminpwd" id="adminpwd" placeholder="Your admin password" required class="loginsignupinput">
                        <select name="permissions" id="permissions" required class="loginsignupinput">
                            <option value="user">Normal user permissions</option>
                            <option value="admin">User with administrator permissions</option>
                        </select>
                        <button type="submit" name="changepermissionsotuf" class="loginsignupinput loginsignupinputbutton">Change permissions from another user</button>
                    </form>
                    </div>
                    ';
                        include_once '../inc/footer.php';
                    echo '
                </body>
                </html>';
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?error=nopermission");
            exit();
        }
    }
    // Check if the parameter changepermissionsotuf in $_POST 
    elseif (isset($_POST['changepermissionsotuf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';

        // Get parameters out of $_POST
        // Account ID
        $changeuserid = $_POST['changeuserid'];
        // Account username
        $changeuseruid = $_POST['changeuseruid'];
        // Admin password
        $adminpwd = $_POST['adminpwd'];
        // Permissions
        $permissions = $_POST['permissions'];

        // Get data from MySQL for user from the database
        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
        $stmt->bindParam(":id", $_SESSION['username']);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // Check if the user have admin permissions
        if ($row['permissions'] == "admin") {

            // Verify the password from the variable is the same as in the database
            if (password_verify($adminpwd, $row['password'])) {

                // Get data from MySQL for user from the database
                $stmt1 = $mysql->prepare("SELECT * FROM accounts WHERE username = :id");
                $stmt1->bindParam(":id", $changeuseruid);
                $stmt1->execute();
                $count = $stmt1->rowCount();
                $row_update = $stmt1->fetch();

                // Check if the ID from the database as the same as from the input
                if ($row_update['id'] == $changeuserid) {

                    // Prepare MySQL statement
                    $sql = "UPDATE accounts SET permissions='".$permissions."' WHERE id='".$changeuserid."'";
                    // Execute MySQL statement
                    $run = mysqli_query($conn, $sql);

                    // Redirect user to the admin page
                    header("location: /admin/settings.php?updateacc=success");
                    exit();

                } else {
                    // Redirect the user with an error message to the settings page
                    header("location: /admin/settings.php?updateacc=updateidandacciddontmatch");
                    exit();
                }   
            } else {
                // Redirect the user with an error message to the settings page
                header("location: /admin/settings.php?updateacc=adminpwdincorrect");
                exit();
            }
        } else {
            // Redirect the user with an error message to the settings page
            header("location: /admin/settings.php?error=nopermission");
            exit();
        }
    }

}