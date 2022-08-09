<?php 
// Start session
session_start();
// Check if the parameter username in $_SESSION
if (isset($_SESSION['username'])) {
    // Include configs
    include_once '../inc/config.inc.php';
    include_once '../inc/oth.inc.php';

    // Check if the parameter createurl in $_POST
    if (isset($_POST['createurl'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';
        // Get parameters out of $_POST
        // Short URL
        $surl = $_POST['shorturl'];
        // Long URL
        $lurl = $_POST['longurl'];
        // Get parameters out of $_SESSION
        $uid = $_SESSION['username'];

        // Starts a query to look up that the short URL exist
        $check = $conn->query("SELECT * FROM url WHERE url_short = '".$surl."'");

        // If it doesn't exist, starts the change of the URL
        if($check->num_rows == 0) {
            
            // Prepare MySQL statement
            $sql = "INSERT INTO url (url_short, url_long, url_clicks, url_user, url_date) VALUES ('$surl', '$lurl', '0', '$uid', '$date_format')";
            // Execute MySQL statement
            $run = mysqli_query($conn, $sql);
            // Redirect user to the admin page
            header("location: ".$nourl."?info=createlink");
            exit();

        } else {
            // Redirect the user with an error message to the index page
            header("location: /admin/index.php?error=theshorturlexist&url=".$lurl."");
            exit();
        }

    }
    // Check if the parameter changeurl in $_POST
    elseif (isset($_POST['changeurl'])) {
        // Get parameters out of $_POST
        // URL ID
        $id = $_POST['id'];
        // Short URL
        $surl = $_POST['surl'];
        // Long URL
        $lurl = $_POST['lurl'];
        // Get parameters out of $_SESSION
        $uid = $_SESSION['username'];
    
        // Displays an interface to change the links
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Change URL</title>
            <link rel="stylesheet" href="/admin/css/main.php?v=1">
        </head>
        <body>
            <div class="page">
                <header class="pageheader">
                    <div class="phd1 flex1">
                        <div class="headerd flex2">
                            <h2>URL shortener / Change URL</h2>
                        </div>
                        <div class="headerd flex3">
                            <nav class="mainnav">
                                <ul class="navul">
                                    <li class="navli"><a href="/admin/logout.php" class="nava">Logout</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </header>
                <main class="pagemain">
                    <div class="spaceh"></div>
                    <div class="pflex">
                        <div class="maind">
                            <h1>Welcome '.$uid.'</h1>
                            <div class="spacer"></div>
                            <div class="createurl">
                                <form action="edit.php" method="post">
                                    <div class="formd">
                                        <div class="formdf longurld">
                                            <label for="longurl">Long URL:</label>
                                            <input type="url" name="longurl" id="longurl" class="forminput linput" placeholder="https://" required value="'.$lurl.'">
                                        </div>
                                        <div class="formdf shorturld">
                                            <label for="shorturl">Short URL:</label>
                                            <input type="text" name="shorturl" id="shorturl" class="forminput sinput" required value="'.$surl.'">
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="'.$id.'">
                                    <button type="submit" name="changeurlf" class="submitform">Edit!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
                '; include_once '../inc/footer.php'; echo '
            </div>
        </body>
        </html>';
    }
    // Check if the parameter changeurlf in $_POST
    elseif (isset($_POST['changeurlf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';
        // Get parameters out of $_POST
        // URL ID
        $id = $_POST['id'];
        // Short URL
        $surl = $_POST['shorturl'];
        // Long URL
        $lurl = $_POST['longurl'];
        // Get parameters out of $_SESSION
        $uid = $_SESSION['username'];

        // Starts a query to look up that the short URL exist
        $check = $conn->query("SELECT * FROM url WHERE url_short = '".$surl."'");
        // Get data from MySQL for the short URL from the database
        $stmt = $mysql->prepare("SELECT * FROM url WHERE url_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        // If it doesn't exist, starts the change of the URL
        if($check->num_rows == 0) {

            // Checks that the long URL hasn't changed
            if ($lurl == $row['url_long']) {

                // Prepare MySQL statement
                $sql = "UPDATE url SET url_short='".$surl."' WHERE url_id='".$id."'";
                // Execute MySQL statement
                $run = mysqli_query($conn, $sql);
                // Redirect user to the admin page
                header("location: ".$nourl."?info=updatelink");
                exit();
                
            } 
            // Checks that the long URL have changed
            elseif ($lurl != $row['url_long']) {
                
                // Prepare MySQL statement
                $sql = "UPDATE url SET url_short='".$surl."' WHERE url_id='".$id."'";
                $sql1 = "UPDATE url SET url_long='".$lurl."' WHERE url_id='".$id."'";
                // Execute MySQL statement
                $run = mysqli_query($conn, $sql);
                $run1 = mysqli_query($conn, $sql1);
                // Redirect user to the admin page
                header("location: ".$nourl."?info=updatelink");
                exit();

            }

        } elseif ($row['url_short'] == $surl) {

            // Prepare MySQL statement
            $sql = "UPDATE url SET url_long='".$lurl."' WHERE url_id='".$id."'";
            // Execute MySQL statement
            $run = mysqli_query($conn, $sql);
            // Redirect user to the admin page
            header("location: ".$nourl."?info=updatelink");
            exit();

        } else {
            // Redirect the user with an error message to the index page
            header("location: /admin/index.php?error=theshorturlexist");
            exit();

        }

    }
    // Check if the parameter deleteurl in $_POST
    elseif (isset($_POST['deleteurl'])) {
        
        // Get parameters out of $_POST
        // URL ID
        $id = $_POST['id'];
        // Get parameters out of $_SESSION
        $uid = $_SESSION['username'];

        // Displays an interface to delete the URL
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Delete URL</title>
            <link rel="stylesheet" href="/admin/css/main.php?v=1">
        </head>
        <body>
            <div class="page">
                <header class="pageheader">
                    <div class="phd1 flex1">
                        <div class="headerd flex2">
                            <h2>URL shortener / Delete URL</h2>
                        </div>
                        <div class="headerd flex3">
                            <nav class="mainnav">
                                <ul class="navul">
                                    <li class="navli"><a href="/admin/logout.php" class="nava">Logout</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </header>
                <main class="pagemain">
                    <div class="spaceh"></div>
                    <div class="pflex">
                        <div class="maind">
                            <h1>Welcome '.$uid.'</h1>
                            <div class="spacer"></div>
                            <div class="createurl">
                                <form action="edit.php" method="post">
                                    <div class="formd">
                                        <h2>Do you really delete this short URL and all the data?</h2>
                                    </div>
                                    <input type="hidden" name="id" value="'.$id.'">
                                    <button type="submit" name="deleteurlf" class="submitform">Delete!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
                '; include_once '../inc/footer.php'; echo '
            </div>
        </body>
        </html>';

    }
    // Check if the parameter deleteurlf in $_POST
    elseif (isset($_POST['deleteurlf'])) {
        // Include DB connections
        include_once '../inc/dbconn.php';
        include_once '../inc/pdo.mysql.php';
        // Get parameters out of $_POST
        // URL id
        $id = $_POST['id'];
        // Get parameters out of $_SESSION
        $uid = $_SESSION['username'];

        //Get data from MySQL for the short url from the database
        $stmt = $mysql->prepare("SELECT * FROM url WHERE url_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $count = $stmt->rowCount();
        $row = $stmt->fetch();

        if ($uid == $row['url_user']) {

            // Prepare MySQL statement
            $sql = "DELETE FROM url WHERE url_id=".$id."";
            // Execute MySQL statement
            $run = mysqli_query($conn, $sql);
            // Redirect user to the admin page
            header("location: ".$nourl."?info=deleteurl");
            exit();

        } else {
            // Redirect the user with an error message to the index page
            header("location: ".$nourl."?error=youhavenopermissiontodeletethisurl");
            exit();
        }

    }
    
    else {
        // Redirect the user with an error message to the index page
        header("location: /admin/index.php?error=nodata");
    }
} else {
    header("location: /admin/login.php");
}