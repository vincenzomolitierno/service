<?php

require_once 'Configuration.class.php';

class Authorization
{
    const ERROR_NOT_AUTHORIZED = "ERRORE_CLIENT_NON_AUTORIZZATO";
    const AUTHORIZED = "CLIENT_AUTORIZZATO";
    const ERROR_INVALID_KEY = "INVALID KEY VALUE";

    static $validKey = '';

    public static function isAuthorizedClient($ip)
    {

        if (in_array($ip, Configuration::$ip_dispositivi)) {
            return true;
        }
        return false;
    }

    public static function isValidKey()
    {
        $headers = apache_request_headers();
        if (key_exists('Api-Key', $headers)) {
            if (password_verify($headers['Api-Key'], Configuration::SERVICE_KEY_HASH)) { 
                return true;
            }
        }

        return false; // in case of wrong value or Api-Key not set
    }

    public static function decryptAquaRoburApiKey()
    {
        $headers = apache_request_headers();
        if (key_exists('Api-Key', $headers)) {
            if (password_verify($headers['Api-Key'], Configuration::SERVICE_KEY_HASH)) {
                Authorization::$validKey =  $headers['Api-Key'];
                return true;
            }
        }

        return false; // in case of wrong value or Api-Key not set
    }

    public static function getValidKey(){
        return Authorization::$validKey;
    }
}
