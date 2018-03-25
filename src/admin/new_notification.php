<?php

$page_title = "Crea Notifica";

include("../auth.php");

//print_r($_POST);

// Get user ID
if (isset($_SESSION[KEY_LOGGED_IN]))
{
    $q = "SELECT id FROM utente WHERE user=?";
    $stmt = executePrep($dbc, $q, "s", [$_SESSION[KEY_LOGGED_IN]]);
    
    $stmt_result = $stmt->get_result();
    
    // corrispondenza utente trovata, salvare il valore tramite le sessioni
    if ($stmt_result->num_rows == 1)
    {
        $id = $stmt_result->fetch_array(MYSQLI_NUM)[0];
    }
    else
    {
        $alert = alertEmbedded("danger", "Errore!", "Combinazione utente e password errata!");
        echo '<script type="text/javascript"> window.open("' . BASE_URL . '../index.php' . '" , "_self");</script>';
    }
    
    $stmt->close();
}
else
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . '../index.php' . '" , "_self");</script>';
}

// On form submit
if (isset($_POST[KEY_NEW_SUBMIT]))
{
    // Data di invio
    $data = date('Y-m-d H:i:s');
    
    
    // Se il file è valido e non ci sono errori
    if(isset($_FILES[KEY_NEW_PDF]) && $_FILES[KEY_NEW_PDF]['error'] === UPLOAD_ERR_OK)
    {
        $files = $_FILES[KEY_NEW_PDF];
        
        $target_dir = $_SERVER["DOCUMENT_ROOT"] . '\WebApp\pdf\\';
    
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $pdf = basename($files["name"]);
        
        $target_file = $target_dir . basename($files["name"]);
        
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is an actual PDF or fake PDF
        if (!empty($files['tmp_name']))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $files['tmp_name']);
            if ($mime != 'application/pdf')
            {
                //echo "File is not a real PDF.";
                alert("danger", "Invalid format!", "Sorry, the file must be a PDF.");
                $errors[] = "invalid upload: the file must be a PDF";
                $uploadOk = 0;
            }
        }
        
        // Check if file already exists
        if (file_exists($target_file)) {
            alert("danger", "File already existing!", "Sorry, file you uploaded already exists.");
            $errors[] = "invalid upload: file already existing";
            $uploadOk = 0;
        }
        // Check file size
        /*if ($files["size"] > 500000) {
            alert("danger", "File too big!", "Sorry, file you uploaded is too big.");
            $errors[] = "invalid upload: file too big";
            $uploadOk = 0;
        }*/
        // Allow certain file formats
        if($fileType != "pdf") {
            alert("danger", "Invalid format!", "Sorry, only PDF files are allowed.");
            $errors[] = "invalid upload: invalid image format";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            alert("danger", "Upload not permitted!", "Sorry, your file was not accepted.");
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($files["tmp_name"], $target_file)) {
                alert("success", "File uploaded!", "The file ". basename($files["name"]). " has been uploaded successfully.");
            } else {
                alert("danger", "Upload failed!", "Sorry, there was an error uploading your file.");
                $errors[] = "invalid upload: sorry, there was an error uploading your file.";
            }
        }
    }
    else
    {
        echo 'Non hai inviato nessun file...';
        exit;
    }
    
    
    
    /*
    //salvo nel db i dati e nel server il pdf
    //print_r($_FILES);
    // per prima cosa verifico che il file sia stato effettivamente caricato
    if (!isset($_FILES['PDF']) || !is_uploaded_file($_FILES['PDF']['tmp_name']))
    {
        echo 'Non hai inviato nessun file...';
        exit;
    }
    else
    {
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
    }*/
    
    $titolo = $_POST[KEY_NEW_TITOLO];
    $descrizione = $_POST[KEY_NEW_DESCRIZIONE];
    $stelle = $_POST[KEY_NEW_STELLE];
    $provenienza = $_POST[KEY_NEW_PROVENIENZA];
    $colore = $_POST[KEY_NEW_COLORE];
    //$data = $_POST['data'];
    //$q = "INSERT INTO notifiche (stelle, pdf, provenienza, colore, data) VALUES (?,?,?,?,?)";
    
    $q = "insert into notifica (titolo, descrizione, stelle, pdf, colore, data, id_provenienza, id_utente) values (?, ?, ?, ?, ?, ?, ?, ?);";
    
    $esempioquery1 = "insert into notifica (titolo, descrizione, stelle, pdf, colore, data, id_provenienza, id_utente) values ('lorem', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco l', '1', '2016-02-27 19:52:12', 1, 1);";
    
    $stmt = executePrep($dbc, $q, "ssssssii", [$titolo, $descrizione, $stelle, $pdf, $colore, $data, $provenienza, $id]);
    
    //a questo punto invio la notifica a tutti i cellulari interessati
    //$messaggio = "Nuovo messaggio: $userfile_name";
    //$comuneTAG = $provenienza;
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
        <form class="flex-even" name="new_notification" action="new_notification.php" method="post" enctype="multipart/form-data">
            <!-- Seleziona Titolo -->
            <div class="form-element">
                <span>Titolo </span>
                <input class="form-control" type="text" maxlength="40" required name="<?php echo KEY_NEW_TITOLO ?>" placeholder="Titolo Notifica">
            </div>

            <!-- Seleziona Stelle -->
            <div class="form-element">
                <span>Stelle</span>

                <div class="starrating risingstar d-flex justify-content-center flex-row-reverse">
                    <input type="radio" id="star3" name="<?php echo KEY_NEW_STELLE ?>" value="3"/><label for="star3" title="3 star"></label>
                    <input type="radio" id="star2" name="<?php echo KEY_NEW_STELLE ?>" value="2"/><label for="star2" title="2 star"></label>
                    <input type="radio" id="star1" name="<?php echo KEY_NEW_STELLE ?>" value="1" checked="checked"/><label for="star1" title="1 star"></label>
                </div>
            </div>

            <!-- Seleziona Descrizione -->
            <div class="form-element">
                <span>Descrizione (opzionale) </span>
                <textarea class="form-control" rows="4" maxlength="250" name="<?php echo KEY_NEW_DESCRIZIONE ?>" placeholder="Descrizione Notifica"></textarea>
            </div>

            <!-- Seleziona PDF -->
            <div class="form-element">
                <span>PDF </span>
                <input class="form-control" type="file" placeholder="Seleziona PDF" name="<?php echo KEY_NEW_PDF ?>" id="<?php echo KEY_NEW_PDF ?>" accept="application/pdf"
                       required>
            </div>

            <!-- Seleziona Provenienza -->
            <div class="form-element">
                <span>Provenienza </span>
                
                <div>
                    <select class="form-control" name="<?php echo KEY_NEW_PROVENIENZA ?>">
                        <?php
                        $q = "SELECT id, nome FROM provenienza";
                        $r = $dbc->query($q);
        
                        while($row = $r->fetch_row()) {
                            //var_dump($row);
            
                            echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                        }
                        
                        ?>
                    </select>
                </div>
            </div>

            <!-- Seleziona Colore -->
            <div class="form-element color-div">
                <span>Colore </span>
                <select id="colorselector" name="<?php echo KEY_NEW_COLORE ?>">
                    <option value="155724" data-color="#155724" selected="selected">green</option>
                    <option value="856404" data-color="#856404">yellow</option>
                    <option value="721c24" data-color="#721c24">red</option>
                    <option value="004085" data-color="#004085">blue</option>
                    <option value="0c5460" data-color="#0c5460">light_blue</option>
                    <option value="383d41" data-color="#383d41">grey</option>
                    <option value="818182" data-color="#818182">light</option>
                    <option value="1b1e21" data-color="#1b1e21">dark</option>
                </select>
            </div>

            <!-- Seleziona Data --><!--
            <div class="form-element">
                <span>Data</span>
                <input class="form-control" type="date" name="Data">
            </div>-->

            <!-- Pulsante Invio -->
            <div class="form-element">
                <input class="btn btn-danger btn-full-large" name="<?php echo KEY_NEW_SUBMIT ?>" type="submit"
                       value="INVIA">
            </div>

            <input type="hidden" name="id" value="<?php echo $id ?>">
        </form>
    </div>
</div>

</body>
</html>