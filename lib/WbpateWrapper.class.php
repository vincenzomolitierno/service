<?php

/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/03/2021
 * Time: 09:35
 */

/*
 * wrapper per le API wbpate
 * - info: http://wbpate-test.dipvvf.it/swagger/index.html
 */
require_once "RestWrapper.class.php";

class WbpateWrapper
{
    const FIELD_CODICE_FISCALE = "codiceFiscale";

    public static function GetAssociazioneByRfidCode($rfidCode){
        $url = Configuration::API_WBPATE_URL."GetAssociazioneByRfidCode?rfidCode=".$rfidCode;
        return RestWrapper::getFromUrl($url);
    }
}

/* STUB */
//echo WbpateWrapper::GetAssociazioneByRfidCode("63cfe34d");
