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
    
    // manca il colore e il pdf
    $esempioquery = "insert into notifica (titolo, descrizione, stelle, data, id_provenienza, id_utente) values ('lorem', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco l', '1', '2016-02-27 19:52:12', 1, 1);";
    
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

<div id="new-notification-card" class="custom-card fit-content-height container-fluid Absolute-Center is-Responsive">
    <div id="new-notification-form-container">
        <h1>INVIA NOTIFICA</h1>
        <hr>
        <form class="flex-even" name="new_notification" enctype="multipart/form-data" action="new_notification.php" method="POST">
            
            <!-- Seleziona Stelle -->
            <div class="form-element">
                <span>Stelle</span>

                <div class="starrating risingstar d-flex justify-content-center flex-row-reverse">
                    <input type="radio" id="star3" name="Stelle" value="3"/><label for="star3" title="3 star"></label>
                    <input type="radio" id="star2" name="Stelle" value="2"/><label for="star2" title="2 star"></label>
                    <input type="radio" id="star1" name="Stelle" value="1"/><label for="star1" title="1 star"></label>
                </div>
            </div>

            <!-- Seleziona PDF -->
            <div class="form-element">
                <span>PDF </span>
                <input class="form-control" type="file" placeholder="Inserisci cover" name="PDF" accept="application/pdf"
                       required>
            </div>

            <!-- Seleziona Provenienza -->
            <div class="form-element">
                <span>Provenienza </span>
                
                <div>
                    <select class="form-control" name="provenienza">
                        <?php
                        $q = "SELECT id, nome FROM provenienza";
                        $r = $dbc->query($q);
        
                        while($row = $r->fetch_row()) {
                            //var_dump($row);
            
                            echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                        }
        
                        //mysqli_close($dbc);
        
                        ?>
                    </select>
                </div>
            </div>

            <!-- Seleziona Colore -->
            <div class="form-element color-div">
                <span>Colore </span>
                <!--<input class="form-control" id="html5colorpicker" onchange="clickColor(0, -1, -1, 5)" value="#ff0000"
                       style="width:85%;"
                       type="color"
                       name="Colore">-->

                <select id="colorselector" name="colore">
                    <option value="1" data-color="#155724" selected="selected">green</option>
                    <option value="2" data-color="#856404">yellow</option>
                    <option value="3" data-color="#721c24">red</option>
                    <option value="4" data-color="#004085">blue</option>
                    <option value="5" data-color="#0c5460">light_blue</option>
                    <option value="6" data-color="#383d41">grey</option>
                    <option value="7" data-color="#818182">light</option>
                    <option value="8" data-color="#1b1e21">dark</option>
                </select>
            </div>

            <!-- Seleziona Data -->
            <div class="form-element">
                <span>Data</span>
                <input class="form-control" type="date" name="Data">
            </div>

            <!-- Pulsante Invio -->
            <div class="form-element">
                <input class="btn btn-danger btn-full-large" name="btn_send_notification" type="submit"
                       value="INVIA">
            </div>
        </form>
    </div>
</div>

</body>
</html>