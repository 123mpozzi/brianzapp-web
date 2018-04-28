<?php

$page_title = "Login";

include("../auth.php");

// Se l'utente è già loggato, fa il redirect sulla homepage
if (isset($_SESSION[KEY_LOGGED_IN]))
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/homepage.php' . '" , "_self");</script>';
}

// Only for Password Reset - with token and email in URL
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET[KEY_LOGRESET_USERNAME]) && isset($_GET[KEY_LOGRESET_TOKEN]))
{
    $user = $_GET[KEY_LOGRESET_USERNAME];
    $token = $_GET[KEY_LOGRESET_TOKEN];
    
    $q = "SELECT id FROM utente WHERE user=? AND token=?";
    $stmt = executePrep($dbc, $q, "ss", [$user, $token]);
    
    $stmt_result = $stmt->get_result();
    
    // corrispondenza utente trovata, salvare il valore tramite le sessioni
    if ($stmt_result->num_rows == 1)
    {
        $_SESSION[KEY_LOGGED_IN] = $user;
        // forzare il reset password
        $_SESSION[KEY_FORCE_RESET_PASSWORD] = true;
        
        // redirect on force reset password page
        echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/reset_pass/force_reset_pass.php' . '" , "_self");</script>';
    }
    else
    {
        $alert = alertEmbedded("danger", "Errore!", "Combinazione utente e token errata, se i problemi persistono, contattare i tecnici.");
        
        array_push($errors, mysqli_error($dbc), 'Query' . interpolateQuery($q, [$user, $token]));
        reportErrors($alert, $errors, false);
    }
    
    $stmt->close();
}

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="login-form" class="custom-card container-fluid Absolute-Center is-Responsive"
<?php

if($alert == null || empty($alert))
{
    echo 'style="max-height: 21rem; "';
}
// se c'è qualche messaggio di errore, fargli spazio
else
{
    echo 'style="min-height: 25rem; "';
}

?>>
    <h1>LOGIN</h1>
    <form action="login.php" method="post">
        <!-- Campo Username -->
        <div>
            <input type="email" class="form-control" size="20" placeholder="Username" required name="<?php echo KEY_USERNAME?>">
        </div>
        <br>
        <!-- Campo Password -->
        <div>
            <input type="password" class="form-control" size="20" placeholder="Password" required name="<?php echo KEY_PASSWORD?>">
        </div>
        <br>
        <!-- Pulsante Login -->
        <div>
            <input type="submit" class="btn btn-danger btn-full-large" value="Login" name="<?php echo KEY_LOGIN_SUBMIT?>">
        </div>
    </form>
    <!-- Link Reset-Password -->
    <p class="reset-pass"><a href="reset_password.php">Recupera password</a></p>
    <?php

    // se c'è qualche messaggio di errore, fargli spazio
    if($alert != null && !empty($alert))
    {
        echo '<div class="custom-alert-embedded">';
        echo $alert;
        echo '</div>';
    }
    
    ?>
</div>
</body>
</html>
