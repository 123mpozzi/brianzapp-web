<?php

/*
 * #------------#
 * # Utils.PHP  #
 * #------------#
 *
 *
 * Questo script contiene tutte le funzioni di utilità
 *
 */

/**
 * Fetch a GET string given a key.
 *
 * @param mysqli   $dbc Database connection
 * @param string[] $errors REFERENCE to the array in which errors will be appended in
 * @param string   $key Key from which the value will be retrieved
 *
 * @return null|string Null if errors, otherwise the GET string retrieved from the given key
 */
function getGetString($dbc, &$errors, $key)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        // if parameters are valid
        if ($key == null)
        {
            $errors[] = "key null: " . $key;
            return null;
        }
        else if (empty($key))
        {
            $errors[] = "key is an empty string: " . $key;
            return null;
        }
        else if (!isset($_GET[$key]))
        {
            $errors[] = "key is not set as get data: " . $key;
            return null;
        }
        else
        {
            $get_value = $_GET[$key];
            
            if ($get_value == null)
            {
                $errors[] = 'A get value was null: ' . $key;
                return null;
            }
            else if (empty($get_value))
            {
                $errors[] = 'A get value was empty: ' . $key;
                return null;
            }
            
            $result = mysqli_real_escape_string($dbc, trim($get_value));
            return $result;
        }
    }
    else
    {
        $errors[] = "Server request method was not get";
        return null;
    }
}

/**
 * Fetch a POST string given a key.
 *
 * @param mysqli   $dbc - Database connection
 * @param string[] $errors REFERENCE to the array in which errors will be appended in
 * @param string   $key Key from which the value will be retrieved
 *
 * @return null|string Null if errors, otherwise the POST string retrieved from the given key
 */
function getPostString($dbc, &$errors, $key)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        // if parameters are valid
        if ($key == null)
        {
            $errors[] = "key null: " . $key;
            return null;
        }
        else if (empty($key))
        {
            $errors[] = "key is an empty string: " . $key;
            return null;
        }
        else if (!isset($_POST[$key]))
        {
            $errors[] = "key is not set as post data: " . $key;
            return null;
        }
        else
        {
            $post_value = $_POST[$key];
            
            if ($post_value == null)
            {
                $errors[] = 'A post value was null: ' . $key;
                return null;
            }
            else if (empty($post_value))
            {
                $errors[] = 'A post value was empty: ' . $key;
                return null;
            }
            
            $result = mysqli_real_escape_string($dbc, trim($post_value));
            return $result;
        }
    }
    else
    {
        $errors[] = "Server request method was not post";
        return null;
    }
}

/**
 * Logs and optionally displays errors.
 *
 * @param string[]|string $alert Var that will contain the HTML alert div body
 * @param string[]        $errors Errors to report
 * @param bool            $display Whether to display the alert to the user or not; true by default
 * @param string          $alertType Type of the alert (danger, warning, success, info, ...); 'warning' by default
 */
function reportErrors(&$alert, $errors, bool $display = null, string $alertType = null)
{
    if ($display == null)
        $display = true;
    
    if ($alertType == null)
        $alertType = 'warning';
    
    // Se l'alert è da mostrare
    if ($display)
        $alert = alertEmbedded($alertType, "Errore!", "Si sono verificati i seguenti errori: ", json_encode($errors), "Per favore riprova un'altra volta.");
    
    $log_folder = $_SERVER["DOCUMENT_ROOT"] . '/WebApp/private/logs/errors/';
    
    logData($log_folder . date('d-m-Y') . '.log', gmdate('d-m-Y H:i:s') . json_encode($errors));
}

/**
 * Generates a custom alert using Bootstrap colors with the given data.
 * This alert type must be embedded somewhere into the page (it's not standalone).
 *
 * <p>
 * Links:
 * <p>
 * [Bootstrap 4 Button Types](https://getbootstrap.com/docs/4.0/components/buttons/)
 *
 *
 * @param string $alertType the type of the alert: <ul><li>success</li> <li>info</li> <li>warning</li> <li>danger</li>
 *     <li>primary</li> <li>secondary</li> <li>dark</li> <li>light</b>
 * @param null   $title the title of the alert (will be <b>on top</b> of the alert and <b>h4</b> size)
 * @param array  ...$messages the messages to print in the alert below the title, they are splitted into paragraphs
 *
 * @return string The resulting div element
 */
function alertEmbedded(string $alertType, $title = null, ...$messages)
{
    $result = "";
    
    $title = $title = null ? ucwords($alertType) : '<h4 class="alert-heading">' . $title . '</h4>';
    
    $result .= '<div class="alert-' . $alertType . '">' . $title;
    
    for ($i = 0; $i < count($messages); $i++)
    {
        $spacing = $i == 0 ? '' : 'class="mb-0"';
        
        // if an argument is an array, iterate through it or it will be printed 'Array' as a message.
        if (is_array($messages[$i]))
        {
            $result .= '<p></p>';
            
            foreach ($messages[$i] as $array_row)
            {
                $result .= '<p ' . $spacing . '>' . $array_row . '</p>';
            }
        }
        else
        {
            $result .= '<p ' . $spacing . '>' . $messages[$i] . '</p>';
        }
    }
    
    $result .= '</div>';
    
    return $result;
}

/**
 * Executes a prepared statement query and returns the statement object.
 * <p>
 * It handles all possible prepared statement errors.
 *
 * <p>
 * Example usage:
 *
 * ```php
 * $q = "UPDATE users SET name=?, surname=?, login=?, admin=? WHERE id_user=? LIMIT 1";
 * $stmt = executePrep($dbc, $q, "ssssi", [$fn, $ln, $e, $a, $id]);
 * ```
 *
 * <p>
 * Inspired by:
 * <p>
 * [MySQLi error handling](https://stackoverflow.com/a/2553892)
 * <p>
 * [PHP Argument Unpacking](https://wiki.php.net/rfc/argument_unpacking)
 * <p>
 * [PHP Argument Unpacking in bind_params](https://stackoverflow.com/a/43134929)
 *
 * @param mysqli $dbc the database connection
 * @param string $query the MySQL query to execute
 * @param string $type <b>i</b> - integer, <b>d</b> - double, <b>s</b> - string, <b>b</b> - BLOB
 * @param array  $params the parameters to bind into the query
 *
 * @return mysqli_stmt the statement object
 *
 * @author Michele Pozzi <MICHELE.POZZI@ISSGREPPI.IT>
 * @version 1.0
 */
function executePrep(mysqli $dbc, string $query, string $type, array $params)
{
    // prepare() can fail because of syntax errors, missing privileges, ....
    if ($stmt = $dbc->prepare($query))
    {
        /*
         * Prepared Statement Errors Reporting:
         * bind_param() can fail because the number of parameter doesn't match the placeholders in the statement
         * or there's a type conflict(?), or ....
         *
         *
         * PHP argument unpacking:
         * https://wiki.php.net/rfc/argument_unpacking
         * https://stackoverflow.com/a/43134929
         */
        if ($stmt->bind_param($type, ...$params))
        {
            // execute() can fail for various reasons. And may it be as stupid as someone tripping over the network cable
            // 2006 "server gone away" is always an option
            if ($stmt->execute())
            {
                return $stmt;
            }
            else
            {
                die('execute() failed: ' . htmlspecialchars($dbc->error));
            }
        }
        else
        {
            // again execute() is useless if you can't bind the parameters. Bail out somehow.
            die('bind_param() failed: ' . htmlspecialchars($dbc->error));
        }
    }
    else
    {
        // and since all the following operations need a valid/ready statement object
        // it doesn't make sense to go on
        die('prepare() failed: ' . htmlspecialchars($dbc->error));
    }
}

/**
 * Returns the true query (without ? as params) executed by the prepared statement.
 *
 * <a href="https://stackoverflow.com/a/1376838">Function taken from here</a>
 *
 * Replaces any parameter placeholders in a query with the value of that
 * parameter. Useful for debugging. Assumes anonymous parameters from
 * $params are are in the same order as specified in $query
 *
 * @param string $query The sql query with parameter placeholders
 * @param array  $params The array of substitution parameters
 *
 * @return string The interpolated query
 */
function interpolateQuery($query, $params)
{
    try
    {
        $keys = array();
        
        # build a regular expression for each parameter
        foreach ($params as $key => $value)
        {
            if (is_string($key))
            {
                $keys[] = '/:' . $key . '/';
            }
            else
            {
                $keys[] = '/[?]/';
            }
        }
        
        $query = preg_replace($keys, $params, $query, 1, $count);
        
        return $query;
    }
    catch (Exception $e)
    {
        // interpolating error
        return 'interpolating error - interpolateQuery()';
    }
}

/**
 *
 * ```html
 *
 * <div class="homepage-item alert-danger">
 *     <div class="flex-row-space-between">
 *         <!-- Titolo -->
 *         <h3>Titolo Notizia - testo corto</h3>
 *         <!-- Data -->
 *         <p><b>23/07/2019</b></p>
 *     </div>
 *     <div class="flex-row-space-between">
 *         <!-- Stelle -->
 *         <div class="priority alert-danger">
 *             <i class="material-icons">star</i>
 *             <i class="material-icons">star</i>
 *             <i class="material-icons">star</i>
 *         </div>
 *         <!-- Provenienza -->
 *         <div>
 *             <p>
 *                 <b>Provenienza:</b> Protezione Civile Casate
 *             </p>
 *         </div>
 *     </div>
 *
 *     <div class="flex-row-space-between">
 *          <p>
 *              <b>Comuni Destinatari:</b> Missaglia
 *          </p>
 *     </div>
 *
 *     <!-- Descrizione -->
 *         <p>Questo è il testo della notizia.
 *         </p>
 *     <div class="allegato">
 *         <button class="btn btn-dark">
 *             <i class="material-icons">attach_file</i>
 *         </button>
 *     </div>
 * </div>
 * ```
 *
 *
 * @param string $titolo Titolo
 * @param string $descrizione Descrizione
 * @param int    $stelle Stelle (livello di importanza) da attribuire
 * @param string $data Data dell'invio
 * @param string $provenienza Mittente
 * @param string $pdf Percorso dell'allegato
 * @param string $comuni Stringa contenente la lista dei comuni destinatari
 * @param array  $notifiche_json REFERENCE all'array contenente i dati delle notifiche che verranno inviati ai client
 *     android via JSON
 *
 * @return string Codice HTML che rappresenta la notifica
 */
function genNotifica($titolo, $descrizione, $stelle, $data, $provenienza, $pdf, $comuni, &$notifiche_json)
{
    // descrizione è opzionale
    if ($descrizione == null)
        $descrizione = '';
    
    // ottieni data da stringa
    $phpdate = strtotime($data);
    $data = date('d-m-Y H:i:s', $phpdate);
    
    // genera le stelle
    $amount = $stelle;
    $stelle = '';
    
    for ($x = 1; $x <= $amount; $x++)
    {
        $stelle .= ' <i class="material-icons">star</i>';
    }
    
    $comuni_json = $comuni;
    // array to comma separated string
    $comuni = implode(', ', $comuni);
    
    // genera il collegamento all'allegato
    $pdf_link = BASE_URL . '../pdf/' . $pdf;
    $pdf = '<div class="allegato alert-warning">
                <a class="btn btn-dark" href="' . $pdf_link . '">
                    <i class="material-icons">attach_file</i>
                </a>
           </div>';
    
    $json_data = [];
    
    $json_data['titolo'] = $titolo;
    $json_data['descrizione'] = $descrizione;
    $json_data['data'] = $data;
    $json_data['stelle'] = $amount;
    $json_data['provenienza'] = $provenienza;
    $json_data['pdf'] = $pdf_link;
    $json_data['comuni'] = $comuni_json;
    
    array_push($notifiche_json, $json_data);
    
    return '<div class="homepage-item alert-warning">
                <div class="flex-row-space-between">
                    <!-- Titolo -->
                    <h3>' . $titolo . '</h3>
                    <!-- Data -->
                    <p><b>' . $data . '</b></p>
                </div>
                <div class="flex-row-space-between">
                    <!-- Stelle -->
                    <div class="priority">
                    ' . $stelle . '
                    </div>
                    <!-- Provenienza -->
                    <div>
                        <p>
                            <b>Provenienza:</b> ' . $provenienza . '
                        </p>
                    </div>
                </div>
                
                <!-- Comuni Destinatari -->
                <div class="flex-row-space-between">
                    <p>
                        <b>Comuni Destinatari:</b> ' . $comuni . '
                    </p>
                </div>

                <!-- Descrizione -->
                <p>
                    ' . $descrizione . '
                </p>
                <!-- Allegato -->
                ' . $pdf . '
            </div>';
}


function genLoadMore($sd, $days = 90)
{
    // Tiene nell\'URL i valori GET derivati dai filtri, resetta quelli derivati dall\'ordinamento (verranno cambiati e reimpostati)
    
    //unset($_GET[KEY_NOTIFICATIONS_LIMIT_GET]);
    //keepGETParams();
    
    return '<div class="homepage-item alert-info">
                <div class="flex-row-space-between">
                    <!-- Titolo -->
                    <h3>... Caricare altre notifiche?</h3>
                </div>

                <!-- Descrizione -->
                <p>
                    Per ora sono presenti le notifiche inviate negli ultimi <b>' . $days . ' giorni</b>.
                    <br>
                    Se si desidera caricarne altre, indicare il giorno di partenza da cui caricare le notifiche.
                </p>
                <!-- Allegato -->
                <form id="load-more-form" action="'. BASE_URL . 'admin/homepage.php" method="GET">
                    <input name="' . KEY_FILTER_START_DATE . '" class="form-control" type="date" title="Data da cui caricare" style="margin-bottom: 10px;">
                    <input id="filter-btn" class="btn btn-primary" type="submit"
                           value="Carica più notifiche"/>
                </form>
            </div>';
}

/**
 * Creates a log file and log given data.
 * If log file does not exists, the <i>file_put_contents()</i> function will create it.
 *
 * inspired by <a href="https://stackoverflow.com/a/8400489">this</a>
 *
 * @param string $file Path where the log file will be generated
 * @param mixed  $data Data to log
 * @param mixed  $first_row Whether the first row of the file should be different
 */
function logData($file, $data, $first_row = null)
{
    if (!is_dir(dirname($file)))
    {
        // dir doesn't exist, make it
        mkdir(dirname($file), 0777, true);
    }
    
    if ($first_row != null)
    {
        clearstatcache();
        if (!file_exists($file) or !filesize($file))
        {
            // the file is empty or does not exists yet
            file_put_contents($file, $first_row . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
    
    // Write the contents to the file,
    // using the FILE_APPEND flag to append the content to the end of the file
    // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
    file_put_contents($file, $data . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * Keep already submitted GET parameters (do not reset the url)
 *
 * Quando esegue l'ordinamento via GET non perde i valori GET dei filtri.
 * Crea degli input nascosti nel form contenenti i vecchi dati GET dei filtri, così vengono mandati assieme ai nuovi
 * dati di ordinamento.
 */
function keepGETParams()
{
    foreach ($_GET as $key => $value)
    {
        if (is_array($value))
        {
            foreach ($value as $aitem)
            {
                echo '<input type="hidden" name="' . $key . '[]" value="' . $aitem . '">';
            }
        }
        else
        {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
    }
}
