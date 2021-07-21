<?php

require_once dirname(__DIR__) . '/lib/Authorization.class.php';
require_once dirname(__DIR__) . '/lib/AusinoWrapper.class.php';
require_once dirname(__DIR__) . '/lib/Log.class.php';

if (Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])) {

    if(!Authorization::isValidKey()){
        $codice_esito = HttpResponse::UNAUTHORIZED;
        $descrizione_esito = Authorization::ERROR_INVALID_KEY." - from client: ".$_SERVER['REMOTE_ADDR'];
        $errore = true;
        $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);
    
        header('HTTP/1.1 '.HttpResponse::UNAUTHORIZED);
        header('Content-type: application/json');
        echo $json;
    
    } else if(isset($_GET['start'])) {
        
        $UTC = new DateTimeZone("UTC");

        $startTime = new DateTime($_GET['start'],$UTC); // the last timepoint
        $startTimeTimestamp = $startTime->getTimestamp();      

        if(isset($_GET['end'])){
            $endTime = new DateTime($_GET['end'],$UTC); 
            $endTimeTimestamp = $endTime->getTimestamp();
            
        } else {
            $endTime = clone $startTime;
            $endTime->modify('-1 day');
            $endTimeTimestamp = $endTime->getTimestamp();
        }
        
        // *** REST Aqua Robur CALL BY CLASS AusinoWrapper
        $arrHttpResponse = AusinoWrapper::getAllDataFromField($endTime->format('Y-m-d%20H:i'),$startTime->format('Y-m-d%20H:i'));

        header('HTTP/1.1 '.$arrHttpResponse[HttpResponse::CODICE_ESITO]);
        header('Content-type: application/json');    
    
        if(!$arrHttpResponse[HttpResponse::ERRORE]){
            echo json_encode($arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO]);                
        }
        else{
            echo json_encode($arrHttpResponse);
        }  
    
    } else {
        $codice_esito = HttpResponse::BAD_REQUEST;
        $descrizione_esito = 'Invalid date parameter';
        $errore = true;
        $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);

        header('HTTP/1.1 '. HttpResponse::BAD_REQUEST);
        header('Content-type: application/json');   
        echo $json;
    }

} else {
    $codice_esito = HttpResponse::FORBIDDEN;
    $descrizione_esito = Authorization::ERROR_NOT_AUTHORIZED.": ".$_SERVER['REMOTE_ADDR'];
    $errore = true;
    $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);

    header('HTTP/1.1 '. HttpResponse::FORBIDDEN);
    header('Content-type: application/json');
    echo $json;
}

