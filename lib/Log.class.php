<?php

require_once 'Configuration.class.php';

class Log{

    const SEPARATORE = "|";

    public static function add($dati, $client_autorizzato, $esito_chiamata){
        try{
            $data_attuale = new DateTime(null, new DateTimeZone('Europe/Rome'));
            $myfile = fopen(Configuration::FILE_LOG, "a+");
            $txt = $data_attuale->format("d-m-Y H:i:s").self::SEPARATORE.$client_autorizzato."(".$_SERVER['REMOTE_ADDR'].")".self::SEPARATORE.basename($_SERVER['PHP_SELF']).self::SEPARATORE.$dati.self::SEPARATORE.$esito_chiamata."\n";
            fwrite($myfile, $txt);
            fclose($myfile);
        }catch (Exception $e){}
    }

}