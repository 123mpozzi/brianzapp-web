<?php
/**
 * Created by PhpStorm.
 * User: Simone
 * Date: 04/03/2018
 * Time: 11:08
 */

include("../config.php");
include("../utils.php");
$comuniScelti =  bho // array presi dall app in base alle preferenze da modificare in base all interfacciamento di android con il server
$notifiche = array();
$q = "SELECT * FROM notifiche WHERE provenienza = ?";//seleziono le notizie dei comuni scelti
foreach($comuniScelti as $comune) {
    $stmt = executePrep($dbc, $q, "s", [$comune]);
    $notifiche += $stmt->get_result();
}
foreach($notifiche as $notifica) {
    echo json_encode($notifica);
}


?>