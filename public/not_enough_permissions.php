<?php

// Pagina mostrata quando non si dispone dei permessi sufficienti (quando non si è loggati) per visualizzare una certa pagina

$page_title = "Insufficient Permissions";

include("./auth.php");

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="not-allowed" class="custom-card container-fluid Absolute-Center is-Responsive alert-danger">
    <h1>Non Autorizzato</h1>
    <p>Non hai abbastanza permessi per visualizzare questa pagina!
        Verrai reindirizzato automaticamente al login!
    </p>
</div>

<?php

echo '<script type="text/javascript"> window.open("' . BASE_URL . './index.php' . '" , "_self");</script>';

?>

</body>
</html>
