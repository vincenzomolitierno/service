<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/03/2021
 * Time: 07:25
 */

/*
 * wrapper per la chiamata GET
 * - http://wbpate-test.dipvvf.it/api/ATe/GetAssociazioneByRfidCode
 * - Ricerca tessera ATe, inserendo come chiave di ricerca il codice identificativo
 *
 * INFO API WBPATE
 * - http://wbpate-test.dipvvf.it/swagger/index.html
 */

require_once("lib/Configuration.class.php");
require_once("lib/HttpResponse.class.php");
require_once("lib/Authorization.class.php");
require_once("lib/Log.class.php");
require_once("lib/WbpateWrapper.class.php");
require_once("lib/Notifiche.class.php");

if(Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])){
    $arrHttpResponse = WbpateWrapper::GetAssociazioneByRfidCode($_GET["rfidCode"]);
    Log::add($_GET["rfidCode"], Authorization::AUTHORIZED, json_encode($arrHttpResponse));

    header('HTTP/1.1 '.$arrHttpResponse[HttpResponse::CODICE_ESITO]);
    header('Content-type: application/json');

    if(!$arrHttpResponse[HttpResponse::ERRORE]){
        echo json_encode($arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO]);
        if($arrHttpResponse[HttpResponse::CODICE_ESITO] == HttpResponse::INTERNAL_SERVER_ERROR){
            // Notifiche::inviaNotifiche("(".$_GET["rfidCode"]."). ".json_encode($arrHttpResponse));
        }
    }
    else{
        echo json_encode($arrHttpResponse);
        Notifiche::inviaNotifiche("(".$_GET["rfidCode"]."). ".json_encode($arrHttpResponse));
    }
}
else{
    $codice_esito = HttpResponse::FORBIDDEN;
    $descrizione_esito = Authorization::ERROR_NOT_AUTHORIZED.": ".$_SERVER['REMOTE_ADDR'];
    $errore = true;

    $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);
    Log::add($_GET["rfidCode"], Authorization::ERROR_NOT_AUTHORIZED,$json);

    Notifiche::inviaNotifiche("(".$_GET["rfidCode"]."). ".$json);

    header('HTTP/1.1 '.HttpResponse::FORBIDDEN);
    header('Content-type: application/json');
    echo $json;
}



