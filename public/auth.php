<?php

/*
 * #----------#
 * # Auth.PHP #
 * #----------#
 *
 *
 * Questo script verrà incluso in ogni altri script.
 *
 * Contiene:
 * - il codice per gestire l'autenticazione
 * - il tag <head> a cui verrà aggiunto il resto della pagina
 *
 */

include("config.php");
include("utils.php");

// il login è gestito tramite le sessioni
// login inspired by: https://stackoverflow.com/a/20932020
session_start();

// messaggio di alert che apparirà nel caso si verifichino errori
$alert = [];

// Initialize an error array.
$errors = [];

// Se l'utente sta cercando di loggarsi (cliccato sul pulsante login)
if (isset($_POST[KEY_LOGIN_SUBMIT]))
{
    $user = getPostString($dbc, $errors, KEY_USERNAME);
    $pass = getPostString($dbc, $errors, KEY_PASSWORD);
    
    // decifra la password tramite sha2=>256
    $hp = substr(hash('sha256', $pass), 0, 64);
    $q = "SELECT id FROM utente WHERE user=? AND password=?";
    $stmt = executePrep($dbc, $q, "ss", [$user, $hp]);
    
    $stmt_result = $stmt->get_result();
    
    // corrispondenza utente trovata, salvare il valore tramite le sessioni
    if ($stmt_result->num_rows == 1)
    {
        $_SESSION[KEY_LOGGED_IN] = $user;
        
        // redirect on index page
        echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/homepage.php' . '" , "_self");</script>';
    }
    else
    {
        $alert = alertEmbedded("danger", "Errore!", "Combinazione utente e password errata!");
    }
    
    $stmt->close();
}
else
{
    $user_links = [
        'index.php', 'login.php', 'logout.php', 'not_enough_permissions.php', 'reset_password.php'
    ];
    
    // logged in
    if(isset($_SESSION[KEY_LOGGED_IN]))
    {
        $user = $_SESSION[KEY_LOGGED_IN];
        
        $q = "SELECT id FROM utente WHERE user=?";
        $stmt = executePrep($dbc, $q, "s", [$user]);
        
        $stmt_result = $stmt->get_result();
        
        // not found
        if ($stmt_result->num_rows != 1)
        {
            unset($_SESSION[KEY_LOGGED_IN]);
    
            if (!in_array(basename($_SERVER['SCRIPT_NAME']), $user_links))
            {
                echo '<script type="text/javascript"> window.open("' . BASE_URL . 'not_enough_permissions.php' . '" , "_self");</script>';
            }
        }
        else
        {
            // Found: check if password have to be reset
            if(isset($_SESSION[KEY_FORCE_RESET_PASSWORD]) and $_SESSION[KEY_FORCE_RESET_PASSWORD] === true and basename($_SERVER['SCRIPT_NAME']) != 'force_reset_pass.php')
            {
                echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/reset_pass/force_reset_pass.php' . '" , "_self");</script>';
            }
        }
        
        $stmt->close();
    }
    // Not logged in
    else
    {
        if (!in_array(basename($_SERVER['SCRIPT_NAME']), $user_links))
        {
            echo '<script type="text/javascript"> window.open("' . BASE_URL . 'not_enough_permissions.php' . '" , "_self");</script>';
        }
    }
}

?>

<!-- HTML head -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Icone -->
    <?php include __DIR__ . '/../../html_code.html'; ?>
    
    <!-- Titolo della pagina: verrà preso il valore di $page_title (solo se esiste) -->
    <title><?php if(isset($page_title) && $page_title != null && !empty($page_title)) echo $page_title . ' - '; ?>BrianzApp</title>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
    
    <!-- Icon Fonts: Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    
    <!-- BootStrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    
    <!-- BootStrap Color Picker -->
    <link href="<?php echo BASE_URL; ?>css/bootstrap-colorselector.min.css" rel="stylesheet">
    <script src="<?php echo BASE_URL; ?>js/bootstrap-colorselector.min.js"></script>
    
    <!-- Custom JS-->
    <script src="<?php echo BASE_URL; ?>js/main.js"></script>
</head>
