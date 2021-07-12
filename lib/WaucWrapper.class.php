<?php

/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/03/2021
 * Time: 09:35
 */

/*
 * wrapper per le API wauc
 * - info: http://wauc.dipvvf.it/index.html
 */

require_once "RestWrapper.class.php";

class WaucWrapper
{
    const FIELD_CODICE_INTERNO = "codiceInterno";

    public static function GetInfoBadgeDipendente($codiceFiscale){
        $url = Configuration::API_WAUC_URL."GetInfoBadgeDipendente?codiceFiscale=".$codiceFiscale;
        return RestWrapper::getFromUrl($url);
    }
}

/* STUB */
//echo WaucWrapper::GetInfoBadgeDipendente("BRBGNN80S18F839Z");