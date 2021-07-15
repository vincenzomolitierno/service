<?php

require_once "RestWrapper.class.php";
require_once "Configuration.class.php";

class AusinoWrapper
{
    public static function getAllDataFromField($FromDate, $ToDate, $SerialNumber=''){
        $url = 'https://aquaexportapi.azurewebsites.net/api/External/v1/export';
        $url .= '?FromDate=' . $FromDate . '&ToDate=' . $ToDate;

        if ($SerialNumber!==''){
            $url .= '&SerialNumber=' . $SerialNumber;
        }
        $key = Configuration::AQUA_ROBUR_KEY;
        return RestWrapper::getFromUrlByKey($url,$key);
    }


}

/* STUB */
//echo WbpateWrapper::GetAssociazioneByRfidCode("63cfe34d");
