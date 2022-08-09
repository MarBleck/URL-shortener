<?php 
require_once '../inc/config.inc.php';
// Start session
session_start();
// Unset session
session_unset();
// Destroy session
session_destroy();
// Redirect to log in page after the account was deleted
if (isset($_GET['rd'])) {
    if ($_GET['rd'] == "deleteacc") {
        header("location: /admin/login.php?delete=acc");
        exit();
    }
}
// Redirect to log in
header("location: ".$nourl."");
exit();