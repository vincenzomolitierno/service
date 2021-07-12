<?php

/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/03/2021
 * Time: 09:57
 */

/* Rappresenta il Rifornimento effettuato */

require_once("WbpateWrapper.class.php");
require_once("WaucWrapper.class.php");

class Rifornimento{

    /* dati per API GAC */
    private $idChiamante;
    private $dataRifornimento;
    private $idColonnina;
    private $tipoMovimento;
    private $idUtente;
    private $idSelettiva;
    private $tag;
    private $targa;
    private $tagAttrezzatura;
    private $codiceAttrezzatura;
    private $litri;
    private $totalizzatore;

    /* altri dati presenti nella stringa del CCEC */
    private $stringaRifornimento;
    private $tipoCarburante;
    private $letturaKm;
    private $autenticato;
    private $codiceSede;

    /* variabili di comodo */
    private $lastError;

    public function  __construct($ccec_stringa_rifornimento){
        $this->stringaRifornimento = $ccec_stringa_rifornimento;
        if(!empty($ccec_stringa_rifornimento)){
            $this->decodeStringaRifornimento();
        }
    }

    private function decodeStringaRifornimento(){
        $temp_array = explode(";",$this->stringaRifornimento);

        //traduco in codice_interno_utente l'rfidcode presente nella stringa rifornimento
        $rfid_code = $temp_array[0];

        if(!empty($rfid_code)){
            $codiceInterno = $this->getCodiceInternoUtente($rfid_code);

            if(!empty($codiceInterno)){
                $this->setIdUtente($codiceInterno);
                $this->setTarga($temp_array[1]);
                $this->setLitri($temp_array[3]);

                $this->setTipoCarburante($temp_array[2]);
                $this->setLetturaKm($temp_array[4]);
                $this->setAutenticato($temp_array[5]);
                $this->setCodiceSede($temp_array[6]);

                $this->setIdChiamante(Configuration::GAC_USER);
                $this->setIdColonnina(Configuration::IDGAC_COLONNINA);
                $this->setTipoMovimento("m");
                $this->setDataRifornimento(new DateTime("Europe/Rome"));
            }
            else{
                $this->setIdUtente(false);
            }
        }
        else{
            $this->setIdUtente(false);
            $this->setLastError("Nessun rfidcode fornito");
        }
    }

    private function getCodiceInternoUtente($rfidCode){
        $this->setLastError("");
        $codiceInterno = "";

        $arrHttpResponse = WbpateWrapper::GetAssociazioneByRfidCode($rfidCode);
        
        if(!$arrHttpResponse[HttpResponse::ERRORE]){
            if($arrHttpResponse[HttpResponse::CODICE_ESITO] != HttpResponse::INTERNAL_SERVER_ERROR){
                $codice_fiscale = $arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO][WbpateWrapper::FIELD_CODICE_FISCALE];
                $arrHttpResponse2 = WaucWrapper::GetInfoBadgeDipendente($codice_fiscale);
                if(!$arrHttpResponse2[HttpResponse::ERRORE]){
                    if($arrHttpResponse2[HttpResponse::CODICE_ESITO] == 200){
                        $codiceInterno = $arrHttpResponse2[HttpResponse::DESCRIZIONE_ESITO][WaucWrapper::FIELD_CODICE_INTERNO];
                    }
                    else{
                        $this->setLastError(json_encode($arrHttpResponse2));
                    }
                }
                else{

                    $this->setLastError(json_encode($arrHttpResponse2));
                }
            }
            else{
                $this->setLastError(json_encode($arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO]));
            }
        }
        else{
            $this->setLastError($arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO]);
        }
        return $codiceInterno;
    }

    public function getJsonPerGac(){
        $array_dati["idChiamante"] = $this->getIdChiamante();
        $array_dati["dataRifornimento"] = $this->getDataRifornimento();
        $array_dati["idColonnina"] = $this->getIdColonnina();
        $array_dati["tipoMovimento"] = $this->getTipoMovimento();
        $array_dati["idUtente"] = $this->getIdUtente();
        $array_dati["idSelettiva"] = $this->getIdSelettiva();
        $array_dati["tag"] = $this->getTag();
        $array_dati["targa"] = $this->getTarga();
        $array_dati["tagAttrezzatura"] = $this->getTagAttrezzatura();
        $array_dati["codiceAttrezzatura"] = $this->getCodiceAttrezzatura();
        $array_dati["litri"] = $this->getLitri();
        $array_dati["totalizzatore"] = $this->getTotalizzatore();

        return json_encode($array_dati);
    }

    public function registraRifornimentoFallito($ccec_string_rifornimento){
        $esito_registrazione = "";
        $content = file_get_contents(Configuration::FILE_RIFORNIMENTI_DA_RITRASMETTERE);
        if (strpos($content, $ccec_string_rifornimento) === false) {
            $data_attuale = new DateTime(null, new DateTimeZone('Europe/Rome'));
            $file_rifornimenti = fopen(Configuration::FILE_RIFORNIMENTI_DA_RITRASMETTERE, "a+");
            $txt = "1|".$ccec_string_rifornimento."|".$data_attuale->format("d-m-Y H:i:s")."\n";
            fwrite($file_rifornimenti, $txt);
            fclose($file_rifornimenti);
            $esito_registrazione = "Registrazione effettuata con successo";
        }
        else{
            $esito_registrazione = "Rifornimento gia' presente in quelli da ritrasmettere.";
        }

        return $esito_registrazione;
    }

    public static function trasmettiRifornimentiFalliti(){
        $totale_trasmessi_con_successo = 0;
        $separatore_campi = "|";
        $content = file_get_contents(Configuration::FILE_RIFORNIMENTI_DA_RITRASMETTERE);
        $array_contents = explode("\n", $content);
        if(!empty($array_contents)){
            foreach ($array_contents as $key => $riga){
                if (strpos($riga, $separatore_campi) !== false) {
                    $riga_contents = explode($separatore_campi, $riga);
                    $contatore_ritrasmissioni = $riga_contents[0]+0;
                    $ccec_string_rifornimento = $riga_contents[1];
                    $Rifornimento = new Rifornimento($ccec_string_rifornimento);
                    if($contatore_ritrasmissioni < Configuration::NUMERO_MASSIMO_RITRASMISSIONI){
                        if($Rifornimento->getIdUtente() !== FALSE){
                            $gacWrapper = new GacwebWrapper();
                            $arrHttpResponse = $gacWrapper->Rifornimento($Rifornimento->getJsonPerGac());
                            Log::add("stringa_rifornimento:".$ccec_string_rifornimento.", richesta_al_gac:".$Rifornimento->getJsonPerGac(), "RITRASMISSIONE", json_encode($arrHttpResponse));
                            if(!$arrHttpResponse[HttpResponse::ERRORE] && !$arrHttpResponse[HttpResponse::DESCRIZIONE_ESITO][HttpResponse::ERRORE]){
                                unset($array_contents[$key]);
                                ++$totale_trasmessi_con_successo;
                                Log::add($ccec_string_rifornimento, "NOTIFICA RITRASMISSIONE AVVENUTA", json_encode($arrHttpResponse));
                                Notifiche::inviaNotifiche("Ritrasmissione avvenuta con successo. Rifornimento: ".$ccec_string_rifornimento. ", esito:".json_encode($arrHttpResponse));
                            }
                            else{
                                $riga_contents[0] = ++$contatore_ritrasmissioni;
                                $array_contents[$key] = implode($separatore_campi, $riga_contents);
                                Log::add($ccec_string_rifornimento, "NOTIFICA RITRASMISSIONE FALLITA", json_encode($arrHttpResponse));
                                Notifiche::inviaNotifiche("Attenzione: il rifornimento ".$ccec_string_rifornimento. " non e' stato inviato per la ".$contatore_ritrasmissioni."a volta. Verificare la sua esatezza. Richesta_al_gac:".$Rifornimento->getJsonPerGac().", esito:".json_encode($arrHttpResponse));
                            }
                        }
                        else{
                            Log::add($ccec_string_rifornimento, "NOTIFICA RITRASMISSIONE FALLITA", $Rifornimento->getLastError());
                            Notifiche::inviaNotifiche("Attenzione: il rifornimento ".$ccec_string_rifornimento. " non ha un utente valido. Verificare la sua esattezza. ".$Rifornimento->getLastError());
                        }
                    }
                    else{
                        Log::add($ccec_string_rifornimento, "NOTIFICA RITRASMISSIONE FALLITA", "Numero massimo di ritrasmissione raggiunto: ".Configuration::NUMERO_MASSIMO_RITRASMISSIONI);
                        Notifiche::inviaNotifiche("Attenzione: il rifornimento ".$ccec_string_rifornimento. " ha raggiunto il numero massimo di ritrasmissioni (".Configuration::NUMERO_MASSIMO_RITRASMISSIONI."). Verificare la sua esattezza.");
                    }
                }
                else{
                    unset($array_contents[$key]);
                }
            }


            $file_rifornimenti = fopen(Configuration::FILE_RIFORNIMENTI_DA_RITRASMETTERE, "w");
            if(!empty($array_contents)){
                foreach ($array_contents as $riga){
                    fwrite($file_rifornimenti, $riga."\n");
                }
            }
            fclose($file_rifornimenti);
    
        }

        return $totale_trasmessi_con_successo;
    }

    public function cancellaRifornimentoDaiFalliti($rifornimento){
        $esito_cancellazione = "";
        $content = file_get_contents(Configuration::FILE_RIFORNIMENTI_DA_RITRASMETTERE);
        if (strpos($content, $rifornimento) !== false) {
            $array_contents = explode("\n", $content);
            foreach ($array_contents as $key => $riga){
                if (strpos($riga, $rifornimento) !== false) {
                   unset($array_contents[$key]);
                   $file_rifornimenti = fopen(Configuration::FILE_RIFORNIMENTI_DA_RITRASMETTERE, "w");
                   if(!empty($array_contents)){
                        foreach ($array_contents as $row){
                            fwrite($file_rifornimenti, $row."\n");
                        }
                   }
                   fclose($file_rifornimenti);
                }
            }
            $esito_cancellazione = "Cancellazione avvenuta con successo.";
        }
        else{
            $esito_cancellazione = "Rifornimento non presente.";
        }

        return $esito_cancellazione;
    }

    /**
     * @return mixed
     */
    public function getIdChiamante()
    {
        return $this->idChiamante;
    }

    /**
     * @param mixed $idChiamante
     */
    public function setIdChiamante($idChiamante)
    {
        $this->idChiamante = $idChiamante;
    }

    /**
     * @return mixed
     */
    public function getDataRifornimento()
    {
        return $this->dataRifornimento;
    }

    /**
     * @param mixed DateTime $DataRifornimento
     */
    public function setDataRifornimento($DataRifornimento)
    {
        $this->dataRifornimento = $DataRifornimento->format("Y-m-d\TH:i:s\Z");
    }

    /**
     * @return mixed
     */
    public function getIdColonnina()
    {
        return $this->idColonnina;
    }

    /**
     * @param mixed $idColonnina
     */
    public function setIdColonnina($idColonnina)
    {
        $this->idColonnina = $idColonnina;
    }

    /**
     * @return mixed
     */
    public function getTipoMovimento()
    {
        return $this->tipoMovimento;
    }

    /**
     * @param mixed $tipoMovimento
     */
    public function setTipoMovimento($tipoMovimento)
    {
        $this->tipoMovimento = $tipoMovimento;
    }

    /**
     * @return mixed
     */
    public function getIdUtente()
    {
        return $this->idUtente;
    }

    /**
     * @param mixed $idUtente
     */
    public function setIdUtente($idUtente)
    {
        $this->idUtente = $idUtente;
    }

    /**
     * @return mixed
     */
    public function getIdSelettiva()
    {
        return $this->idSelettiva;
    }

    /**
     * @param mixed $idSelettiva
     */
    public function setIdSelettiva($idSelettiva)
    {
        $this->idSelettiva = $idSelettiva;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getTarga()
    {
        return $this->targa;
    }

    /**
     * @param mixed $targa
     */
    public function setTarga($targa)
    {
        $this->targa = $targa;
    }

    /**
     * @return mixed
     */
    public function getTagAttrezzatura()
    {
        return $this->tagAttrezzatura;
    }

    /**
     * @param mixed $tagAttrezzatura
     */
    public function setTagAttrezzatura($tagAttrezzatura)
    {
        $this->tagAttrezzatura = $tagAttrezzatura;
    }

    /**
     * @return mixed
     */
    public function getCodiceAttrezzatura()
    {
        return $this->codiceAttrezzatura;
    }

    /**
     * @param mixed $codiceAttrezzatura
     */
    public function setCodiceAttrezzatura($codiceAttrezzatura)
    {
        $this->codiceAttrezzatura = $codiceAttrezzatura;
    }

    /**
     * @return mixed
     */
    public function getLitri()
    {
        return $this->litri;
    }

    /**
     * @param mixed $litri
     */
    public function setLitri($litri)
    {
        $this->litri = $litri;
    }

    /**
     * @return mixed
     */
    public function getTotalizzatore()
    {
        return $this->totalizzatore;
    }

    /**
     * @param mixed $totalizzatore
     */
    public function setTotalizzatore($totalizzatore)
    {
        $this->totalizzatore = $totalizzatore;
    }

    /**
     * @return mixed
     */
    public function getTipoCarburante()
    {
        return $this->tipoCarburante;
    }

    /**
     * @param mixed $tipoCarburante
     */
    public function setTipoCarburante($tipoCarburante)
    {
        $this->tipoCarburante = $tipoCarburante;
    }

    /**
     * @return mixed
     */
    public function getLetturaKm()
    {
        return $this->letturaKm;
    }

    /**
     * @param mixed $letturaKm
     */
    public function setLetturaKm($letturaKm)
    {
        $this->letturaKm = $letturaKm;
    }

    /**
     * @return mixed
     */
    public function getAutenticato()
    {
        return $this->autenticato;
    }

    /**
     * @param mixed $autenticato
     */
    public function setAutenticato($autenticato)
    {
        $this->autenticato = $autenticato;
    }

    /**
     * @return mixed
     */
    public function getCodiceSede()
    {
        return $this->codiceSede;
    }

    /**
     * @param mixed $codiceSede
     */
    public function setCodiceSede($codiceSede)
    {
        $this->codiceSede = $codiceSede;
    }

    /**
     * @return mixed
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param mixed $lastError
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;
    }

    /**
     * @return mixed
     */
    public function getStringaRifornimento()
    {
        return $this->stringaRifornimento;
    }

    /**
     * @param mixed $stringaRifornimento
     */
    public function setStringaRifornimento($stringaRifornimento)
    {
        $this->stringaRifornimento = $stringaRifornimento;
    }



 

}