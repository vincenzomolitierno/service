<?php

class Configuration{

    const VERSIONE_REST_SERVER = "Ver 1.0 21-07-2021";

    const API_AQUA_ROBUR_URL = 'https://aquaexportapi.azurewebsites.net/api/External/v1/export';

    const FILE_LOG = "log.txt";

    // ############
    const IP_DISPOSITIVO_ABILITATO_1 = "192.168.3.100";
    public static $ip_dispositivi = array(self::IP_DISPOSITIVO_ABILITATO_1, "::1" ,"127.0.0.1");

    // ############
    const SERVICE_KEY_HASH = '$2y$10$ckTzhrFG8Dyjdm6YFCVfAeKzriWgFLpPeHd7Voe3wUTzEy5w8LMBe';

    const CIPHER = 'aes-128-gcm';
    const IV_BASE_64 = 'j6sjfEF592e6sxHO';
    const TAG_BASE_64 = 'i0J4flW7CygcpoFPql3iKQ==';
    const AQUA_ROBUR_CRYPTED_KEY = 'xGYtC92sy0107PAe5AU8G9FdG5wDHhYSayhoS1o6MMn/OZQsbOmHtYSkFIjI';

}
