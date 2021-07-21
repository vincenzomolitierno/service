<?php

require_once 'RestWrapper.class.php';
require_once 'Configuration.class.php';

class AusinoWrapper
{
    public static function getAllDataFromField($FromDate, $ToDate, $key, $SerialNumber=''){
        $url = 'https://aquaexportapi.azurewebsites.net/api/External/v1/export';
        $url .= '?FromDate=' . $FromDate . '&ToDate=' . $ToDate;

        if ($SerialNumber!==''){
            $url .= '&SerialNumber=' . $SerialNumber;
        }

        $XApiKey = openssl_decrypt(Configuration::AQUA_ROBUR_CRYPTED_KEY, 
                                Configuration::CIPHER, 
                                $key, 
                                $options=0, 
                                base64_decode(Configuration::IV_BASE_64), 
                                base64_decode(Configuration::TAG_BASE_64));
        
        return RestWrapper::getFromUrlByKey($url,$XApiKey);
    }
}

