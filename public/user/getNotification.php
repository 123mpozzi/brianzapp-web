<?php

include("../config.php");
include("../utils.php");

// array presi dall app in base alle preferenze da modificare in base all interfacciamento di android con il server
$comuniScelti =  $_GET["comuni"];
$notifiche = array();

// seleziono le notizie dei comuni scelti
$q = "SELECT * FROM notifica n JOIN notifica_comune c ON n.id = c.id_notifica WHERE c.cap_comune = ?";

foreach($comuniScelti as $comune) {
    $stmt = executePrep($dbc, $q, "s", [$comune]);
    $notifiche += $stmt->get_result();
}

foreach($notifiche as $notifica) {
    $listaId = array();
    
    if (in_array($notifica["id"], $listaId)) {
        $listaId += $notifica["id"];
        echo json_encode($notifica);
    }
}

?>
