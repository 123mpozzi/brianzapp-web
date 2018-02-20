<?php

// Define the root path of the projects
// https://stackoverflow.com/a/22052697
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/WebApp/src/');

// #-----------------------------------------------------------#
// # Constants used as keys to get values from POST or SESSION #
// #-----------------------------------------------------------#

// Login Form
define('KEY_USERNAME', 'username');
define('KEY_PASSWORD', 'password');
define('KEY_LOGIN_SUBMIT', 'login_clicked');

// Session
define('KEY_LOGGED_IN', 'logged_user');


// #---------------------#
// # MySQL configuration #
// #---------------------#

// This file contains the database access information.
// This file also establishes a connection to MySQL,
// selects the database, and sets the encoding.

// Set the database access information as constants:


$ini_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/WebApp/config.ini", true);

define('DB_USER', $ini_array['mysql']['user']);
define('DB_PASSWORD', $ini_array['mysql']['password']);
define('DB_HOST', $ini_array['mysql']['host']);
define('DB_NAME', $ini_array['mysql']['dbname']);

// Make the connection:
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL: ' . mysqli_connect_error());

// Set the encoding...
mysqli_set_charset($dbc, 'utf8');
