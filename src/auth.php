<?php

include("config.php");
include("utils.php");

// login inspired by: https://stackoverflow.com/a/20932020

$special_alert = '';

// Se l'utente sta cercando di loggarsi (cliccato sul pulsante login)
if (isset($_POST['login_clicked']))
{
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $hp = substr(hash('sha256', $pass), 0, 64);
    $q = "SELECT id FROM utenti WHERE user=? AND password=?";
    $stmt = executePrep($dbc, $q, "ss", [$user, $hp]);
    
    $stmt_result = $stmt->get_result();
    
    if ($stmt_result->num_rows == 1)
    {
        $_SESSION['use'] = $user;
        // redirect on index page
        echo '<script type="text/javascript"> window.open("' . BASE_URL . 'funziona.php' . '" , "_self");</script>';
    }
    else
    {
        $special_alert = '
        <div class="alert alert-danger">
  <strong>Danger!</strong> Invalid Username or Password.
</div>';
    }
    
    $stmt->close();
}
else
{
    // logged in
    if(isset($_SESSION['use']))
    {
        $user = $_SESSION['use'];
        
        $q = "SELECT id FROM utenti WHERE user=?";
        $stmt = executePrep($dbc, $q, "s", [$user]);
        
        $stmt_result = $stmt->get_result();
        
        // admin
        if ($stmt_result->num_rows == 1)
        {
        
        }
        // query result empty, user not found
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

echo $special_alert;

?>