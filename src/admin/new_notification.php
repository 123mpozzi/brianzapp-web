<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 02/03/2018
 * Time: 17:51
 */

    include("../config.php");
    include("../utils.php");

    session_start();
    //print_r($_POST);

    if(isset($_POST['btn_send_notification'])) {//salvo nel db i dati e nel server il pdf



        // per prima cosa verifico che il file sia stato effettivamente caricato
        if (!isset($_FILES['cover']) || !is_uploaded_file($_FILES['cover']['tmp_name'])) {
            echo 'Non hai inviato nessun file...';
            exit;
        }

        //percorso della cartella dove mettere i file caricati dagli utenti
        $uploaddir = '..\..\PDF\\';

        //Recupero il percorso temporaneo del file
        $userfile_tmp = $_FILES['cover']['tmp_name'];

        //scelgo il nome del file caricato
        $nomeFile = $_FILES['cover']['name'];//MODIFICARE CON NOME UNIVOCO
        $userfile_name = $nomeFile;

        //copio il file dalla sua posizione temporanea alla mia cartella upload
        if (move_uploaded_file($userfile_tmp, $uploaddir . $userfile_name)) {
            //Se l'operazione è andata a buon fine...
            //echo 'File inviato con successo.';
        }else{
            //Se l'operazione è fallta...
            echo 'Upload NON valido!';
        }

        $Stelle = $_POST['Stelle'];
        $Provenienza = $_POST['Provenienza'];
        $Colore = substr($_POST['Colore'], 1);
        $Data = $_POST['Data'];
        $q = "INSERT INTO notifiche (stelle, pdf, provenienza, colore, data) VALUES (?,?,?,?,?)";
        $stmt = executePrep($dbc, $q, "sssss", [$Stelle, $nomeFile, $Provenienza, $Colore, $Data]);

    }
?>

<body>
    <form name="new_notification" enctype="multipart/form-data" action="new_notification.php" method="POST">
        <label>Stelle   </label>
        <input type="radio" name="Stelle" value="1"> 1<br>
        <input type="radio" name="Stelle" value="2"> 2<br>
        <input type="radio" name="Stelle" value="3"> 3
        <br>
        <br>
        <label>PDF  </label>
        <input type="file" placeholder="Inserisci cover" name="PDF" accept="application/pdf" required></label>
        <br>
        <br>
        <label>Provenienza  </label>
        <input type="radio" name="Provenienza" value="23893"> 1322<br>
        <input type="radio" name="Provenienza" value="23893"> 2386<br>
        <input type="radio" name="Provenienza" value="23893"> Other
        <br>
        <br>
        <label>Colore  </label>
        <input id="html5colorpicker" onchange="clickColor(0, -1, -1, 5)" value="#ff0000" style="width:85%;" type="color" name="Colore">
        <br>
        <label>Data</label>
        <input type="date" name="Data">
        <br>
        <br>
        <input class = "button" name = "btn_send_notification" type = "submit" value = "INVIA">
    </form>

</body>
</html>