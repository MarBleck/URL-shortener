<?php
/*
 * URL shortener (https://github.com/marlonbde/URL-shortener)
 * Copyright 2022 Marlon Bleckwehl
 * Licensed under BSD 2-Clause "Simplified" License (https://github.com/marlonbde/URL-shortener/blob/main/license)
 */

// Create tables and create first admin user
$newusername = "New_username";
$newuserpassword = "New_password";

include_once 'inc/config.inc.php';
include_once 'inc/oth.inc.php';
include_once 'inc/dbconn.php';
include_once 'inc/pdo.mysql.php';

// Create table accounts
$sql = "CREATE TABLE accounts (
    id int(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    permissions TEXT NOT NULL
);";
$hashedpwd = password_hash($newuserpassword, PASSWORD_BCRYPT);
$sql1 = "INSERT INTO accounts (username, password, permissions) VALUES ('$newusername', $hashedpwd, 'admin');";
$sql2 = "CREATE TABLE url (
    url_id int(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    url_short TEXT NOT NULL,
    url_long MEDIUMTEXT NOT NULL,
    url_clicks int(255) NOT NULL,
    url_user TEXT NOT NULL,
    url_date TEXT NOT NULL
)";