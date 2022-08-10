<?php
/*
 * URL shortener (https://github.com/marlonbde/URL-shortener)
 * Copyright 2022 Marlon Bleckwehl
 * Licensed under BSD 2-Clause "Simplified" License (https://github.com/marlonbde/URL-shortener/blob/main/license)
 */
// Include general configs
require_once 'inc/config.inc.php';
require_once 'inc/oth.inc.php';
require_once 'inc/dbconn.php';

// Get requested URL from server
$str = $_SERVER['REQUEST_URI'];
// Delete the first symbol '/' from URL
$str1 = substr($str, 1);
// Create the server URL for checks
$server_url = $_SERVER['SERVER_NAME']."/".$str1;

// Get the length of the requestet URL string
$shorturllength = strlen($str1);

// Check if the requested URL is equal to the domain name with nothing URL short link
if ($server_url == $newdomain) {
    // Redirect the user to the defined URL and exit the script
    // Defined in config.inc.php
    header("location: ".$nourl."");
    exit();
}
// Check if the requested URL is not equal to the domain name with nothing URL short link
if ($server_url != $newdomain) {
    // Checks if the Short URL in the database exist
    $check = $conn->query("SELECT url_short FROM url WHERE url_short = '".$str1."'");
    if($check->num_rows == 0) {
        // When the URL doesn't exist then it displays an error message and exit the script
        // Echo that the page was not found on the system (error 404)
        header("HTTP/1.0 404 Not Found");
        // Displays the error message
        echo $error_message;
        // Exit the script
        exit();
    } else {
        // Select all from the Table 'url' and get data
        $sql_url = "SELECT * FROM url";
        $result_url = mysqli_query($conn, $sql_url);
        $queryResult = mysqli_num_rows($result_url);
        // Checks if the result from the database bigger than 0
        if ($queryResult > 0) {
            // Starts a while loop for checking the URL
            while ($row_url = mysqli_fetch_assoc($result_url)) {
                // Get the length of the shorten URL from the database
                $row_url_length = strlen($row_url['url_short']);
                // If the length of the shorten URL and the shorten URL in the database the same then start redirect
                if ($row_url_length == $shorturllength) {
                    // Check if the short URL is the same as in the Database
                    if ($str1 == $row_url['url_short']) {
                        // Checks is the link analytics enabled
                        if ($enable_link_analytics == true) {
                            // When the link analytics is active, the script add one to the count in the database
                            $link_analytics_count = intval($row_url['url_clicks']) + 1;
                            // Prepare the MySQL statement
                            $sql = "UPDATE url SET url_clicks='".$link_analytics_count."' WHERE url_id=".$row_url['url_id']."";
                            // Execute the MySQL statement
                            mysqli_query($conn, $sql);
                        }
                        // Redirect the user, close the MySQL connection and exit the script
                        header("location: ".$row_url['url_long']."");
                        $conn->close();
                        exit();
                    }
                } else {
                    # nothing
                }
            }
        }
    }
}