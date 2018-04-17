<?php

function getBroadcastMailBody()
{
    $body = '';
    
    $body .= "
<h1> ProCi Web App </h1>

<p>
    E' stata cambiata la password di accesso alle ore <b>" . date("h:i") . "</b> del giorno <b>" . date("d/m/Y") . "</b>";
    $body .= "</p>";
    
    return $body;
}

function getResetMailBody(mysqli $dbc, $config)
{
    $body = '';
    
    if(isset($_SESSION[KEY_LOGRESET_LINK]))
    {
        $body .= "
<h1> ProCi Web App </h1>

<p>
    Per reimpostare una nuova password, cliccare ";
    
        $body .=  "<a href=\"";
        $body .=  $_SESSION[KEY_LOGRESET_LINK];
        $body .=  "\">qui</a></p>";
        
        unset($_SESSION[KEY_LOGRESET_LINK]);
    }
    else
    {
        $body .=  "
    <h1> ProCi Web App </h1>

<p>
    Errore nella generazione del link per resettare la password, contattare i tecnici.
</p>";
        
        $user = $_SESSION[KEY_LOGGED_IN];
        $qu = "UPDATE utente SET token=NULL WHERE user=?;";
        $stmt = executePrep($dbc, $qu, "s", [$user]);
        
        if (mysqli_affected_rows($dbc) == 1)
        { // If it ran OK.
            $alert = alertEmbedded("success", "Fatto!", "La password è stata aggiornata.");
            $_SESSION[KEY_FORCE_RESET_PASSWORD] = false;
            unset($_SESSION[KEY_FORCE_RESET_PASSWORD]);
            
            // invia email per avvisare del reset password
            sendMail($config, 'ProCi - Password Cambiata', getBroadcastMailBody());
        }
        else
        { // If it did not run OK.
            $alert = alertEmbedded("warning", "Errore di Sistema!", "Non è stato possibile cambiare la password per un errore di sistema, contattare i tecnici. Ci scusiamo per l'inconveniente.");
    
            $errors[] = [mysqli_error($dbc), "The Query did not run OK.", 'Query' . interpolateQuery($qu, [$user])];
            reportErrors($alert, $errors, false);
        }
        
        $stmt -> close();
        
        if(isset($_SESSION[KEY_FORCE_RESET_PASSWORD]))
        {
            $_SESSION[KEY_FORCE_RESET_PASSWORD] = false;
            unset($_SESSION[KEY_FORCE_RESET_PASSWORD]);
        }
    }
    
    return $body;
}

?>