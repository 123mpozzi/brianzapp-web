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
 * Get a get string from a key.
 *
 * @param      $dbc - the database connection
 * @param      $errors - the REFERENCE to the array in which errors will be appended in
 * @param      $key - the key that will get us the get value
 * @param null $re_pattern - optional regex checking for the get value returned from the key. null > do not check for
 *     matches
 *
 * @return null|string - null if errors, otherwise the get string returned from the key
 */

function getGetString($dbc, &$errors, $key, $re_pattern = null)
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
            
            // SQL injection check (maybe they bypass the html pattern protection by modifying the client)
            if ($re_pattern != null)
            {
                // special value to validate emails
                if ($re_pattern == 'email')
                {
                    if (!filter_var($get_value, FILTER_VALIDATE_EMAIL))
                    {
                        $errors[] = "[SQL Injection threat] Invalid email format";
                        return null;
                    }
                }
                // if string does not matches the regex pattern
                else if (!preg_match('/' . $re_pattern . '/', $get_value))
                {
                    $errors[] = '[SQL Injection threat] String: "' . $get_value . '" does not match regex: ' . $re_pattern;
                    return null;
                }
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
 * Get a get string from a key.
 *
 * @param      $dbc - the database connection
 * @param      $errors - the REFERENCE to the array in which errors will be appended in
 * @param      $key - the key that will get us the get value
 * @param null $re_pattern - optional regex checking for the get value returned from the key. null > do not check for
 *     matches
 *
 * @return null|string - null if errors, otherwise the get string returned from the key
 */

function getPostString($dbc, &$errors, $key, $re_pattern = null)
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
            
            // SQL injection check (maybe they bypass the html pattern protection by modifying the client)
            if ($re_pattern != null)
            {
                // special value to validate emails
                if ($re_pattern == 'email')
                {
                    if (!filter_var($post_value, FILTER_VALIDATE_EMAIL))
                    {
                        $errors[] = "[SQL Injection threat] Invalid email format";
                        return null;
                    }
                }
                // if string does not matches the regex pattern
                else if (!preg_match('/' . $re_pattern . '/', $post_value))
                {
                    $errors[] = '[SQL Injection threat] String: "' . $post_value . '" does not match regex: ' . $re_pattern;
                    return null;
                }
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
 * Creates an alert dialog that report the errors.
 *
 * @param $errors - the errors to report
 */
function reportErrors(&$errors)
{
    // TODO: check if $errors is splitted by ..., use unpacking?
    alert("warning", "Error!", "The following error(s) occurred:", $errors, "Please try again.");
}

/**
 * Generates a Bootstrap alert with the given data
 *
 * <p>
 * Links:
 * <p>
 * [Bootstrap 4 Button Types](https://getbootstrap.com/docs/4.0/components/buttons/)
 *
 *
 * @param string $alertType the type of the alert: <ul><li>success</li> <li>info</li> <li>warning</li> <li>danger</li> <li>primary</li> <li>secondary</li> <li>dark</li> <li>light</b>
 * @param null   $title the title of the alert (will be <b>on top</b> of the alert and <b>h4</b> size)
 * @param array  ...$messages the messages to print in the alert below the title, they are splitted into paragraphs
 *
 * @return string The resulting div element
 */
function alert(string $alertType, $title = null, ...$messages)
{
    $result = "";
    
    $title = $title = null ? ucwords($alertType) : '<h4 class="alert-heading">' . $title . '</h4>';
    
    $result .= '<div class="custom-alert-container"><div class="custom-alert custom-alert-' . $alertType . '">' . $title;
    
    for ($i = 0; $i < count($messages); $i++)
    {
        $spacing = $i == 0 ? '' : 'class="mb-0"';
        
        // if an argument is an array, iterate through it or it will be printed 'Array' as a message.
        if (is_array($messages[$i]))
        {
            $result .=  '<p></p>';
            
            foreach ($messages[$i] as $array_row)
            {
                $result .=  '<p ' . $spacing . '>' . $array_row . '</p>';
            }
            
            //echo '<p ' . $spacing . '>' . json_encode($messages[$i]) . '</p>';
        }
        else
        {
            $result .=  '<p ' . $spacing . '>' . $messages[$i] . '</p>';
        }
    }
    
    $result .=  '</div></div>';
    
    return $result;
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
 * @param string $alertType the type of the alert: <ul><li>success</li> <li>info</li> <li>warning</li> <li>danger</li> <li>primary</li> <li>secondary</li> <li>dark</li> <li>light</b>
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
            $result .=  '<p></p>';
            
            foreach ($messages[$i] as $array_row)
            {
                $result .=  '<p ' . $spacing . '>' . $array_row . '</p>';
            }
            
            //echo '<p ' . $spacing . '>' . json_encode($messages[$i]) . '</p>';
        }
        else
        {
            $result .=  '<p ' . $spacing . '>' . $messages[$i] . '</p>';
        }
    }
    
    $result .=  '</div>';
    
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
            if($stmt->execute())
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
 * Generates a link to use in navigation.php
 *
 * @param $script_name 'register_users.php'
 * @param $relative_url 'user/register_users.php'
 * @param $link_title  - Registra Utente
 */
function genNavLink($script_name, $relative_url, $link_title)
{
    echo '<li class="';
    
    // <!--https://stackoverflow.com/a/16821093-->
    // check if it the active sidebar link
    if (basename($_SERVER['SCRIPT_NAME']) == $script_name)
        echo 'active';
    
    echo '"><a href="';
    
    // link href
    echo BASE_URL . $relative_url;
    
    // string displayed in the sidebar
    echo '">'. $link_title . '</a></li>';
}

/**
 * Returns the true query (without ? as params) executed by the prepared statement.
 *
 * https://stackoverflow.com/a/1376838
 *
 * Replaces any parameter placeholders in a query with the value of that
 * parameter. Useful for debugging. Assumes anonymous parameters from
 * $params are are in the same order as specified in $query
 *
 * @param string $query The sql query with parameter placeholders
 * @param array $params The array of substitution parameters
 * @return string The interpolated query
 */
function interpolateQuery($query, $params) {
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
    
        #trigger_error('replaced '.$count.' keys');
    
        return $query;
    }
    catch (Exception $e)
    {
        // interpolating error
        return '';
    }
}

function hash_pwd($password)
{
	return substr(hash('sha256', $password), 0, 20);
}

function delete_cookie () {
    if (isset($_COOKIE['carrello'])) {
        unset($_COOKIE['carrello']);
        setcookie('carrello', '', time() - 3600, '/'); // empty value and old timestamp
    }
}

function genNotifica($titolo, $descrizione, $stelle, $data, $provenienza, $colore = 'FFFFFF', $pdf = null)
{
    $phpdate = strtotime( $data );
    $data = date( 'Y-m-d H:i:s', $phpdate );
    
    $amount = $stelle;
    $stelle = '';
    
    for ($x = 1; $x <= $amount; $x++)
    {
        $stelle .= ' <i class="material-icons">star</i>';
    }
    
    if($pdf == null)
    {
        $pdf = '';
    }
    else
    {
        $pdf = '<div class="allegato">
                    <button class="btn btn-dark">
                        <i class="material-icons">attach_file</i>
                    </button>
               </div>';
    }
    
    return '<div class="homepage-item alert-danger">
                <div class="flex-row-space-between">
                    <!-- Titolo -->
                    <h3>' . $titolo . '</h3>
                    <!-- Data -->
                    <p>' . $data . '</p>
                </div>
                <div class="flex-row-space-between">
                    <!-- Stelle -->
                    <div class="priority alert-danger">
                    ' . $stelle . '
                    </div>
                    <div>
                        <p>
                            ' . $provenienza . '
                        </p>
                    </div>
                </div>

                <!-- Descrizione -->
                <p>
                    ' . $descrizione . '
                </p>
                <!-- Allegato -->
                ' . $pdf . '
            </div>';
}
