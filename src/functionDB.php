<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 16/02/2018
 * Time: 14:07
 */

function db_connettiti()
{
    try
    {
        //CONNESSIONE
        //$conn = new PDO("mysql:host=localhost;dbname=db_squadre", "root","");
        $host = "nomehost";
        $dbname = "nomedb";
        $username = "username";
        $password = "password";
        $conn = new PDO("mysql:host=$host;dbname=$dbname","username","password");
    }catch (PDOException $e){
        echo $e->getMessage() . "<br/>";
        echo "Connessione al server fallita. Impossibile procedere. Contattare ...";
        die();
    }
    //ATTIVAZIONE ECCEZIONI PER METODO QUERY
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function db_sanifica_parametro($conn, $parametro)
{
    return $conn->quote($parametro);
}

function db_select($conn, $query)
{
    $righe_estratte = array();
    try{	//2 inviare il comando
        foreach($conn->query($query) as $row){
            $righe_estratte[] = $row;
        }
    }
    catch (PDOException $e)//fallita mysqli_query
    {	echo $e->getMessage() . "<br/>";
        die();
    }
    return $righe_estratte;
}

function db_insert($conn, $comandoSQL)
{
    try
    {
        $conn->query($comandoSQL);
        return true;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage() . "<br/>";
        echo "Inserimento fallito ...";
        return false;
    }
    return false;
}
function db_delete($conn, $comandoSQL){
    try
    {

        $cancella_dati = $conn->exec($comandoSQL);
        //$conn->query($comandoSQL);
        return true;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage() . "<br/>";
        echo "Cancellazione fallita ...";
        return false;
    }
    return false;
}
function db_update($conn, $comandoSQL){
    try
    {
        $aggiorna_dati = $conn->exec($comandoSQL);
        //$conn->query($comandoSQL);
        return true;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage() . "<br/>";
        echo "Inserimento fallito ...";
        return false;
    }
    return false;
}

function db_close($conn)
{
    //CHIUDIAMO LA CONNESSIONE E LIBERIAMO LE RISORSE OCCUPATE ...
    $conn=null;
}

function updateFast($query){
    $conn = db_connettiti();
    $ris = db_update($conn, $query);
    db_close($conn);
    return $ris;
}
function deleteFast($query){
    $conn = db_connettiti();
    $ris = db_delete($conn, $query);
    db_close($conn);
    return $ris;
}
function selectFast($query){
    $conn = db_connettiti();
    $ris = db_select($conn, $query);
    db_close($conn);
    return $ris;
}
function insertFast($query){
    $conn = db_connettiti();
    $ris = db_insert($conn, $query);
    db_close($conn);
    return $ris;
}
?>