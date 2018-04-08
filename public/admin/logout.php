<?php

$page_title = "Logout";

include("../auth.php");

// https://stackoverflow.com/a/20932020
session_destroy();   // function that Destroys Session

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="not-allowed" class="custom-card container-fluid Absolute-Center is-Responsive alert-danger">
    <!-- Questo testo verrà mostrato soltanto se la connessione è molto lenta -->
    <h1>Logging out...</h1>
    <p>Sei uscito dall'account!
        Verrai reindirizzato automaticamente al login!
    </p>
</div>

<?php

// on logout redirect on index page
echo '<script type="text/javascript"> window.open("' . BASE_URL . './index.php' . '" , "_self");</script>';

?>

</body>
</html>
