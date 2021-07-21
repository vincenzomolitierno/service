<?php

class Configuration{

    const VERSIONE_REST_SERVER = "Ver 1.7 11-06-2021";
    const REFRESH_INDEX_PAGE = "60";

    const API_AQUA_ROBUR_URL = 'https://aquaexportapi.azurewebsites.net/api/External/v1/export';

    const FILE_LOG = "log.txt";

    // ############
    const IP_DISPOSITIVO_ABILITATO_1 = "192.168.3.100";
    public static $ip_dispositivi = array(self::IP_DISPOSITIVO_ABILITATO_1, "::1" ,"127.0.0.1");

    // ############
    const AQUA_ROBUR_KEY = 'C291C4EAFC0571E90AB6D31C2FE37E16296405C188C38';
    const AQUA_ROBUR_KEY_FILE = 'key_file.txt';

}
