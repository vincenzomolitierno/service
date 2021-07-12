<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 12/05/2021
 * Time: 12:56
 */
require_once "Configuration.class.php";

class Authorization{

    const ERROR_NOT_AUTHORIZED = "ERRORE_CLIENT_NON_AUTORIZZATO";
    const AUTHORIZED = "CLIENT_AUTORIZZATO"; 

    public static function isAuthorizedClient($ip){

        return true;

        // if(in_array($ip, Configuration::$ip_dispositivi)){
        //     return true;
        // }
        // return false;
    }

}

