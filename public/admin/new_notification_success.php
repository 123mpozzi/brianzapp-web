<?php

// Pagina mostrata quando non si dispone dei permessi sufficienti (quando non si è loggati) per visualizzare una certa pagina

$page_title = "Invio Notifica Successo :D";

include("../auth.php");

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="new-notification-success" class="custom-card container-fluid Absolute-Center is-Responsive alert-success">
    <a href="homepage.php"><img id="logo-full-login" src="../res/ba_scrittolato.png" alt="BrianzApp"></a>
    <h1>Notifica Inviata!</h1>
    <p>
        Invio notifica avvenuto con successo!
    </p>
    <a class="btn btn-success btn-full-large" href="homepage.php">Torna alla Homepage</a>
</div>
</body>
</html>
