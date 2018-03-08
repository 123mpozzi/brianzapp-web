<?php

/*
 * #------------#
 * # Config.PHP #
 * #------------#
 */


// Define the root path of the projects
// https://stackoverflow.com/a/22052697
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/WebApp/src/');

/*
 * #-----------#
 * # Constants #
 * #-----------#
 *
 *
 * Costanti usate come chiavi per ottenere i valori da POST, GET, SESSION
 *
 * In questo modo nel caso bisogna cambiare la chiave, basterà modificarla qui
 *
 *
 *
 * esempio pratico:
 *
 * invece che fare:
 *
 * <esempio1.php>
 * $user = $_POST['user'];
 * ...
 *
 * <esempio2.php>
 * $user = $_POST['user'];
 * ...
 *
 * e quindi dovere modificare il codice in entrambi i file, nel caso si voglia
 * scegliere una chiave differente da 'user'; basterà modificarla soltanto qui
 * e il valore si aggiornerà in automatico in ogni file.
 *
 */


// Session
define('KEY_LOGGED_IN', 'logged_user');

// Login Form
define('KEY_USERNAME', 'username');
define('KEY_PASSWORD', 'password');
// pulsante di login
define('KEY_LOGIN_SUBMIT', 'login_clicked');

// Filtri della homepage
define('KEY_FILTER_TITOLO', 'filter_title');
define('KEY_FILTER_PROVENIENZA', 'filter_provenienza');
define('KEY_FILTER_STELLE', 'filter_stelle');
define('KEY_FILTER_START_DATE', 'filter_startdate');
define('KEY_FILTER_END_DATE', 'filter_enddate');
// Sorting
define('KEY_SORT_TITOLO', 'sort_titolo');
define('KEY_SORT_STELLE', 'sort_stelle');
define('KEY_SORT_DATA', 'sort_data');


// #---------------------#
// # MySQL configuration #
// #---------------------#


/*
 * Fa il parsing del file config.ini che contiene i valori di accesso al database
 * in questo modo sarà più facile modificare tali valori: basterà modificare
 * il file .ini
 */
$ini_array = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/WebApp/config.ini", true);

// Costanti che definiscono i valori di accesso al database
// utente mysql
define('DB_USER', $ini_array['mysql']['user']);
// password dell'utente mysql
define('DB_PASSWORD', $ini_array['mysql']['password']);
// server host (esempio: per le prove sarà localhost)
define('DB_HOST', $ini_array['mysql']['host']);
// nome del database
define('DB_NAME', $ini_array['mysql']['dbname']);

// Esegue la connessione al database:
// se la connessione fallisce, lo script termina l'esecuzione
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL: ' . mysqli_connect_error());

// Imposta l'encoding...
mysqli_set_charset($dbc, 'utf8');
