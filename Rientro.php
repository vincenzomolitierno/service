<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 18/03/2021
 * Time: 11:44
 */

/*
 * wrapper per la chiamata PUT
 * - https://gacweb-test.dipvvf.it/gac-servizi/integrazione/RilevamentiAutomatici/Rientro
 * - Effettua il rientro del mezzo
 *
 * INFO API GACWEB
 * - https://gacweb-test.dipvvf.it/gac-servizi/swagger/#/
 */

require_once("lib/Authorization.class.php");
require_once("lib/GacwebWrapper.class.php");

if(Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])){
    //SVILUPPO FUTURO
    $gacWrapper = new GacwebWrapper();
    echo $gacWrapper->Rientro();
}
else{
    echo Authorization::ERROR_NOT_AUTHORIZED.": ".$_SERVER['REMOTE_ADDR'];
}