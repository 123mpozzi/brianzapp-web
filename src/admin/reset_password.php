<?php

$page_title = "Reset Password";

include("../auth.php");

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="reset-pass-form" class="custom-card container-fluid Absolute-Center is-Responsive "
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
    <?php
    
    // Se è stato premuto il pulsante conferma
    if (isset($_POST[KEY_SUBMIT_RESET_PASSWORD]))
    {
        echo '<p>
Email di ripristino password inviata all\'indirizzo salvato!
</p>

<a class="btn btn-success btn-full-large" href="homepage.php">Torna Indietro</a>
';
    }
    // Pagina normale, prima che venga premuto il pulsante conferma
    else
    {
        echo '    <p>
        Inviare una mail all\'indirizzo registrato per resettare la password?
    </p>
    <form action="reset_password.php" method="post">
        <!-- Pulsante Conferma -->
        <div>
            <input type="submit" class="btn btn-danger btn-full-large" value="Invia" name="' . KEY_SUBMIT_RESET_PASSWORD . '">
        </div>
    </form>';
    }
    
    ?>
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
