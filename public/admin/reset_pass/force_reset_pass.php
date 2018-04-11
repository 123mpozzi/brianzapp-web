<?php

$page_title = "Insert New Password";

include("../../auth.php");
include_once "mail_bodies.php";
include_once "sendmail.php";

//TODO: errore: password diverse, togliere debug dopo reset_pass invio email -> mettere il debug in un file log?

// Se non c'è bisogno di cambiare password, torna alla homepage
if (!isset($_SESSION[KEY_FORCE_RESET_PASSWORD]))
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/homepage.php' . '" , "_self");</script>';
}

// On Submit
if (isset($_POST[KEY_RESETPASS_SUBMIT]))
{
    // Verify that the password and the password confirmations are equal
    $errors = []; // Initialize an error array.
    
    $pass = getPostString($dbc, $errors, KEY_RESETPASS_PASS);
    $passconf = getPostString($dbc, $errors, KEY_RESETPASS_PASSCONFIRM);
    
    if (!empty($_POST[KEY_RESETPASS_PASS]))
    {
        if ($_POST[KEY_RESETPASS_PASSCONFIRM] != $_POST[KEY_RESETPASS_PASSCONFIRM])
        {
            $errors[] = 'Your new password did not match the confirmed password.';
        }
        else
        {
            $new_pass = mysqli_real_escape_string($dbc, trim($_POST[KEY_RESETPASS_PASS]));
        }
    }
    else
    {
        $errors[] = 'You forgot to enter your new password.';
    }
    
    if (empty($errors))
    {
        // update password and reset token to null
        $user = $_SESSION[KEY_LOGGED_IN];
        $qu = "UPDATE utente SET password=SHA2(?, 256), token=NULL WHERE user=?;";
        $stmt = executePrep($dbc, $qu, "ss", [$new_pass, $user]);
    
        if (mysqli_affected_rows($dbc) == 1)
        { // If it ran OK.
            alert("success", "Fatto!", "La password è stata aggiornata.");
            $_SESSION[KEY_FORCE_RESET_PASSWORD] = false;
            unset($_SESSION[KEY_FORCE_RESET_PASSWORD]);
        
            // invia email per avvisare del reset password
            sendMail($config, 'ProCi - Password Cambiata', getBroadcastMailBody());
        }
        else
        { // If it did not run OK.
            alert("warning", "Errore di Sistema!", "Non è stato possibile cambiare la password per un errore di sistema, contattare i tecnici. Ci scusiamo per l'inconveniente.");
            //logError( mysqli_error($dbc), "The Query did not run OK.", 'Query' . interpolateQuery($qu, [$new_pass, $user]));
            $errors[] = [mysqli_error($dbc), "The Query did not run OK.", 'Query' . interpolateQuery($qu, [$new_pass, $user])];
            reportErrors($errors);
            
            //TODO: cosa fare in questo caso?
        }
    
        $stmt -> close();
    }
    else
    {
        reportErrors($errors);
    }
    
    mysqli_close($dbc);
}

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="force-reset-pass-form" class="custom-card container-fluid Absolute-Center is-Responsive "
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
    <h1>RESET PASSWORD</h1>
    <form action="force_reset_pass.php" method="post">
        <!-- Password -->
        <div class="form-element">
            <span>Nuova Password </span>
            <input type="password" class="form-control" size="20" placeholder="Nuova Password" required name="<?php echo KEY_RESETPASS_PASS?>">
        </div>
        <!-- Conferma Password -->
        <div class="form-element">
            <span>Conferma Password </span>
            <input type="password" class="form-control" size="20" placeholder="Conferma Password" required name="<?php echo KEY_RESETPASS_PASSCONFIRM?>">
        </div>
        <!-- Pulsante Conferma Reset -->
        <div>
            <input type="submit" class="btn btn-danger btn-full-large" value="Conferma" name="<?php echo KEY_RESETPASS_SUBMIT?>">
        </div>
    </form>
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
