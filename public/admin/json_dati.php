<?php

include("../config.php");
include("../utils.php");

$alert = [];

// Filtri
// valori che andranno poi a sostituire i '?' nel prepared statement, di default è 1=1 (non applica il filtro)

$filter_titolo_1 = '1';
$filter_titolo_2 = '1';

$filter_provenienza_1 = '1';
$filter_provenienza_2 = '1';

$filter_stelle_1 = '1';
$filter_stelle_2 = '1';

$filter_startdate = '1';
$filter_enddate = '1';


// Ordinamento

// 0 -> null, 1 -> ASC, -1 -> DESC
$sort_titolo = 0;
$sort_stelle = 0;
$sort_data = -1;

// controlla richieste GET (dall'URL) per i filtri e l'ordinamento
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // Titolo
    $errors = [];
    
    $ti = getGetString($dbc, $errors, KEY_FILTER_TITOLO);
    
    if ($ti != null)
    {
        if (empty($errors))
        {
            // ignore il case (maiuscole e minuscole) mettendo tutto a minuscolo (lower)
            $filter_titolo_1 = 'lower(n.titolo)';
            // matcha stringhe CONTENENTI (%stringa%) il valore cercato
            $filter_titolo_2 = "'%" . $ti . "%'";
        }
        else
            reportErrors($alert, $errors);
    }
    
    
    // Provenienza
    $errors = [];
    
    $prov = getGetString($dbc, $errors, KEY_FILTER_PROVENIENZA);
    
    if ($prov != null and $prov != 0)
    {
        if (empty($errors))
        {
            // matcha gli ID (numeri interi)
            $filter_provenienza_1 = 'p.id';
            $filter_provenienza_2 = $prov;
        }
        else
            reportErrors($alert, $errors);
    }
    
    
    // Comuni
    $errors = [];
    
    if(isset($_GET[KEY_FILTER_COMUNI]))
    {
        $com = $_GET[KEY_FILTER_COMUNI];
        
        if (!isset($com) or $com == null or empty($com))
        {
            $errors[] = "a key is null or empty: " . KEY_FILTER_COMUNI;
            reportErrors($alert, $errors);
        }
    }
    
    
    // Stelle
    $errors = [];
    
    $stelle = getGetString($dbc, $errors, KEY_FILTER_STELLE);
    
    if ($stelle != null and $stelle != 0)
    {
        if (empty($errors))
        {
            $filter_stelle_1 = 'n.stelle';
            $filter_stelle_2 = $stelle;
        }
        else
            reportErrors($alert, $errors);
    }
    
    
    // Start Date
    $errors = [];
    
    $sd = getGetString($dbc, $errors, KEY_FILTER_START_DATE);
    
    if ($sd != null)
    {
        if (empty($errors))
        {
            $filter_startdate = $sd;
        }
        else
            reportErrors($alert, $errors);
    }
    
    
    // End Date
    $errors = [];
    
    $ed = getGetString($dbc, $errors, KEY_FILTER_END_DATE);
    
    if ($ed != null)
    {
        if (empty($errors))
        {
            $filter_enddate = $ed;
        }
        else
            reportErrors($alert, $errors);
    }
    
    // fetch max date (ottiene la data massima, cioè la data presa dalla notifica con la data massima)
    if ($filter_enddate == 1)
    {
        $q = "SELECT MAX(data) FROM notifica n;";
        $r = @mysqli_query($dbc, $q);
        
        if (mysqli_num_rows($r) == 1)
        {
            $row = mysqli_fetch_array($r, MYSQLI_NUM);
            $filter_enddate = $row[0];
        }
        else
        {
            // irraggiungibile: al massimo uscirà  NULL  ( 1 riga, mai 0 )
            // valore di default nel caso il db sia vuoto (non filtrare)
            $filter_enddate = date('Y/m/d H:i:s', strtotime(date('Y/m/d H:i:s') . ' +1 day'));
        }
    }
    
    // fetch min date (ottiene la data minima, cioè la data presa dalla notifica con la data minima)
    if ($filter_startdate == 1)
    {
        $q = "SELECT MIN(data) FROM notifica n;";
        $r = @mysqli_query($dbc, $q);
        
        if (mysqli_num_rows($r) == 1)
        {
            $row = mysqli_fetch_array($r, MYSQLI_NUM);
            $filter_startdate = $row[0];
        }
        else
        {
            // valore di default nel caso il db sia vuoto (non filtrare)
            $filter_startdate = '2002-12-30 06:33:45';
        }
    }
    
    
    
    // ORDINAMENTO
    
    // Titolo
    $errors = [];
    
    $so_ti = getGetString($dbc, $errors, KEY_SORT_TITOLO);
    
    if ($so_ti != null)
    {
        if (empty($errors))
        {
            // se non è applicato applica l'ordine di default
            // se è applicato, inverte l'ordine
            // azzera gli altri ordinamenti (solo un ordinamento per volta)
            switch ($so_ti)
            {
                case 0:
                case 1:
                    // può essere applicato solo un ordinamento per volta, metti gli altri a 0
                    $sort_titolo = -1;
                    $sort_data = 0;
                    $sort_stelle = 0;
                    break;
                case -1:
                    $sort_titolo = 1;
                    $sort_data = 0;
                    $sort_stelle = 0;
                    break;
                default:
                    $sort_titolo = $so_ti;
                    break;
            }
        }
        else
            reportErrors($alert, $errors);
    }
    
    
    // Stelle
    $errors = [];
    
    $so_st = getGetString($dbc, $errors, KEY_SORT_STELLE);
    
    if ($so_st != null)
    {
        if (empty($errors))
        {
            // se non è applicato applica l'ordine di default
            // se è applicato, inverte l'ordine
            // azzera gli altri ordinamenti (solo un ordinamento per volta)
            switch ($so_st)
            {
                case 0:
                case 1:
                    // può essere applicato solo un ordinamento per volta, metti gli altri a 0
                    $sort_stelle = -1;
                    $sort_data = 0;
                    $sort_titolo = 0;
                    break;
                case -1:
                    $sort_stelle = 1;
                    $sort_data = 0;
                    $sort_titolo = 0;
                    break;
                default:
                    $sort_stelle = $so_ti;
                    break;
            }
        }
        else
            reportErrors($alert, $errors);
    }
    
    
    // Data
    $errors = [];
    
    $so_da = getGetString($dbc, $errors, KEY_SORT_DATA);
    
    if ($so_da != null)
    {
        if (empty($errors))
        {
            // se non è applicato applica l'ordine di default
            // se è applicato, inverte l'ordine
            // azzera gli altri ordinamenti (solo un ordinamento per volta)
            switch ($so_da)
            {
                case 0:
                case 1:
                    // può essere applicato solo un ordinamento per volta, metti gli altri a 0
                    $sort_data = -1;
                    $sort_stelle = 0;
                    $sort_titolo = 0;
                    break;
                case -1:
                    $sort_data = 1;
                    $sort_stelle = 0;
                    $sort_titolo = 0;
                    break;
                default:
                    $sort_data = $so_ti;
                    break;
            }
        }
        else
            reportErrors($alert, $errors);
    }
}



// -- JSON REQUEST --
// Invia i dati JSON al client android che lo richiede
$errors = [];

// ottieni notifiche dal db
$q = "SELECT n.id AS notid, n.titolo, n.descrizione, n.stelle, n.pdf, n.colore, n.data, p.nome AS provenienza FROM notifica n INNER JOIN provenienza p ON n.id_provenienza = p.id WHERE ? LIKE ? AND ? LIKE ? AND ? LIKE ? AND n.data BETWEEN '?' AND '?' ORDER BY ";

// Applica Ordinamento
if ($sort_titolo != 0)
{
    // Sort Titolo
    $q .= "n.titolo " . ($sort_titolo == 1 ? "ASC" : "DESC");
}
else if ($sort_stelle != 0)
{
    // Sort Stelle
    $q .= "n.stelle " . ($sort_stelle == 1 ? "ASC" : "DESC");
}
else
{
    // Sort Data, default sorting
    $q .= "n.data " . ($sort_data == 1 ? "ASC" : "DESC");
}

// sostituisce i ? con i vari valori dei filtri (prepara la query)
$q = interpolateQuery($q, [$filter_titolo_1, $filter_titolo_2, $filter_provenienza_1, $filter_provenienza_2,
    $filter_stelle_1, $filter_stelle_2, $filter_startdate, $filter_enddate]);

// esegue la query
$stmt = $dbc->query($q);

// Query utilizzata per ottenere i comuni destinatari legati alle varie notifiche
$qc = "SELECT c.nome AS cnome, c.cap AS ccap FROM comune c INNER JOIN notifica_comune nc ON c.cap = nc.cap_comune WHERE nc.id_notifica = ?;";

$notifiche = [];
$notifiche_json = [];

if ($stmt)
{
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
    {
        // Ottieni i comuni destinatari legati alla notifica dell'iterazione del ciclo
        $stmtc = executePrep($dbc, $qc, "i", [$row['notid']]);
        $stmtc_result = $stmtc->get_result();
        
        $comuni = [];
        
        // filtro dei comuni
        $escludi = false;
        
        if($stmtc_result)
        {
            $arr = [];
            
            while($rowc = $stmtc_result->fetch_array(MYSQLI_ASSOC)){
                $comuni[] = $rowc['cnome'];
                $arr[] = $rowc['ccap'];
            }
            
            // filtra per comuni
            if(isset($com) && $com != null && !empty($com))
            {
                $escludi = true;
                
                foreach ($arr as $acap)
                {
                    if (in_array($acap, $com))
                    {
                        $escludi = false;
                    }
                }
            }
            
            $stmtc->close();
        }
        
        // genera oggetti HTML rappresentanti le notifiche e li inserisce in un'array
        if(!$escludi)
            $notifiche[] = genNotifica($row['titolo'], $row['descrizione'], $row['stelle'], $row['data'], $row['provenienza'], $row['colore'], $row['pdf'], $comuni, $notifiche_json);
    }
    
    $stmt->close();
}
else
{
    $errors = ['Homepage fetching query failed: ', $q];
    reportErrors($alert, $errors, false);
}


if(empty($errors))
{
    if (isset($notifiche_json))
        echo json_encode($notifiche_json);
}
else
{
    reportErrors($alert, $errors, false);
    echo "Errore nell'ottenere i dati JSON, visualizzare i log errori per i dettagli.";
}

exit;

?>
