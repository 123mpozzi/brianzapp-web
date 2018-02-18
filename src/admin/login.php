<?php

include("../auth.php");

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="login-form" class="custom-card container-fluid Absolute-Center is-Responsive"
<?php

if($alert == null || empty($alert))
{
    echo 'style="max-height: 21rem; "';
}
else
{
    echo 'style="min-height: 25rem; "';
}

?>>
    <h1>LOGIN</h1>
    <form action="login.php" method="post">
        <div class="input-group" style="center">
            <input type="text" class="form-control" size="20" placeholder="Username" required name="<?php echo KEY_USERNAME?>">
        </div>
        <br>
        <div class="input-group">
            <input type="password" class="form-control" size="20" placeholder="Password" required name="<?php echo KEY_PASSWORD?>">
        </div>
        <br>
        <div class="input-group-btn">
            <input type="submit" class="btn btn-danger btn-full-large" value="Login" name="<?php echo KEY_LOGIN_SUBMIT?>">
        </div>
    </form>
    <p class="reset-pass"><a href="#">Recupera password</a></p>
    <?php
    
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
