<?php

// Pagina mostrata quando non si dispone dei permessi sufficienti (quando non si è loggati) per visualizzare una certa pagina

$page_title = "Invio Notifica Fallito :(";

include("../auth.php");

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="new-notification-fail" class="custom-card container-fluid Absolute-Center is-Responsive alert-danger">
    <a href="homepage.php"><img id="logo-full-login" src="../res/ba_scrittolato.png" alt="BrianzApp"></a>
    <h1>Invio Notifica Fallito</h1>
    <br>
    <p>
        Si è verificato un errore, per favore contattare i tecnici per la risoluzione.
    </p>
    <?php
    
    // echo the CURL error - Error list: https://curl.haxx.se/libcurl/c/libcurl-errors.html
    
    if(isset($_SESSION[KEY_NEW_CURL_ERROR]))
    {
        echo "<b>CURL ERROR</b>";
        echo "<p>" . $_SESSION[KEY_NEW_CURL_ERROR] . "</p>";
    }
    
    ?>
    <a class="btn btn-danger btn-full-large" href="homepage.php">Torna alla Homepage</a>
</div>
</body>
</html>
