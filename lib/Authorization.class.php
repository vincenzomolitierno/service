<?php

require_once 'Configuration.class.php';

class Authorization{

    const ERROR_NOT_AUTHORIZED = "ERRORE_CLIENT_NON_AUTORIZZATO";
    const AUTHORIZED = "CLIENT_AUTORIZZATO"; 
    const ERROR_INVALID_KEY = "INVALID KEY VALUE";

    public static function isAuthorizedClient($ip){

        return true;

        if(in_array($ip, Configuration::$ip_dispositivi)){
            return true;
        }
        return false;
    }

    public static function isValidKey(){
        $headers = apache_request_headers();
        if(key_exists('Api-Key',$headers)){
            $keyFile = file_get_contents(dirname(__DIR__).'/settings/'.Configuration::AQUA_ROBUR_KEY_FILE);        
            Log::log('keyFile ' . dirname(__DIR__).'/settings/'.Configuration::AQUA_ROBUR_KEY_FILE ); 
            Log::log('keyFile ' . $keyFile ); 
            if ($headers['Api-Key']){

                return true;
            } 
        }

        return false;
    }

}

