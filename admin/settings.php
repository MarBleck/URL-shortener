<?php
// Start session
session_start();
// Check if the parameter username in $_SESSION
if (isset($_SESSION['username'])) {
    // Include configs
    include_once '../inc/config.inc.php';
    include_once '../inc/oth.inc.php';
    include_once '../inc/dbconn.php';
    include_once '../inc/pdo.mysql.php';

    // Get data from MySQL for the user from the database
    $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user");
    $stmt->bindParam(":user", $_SESSION['username']);
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count == 1) {
    $row = $stmt->fetch();
        
        // Shows an interface for settings
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Settings / URL shortener</title>
            <link rel="stylesheet" href="/admin/css/main.php?v=1">
        </head>
        <body>
            <div class="page">
                <header class="pageheader">
                    <div class="phd1 flex1">
                        <div class="headerd flex2">
                            <h2>URL shortener</h2>
                        </div>
                        '; include_once '../inc/header.php'; echo '
                    </div>
                </header>
                <main class="pagemain">
                    <div class="spaceh"></div>
                    <div class="pflex">
                        <div class="maind settings">
                            <h1>Settings for '.$row['username'].'</h1>
                            '; 
                            // Displays error that the user has no permission for this operation
                            if (isset($_GET['error'])) {
                                if ($_GET['error'] == "nopermission") {
                                    echo '<h3 style="color:red;">You don\'t have permission do to this action</h3>';
                                }
                            } echo '
                            <div class="spacer"></div>
                            <div class="spacer"></div>
                            <div class="settingscontainer">
                                <div class="setting">
                                    <h3 class="settingsh3">Username:</h3>
                                    <p>Your username is: '.$row['username'].'</p>
                                    <p>When you want to change your username, please click here:</p>
                                    '; 
                                    // Display error when the username don't match with the other input or that the username already in use
                                    if (isset($_GET['username'])) {
                                        if ($_GET['username'] == "errordontmatch") {
                                            echo '<h3 style="color:red;">The new usernames don\'t match</h3>';
                                        }
                                        elseif ($_GET['username'] == "theusernameexist") {
                                            echo '<h3 style="color:red;">The new usernames exist</h3>';
                                        }
                                    } echo '
                                    <form action="settings-change.php" method="post">
                                        <input type="hidden" name="userid" value="'.$row['id'].'">
                                        <input type="hidden" name="uid" value="'.$row['username'].'">
                                        <button type="submit" name="changeuid" class="settingsformbutton">Change username</button>
                                    </form>
                                </div>
                                <div class="setting">
                                    <h3 class="settingsh3">Password:</h3>
                                    <p>When you want to change your password, please click here:</p>
                                    '; 
                                    // Display error when the current password don't match with the password that stored in the database or when the passwords from the inputs don't match
                                    if (isset($_GET['password'])) {
                                        if ($_GET['password'] == "currentpassworddontmatch") {
                                            echo '<h3 style="color:red;">The current password don\'t match</h3>';
                                        }
                                        elseif ($_GET['password'] == "newpassworddontmatch") {
                                            echo '<h3 style="color:red;">The new passwords don\'t match</h3>';
                                        }
                                        // Display a success message when the password was updated successfully
                                        elseif ($_GET['password'] == "success") {
                                            echo '<h3 style="color:green;">The password was updated</h3>';
                                        }
                                    } echo '
                                    <form action="settings-change.php" method="post">
                                        <input type="hidden" name="userid" value="'.$row['id'].'">
                                        <input type="hidden" name="uid" value="'.$row['username'].'">
                                        <button type="submit" name="changepwd" class="settingsformbutton">Change password</button>
                                    </form>
                                </div>
                                <div class="setting">
                                    <h3 class="settingsh3">Permissions:</h3>
                                    <p>Your permission level is: '.$row['permissions'].'</p>
                                    '; 
                                    // Displays a error message that the feature is disabled from the administrator from this server in config.inc.php or shows a message that the opperation doesn't work because the token doesn't exist
                                    if (isset($_GET['permissions'])) {
                                        if ($_GET['permissions'] == "fatalerror") {
                                            echo '<h3 style="color:red;">This feature is deactivated on this instance from the administrator</h3>';
                                        }
                                        elseif ($_GET['permissions'] == "failtoken") {
                                            echo '<h3 style="color:red;">This hasn\'t work, please try again or try it later</h3>';
                                        }
                                    }
                                    // Shows a form with an button when the feature is enabled to change his permission with tokens
                                    if ($enable_change_permission_with_tokens == true) {
                                        echo '<form action="settings-change.php" method="post">
                                        <input type="hidden" name="userid" value="'.$row['id'].'">
                                        <input type="hidden" name="uid" value="'.$row['username'].'">
                                        <button type="submit" name="changepermissions" class="settingsformbutton">Change permissions</button>
                                    </form>';
                                    } 
                                    else {
                                        # Show nothing
                                    }
                                    // Shows a button when the user has admin rights on this server to change his rights to a normal user, but he cannot go back from normal user right when he doesn't have a token or contact to an administrator that have access to the database
                                    if ($row['permissions'] == "admin") {
                                        echo '<form action="settings-change.php" method="post">
                                        <input type="hidden" name="userid" value="'.$row['id'].'">
                                        <input type="hidden" name="uid" value="'.$row['username'].'">
                                        <button type="submit" name="changepermissionstouser" class="settingsformbutton">Change permissions to user</button>
                                    </form>';
                                    }  echo '
                                </div>
                                <div class="setting">
                                ';
                                // Shows a container with a form when the feature “user can delete account“ is active to delete his account self without admin rights, it is recommended in regions that have the GDPR
                                if ($enable_user_can_delete_account = true) {
                                    echo '<h3 class="settingsh3">Delete your account:</h3>
                                    <p>When you want to delete your account with the right to erasure [\'right to be forgotten\'] (GDPR), please click here:</p>
                                    '; 
                                    // Display an error message when the function is disabled on the server or when it doesn't work
                                    if (isset($_GET['delete'])) {
                                        if ($_GET['delete'] == "fatalerror") {
                                            echo '<h3 style="color:red;">This feature is deactivated on this instance from the administrator</h3>';
                                        }
                                        elseif ($_GET['delete'] == "failtoken") {
                                            echo '<h3 style="color:red;">This hasn\'t work, please try again or try it later</h3>';
                                        }
                                    } echo '
                                    <form action="settings-change.php" method="post">
                                        <input type="hidden" name="userid" value="'.$row['id'].'">
                                        <input type="hidden" name="uid" value="'.$row['username'].'">
                                        <button type="submit" name="deleteaccount" class="settingsformbutton">Delete account</button>
                                    </form>';
                                }
                                echo '
                                </div>
                                '; 
                                // Check if the user have admin permissions when yes it shows an extra section for administrators
                                if ($row['permissions'] == "admin") {
                                    echo '<h2 class="settingsw1h2">Administration:</h2>
                                    <div class="setting">
                                        <h3 class="settingsh3">Create user:</h3>
                                        <p>When you want to create a new user, click here:</p>
                                        '; 
                                        // Display errors when the passwords don't the same from the input or that the username is taken
                                        if (isset($_GET['newacc'])) {
                                            if ($_GET['newacc'] == "errorpwdr") {
                                                echo '<h3 style="color:red;">The passwords are not the same</h3>';
                                            }
                                            elseif ($_GET['newacc'] == "usernametaken") {
                                                echo '<h3 style="color:red;">The username is taken</h3>';
                                            }
                                            // Displays a success message that the user was created successfully
                                            elseif ($_GET['newacc'] == "success") {
                                                echo '<h3 style="color:green;">The user was created</h3>';
                                            }
                                        } echo '
                                        <form action="settings-change.php" method="post">
                                            <input type="hidden" name="userid" value="'.$row['id'].'">
                                            <input type="hidden" name="uid" value="'.$row['username'].'">
                                            <button type="submit" name="createnewuseradmin" class="settingsformbutton">Create user</button>
                                        </form>
                                    </div>
                                    <div class="setting">
                                        <h3 class="settingsh3">Delete user:</h3>
                                        <p>When you want to delete a user, click here:</p>
                                        '; 
                                        // Display error messages when the admin password not correct or that the inputted account ID don't match with the ID in the database
                                        if (isset($_GET['delacc'])) {
                                            if ($_GET['delacc'] == "adminpwdincorrect") {
                                                echo '<h3 style="color:red;">The admin passwords don\'t correct</h3>';
                                            }
                                            elseif ($_GET['delacc'] == "delidandacciddontmatch") {
                                                echo '<h3 style="color:red;">The ID from the input and the ID from the database don\'t match</h3>';
                                            }
                                            // Displays a success message when the user was successfully deleted
                                            elseif ($_GET['delacc'] == "success") {
                                                echo '<h3 style="color:green;">The user was deleted</h3>';
                                            }
                                        } echo '
                                        <form action="settings-change.php" method="post">
                                            <input type="hidden" name="userid" value="'.$row['id'].'">
                                            <input type="hidden" name="uid" value="'.$row['username'].'">
                                            <button type="submit" name="deleteuseradmin" class="settingsformbutton">Delete user</button>
                                        </form>
                                    </div>
                                    <div class="setting">
                                        <h3 class="settingsh3">Update permissions from another user:</h3>
                                        <p>When you want to update permissions from another user, click here:</p>
                                        '; 
                                        // Display error messages when the admin password not correct or that the inputted account ID don't match with the ID in the database
                                        if (isset($_GET['updateacc'])) {
                                            if ($_GET['updateacc'] == "adminpwdincorrect") {
                                                echo '<h3 style="color:red;">The admin passwords don\'t correct</h3>';
                                            }
                                            elseif ($_GET['updateacc'] == "updateidandacciddontmatch") {
                                                echo '<h3 style="color:red;">The ID from the input and the ID from the database don\'t match</h3>';
                                            }
                                            // Displays a success message when the user was successfully updated
                                            elseif ($_GET['updateacc'] == "success") {
                                                echo '<h3 style="color:green;">The user was deleted</h3>';
                                            }
                                        } echo '
                                        <form action="settings-change.php" method="post">
                                            <input type="hidden" name="userid" value="'.$row['id'].'">
                                            <input type="hidden" name="uid" value="'.$row['username'].'">
                                            <button type="submit" name="changepermissionsotu" class="settingsformbutton">Change permissions</button>
                                        </form>
                                    </div>
                                    ';
                                } echo '
                            </div>
                        </div>
                    </div>
                </main>
                '; include_once '../inc/footer.php'; echo '
            </div>
        </body>
        </html>';
    }
} else {
    header("location: /admin/login.php");
}