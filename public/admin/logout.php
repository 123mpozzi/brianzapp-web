<?php

$page_title = "Logout";

include("../auth.php");

// Se si deve resettare la password non si può eseguire il logout finchè non la si ha resettata
if(isset($_SESSION[KEY_FORCE_RESET_PASSWORD]) and $_SESSION[KEY_FORCE_RESET_PASSWORD] === true)
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/reset_pass/force_reset_pass.php' . '" , "_self");</script>';
}
else
{
    // https://stackoverflow.com/a/20932020
    session_destroy();   // function that Destroys Session
}

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
