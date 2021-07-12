<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 25/05/2021
 * Time: 12:19
 */

/*
 * wrapper per la chiamata PUT
 * - https://gacweb-test.dipvvf.it/gac-servizi/integrazione/RilevamentiAutomatici/Rifornimento
 * - Effettua il rifornimento del mezzo o dell'attrezzatura
 *
 * INFO API GACWEB
 * - https://gacweb-test.dipvvf.it/gac-servizi/swagger/#/
 *
 * La chiamata REST Rifornimento può restituire al CCEC i seguenti codici:
 * - 403 FORBIDDEN se il client chiamante non è autorizzato
 * - 500 INTERNAL SERVER ERROR se la stringa rifornimento ricevuta non è valida (vuota o con un numero di campi inatteso)
 * - 200 OK in caso di stringa rifornimento ricevuta correttamente
 *
 * Successivamente avviene l'interfacciamento con il GAC. Eventuali inconvenienti in questa fase vengono gestiti con il
 * file "rifornimenti_da_ritrasmettere.txt"
 * *
 * ALTRE INFO
 *
 * Il CCEC al termine del rifornimento invia in POST una stringa. Es: 63cfe34d;00001;D;0.00;1234;000;SA1001
 * - 63cfe34d = Id del tesserino ATE
 * - 00001 = IdMezzo (Targa o tag)
 * - D = Diesel (in caso di Benzina B)
 * - 0.00 sono i litri erogati
 * - 1234 Km (ultime 4 cifre del conta km)
 * - 000 = Autenticato OK (111 se id badge non viene riconosciuto)
 * - SA1001 un codice associato alla sede
 *
 * La chiamata GACWEB Rifornimento, invece, necessita dei seguenti dati obbligatori
 *
 * - "idChiamante": "CCEC", (nome del sistema che automatizza il rifornimento)
 * - "dataRifornimento": "2021-03-16T14:01:29.960Z",
 * - "idColonnina": "73", (identificativo colonnina nel gacweb)
 * - "tipoMovimento": "m",
 * - "idUtente": "1109041215", (codiceInterno ricavabile dalle API WUAC mediante GetInfoBadgeDipendente?codiceFiscale)
 * - "targa": "29211",
 * - "litri": "15"
 *
 * La classe lib/Rifornimento.class.php si occuperà, quindi, della formattazione dei dati ai fini
 * dell'interfacciamento con le API GAC. In particolare:
 * - traduce IdBadge in Codice Interno
 * - formatta opportunamente la data
 *
 * OSSERVAZIONI
 *
 * - Occorre identificare in qualche modo l'idColonnina GACWEB da cui si è fatto rifornimento
 *   - Per il Comando di Novara la colonnina è unica (id 73) ed eroga diesel con 2 pistole.
 */


require_once("lib/Authorization.class.php");
require_once("lib/Configuration.class.php");
require_once("lib/Log.class.php");
require_once("lib/Totalizzatore.class.php");
require_once("lib/GacwebWrapper.class.php");
require_once("lib/Notifiche.class.php");
require_once("lib/Rifornimento.class.php");


const TOT_CAMPI_STRINGA = 7;

if(Authorization::isAuthorizedClient($_SERVER['REMOTE_ADDR'])){

    /* ANALIZZO LA STRINGA RIFORNIMENTO RICEVUTA */
    $ccec_string_rifornimento = "";
    $codice_esito_ricezione_stringa = "";
    $descrizione_esito_ricezione_stringa = "";
    $errore_ricezione_stringa = false;

    try{
        $ccec_string_rifornimento = file_get_contents("php://input");
        //$ccec_string_rifornimento = "DD92743A;28530;D;0.00;2222;000;SA1001";

        if(empty($ccec_string_rifornimento)){
            $errore_ricezione_stringa = true;
            $descrizione_esito_ricezione_stringa = "Stringa rifornimento vuota";
        }
        $arrTemp = explode(";",$ccec_string_rifornimento);
        if(count($arrTemp) != TOT_CAMPI_STRINGA){
            $errore_ricezione_stringa = true;
            $descrizione_esito_ricezione_stringa = "Stringa rifornimento con un numero di campi diverso da ".TOT_CAMPI_STRINGA."(tot campi = ".count($arrTemp).")";
        }
        foreach($arrTemp as $key=>$value){
            if(empty($value)){
                $errore_ricezione_stringa = true;
                $descrizione_esito_ricezione_stringa = "Stringa rifornimento con uno o piu' campi vuoti";
                break;
            }
        }
    }
    catch(Exception $e){
        $errore_ricezione_stringa = true;
        $descrizione_esito_ricezione_stringa = "Eccezione nel ricevimento della Stringa rifornimento.";
    }


    /* SEGNALO AL CCEC L'ESITO DELLA RICEZIONE DELLA STRINGA */
    if($errore_ricezione_stringa){
        $codice_esito_ricezione_stringa = HttpResponse::INTERNAL_SERVER_ERROR;
    }
    else{
        $codice_esito_ricezione_stringa = HttpResponse::OK_200;
        $descrizione_esito_ricezione_stringa = "Stringa rifornimento ricevuta con successo.";
    }


    ignore_user_abort();
    ob_start();

    // do stuff, generate output
    $json = HttpResponse::createJsonResponse($codice_esito_ricezione_stringa, $descrizione_esito_ricezione_stringa, $errore_ricezione_stringa);
    echo $json;

    // get size of the content
    $length = ob_get_length();

    // tell client to close the connection after $length bytes received
    header('HTTP/1.1 '.$codice_esito_ricezione_stringa);
    header('Content-type: application/json');
    header('Connection: close');
    header("Content-Length: $length");

    // flush all output
    ob_end_flush();
    ob_flush();
    flush();

    // continue your processing tasks
    if($errore_ricezione_stringa) {
        Log::add($ccec_string_rifornimento, Authorization::AUTHORIZED, $json);
        Notifiche::inviaNotifiche("stringa_rifornimento:".$ccec_string_rifornimento.", esito:".$json);
    }
    else{
        /* IN CASO DI STRINGA VALIDA REGISTRO IL RIFORNIMENTO SUL GAC */
        $Rifornimento = new Rifornimento($ccec_string_rifornimento);
        if($Rifornimento->getIdUtente() !== FALSE){

            $totalizzatore = Totalizzatore::aggiornaContatori(Configuration::IDGAC_COLONNINA, $Rifornimento->getLitri());
            if($totalizzatore > 0){
                $Rifornimento->setTotalizzatore($totalizzatore);
            }

            $gacWrapper = new GacwebWrapper();
            $arrHttpResponse = $gacWrapper->Rifornimento($Rifornimento->getJsonPerGac());
            Log::add("stringa_rifornimento:".$ccec_string_rifornimento.", richiesta_al_gac:".$Rifornimento->getJsonPerGac(),Authorization::AUTHORIZED, json_encode($arrHttpResponse));

            if(!$arrHttpResponse[HttpResponse::ERRORE] && !$arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO][HttpResponse::ERRORE]){
                $esito_cancellazione = $Rifornimento->cancellaRifornimentoDaiFalliti($Rifornimento->getStringaRifornimento());
                Log::add($Rifornimento->getStringaRifornimento(),"CANCELLAZIONE_DAI_RIFORNIMENTI_DA_RITRASMETTERE", $esito_cancellazione);
            }
            else{
                $esito_registrazione_falliti = $Rifornimento->registraRifornimentoFallito($Rifornimento->getStringaRifornimento());
                Log::add($Rifornimento->getStringaRifornimento(),"REGISTRAZIONE_RIFORNIMENTO_DA_RITRASMETTERE", $esito_registrazione_falliti);
                Notifiche::inviaNotifiche("(".$Rifornimento->getStringaRifornimento()."). ".json_encode($arrHttpResponse).", ESITO_REGISTRAZIONE_RIFORNIMENTO_DA_RITRASMETTERE:".$esito_registrazione_falliti);
            }
        }
        else{
            $codice_esito = HttpResponse::INTERNAL_SERVER_ERROR;
            $descrizione_esito = $Rifornimento->getLastError();
            $errore = true;

            $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);
            Log::add($Rifornimento->getStringaRifornimento(), Authorization::AUTHORIZED, $json);
            Notifiche::inviaNotifiche("(".$Rifornimento->getStringaRifornimento()."). ".$json);

            $esito_registrazione_falliti = $Rifornimento->registraRifornimentoFallito($Rifornimento->getStringaRifornimento());
            Log::add($Rifornimento->getStringaRifornimento(),"REGISTRAZIONE_RIFORNIMENTO_DA_RITRASMETTERE", $esito_registrazione_falliti);
            Notifiche::inviaNotifiche("(".$Rifornimento->getStringaRifornimento()."). ".json_encode($arrHttpResponse).", ESITO REGISTRAZIONE_RIFORNIMENTO_DA_RITRASMETTERE:".$esito_registrazione_falliti);

        }
    }

    /* TRASMETTO EVENTUALI RIFORNIMENTI FALLITI IN PRECEDENZA */
    Rifornimento::trasmettiRifornimentiFalliti();

}
else{
    $ccec_string_rifornimento = file_get_contents("php://input");

    $codice_esito = HttpResponse::FORBIDDEN;
    $descrizione_esito = Authorization::ERROR_NOT_AUTHORIZED.": ".$_SERVER['REMOTE_ADDR'];
    $errore = true;

    $json = HttpResponse::createJsonResponse($codice_esito, $descrizione_esito, $errore);

    Notifiche::inviaNotifiche("(".$ccec_string_rifornimento."). ".$json);
    Log::add($ccec_string_rifornimento, Authorization::ERROR_NOT_AUTHORIZED,$json);

    header('HTTP/1.1 '.HttpResponse::FORBIDDEN);
    header('Content-type: application/json');
    echo $json;
}



