<?php

$page_title = "Crea Notifica";

include("../auth.php");


//print_r($_POST);

if (isset($_POST['btn_send_notification']))
{//salvo nel db i dati e nel server il pdf
    //print_r($_FILES);
    // per prima cosa verifico che il file sia stato effettivamente caricato
    if (!isset($_FILES['PDF']) || !is_uploaded_file($_FILES['PDF']['tmp_name']))
    {
        echo 'Non hai inviato nessun file...';
        exit;
    }
    
    //percorso della cartella dove mettere i file caricati dagli utenti
    $uploaddir = '..\..\PDF\\';
    
    //Recupero il percorso temporaneo del file
    $userfile_tmp = $_FILES['PDF']['tmp_name'];
    
    //scelgo il nome del file caricato
    $nomeFile = $_FILES['PDF']['name'];//MODIFICARE CON NOME UNIVOCO
    $userfile_name = $nomeFile;
    
    //copio il file dalla sua posizione temporanea alla mia cartella upload
    if (move_uploaded_file($userfile_tmp, $uploaddir . $userfile_name))
    {
        //Se l'operazione è andata a buon fine...
        //echo 'File inviato con successo.';
    }
    else
    {
        //Se l'operazione è fallta...
        echo 'Upload NON valido!';
    }
    
    $Stelle = $_POST['Stelle'];
    $Provenienza = $_POST['Provenienza'];
    $Colore = substr($_POST['Colore'], 1);
    $Data = $_POST['Data'];
    $q = "INSERT INTO notifiche (stelle, pdf, provenienza, colore, data) VALUES (?,?,?,?,?)";
    $stmt = executePrep($dbc, $q, "sssss", [$Stelle, $nomeFile, $Provenienza, $Colore, $Data]);
    
    //a questo punto invio la notifica a tutti i cellulari interessati
    $messaggio = "Nuovo messaggio: $userfile_name";
    $comuneTAG = $Provenienza;
    //$risultato = sendMessage($comuneTAG, $messaggio); //il primo parametro indica i TAG one signal a cui deve essere spedito il messaggio, il secondo il testo del messaggio
    
}

function sendMessage($ListaAccount, $messaggio)
{
    fsockopen();
    $content = array(
        "en" => $messaggio
    );
    $arr = array();
    foreach ($ListaAccount as $account)
    {
        array_push($arr, array("field" => "tag", "key" => "nickname",
            "relation" => "=", "value" => $account['id_account']));
        array_push($arr, array("operator" => "OR"));
    }
    array_push($arr, array("field" => "tag", "key" => "errore",
        //questo è per evitare il "A or B or" facendo "A or B or false"
        "relation" => "=", "value" => "errore"));
    $fields = array(
        'app_id' => "***REMOVED***",
        'filters' => $arr,
        'data' => array("foo" => "bar"),
        'contents' => $content
    );
    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ***REMOVED***'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

?>

<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">

<div class="custom-card container-fluid Absolute-Center is-Responsive">
    <form name="new_notification" enctype="multipart/form-data" action="new_notification.php" method="POST">
        <div class="form-group">
            <span>Stelle</span>
            
            <div class="starrating risingstar d-flex justify-content-center flex-row-reverse">
                <input type="radio" id="star3" name="Stelle" value="3" /><label for="star3" title="3 star"></label>
                <input type="radio" id="star2" name="Stelle" value="2" /><label for="star2" title="2 star"></label>
                <input type="radio" id="star1" name="Stelle" value="1" /><label for="star1" title="1 star"></label>
            </div>
        </div>
        <label>PDF </label>
        <input class="form-control" type="file" placeholder="Inserisci cover" name="PDF" accept="application/pdf"
               required></label>
        <br>
        <br>
        <label>Provenienza </label>
        <?php /*STAMPARE I DATI DEL COMUNI PRENDENDOLI DAL DB
            $q = "SELECT * FROM comuni";
            $stmt = executePrep($dbc, $q, "", [null]);
            $comuni = $stmt->get_result();
            foreach($comuni as $comune){
                echo '<input type="radio" name="Provenienza" value="' . $comune['cap'] . '"> ' . $comune['nome'] . '<br>';
            }*/
        ?>
        <input type="radio" name="Provenienza" value="23876"> Cremella<br>
        <input t1ype="radio" name="Provenienza" value="23891"> Napoli<br>
        <input type="radio" name="Provenienza" value="23873"> Seregno
        <br>
        <br>
        <label>Colore </label>
        <input class="form-control" id="html5colorpicker" onchange="clickColor(0, -1, -1, 5)" value="#ff0000"
               style="width:85%;"
               type="color"
               name="Colore">
        <br>
        <label>Data</label>
        <input class="form-control" type="date" name="Data">
        <br>
        <br>
        <input class="btn btn-danger btn-full-large" class="button" name="btn_send_notification" type="submit"
               value="INVIA">
    </form>
</div>

</body>
</html>