<?php

/*
 * #------------#
 * # Config.PHP #
 * #------------#
 */

// Imposta l'ora corretta
date_default_timezone_set('Europe/Rome');


// Define the root path of the projects
// https://stackoverflow.com/a/22052697
define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/WebApp/public/');

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


// Reset Password
define('KEY_SUBMIT_RESET_PASSWORD', 'btn_reset_password');
define('KEY_RESET_PASSWORD_EMAIL', 'input_reset_password');
define('KEY_FORCE_RESET_PASSWORD', 'forcereset_pass');
// force_reset_pass.php
define('KEY_RESETPASS_PASS', 'resetform_pass');
define('KEY_RESETPASS_PASSCONFIRM', 'resetform_passconf');
define('KEY_RESETPASS_SUBMIT', 'resetform_submit');
// Used in login.php
define('KEY_LOGRESET_USERNAME', 'lou');
define('KEY_LOGRESET_TOKEN', 'lot');
define('KEY_LOGRESET_LINK', 'logreset_link');


// Homepage GET JSON
define('KEY_JSON_HOMEPAGE', 'json_homepage');
// Filtri della homepage
define('KEY_FILTER_TITOLO', 'filter_title');
define('KEY_FILTER_PROVENIENZA', 'filter_provenienza');
define('KEY_FILTER_STELLE', 'filter_stelle');
define('KEY_FILTER_START_DATE', 'filter_startdate');
define('KEY_FILTER_END_DATE', 'filter_enddate');
define('KEY_FILTER_COMUNI', 'filter_comuni');
// Sorting
define('KEY_SORT_TITOLO', 'sort_titolo');
define('KEY_SORT_STELLE', 'sort_stelle');
define('KEY_SORT_DATA', 'sort_data');
// Previous GET data (so you can submit more than one form without losing data from the URL)
define('KEY_PREVIOUS_GET', 'previous_get');


// Nuova Notifica
define('KEY_NEW_PDF', 'new_pdf');
define('KEY_NEW_TITOLO', 'new_titolo');
define('KEY_NEW_DESCRIZIONE', 'new_descrizione');
define('KEY_NEW_STELLE', 'new_stelle');
define('KEY_NEW_PROVENIENZA', 'new_provenienza');
define('KEY_NEW_SUBMIT', 'btn_send_notification');
define('KEY_NEW_CURL_ERROR', 'new_notification_curl_error');


// #---------------------#
// # MySQL configuration #
// #---------------------#


/*
 * Fa il parsing del file di configurazione config.ini.php
 */
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/WebApp/private/config.ini.php", true);

// Esegue la connessione al database:
// se la connessione fallisce, lo script termina l'esecuzione
$dbc = @mysqli_connect($config['mysql']['host'], $config['mysql']['user'], $config['mysql']['password'], $config['mysql']['dbname']) OR die('Impossibile connettersi al database MySQL: ' . mysqli_connect_error());

// Imposta l'encoding...
mysqli_set_charset($dbc, 'utf8');

