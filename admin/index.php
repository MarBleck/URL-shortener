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

        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>URL shortener interface</title>
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
                        <div class="maind">
                            <h1>Welcome '.$row['username'].'</h1>
                            <div class="spacer"></div>
                            <div class="createurl">
                                <form action="edit.php" method="post">
                                    <div class="formd">
                                        <div class="formdf longurld">
                                            <label for="longurl">Long URL:</label>
                                            <input type="url" name="longurl" id="longurl" class="forminput linput" placeholder="https://" required '; if (isset($_GET['url'])) {echo 'value="'.$_GET['url'].'"';}echo '>
                                        </div>
                                        <div class="formdf shorturld">
                                            <label for="shorturl">Short URL:</label>
                                            <input type="text" name="shorturl" id="shorturl" class="forminput sinput" required>
                                        </div>
                                    </div>
                                    <button type="submit" name="createurl" class="submitform">Short!</button>
                                </form>
                            </div>
                            <div class="spacer"></div>
                            <div class="yoururls">
                                <h1>Your shorten URL\'s</h1>
                                ';
                                // Check if the $_GET parameter info exist 
                                if (isset($_GET['info'])) {
                                    // Displays the user the output that the URL/s was updated
                                    if ($_GET['info'] == "updatelink") {
                                        echo '<h2 id="updateinfo" style="color:green;">The URL was updated</h2><script>setTimeout(() => {const elem = document.getElementById("updateinfo");elem.parentNode.removeChild(elem);}, 4000);</script>';
                                    }
                                    // Displays the user the output that the URL was deleted
                                    elseif ($_GET['info'] == "deleteurl") {
                                        echo '<h2 id="updateinfo" style="color:green;">The URL was deleted</h2><script>setTimeout(() => {const elem = document.getElementById("updateinfo");elem.parentNode.removeChild(elem);}, 4000);</script>';
                                    }
                                }
                                // Check if the $_GET parameter error exist 
                                if (isset($_GET['error'])) {
                                    // Displays the user the output that the new short URL is existing
                                    if ($_GET['error'] == "theshorturlexist") {
                                        echo '<h2 id="updateinfo" style="color:red;">The new short URL exist in the Database</h2><script>setTimeout(() => {const elem = document.getElementById("updateinfo");elem.parentNode.removeChild(elem);}, 8000);</script>';
                                    }
                                    // Displays the user the output that he has no permission to delete this URL
                                    elseif ($_GET['error'] == "youhavenopermissiontodeletethisurl") {
                                        echo '<h2 id="updateinfo" style="color:red;">You have no permission to delete this URL</h2><script>setTimeout(() => {const elem = document.getElementById("updateinfo");elem.parentNode.removeChild(elem);}, 8000);</script>';
                                    }
                                    // Displays the user the output that he hasn't sent data
                                    elseif ($_GET['error'] == "nodata") {
                                        echo '<h2 id="updateinfo" style="color:red;">You don\'t have sent data to edit</h2><script>setTimeout(() => {const elem = document.getElementById("updateinfo");elem.parentNode.removeChild(elem);}, 8000);</script>';
                                    }
                                } echo '
                                <div class="spacer"></div>
                                <div class="table">
                                    <div class="tablerow tdemo">
                                        <div class="tdrow shorturl">Short URL</div>
                                        <div class="tdrow longurl">Long URL</div>
                                        '; if ($enable_link_analytics == true) {
                                            echo '<div class="tdrow clicks">Clicks</div>';
                                        }
                                        echo '
                                        <div class="tdrow date">Date</div>
                                    </div>
                                    ';
                                    $sql_url = "SELECT * FROM url";
                                    $result_url = mysqli_query($conn, $sql_url);
                                    $queryResult_url = mysqli_num_rows($result_url);
            
                                    if ($queryResult_url > 0) {
                                        while ($row_url = mysqli_fetch_assoc($result_url)) {
                                            if ($row_url['url_user'] === $_SESSION['username']) {
                                                echo '<div class="tablerowgen">
                                                    <div class="tablerowgeni">
                                                        <div class="tdrow shorturl"><a href="https://'.$admin_link_url.$row_url['url_short'].'" target="_blank" rel="noopener noreferrer" class="longurla1">'.$row_url['url_short'].'</a></div>
                                                        <div class="tdrow longurl"><a href="'.$row_url['url_long'].'" target="_blank" rel="noopener noreferrer" class="longurla">'.$row_url['url_long'].'</a></div>
                                                        '; if ($enable_link_analytics == true) {
                                                            echo '<div class="tdrow clicks">'.$row_url['url_clicks'].'</div>';
                                                        }
                                                        echo '
                                                        <div class="tdrow date">'.$row_url['url_date'].'</div>
                                                    </div>
                                                    <div class="tableoptions">
                                                        <p>Edit entry: </p>
                                                        <div class="changeurl"><form action="/admin/edit.php" method="post"><input type="hidden" name="id" value="'.$row_url['url_id'].'"><input type="hidden" name="surl" value="'.$row_url['url_short'].'"><input type="hidden" name="lurl" value="'.$row_url['url_long'].'"><button type="submit" title="Change link" name="changeurl" class="changeurlb"><i class="bi bi-pencil-square"></i></button><input type="hidden" name="urlcheck" value="'.$row_url['url_short'].'"></form></div>
                                                        <div class="deleteurl"><form action="/admin/edit.php" method="post"><input type="hidden" name="id" value="'.$row_url['url_id'].'"><button type="submit" title="Delete link" name="deleteurl" class="deleteurlb"><i class="bi bi-trash"></i></button></form></div>
                                                    </div>
                                            </div>';
                                            }
                                        }
                                    } else {
                                        
                                    } echo '
                                </div>
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