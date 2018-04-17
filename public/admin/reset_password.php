<?php

$page_title = "Reset Password";

include("../auth.php");
include("reset_pass/sendmail.php");
include_once "reset_pass/mail_bodies.php";

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="reset-pass-form" class="custom-card container-fluid Absolute-Center is-Responsive "
    <?php
    
    if ($alert == null || empty($alert))
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
    <?php
    
    // Se è stato premuto il pulsante conferma
    if (isset($_POST[KEY_SUBMIT_RESET_PASSWORD]))
    {
        $errors = [];
        $user = getPostString($dbc, $errors, KEY_RESET_PASSWORD_EMAIL);
        
        if ($user != null)
        {
            if (empty($errors))
            {
// Genera un token univoco per il reset password
                $q = "SELECT user FROM utente WHERE user=?";
                $stmt = executePrep($dbc, $q, "s", [$user]);
                $stmt_result = $stmt->get_result();
                
                // corrispondenza utente trovata, tentare di inviare la mail di reset password
                if ($stmt_result->num_rows == 1)
                {
                    $stmt->close();
                    
                    echo '<p>
Un \'email di ripristino della password verrà inviata all\'indirizzo salvato!
</p>

<a class="btn btn-success btn-full-large" href="homepage.php">Torna Indietro</a>
';
                    
                    // salt is automatically generated in password_hash()
                    $token = password_hash($stmt_result->fetch_array(MYSQLI_NUM)[0], PASSWORD_BCRYPT);
                    
                    // update user entry with generated token
                    $qu = "UPDATE utente SET token=? WHERE user=?";
                    $stmt = executePrep($dbc, $qu, "ss", [$token, $user]);
                    $stmt->close();
                    
                    // gen link
                    $_SESSION[KEY_LOGRESET_LINK] = BASE_URL . 'admin/login.php?' . KEY_LOGRESET_USERNAME . '=' . $user . '&' . KEY_LOGRESET_TOKEN . '=' . $token;
                    
                    sendMail($config, 'ProCi - Reset Password', getResetMailBody($dbc, $config));
                }
                else
                {
                    echo '<p>
                        Utente non trovato, controllare di aver inserito la mail corretta!
                        </p>
                        
                        <a class="btn btn-success btn-full-large" href="homepage.php">Torna Indietro</a>
                        ';
                }
            }
            else
            {
                echo '<p>
Utente non trovato, controllare di aver inserito la mail corretta!
</p>

<a class="btn btn-success btn-full-large" href="homepage.php">Torna Indietro</a>
';
                
                reportErrors($alert, $errors);
            }
        }
        else
        {
            echo '<p>
                        Utente non trovato, controllare di aver inserito la mail corretta!
                        </p>
                        
                        <a class="btn btn-success btn-full-large" href="homepage.php">Torna Indietro</a>
                        ';
        }
    }
    // Pagina normale, prima che venga premuto il pulsante conferma
    else
    {
        echo '    <p>
        Confermare l\'indirizzo email registrato per resettare la password
    </p>
    <form action="reset_password.php" method="post">
        <!-- Input Email (Per evitare che qualche malandrino clicchi a caso, devono sapere la mail giusta per inviare la mail di reset password) -->
        <div>
            <span>Email Utente </span>
            <input class="form-control" type="email" maxlength="20" required name="' . KEY_RESET_PASSWORD_EMAIL . '" placeholder="email@gmail.com">
        </div>
        <!-- Pulsante Conferma -->
        <div id="send-reset-pass">
            <input type="submit" class="btn btn-danger btn-full-large" value="Invia" name="' . KEY_SUBMIT_RESET_PASSWORD . '">
        </div>
    </form>';
    }
    
    ?>
    <?php
    
    // se c'è qualche messaggio di errore, fargli spazio
    if ($alert != null && !empty($alert))
    {
        echo '<div class="custom-alert-embedded">';
        echo $alert;
        echo '</div>';
    }
    
    ?>
</div>
</body>
</html>
