<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/03/2021
 * Time: 07:25
 */


/*
 * wrapper per la chiamata PUT
 * - https://gacweb-test.dipvvf.it/gac-servizi/integrazione/RilevamentiAutomatici/AbilitazioneColonnina
 * - Effettua l'abilitazione della colonnina per il rifornimento
 *
 * INFO API GACWEB
 * - https://gacweb-test.dipvvf.it/gac-servizi/swagger/#/
 */

require_once("lib/Authorization.class.php");
require_once("lib/GacwebWrapper.class.php");

if(Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])){
    //SVILUPPO FUTURO
    $gacWrapper = new GacwebWrapper();
    echo $gacWrapper->AbilitazioneColonnina();
}
else{
    echo Authorization::ERROR_NOT_AUTHORIZED.": ".$_SERVER['REMOTE_ADDR'];
}

