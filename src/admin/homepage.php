<?php

$page_title = "HomePage";

include("../auth.php");

// Filtri

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

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // Titolo
    $errors = [];
    
    $ti = getGetString($dbc, $errors, KEY_FILTER_TITOLO);
    
    if ($ti != null)
    {
        if (empty($errors))
        {
            $filter_titolo_1 = 'lower(n.titolo)';
            $filter_titolo_2 = "'%" . $ti . "%'";
        }
        else
            reportErrors($errors);
    }
    
    
    // Provenienza
    $errors = [];
    
    $prov = getGetString($dbc, $errors, KEY_FILTER_PROVENIENZA);
    
    if ($prov != null and $prov != 0)
    {
        if (empty($errors))
        {
            $filter_provenienza_1 = 'p.id';
            $filter_provenienza_2 = $prov;
        }
        else
            reportErrors($errors);
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
            reportErrors($errors);
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
            reportErrors($errors);
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
            reportErrors($errors);
    }
    
    // fetch max date
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
            // TODO: provare (db vuoto)
            $filter_enddate = '2020-12-30 06:33:45';
        }
    }
    
    // fetch min date
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
            switch ($so_ti)
            {
                case 0:
                case 1:
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
            reportErrors($errors);
    }
    
    // Stelle
    $errors = [];
    
    $so_st = getGetString($dbc, $errors, KEY_SORT_STELLE);
    
    if ($so_st != null)
    {
        if (empty($errors))
        {
            switch ($so_st)
            {
                case 0:
                case 1:
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
            reportErrors($errors);
    }
    
    // Data
    $errors = [];
    
    $so_da = getGetString($dbc, $errors, KEY_SORT_DATA);
    
    if ($so_da != null)
    {
        if (empty($errors))
        {
            switch ($so_da)
            {
                case 0:
                case 1:
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
            reportErrors($errors);
    }
}

$q = "SELECT n.titolo, n.descrizione, n.stelle, n.pdf, n.colore, n.data, p.nome AS provenienza FROM notifica n INNER JOIN provenienza p ON n.id_provenienza=p.id WHERE ? LIKE ? AND ? LIKE ? AND ? LIKE ? AND n.data BETWEEN '?' AND '?' ORDER BY ";

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


$q = interpolateQuery($q, [$filter_titolo_1, $filter_titolo_2, $filter_provenienza_1, $filter_provenienza_2,
    $filter_stelle_1, $filter_stelle_2, $filter_startdate, $filter_enddate]);

$stmt = $dbc->query($q);

/*$paging = getPagingFromInt($stmt->num_rows);
$pages = $paging['p'];
$start = $paging['s'];
$display = $paging['d'];*/

$notifiche = [];

if ($stmt)
{
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
    {
        // $titolo, $descrizione, $stelle, $data, $provenienza, $colore = 'FFFFFF', $pdf = null
        $notifiche[] = genNotifica($row['titolo'], $row['descrizione'], $row['stelle'], $row['data'], $row['provenienza'], $row['colore'], $row['pdf']);
    }
    
    $stmt->close();
}

?>
<body class="gradient-background" data-spy="scroll" data-target=".navbar" data-offset="60">

<div class="homepage-container custom-card">
    <!-- HomePage Header: titolo e pulsante logout + filtri -->
    <div class="homepage-header">
        <!-- TitleBar: titolo + pulsante logout -->
        <div class="homepage-titlebar">
            <h1>Proci</h1>
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
        <div class="homepage-searchbar">
            <i class="material-icons">arrow_upward</i>
            <i class="material-icons">arrow_downward</i>
            <form id="sort_titolo" action="homepage.php" method="GET">
                <?php
                // Keep already submitted GET parameters (do not reset the url)
                
                unset($_GET[KEY_SORT_TITOLO]);
                unset($_GET[KEY_SORT_STELLE]);
                unset($_GET[KEY_SORT_DATA]);
                
                foreach($_GET as $key => $value)
                {
                    echo '<input type="hidden" name="'. $key .'" value="'. $value .'">';
                }
                ?>
                <input type="hidden" name="<?php echo KEY_SORT_TITOLO; ?>" value="
                <?php
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
                if (isset($sort_titolo))
                {
                    switch ($sort_titolo)
                    {
                        case -1:
                            echo '<i class="material-icons">arrow_upward</i>';
                            break;
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
            <form id="sort_stelle" action="homepage.php" method="GET">
                <?php
                // Keep already submitted GET parameters (do not reset the url)
    
                unset($_GET[KEY_SORT_TITOLO]);
                unset($_GET[KEY_SORT_STELLE]);
                unset($_GET[KEY_SORT_DATA]);
    
                foreach($_GET as $key => $value)
                {
                    echo '<input type="hidden" name="'. $key .'" value="'. $value .'">';
                }
                ?>
                <input type="hidden" name="<?php echo KEY_SORT_STELLE; ?>" value="
                <?php
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
                if (isset($sort_stelle))
                {
                    switch ($sort_stelle)
                    {
                        case 1:
                            echo '<i class="material-icons">arrow_upward</i>';
                            break;
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
            <form id="sort_data" action="homepage.php" method="GET">
                <?php
                // Keep already submitted GET parameters (do not reset the url)
                
                unset($_GET[KEY_SORT_TITOLO]);
                unset($_GET[KEY_SORT_STELLE]);
                unset($_GET[KEY_SORT_DATA]);
    
                foreach($_GET as $key => $value)
                {
                    echo '<input type="hidden" name="'. $key .'" value="'. $value .'">';
                }
                ?>
                <input type="hidden" name="<?php echo KEY_SORT_DATA; ?>" value="
                <?php
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
                if (isset($sort_data))
                {
                    switch ($sort_data)
                    {
                        case 1:
                            echo '<i class="material-icons">arrow_upward</i>';
                            break;
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
    <!-- Homepage Filters for Mobiles, hidden by default -->
    <div id="homepage-mobile-filters">
        <form id="homepage-mobile-filter-form" action="homepage.php" method="GET">
            <?php
            // Keep already submitted GET parameters (do not reset the url)
    
            unset($_GET[KEY_FILTER_TITOLO]);
            unset($_GET[KEY_FILTER_PROVENIENZA]);
            unset($_GET[KEY_FILTER_STELLE]);
            unset($_GET[KEY_FILTER_START_DATE]);
            unset($_GET[KEY_FILTER_END_DATE]);
            
            foreach($_GET as $key => $value)
            {
                echo '<input type="hidden" name="'. $key .'" value="'. $value .'">';
            }
            ?>
            Titolo
            <input name="<?php echo KEY_FILTER_TITOLO; ?>" class="form-control" type="text"
                   placeholder="cerca titoli..." maxlength="250"
                   value="<?php if (isset($ti)) echo $ti; ?>">
            Provenienza
            <select name="<?php echo KEY_FILTER_PROVENIENZA; ?>" class="form-control" title="Stelle">
                <option value="0">Tutte</option>
                <?php
                $q = "SELECT id, nome FROM provenienza";
                $r = $dbc->query($q);
                
                while ($row = $r->fetch_row())
                {
                    $selected = '';
                    
                    if ($row[0] == $prov)
                    {
                        $selected = 'selected="selected"';
                    }
                    
                    echo '<option value="' . $row[0] . '" ' . $selected . ' >' . $row[1] . '</option>';
                }
                
                //mysqli_close($dbc);
                
                ?>
            </select>
            Stelle
            <select name="<?php echo KEY_FILTER_STELLE; ?>" class="form-control" title="Stelle">
                <?php
                for ($i = 0; $i <= 3; $i++)
                {
                    $value = $i == 0 ? 'Tutte' : $i;
                    
                    $selected = '';
                    
                    if ($stelle == $i)
                    {
                        $selected = 'selected="selected"';
                    }
                    
                    echo '<option value="' . $i . '" ' . $selected . ' >' . $value . '</option>';
                }
                
                //mysqli_close($dbc);
                
                ?>
            </select>
            Data Iniziale
            <input name="<?php echo KEY_FILTER_START_DATE; ?>" class="form-control" type="date" title="Data iniziale"
                   value="<?php if (isset($sd)) echo $sd ?>">
            Data Finale
            <input name="<?php echo KEY_FILTER_END_DATE; ?>" class="form-control" type="date" title="Data finale"
                   value="<?php if (isset($ed)) echo $ed ?>">
            <div class="btn-group" role="group">
                <input class="btn btn-danger" type="reset" value="Reset" onclick="return resetForm(this.form);">
                <input id="filter-btn" class="btn btn-primary" type="submit"
                       value="Filtra"/>
            </div>
        </form>
    </div>
    <!-- HomePage Content Wrapper: wrapper del contenitore delle notifiche -->
    <div class="homepage-content-wrapper">
        <!-- Contenitore delle notifiche -->
        <div class="homepage-content">
            <?php
            foreach ($notifiche as $notifica)
                echo $notifica;
            ?>
            <!-- Notifiche -->
            <!-- Il codice delle varie notifiche verrà aggiunto dagli script PHP -->
        </div>
    </div>
</div>

</body>
</html>
