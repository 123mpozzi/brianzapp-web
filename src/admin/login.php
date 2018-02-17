<?php

include("../auth.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Theme Made By www.w3schools.com - No Copyright -->
    <title>Login - Proci</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
</head>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">
<div id="login-form" class="container-fluid Absolute-Center is-Responsive"
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
            <input type="text" class="form-control" size="20" placeholder="Username" required name="username">
        </div>
        <br>
        <div class="input-group">
            <input type="password" class="form-control" size="20" placeholder="Password" required name="password">
        </div>
        <br>
        <div class="input-group-btn">
            <input type="submit" class="btn btn-danger btn-full-large" value="Login" name="login_clicked">
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