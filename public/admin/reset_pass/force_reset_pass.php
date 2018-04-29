<?php

$page_title = "Insert New Password";

include("../../auth.php");
include_once "mail_bodies.php";
include_once "sendmail.php";

// Se non c'è bisogno di cambiare password, torna alla homepage
if (!isset($_SESSION[KEY_FORCE_RESET_PASSWORD]))
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/homepage.php' . '" , "_self");</script>';
}

// On Submit
if (isset($_POST[KEY_RESETPASS_SUBMIT]))
{
    // Initialize an error array.
    $errors = [];
    
    // Verify that the password and the password confirmations are equal
    $pass = getPostString($dbc, $errors, KEY_RESETPASS_PASS);
    $passconf = getPostString($dbc, $errors, KEY_RESETPASS_PASSCONFIRM);
    
    if (!empty($_POST[KEY_RESETPASS_PASS]))
    {
        if ($_POST[KEY_RESETPASS_PASS] != $_POST[KEY_RESETPASS_PASSCONFIRM])
        {
            $errors[] = 'La nuova password che hai inserito non corrisponde con la password di conferma.';
        }
        else
        {
            $new_pass = mysqli_real_escape_string($dbc, trim($_POST[KEY_RESETPASS_PASS]));
        }
    }
    else
    {
        $errors[] = 'Hai dimenticato di inserire la tua nuova password.';
    }
    
    // Se non ci sono errori (la nuova password è uguale a quella di conferma)
    if (empty($errors))
    {
        // update password and reset token to null
        $user = $_SESSION[KEY_LOGGED_IN];
        $qu = "UPDATE utente SET password=SHA2(?, 256), token=NULL WHERE user=?;";
        $stmt = executePrep($dbc, $qu, "ss", [$new_pass, $user]);
        
        // If query ran OK.
        if (mysqli_affected_rows($dbc) == 1)
        {
            // invia email per avvisare del reset password
            if(sendMail($config, 'ProCi - Password Cambiata', getBroadcastMailBody()))
            {
                $alert = alertEmbedded("success", "Fatto!", "La password è stata aggiornata.");
                $_SESSION[KEY_FORCE_RESET_PASSWORD] = false;
                unset($_SESSION[KEY_FORCE_RESET_PASSWORD]);
                
                // redirect alla homepage
                echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/homepage.php' . '" , "_self");</script>';
            }
            else
            {
                $alert = alertEmbedded("warning", "Errore di Sistema!", "Non è stato possibile cambiare la password per un errore del framework mail, riprovare e, se persiste, contattare i tecnici. Ci scusiamo per l'inconveniente.");
            }
        }
        else
        { // If it did not run OK.
            $alert = alertEmbedded("warning", "Errore di Sistema!", "Non è stato possibile cambiare la password per un errore di sistema, riprovare e, se persiste, contattare i tecnici. Ci scusiamo per l'inconveniente.");
            
            array_push($errors, mysqli_error($dbc), "The Query did not run OK.", interpolateQuery($qu, [$new_pass, $user]));
            reportErrors($alert, $errors, false);
        }
    
        $stmt -> close();
    }
    else
    {
        reportErrors($alert, $errors);
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
    
    // se c'è qualche messaggio di errore, mostralo
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
