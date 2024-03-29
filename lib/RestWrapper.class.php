<?php

/*
 * wrapper per le chiamate REST ad API
 */

require_once 'Configuration.class.php';
require_once 'RestCurl.class.php';
require_once 'HttpResponse.class.php';

class RestWrapper
{
    // FOR AQUA ROBUR REST CALL
    public static function getFromUrlByKey($url,$key){

        $arrResult = array();

        $ch=curl_init($url);
        curl_setopt_array( $ch, RestCurl::getCurlOption("GET",'','',$key) );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );

        // Check if any error occurred
        if(!empty($err))
        {
            $arrResult[HttpResponse::CODICE_ESITO] = HttpResponse::INTERNAL_SERVER_ERROR;
            $arrResult[HttpResponse::DESCRIZIONE_ESITO] = 'Curl error: ' . $err . "-".$errmsg;
            $arrResult[HttpResponse::ERRORE] = true;
        }
        else{
            $arrResult[HttpResponse::CODICE_ESITO] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // modify the json for Ausonia specification 
            //si rimuovono i campi PskId e IoId
            $modifiedContent = json_decode($content,true);

            if($arrResult[HttpResponse::CODICE_ESITO]==200){
                for ($i=0; $i < count($modifiedContent['ExportModels']); $i++) { 
                    // Log::add('','',$modifiedContent['ExportModels'][$i]['PskId']);
                    // Log::add('','',$modifiedContent['ExportModels'][$i]['IoId']);
                    unset($modifiedContent['ExportModels'][$i]['PskId']);
                    unset($modifiedContent['ExportModels'][$i]['IoId']);
                }
            }

            // $arrResult[HttpResponse::DESCRIZIONE_ESITO] = json_decode($content,true);
            $arrResult[HttpResponse::DESCRIZIONE_ESITO] = $modifiedContent;
            $arrResult[HttpResponse::ERRORE] = false;
        }

        curl_close($ch);

        return $arrResult;
    }

    public static function getFromUrl($url){

        $arrResult = array();

        $ch=curl_init($url);
        curl_setopt_array( $ch, RestCurl::getCurlOption("GET") );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );

        // Check if any error occurred
        if(!empty($err))
        {
            $arrResult[HttpResponse::CODICE_ESITO] = HttpResponse::INTERNAL_SERVER_ERROR;
            $arrResult[HttpResponse::DESCRIZIONE_ESITO] = 'Curl error: ' . $err . "-".$errmsg;
            $arrResult[HttpResponse::ERRORE] = true;
        }
        else{
            $arrResult[HttpResponse::CODICE_ESITO] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $arrResult[HttpResponse::DESCRIZIONE_ESITO] = json_decode($content,true);
            $arrResult[HttpResponse::ERRORE] = false;
        }

        curl_close($ch);

        return $arrResult;
    }

    public static function putToUrl($url, $authorization, $jsonData){

        $arrResult = array();

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, RestCurl::getCurlOption("PUT", $authorization, $jsonData) );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        curl_close( $ch );

        // Check if any error occurred
        if(!empty($err))
        {
            $arrResult[HttpResponse::CODICE_ESITO] = HttpResponse::INTERNAL_SERVER_ERROR;
            $arrResult[HttpResponse::DESCRIZIONE_ESITO] = 'Curl error: ' . $err . "-".$errmsg;
            $arrResult[HttpResponse::ERRORE] = true;
        }
        else{
            $arrResult[HttpResponse::CODICE_ESITO] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $arrResult[HttpResponse::DESCRIZIONE_ESITO] = json_decode($content,true);
            $arrResult[HttpResponse::ERRORE] = false;
        }

        return $arrResult;
    }
}

