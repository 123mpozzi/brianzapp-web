<html>
<body>

<?php
if(isset($_SESSION[KEY_LOGRESET_LINK]))
{
    echo "
<h1> ProCi Web App </h1>

<p>
    Per reimpostare una nuova password, cliccare sul seguente link:";
    
    echo "<br>";
    echo $_SESSION[KEY_LOGRESET_LINK];
    echo "</p>";
    
    unset($_SESSION[KEY_LOGRESET_LINK]);
}
else
{
    echo "
    <h1> ProCi Web App </h1>

<p>
    Errore nella generazione del link per resettare la password, contattare i tecnici.
</p>";
}
?>

</body>

</html>