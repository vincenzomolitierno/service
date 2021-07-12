<?php

require_once "RestWrapper.class.php";

class AusinoWrapper
{
    public static function getDataFromField(){
        // $url = Configuration::API_WBPATE_URL."GetAssociazioneByRfidCode?rfidCode=".$rfidCode;
        $url = 'https://aquaexportapi.azurewebsites.net/api/External/v1/export?FromDate=01%2F01%2F2021&ToDate=01%2F07%2F2021';
        $key = 'C291C4EAFC0571E90AB6D31C2FE37E16296405C188C38';
        return RestWrapper::getFromUrlByKey($url,$key);
    }
}

/* STUB */
//echo WbpateWrapper::GetAssociazioneByRfidCode("63cfe34d");
