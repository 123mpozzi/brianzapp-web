<?php

$page_title = "HomePage";

include("../auth.php");

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
    
    
    // -- JSON REQUEST --
    // Controlla se è una richiesta GET del JSON contenente i dati delle notifiche (è il client android che lo richiede)
    $errors = [];
    
    if($jh = getGetString($dbc, $errors, KEY_JSON_HOMEPAGE) != null)
    {
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
            echo "Errore nell'ottenere i dati JSON, visualizzare i log errori per i dettagli.";
        }
        
        exit;
    }
}

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


?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">

<div class="homepage-container custom-card">
    <!-- HomePage Header: titolo e pulsante logout + filtri -->
    <div class="homepage-header">
        <!-- TitleBar: titolo + pulsante logout -->
        <div class="homepage-titlebar">
            <h1>BrianzApp</h1>
            <!-- Invia Nuova Notifica -->
            <div class="link-labeled hide-on-mobile">
                <span class="input-group-text">Nuova</span>
                <a id="homepage-add" class="btn btn-success icon-font-container" title="Nuova notifica" href="new_notification.php">
                    <i class="material-icons">add</i>
                </a>
            </div>
            <!-- Filtri di ricerca -->
            <div class="link-labeled hide-on-mobile">
                <span class="input-group-text">Filtri</span>
                <button id="homepage-filter-icon" class="btn btn-primary icon-font-container" title="Filtra risultati">
                    <i class="material-icons">filter_list</i>
                </button>
            </div>
            <!-- Logout Button -->
            <div class="link-labeled hide-on-mobile">
                <span class="input-group-text">Logout</span>
                <a id="homepage-title-logout" class="btn btn-warning icon-font-container" title="Logout" href="logout.php">
                    <i class="material-icons">exit_to_app</i>
                </a>
            </div>
        </div>
        <!-- SearchBar: div Ordinamento -->
        <div class="homepage-searchbar">
            <!-- Icone Ordinamento -->
            <i class="material-icons">arrow_upward</i>
            <i class="material-icons">arrow_downward</i>
            <!-- Forms che applicano i vari ordinamenti -->
            <!-- Ordinamento per Titolo -->
            <form id="sort_titolo" action="homepage.php" method="GET">
                <?php
                // Tiene nell'URL i valori GET derivati dai filtri, resetta quelli derivati dall'ordinamento (verranno cambiati e reimpostati)
                unset($_GET[KEY_SORT_TITOLO], $_GET[KEY_SORT_STELLE], $_GET[KEY_SORT_DATA]);
                keepGETParams();
                
                ?>
                <input type="hidden" name="<?php echo KEY_SORT_TITOLO; ?>" value="
                <?php
                // Controlla se attualmente si sta ordinando il contenuto per il titolo
                if (isset($sort_titolo))
                {
                    echo $sort_titolo;
                }
                else
                {
                    echo '0';
                }
                ?>">
                <button class="btn btn-link" type="submit">
                    Titolo
                    <?php
                    // Stampa la freccia giusta di ordinamento se l'ordinamento corrente è per titoli
                    if (isset($sort_titolo))
                    {
                        switch ($sort_titolo)
                        {
                            // -1
                            case -1:
                                echo '<i class="material-icons">arrow_upward</i>';
                                break;
                            // 1
                            case 1:
                                echo '<i class="material-icons">arrow_downward</i>';
                                break;
                            default:
                                break;
                        }
                    }
                    ?>
                </button>
            </form>
            <!-- Ordinamento per Stelle -->
            <form id="sort_stelle" action="homepage.php" method="GET">
                <?php

                // Tiene nell'URL i valori GET derivati dai filtri, resetta quelli derivati dall'ordinamento (verranno cambiati e reimpostati)
                unset($_GET[KEY_SORT_TITOLO], $_GET[KEY_SORT_STELLE], $_GET[KEY_SORT_DATA]);
                keepGETParams();
                
                ?>
                <input type="hidden" name="<?php echo KEY_SORT_STELLE; ?>" value="
                <?php
                // Controlla se attualmente si sta ordinando il contenuto per le stelle
                if (isset($sort_stelle))
                {
                    echo $sort_stelle;
                }
                else
                {
                    echo '0';
                }
                ?>">
                <button class="btn btn-link" type="submit">
                    Stelle
                    <?php
                    // Stampa la freccia giusta di ordinamento se l'ordinamento corrente è per titoli, NOTA: lo switch-case non è uguale a quello dei titoli, qui è (1, -1)
                    if (isset($sort_stelle))
                    {
                        switch ($sort_stelle)
                        {
                            // 1
                            case 1:
                                echo '<i class="material-icons">arrow_upward</i>';
                                break;
                            // -1
                            case -1:
                                echo '<i class="material-icons">arrow_downward</i>';
                                break;
                            default:
                                break;
                        }
                    }
                    ?>
                </button>
            </form>
            <!-- Ordinamento per Data -->
            <form id="sort_data" action="homepage.php" method="GET">
                <?php

                // Tiene nell'URL i valori GET derivati dai filtri, resetta quelli derivati dall'ordinamento (verranno cambiati e reimpostati)
                unset($_GET[KEY_SORT_TITOLO], $_GET[KEY_SORT_STELLE], $_GET[KEY_SORT_DATA]);
                keepGETParams();
                
                ?>
                <input type="hidden" name="<?php echo KEY_SORT_DATA; ?>" value="
                <?php
                // Controlla se attualmente si sta ordinando il contenuto per la data
                if (isset($sort_data))
                {
                    echo $sort_data;
                }
                else
                {
                    echo '0';
                }
                ?>">
                <button class="btn btn-link" type="submit">
                    Data
                    <?php
                    // Stampa la freccia giusta di ordinamento se l'ordinamento corrente è per titoli, NOTA: lo switch-case non è uguale a quello dei titoli, qui è (1, -1)
                    if (isset($sort_data))
                    {
                        switch ($sort_data)
                        {
                            // 1
                            case 1:
                                echo '<i class="material-icons">arrow_upward</i>';
                                break;
                            // -1
                            case -1:
                                echo '<i class="material-icons">arrow_downward</i>';
                                break;
                            default:
                                break;
                        }
                    }
                    ?>
                </button>
            </form>
        </div>
    </div>
    <!-- Filtri Homepage, div hidden by default (non c'entra niente la parola 'mobile' è soltanto che era stata pensata in modo differente all'inizio ed è rimasta chiamata così) -->
    <div id="homepage-mobile-filters">
        <!-- Pulsante per chiudere la div dei filtri (x rossa in alto) -->
        <a id="close-filters" class="btn-danger" onclick="this.parentNode.style.display = 'none'"><i class="material-icons">close</i></a>
        <!-- Form dei Filtri -->
        <form id="homepage-mobile-filter-form" action="homepage.php" method="GET">
            <?php
            // Tiene nell'URL i valori GET derivati dall'ordinamento, resetta quelli derivati dai filtri (verranno cambiati e reimpostati)
            unset($_GET[KEY_FILTER_TITOLO]);
            unset($_GET[KEY_FILTER_PROVENIENZA]);
            unset($_GET[KEY_FILTER_STELLE]);
            unset($_GET[KEY_FILTER_START_DATE]);
            unset($_GET[KEY_FILTER_END_DATE]);
            unset($_GET[KEY_FILTER_COMUNI]);
            
            keepGETParams();
            
            ?>
            <!-- Filtra per Titolo -->
            Titolo
            <input name="<?php echo KEY_FILTER_TITOLO; ?>" class="form-control" type="text"
                   placeholder="cerca titoli..." maxlength="250"
                   value="<?php if (isset($ti)) echo $ti; ?>">
            <!-- Filtra per Provenienza -->
            Provenienza
            <select name="<?php echo KEY_FILTER_PROVENIENZA; ?>" class="form-control" title="Stelle">
                <!-- Valore 0: permetti tutte le provenienze -->
                <option value="0">Tutte</option>
                <?php
                // Ottiene la lista delle provenienze dal db e le mette in un dropdown
                $q = "SELECT id, nome FROM provenienza";
                $r = $dbc->query($q);
                
                while ($row = $r->fetch_row())
                {
                    // controlla se si sta già applicando il filtro e nel caso mantiene il valore applicato al filtro
                    $selected = '';
                    
                    if ($row[0] == $prov)
                    {
                        $selected = 'selected="selected"';
                    }
                    
                    echo '<option value="' . $row[0] . '" ' . $selected . ' >' . $row[1] . '</option>';
                }
                ?>
            </select>
            <!-- Filtra per Stelle -->
            Stelle
            <select name="<?php echo KEY_FILTER_STELLE; ?>" class="form-control" title="Stelle">
                <?php
                // crea un dropdown con 4 valori: [Tutte, 1, 2, 3]
                for ($i = 0; $i <= 3; $i++)
                {
                    // Valore 0: permetti tutte le stelle
                    $value = $i == 0 ? 'Tutte' : $i;
                    
                    // controlla se si sta già applicando il filtro e nel caso mantiene il valore applicato al filtro
                    $selected = '';
                    
                    if ($stelle == $i)
                    {
                        $selected = 'selected="selected"';
                    }
                    
                    echo '<option value="' . $i . '" ' . $selected . ' >' . $value . '</option>';
                }
                ?>
            </select>
            <!-- Filtra per Data -->
            Data Iniziale
            <input name="<?php echo KEY_FILTER_START_DATE; ?>" class="form-control" type="date" title="Data iniziale"
                   value="<?php if (isset($sd)) echo $sd ?>">
            Data Finale
            <input name="<?php echo KEY_FILTER_END_DATE; ?>" class="form-control" type="date" title="Data finale"
                   value="<?php if (isset($ed)) echo $ed ?>">
            <!-- Filtra per Comuni Destinatari -->
            Comuni
            <div class="form-control">
                <?php
                
                // ottiene la lista ordinata dei comuni dal db e crea una lista di checkbox
                $q = "SELECT * FROM `comune` ORDER BY nome ASC";
                $comuni = $dbc->query($q);
                
                while($cap = $comuni->fetch_row()) {
                    // controlla i comuni per cui si sta già filtrando e nel caso li mantiene selezionati nei filtri
                    
                    // $var =      if      ?     then         :      else
                    // $var = <condizione> ? <se soddisfatta> : <se non soddisfatta>
                    // qui è concatenato in   $var = if ? (if ? then : else) : else
                    $unchecked = (isset($com) && $com != null && !empty($com)) ? (!in_array($cap[0], $com) ? '' : 'checked') : 'checked';
                    // filter_comuni[]  perchè va considerata come array, così in GET sarà {"filter_comuni":[23457, 23456]} al posto di {"filter_comuni":23456}
                    echo '<div><input type="checkbox" name="' . KEY_FILTER_COMUNI . '[]" value="' . $cap[0] . '" ' . $unchecked . '/> ' . $cap[1] . '' . '</div>';
                }
                
                ?>
            </div>
            <!-- Pulsanti Applica e Resetta Filtri-->
            <div class="btn-group" role="group">
                <!-- Pulsante Reset Filtri: resetta i form dei filtri ai valori base -->
                <input class="btn btn-danger" type="reset" value="Reset" onclick="return resetForm(this.form);">
                <!-- Pulsante Submit (applica) Filtri -->
                <input id="filter-btn" class="btn btn-primary" type="submit"
                       value="Filtra"/>
            </div>
        </form>
    </div>
    <!-- HomePage Content Wrapper: wrapper del contenitore delle notifiche -->
    <div class="homepage-content-wrapper">
        <!-- Contenitore delle notifiche -->
        <div class="homepage-content">
            <!-- Notifiche -->
            <!-- Il codice HTML delle varie notifiche verrà aggiunto dagli script PHP -->
            <?php
            foreach ($notifiche as $notifica)
                echo $notifica;
            ?>
        </div>
    </div>
</div>

</body>
</html>
