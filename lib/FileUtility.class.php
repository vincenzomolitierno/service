<?

require_once 'Configuration.class.php';

class FileUtility{

    public static function readCryptedKey(){

        file_get_contents(dirname(__DIR__) . '/settings/' . Configuration::AQUA_ROBUR_KEY_FILE );

        return true;
    }

}