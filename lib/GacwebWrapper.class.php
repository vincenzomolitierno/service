<?php

/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/03/2021
 * Time: 09:35
 */

/*
 * wrapper per le API Gac Web
 * - info: https://gacweb-test.dipvvf.it/gac-servizi/swagger/#/
 */

require_once "Configuration.class.php";
require_once "RestWrapper.class.php";

class GacwebWrapper
{
    private function create_json_response($codiceEsito,$descrizioneEsito,$errore){
        $arr_temp["codiceEsito"] = $codiceEsito;
        $arr_temp["descrizioneEsito"] = $descrizioneEsito;
        $arr_temp["errore"] = $errore;
        return json_encode($arr_temp);
    }

    public function GenerazioneToken(){
        $url = Configuration::API_GACWEB_URL_RILEVAMENTI_AUTOMATICI."GenerazioneToken?user=".Configuration::TOKEN_GAC_USER."&password=".Configuration::TOKEN_GAC_PASSWORD;
        return RestWrapper::getFromUrl($url);
    }

    /*
    * La chiamata GACWEB Rifornimento necessita del seguente json
    *{
    *  "idChiamante": "PRIMEC", (nome del sistema che automatizza il rifornimento)
    *  "dataRifornimento": "2021-03-16T14:01:29.960Z",
    *  "idColonnina": "73", (identificativo colonnina nel gacweb)
    *  "tipoMovimento": "m",
    *  "idUtente": "1109041215", (codiceInterno ricavabile dalle API WUAC mediante GetInfoBadgeDipendente?codiceFiscale)
    *  "targa": "29211",
    *  "litri": "15"
    *  }
    * */
    public function Rifornimento($jsonData){

        $arrHttpResponse = $this->GenerazioneToken();
        if(!$arrHttpResponse[HttpResponse::ERRORE]){
            $authorization = $arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO];
            $url = Configuration::API_GACWEB_URL_RILEVAMENTI_AUTOMATICI."Rifornimento";
            return RestWrapper::putToUrl($url, $authorization, $jsonData);
        }
        else{
            return $arrHttpResponse;
        }

    }

    public function AbilitazioneColonnina(){
        return "SVILUPPO FUTURO";
    }

    public function Rientro(){
        return "SVILUPPO FUTURO";
    }

}

/* STUB */
/*
$GacWrapper = new GacwebWrapper();
echo $GacWrapper->GenerazioneToken();

require_once "Rifornimento.class.php";

$Rifornimento = new Rifornimento("1DC22A3A;29211;D;15.03;1234;000;NO1000");
$Rifornimento->setIdChiamante(USER);
$Rifornimento->setIdColonnina(73);
$Rifornimento->setTipoMovimento("m");
$Rifornimento->setDataRifornimento(new DateTime());

$json_rifornimento = $Rifornimento->getJsonPerGac();

echo $GacWrapper->Rifornimento($json_rifornimento);
*/

