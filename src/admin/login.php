<?php

$page_title = "Login";

include("../auth.php");

// Se l'utente è già loggato, fa il redirect sulla homepage
if (isset($_SESSION[KEY_LOGGED_IN]))
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/homepage.php' . '" , "_self");</script>';
}

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="login-form" class="custom-card container-fluid Absolute-Center is-Responsive"
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
    <h1>LOGIN</h1>
    <form action="login.php" method="post">
        <!-- Campo Username -->
        <div>
            <input type="text" class="form-control" size="20" placeholder="Username" required name="<?php echo KEY_USERNAME?>">
        </div>
        <br>
        <!-- Campo Password -->
        <div>
            <input type="password" class="form-control" size="20" placeholder="Password" required name="<?php echo KEY_PASSWORD?>">
        </div>
        <br>
        <!-- Pulsante Login -->
        <div>
            <input type="submit" class="btn btn-danger btn-full-large" value="Login" name="<?php echo KEY_LOGIN_SUBMIT?>">
        </div>
    </form>
    <!-- Link Reset-Password -->
    <p class="reset-pass"><a href="#">Recupera password</a></p>
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
