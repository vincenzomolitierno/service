<?php

require_once("lib/Authorization.class.php");
require_once("lib/AusinoWrapper.class.php");
require_once("lib/Log.class.php");


if (Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])) {

    $arrHttpResponse = AusinoWrapper::getDataFromField();

    Log::add('!!!', Authorization::AUTHORIZED, json_encode($arrHttpResponse));

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
        // Notifiche::inviaNotifiche("(".$_GET["rfidCode"]."). ".json_encode($arrHttpResponse));
    }    

    // $telemetryDataArray = array();

    // $ch = curl_init('https://aquaexportapi.azurewebsites.net/api/External/v1/export?FromDate=01%2F01%2F2021&ToDate=01%2F07%2F2021');
    // $arrayOptions = RestCurl::getCurlOption("GET", '', '', 'C291C4EAFC0571E90AB6D31C2FE37E16296405C188C38');
    // curl_setopt_array($ch, $arrayOptions);

    // $content = curl_exec($ch);
    // $err     = curl_errno($ch);
    // $errmsg  = curl_error($ch);

    // // Check if any error occurred
    // if (!empty($err)) {
    //     $telemetryDataArray[HttpResponse::CODICE_ESITO] = HttpResponse::INTERNAL_SERVER_ERROR;
    //     $telemetryDataArray[HttpResponse::DESCRIZIONE_ESITO] = 'Curl error: ' . $err . "-" . $errmsg;
    //     $telemetryDataArray[HttpResponse::ERRORE] = true;

    //     echo $telemetryDataArray;
    //     echo '<br>chiamata NON riuscita';

    // } else {
    //     // echo 'chiamata riuscita<br>';      
        
    //     $telemetryDataArray[HttpResponse::CODICE_ESITO] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $telemetryDataArray[HttpResponse::DESCRIZIONE_ESITO] = json_decode($content, true);
    //     $telemetryDataArray[HttpResponse::ERRORE] = false;

    //     echo $telemetryDataArray;       

    // }

    // curl_close($ch);

} else {
    $codice_esito = HttpResponse::FORBIDDEN;
    $descrizione_esito = Authorization::ERROR_NOT_AUTHORIZED.": ".$_SERVER['REMOTE_ADDR'];
    $errore = true;

    $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);
    Log::add('!!!', Authorization::ERROR_NOT_AUTHORIZED,$json);

    // Notifiche::inviaNotifiche("(".$_GET["rfidCode"]."). ".$json);

    header('HTTP/1.1 '.HttpResponse::FORBIDDEN);
    header('Content-type: application/json');
    echo $json;
}

