<?php

$page_title = "Crea Notifica";

include("../auth.php");

// Get logged user ID
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
    // utente non trovato -> redirect al login
    else
    {
        $alert = alertEmbedded("danger", "Errore!", "Combinazione utente e password errata!");
        echo '<script type="text/javascript"> window.open("' . BASE_URL . '../index.php' . '" , "_self");</script>';
    }
    
    $stmt->close();
}
// utente non trovato -> redirect al login
else
{
    echo '<script type="text/javascript"> window.open("' . BASE_URL . '../index.php' . '" , "_self");</script>';
}

// On form submit
if (isset($_POST[KEY_NEW_SUBMIT]))
{
    // Data di invio
    $data = date('Y-m-d H:i:s');
    
    // informazioni del form
    $titolo = $_POST[KEY_NEW_TITOLO];
    $descrizione = $_POST[KEY_NEW_DESCRIZIONE];
    $stelle = $_POST[KEY_NEW_STELLE];
    $provenienza = $_POST[KEY_NEW_PROVENIENZA];
    $comuni = $_POST["comuniDestinatari"];
    
    // Se il file è valido e non ci sono errori
    if(isset($_FILES[KEY_NEW_PDF]) && $_FILES[KEY_NEW_PDF]['error'] === UPLOAD_ERR_OK)
    {
        $files = $_FILES[KEY_NEW_PDF];
        
        // dir in cui salvare i file caricati
        $target_dir = $_SERVER["DOCUMENT_ROOT"] . '/WebApp/pdf/';
        
        // crearla se non esiste di già
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // nome file univoco contenente provenienza e stelle
        $file_name = md5(basename($files["name"]) . microtime()) . 'p' . $provenienza . 's' . $stelle . '.pdf';
        $target_file = $target_dir . $file_name;
        
        $pdf = $file_name;
        
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is an actual PDF or fake PDF
        if (!empty($files['tmp_name']))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $files['tmp_name']);
            if ($mime != 'application/pdf')
            {
                $alert = alertEmbedded("danger", "Formato non valido!", "Il file deve essere un PDF.");
                $errors[] = "invalid upload: the file must be a real PDF";
                $uploadOk = 0;
            }
        }
        
        // Check if file already exists
        if (file_exists($target_file)) {
            $alert = alertEmbedded("danger", "Il file esiste già!", "Il file che hai caricato è già esistente.");
            $errors[] = "invalid upload: file already existing";
            $uploadOk = 0;
        }
        // Check file size
        /*if ($files["size"] > 500000) {
            $alert = alertEmbedded("danger", "File too big!", "Sorry, file you uploaded is too big.");
            $errors[] = "invalid upload: file too big";
            $uploadOk = 0;
        }*/
        // Allow certain file formats
        if($fileType != "pdf") {
            $alert = alertEmbedded("danger", "Formato non valido!", "Sono permessi solo file PDF.");
            $errors[] = "invalid upload: invalid file format";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $alert = alertEmbedded("danger", "Caricamento fallito!", "Il tuo file non è stato accettato.");
            $errors[] = "upload not permitted: sorry, your file was not accepted.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($files["tmp_name"], $target_file)) {
                $alert = alertEmbedded("success", "File caricato!", "Il file ". $file_name . " è stato caricato con successo.");
            } else {
                $alert = alertEmbedded("danger", "Caricamento fallito!", "Si è presentato un errore durante il caricamento del tuo file.");
                $errors[] = "invalid upload: sorry, there was an error uploading your file.";
            }
        }
        
        if(!empty($errors))
        {
            reportErrors($alert, $errors, false, 'warning');
        }
    }
    else
    {
        $alert = alertEmbedded("warning", "Nessun file inviato!", "Non hai inviato nessun file...");
        exit;
    }
    
    // Nel caso il CURL dia errori, mettili qui
    $curl_error = "";
    
    //a questo punto invio la notifica a tutti i cellulari interessati tramite OneSignal
    //$comuneTAG = $provenienza;
    $risultato = sendMessage($comuni, $titolo, $descrizione, $curl_error); //il primo parametro indica i TAG one signal a cui deve essere spedito il messaggio, il secondo il testo del messaggio
    
    // se non ci sono errori nell'invio della notifica, la aggiunge al db
    if(empty($curl_error))
    {
        // inserisci la notifica nel db
        $q = "insert into notifica (titolo, descrizione, stelle, pdf, data, id_provenienza, id_utente) values (?, ?, ?, ?, ?, ?, ?);";
    
        // esegue il trim (rimuove spazi bianchi all'inizio e alla fine della stringa) per prevenire stranezze nell'ordinamento (spazio bianco viene messo prima dei numeri e delle lettere, ...)
        $stmt = executePrep($dbc, $q, "sssssii", [trim($titolo), trim($descrizione), $stelle, $pdf, $data, $provenienza, $id]);
        $stmt -> close();
    
        //prende l id inserito nella precedente query
        //ATTENZIONE: aggiungere le transazioni altrimenti non è sicuro si ottenga sempre il risultato corretto
    
        $idNotifica =  mysqli_insert_id($dbc);
        foreach ($comuni as $cap){
            $q = "insert into notifica_comune (id_notifica, cap_comune) values (?, ?);";
            $stmt = executePrep($dbc, $q, "ii", [$idNotifica, $cap]);
            $stmt -> close();
        }
    
    
        // Log Data
        // Ottieni nome (per ora abbiamo solo l'id numerico) della provenienza
        // Se non lo trova, mette l'ID (meglio che niente)
        $prov_nome = $provenienza;
    
        $q = "SELECT nome FROM provenienza WHERE id = ?";
        $stmt = executePrep($dbc, $q, "i", [$provenienza]);
    
        $stmt_result = $stmt->get_result();
    
        // corrispondenza utente trovata, salvare il valore tramite le sessioni
        if ($stmt_result->num_rows == 1)
        {
            $prov_nome = $stmt_result->fetch_array(MYSQLI_NUM)[0];
        }
    
        $stmt -> close();
    
        $log_file = $_SERVER["DOCUMENT_ROOT"] . '/WebApp/pdf/' . 'notifica.txt';
        $log_divider = "\t";
        $log_data =
            $data . $log_divider .
            $prov_nome . $log_divider .
            'Stelle: ' . $stelle . $log_divider .
            $pdf . $log_divider .
            'Titolo: ' . $titolo . $log_divider
        ;
    
        $first_row = 'Data' . $log_divider . 'Provenienza' . $log_divider . 'Stelle' . $log_divider . 'PDF' . $log_divider . 'Titolo';
    
        // TODO: aggiungere lista comuni al log
        logData($log_file, $log_data, $first_row);
        
        echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/new_notification_success.php' . '" , "_self");</script>';
    }
    else
    {
        $_SESSION[KEY_NEW_CURL_ERROR] = $curl_error;
        echo '<script type="text/javascript"> window.open("' . BASE_URL . 'admin/new_notification_fail.php' . '" , "_self");</script>';
    }
}

function sendMessage($ListaComuni, $heading, $messaggio, &$curl_error = null)
{
    //fsockopen(); da errore, penso non serva
    
    // titolo notifica
    $heading = array(
            "en" => $heading
    );
    
    // contenuto notifica
    $content = array(
        "en" => $messaggio
    );
    
    $arr = array();
    foreach ($ListaComuni as $cap)
    {
        array_push($arr, array("field" => "tag", "key" => $cap,
            "relation" => "=", "value" => "true"));
        array_push($arr, array("operator" => "OR"));
    }
    array_push($arr, array("field" => "tag", "key" => "errore",
        //questo è per evitare il "A or B or" facendo "A or B or false"
        "relation" => "=", "value" => "errore"));
    $fields = array(
        'app_id' => "***REMOVED***",
        'filters' => $arr,
        'data' => array("foo" => "bar"),
        'headings' => $heading,
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
    
    if($response == false)
    {
        $curl_error = curl_error($ch);
    }
    
    curl_close($ch);
    
    return $response;
}

?>

<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">

<div id="new-notification-card" class="custom-card new-notification-container">
    <div id="new-notification-form-container">
        <div id="new-notification-page-header">
            <!-- Logo -->
            <a href="homepage.php"><img id="logo-full" src="../res/ba_scrittolato.png" alt="BrianzApp"></a>
            <!-- Ritorna alla Homepage -->
            <div class="button-normal">
                <a class="btn btn-info btn-full-large" href="homepage.php">RITORNA ALLA HOME</a>
            </div>
        </div>
        <!-- Titolo Card -->
        <h1>INVIA NOTIFICA</h1>
        <hr>
        <form id="new-notification-form" name="new_notification" action="new_notification.php" method="post" enctype="multipart/form-data">
            <!-- Inserisci Titolo -->
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

            <!-- Inserisci Descrizione -->
            <div class="form-element">
                <span>Descrizione (opzionale) </span>
                <textarea class="form-control" rows="4" maxlength="250" name="<?php echo KEY_NEW_DESCRIZIONE ?>" placeholder="Descrizione Notifica"></textarea>
            </div>

            <!-- Seleziona PDF -->
            <div class="form-element" style="flex: 1 0 50%;">
                <span>PDF (max 4MB)</span>
                <input class="form-control" type="file" placeholder="Seleziona PDF" name="<?php echo KEY_NEW_PDF ?>" id="<?php echo KEY_NEW_PDF ?>" accept="application/pdf"
                       required>
            </div>

            <!-- Seleziona Provenienza -->
            <div class="form-element" style="flex: 1 0 50%;">
                <span>Provenienza </span>
                
                <div>
                    <select class="form-control" name="<?php echo KEY_NEW_PROVENIENZA ?>">
                        <?php
                        $q = "SELECT id, nome FROM provenienza";
                        $r = $dbc->query($q);
        
                        while($row = $r->fetch_row()) {
                            echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                        }
                        
                        ?>
                    </select>
                </div>
            </div>
            
            <!-- Seleziona Comuni -->
            <div class="form-element" style="flex: 1 0 70%;">
                <div>
                    <span>Comuni destinatari </span>
                    <!-- Pulsanti seleziona/deseleziona tutto -->
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <span id="new-select-all" class="btn btn-link">
                            <i class="material-icons">done_all</i>
                        </span>
                        <span id="new-select-none" class="btn btn-link">
                            <i class="material-icons">close</i>
                        </span>
                    </div>
                </div>
                <?php
                    $q = "SELECT * FROM `comune`";
                    $comuni = $dbc->query($q);
                    
                    // TODO: errore se nessun comune viene selezionato (required non va sulle checkbox)
                    while($cap = $comuni->fetch_row()) {
                        echo '<input type="checkbox" name="comuniDestinatari[]" checked value="' . $cap[0] . '"/> ' . $cap[1] . '';
                        echo '<br /> ';
                    }
                ?>
            </div>

            <!-- Pulsante Invio -->
            <div class="button-normal" style="flex: 1 0 100%;">
                <input class="btn btn-danger btn-full-large" name="<?php echo KEY_NEW_SUBMIT ?>" type="submit"
                       value="INVIA">
            </div>

            <input type="hidden" name="id" value="<?php echo $id ?>">
        </form>
    </div>
</div>

</body>
</html>
