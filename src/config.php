<?php

// Define the root path of the projects
// https://stackoverflow.com/a/22052697
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/WebApp/src/');

// #-------------------------------------------#
// # Constants used as keys to get POST values #
// #-------------------------------------------#

// General

define('KEY_ID', 'id');
define('KEY_TABLE', 'table');
define('KEY_DELETE_CONFIRM', 'delete_confirm');


// #---------------------#
// # MySQL configuration #
// #---------------------#

// This file contains the database access information.
// This file also establishes a connection to MySQL,
// selects the database, and sets the encoding.

// Set the database access information as constants:
define('DB_USER', 'root');
define('DB_PASSWORD', 'admin');
define('DB_HOST', 'localhost');
define('DB_NAME', 'dbproci');

// Make the connection:
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL: ' . mysqli_connect_error());

// Set the encoding...
mysqli_set_charset($dbc, 'utf8');