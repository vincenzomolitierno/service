<?php

require_once 'Configuration.class.php';

class HttpResponse{

    const CODICE_ESITO = 'codiceEsito';
    const DESCRIZIONE_ESITO = 'descrizioneEsito';
    const ERRORE = 'errore';

    const INTERNAL_SERVER_ERROR = '500 Internal Server Error';
    const OK_200 = '200 OK';
    const FORBIDDEN = '403 FORBIDDEN';
    const UNAUTHORIZED = '401 UNAUTHORIZED';
    const BAD_REQUEST = '400 BAD REQUEST';

    public static function createJsonResponse($codiceEsito, $descrizioneEsito, $errore){
        $arr_temp[self::CODICE_ESITO] = $codiceEsito;
        $arr_temp[self::DESCRIZIONE_ESITO] = $descrizioneEsito;
        $arr_temp[self::ERRORE] = $errore;
        return json_encode( $arr_temp );
    }

}