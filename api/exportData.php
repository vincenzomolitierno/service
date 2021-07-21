<?php

require_once dirname(__DIR__) . '/lib/Authorization.class.php';
require_once dirname(__DIR__) . '/lib/AusinoWrapper.class.php';
require_once dirname(__DIR__) . '/lib/Log.class.php';

    // 2021-01-06T23:55
    // starttime is chronologically the last timepoint
    // endtime is chronologically the first timepoint

if (Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])) {
 
    if(!Authorization::isValidKey()){
        $codice_esito = HttpResponse::UNAUTHORIZED;
        $descrizione_esito = Authorization::ERROR_INVALID_KEY." - from client: ".$_SERVER['REMOTE_ADDR'];
        $errore = true;
        $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);
    
        header('HTTP/1.1 '.HttpResponse::UNAUTHORIZED);
        header('Content-type: application/json');
        echo $json;
    
    } else if(isset($_GET['start']) && isset($_GET['serial'])) {
        
        $SerialNumber = $_GET['serial'];
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
        
        // *** REST  Aqua Robur CALL BY CLASS AusinoWrapper
        $arrHttpResponse = AusinoWrapper::getAllDataFromField($endTime->format('Y-m-d%20H:i'),$startTime->format('Y-m-d%20H:i'), Authorization::getValidKey(), $SerialNumber);

        header('HTTP/1.1 '.$arrHttpResponse[HttpResponse::CODICE_ESITO]);
        header('Content-type: application/json');    
    
        if(!$arrHttpResponse[HttpResponse::ERRORE]){
            if ($arrHttpResponse[HttpResponse::CODICE_ESITO]!=200) 
                echo $arrHttpResponse[HttpResponse::CODICE_ESITO] ;
            else
                echo json_encode($arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO]);                
        }
        else{
            echo json_encode($arrHttpResponse);
        }  
    
    } else {
        $codice_esito = HttpResponse::BAD_REQUEST;
        $descrizione_esito = 'Invalid date and/or serial parameter';
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
    Log::add('!!!', Authorization::ERROR_NOT_AUTHORIZED,$json);

    header('HTTP/1.1 '.HttpResponse::FORBIDDEN);
    header('Content-type: application/json');
    echo $json;
}

