<?php

include("config.php");
include("utils.php");

session_start();

// login inspired by: https://stackoverflow.com/a/20932020

$alert = '';

// Se l'utente sta cercando di loggarsi (cliccato sul pulsante login)
if (isset($_POST[KEY_LOGIN_SUBMIT]))
{
    $user = $_POST[KEY_USERNAME];
    $pass = $_POST[KEY_PASSWORD];
    
    $hp = substr(hash('sha256', $pass), 0, 64);
    $q = "SELECT id FROM utenti WHERE user=? AND password=?";
    $stmt = executePrep($dbc, $q, "ss", [$user, $hp]);
    
    $stmt_result = $stmt->get_result();
    
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
    // logged in
    if(isset($_SESSION[KEY_LOGGED_IN]))
    {
        $user = $_SESSION[KEY_LOGGED_IN];
        
        $q = "SELECT id FROM utenti WHERE user=?";
        $stmt = executePrep($dbc, $q, "s", [$user]);
        
        $stmt_result = $stmt->get_result();
        
        // not admin or not found
        if ($stmt_result->num_rows != 1)
        {
            $user_links = [
                'index.php', 'login.php', 'logout.php', 'not_enough_permissions.php', 'register_users.php'
            ];
    
            if (!in_array(basename($_SERVER['SCRIPT_NAME']), $user_links))
            {
                echo '<script type="text/javascript"> window.open("' . BASE_URL . 'not_enough_permissions.php' . '" , "_self");</script>';
            }
        }
        
        $stmt->close();
    }
    // Not logged in
    else
    {
        $user_links = [
            'index.php', 'login.php', 'logout.php', 'not_enough_permissions.php', 'register_users.php'
        ];
        
        if (!in_array(basename($_SERVER['SCRIPT_NAME']), $user_links))
        {
            echo '<script type="text/javascript"> window.open("' . BASE_URL . 'not_enough_permissions.php' . '" , "_self");</script>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Proci</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
</head>
