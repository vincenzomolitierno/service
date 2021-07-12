<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 14/05/2021
 * Time: 13:25
 */

class Configuration{

    const VERSIONE_REST_SERVER = "Ver 1.7 11-06-2021";
    const REFRESH_INDEX_PAGE = "60";

    const API_GACWEB_URL_RILEVAMENTI_AUTOMATICI = "https://gacweb-test.dipvvf.it/gac-servizi/integrazione/RilevamentiAutomatici/";
    const API_WBPATE_URL = "http://wbpate-test.dipvvf.it/api/ATe/";
    const API_WAUC_URL = "http://wauc.dipvvf.it/api/";

    const GAC_USER = "CCEC";
    const GAC_PASSWORD = "CCEC";
    const TOKEN_GAC_USER = "PRIMEC";
    const TOKEN_GAC_PASSWORD = "PRIMEC";
    const NUMERO_MASSIMO_RITRASMISSIONI = 1;

    const FILE_LOG = "log.txt";
    const FILE_CONTATORI = "totalizzatori.json";
    const FILE_RIFORNIMENTI_DA_RITRASMETTERE = "rifornimenti_da_ritrasmettere.txt";

    const IDGAC_COLONNINA = "73";

    const CCEC_IP_DISPOSITIVO_1 = "192.168.3.100";

    public static $ip_dispositivi
        = array(self::CCEC_IP_DISPOSITIVO_1, "::1" ,"127.0.0.1");

    /* ############ NOTIFICHE ################ */

    const SEND_EMAIL_ON_ERROR = true;
    const SUBJECT_PREFIX = "#CCEC";
    public static $rcpt
        = array('informatica.novara@vigilfuoco.it');

    const SEND_TELEGRAM_MSG_ON_ERROR = true;
    const TELEGRAM_CHAT_ID_AUTORIMESSA = "-1001482628787";

}
