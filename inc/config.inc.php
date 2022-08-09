<?php
// MySQL connection
$mysql_server = "Your database server";
$mysql_username = "Your database username";
$mysql_password = "Your database password";
$mysql_dbname = "Your database name";

// Your URL shortener URL
$domain = "url.example.com";

// The URL to redirect when no short URL is given
$nourl = "https://".$_SERVER['SERVER_NAME']."/admin/";

// The admin email for errors
$admin_email = "Your admin email";

// Replace the @ for security reasons
$replace_at_str = str_replace("@", "[at]", $admin_email);

// Error message for errors with the URL shortener
$error_message = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>This URL doesn\'t exist</title><style>* {font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";}</style></head><body><p>This short URL was not found in the API, please contact the person who was sending you this link. <br> Are you the person who create this link? When yes, please contact the administrator! <b><i>'.$replace_at_str.'</i></b><p></body></html>';

// Allow registration on this URL shortener
// To create the first account, this has to been enabled 'true'
// Change it to 'false' to disable registration on this instance
$allow_registration = true;

// Allow registration with a token
// The user have to know the token to register on this URL shortener instance
$allow_registration_with_token = true;
// Change the token/s in operation
$registrations_token = array("TestToken", "TestToken2");
// Show token when the user type it
$allow_token_show = false;

// Allow login on this URL shortener
// Change it to 'false' to disable login on this instance
$allow_login = true;

// Enable link click analytics
// Set it to 'true' to enable lick click analytics
$enable_link_analytics = true;

// Enable to change permission in database with tokens
// Set it to 'true' to enable it
$enable_change_permission_with_tokens = true;
// Show token when the user type it on the change permissions site
$allow_token_show_permissions = false;
// Change the token/s in operation
$enable_permission_tokens = array("TestPermission", "TestPermission2");

// Date format for link creation date
$date_format = date('jS \of F Y h:i:s A');

// User can delete account 
// When this instance is public in one country with the GDPR (General Data Protection Regulation) then you must enable this
$enable_user_can_delete_account = true;

// Shows a GitHub link to my repository from this URL shortener
// When you want to disable this feature, set it to false
$show_github_link = true;