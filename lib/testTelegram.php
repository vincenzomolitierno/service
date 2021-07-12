<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 09/06/2021
 * Time: 21:33
 */

require_once("Configuration.class.php");
require_once("Notifier/Notifier.class.php");

$notifier = new Notifier();
$notifier->sendTelegramMessage(Configuration::TELEGRAM_CHAT_ID_AUTORIMESSA, "prova messaggio da bot");

if(!$notifier->isTelegramMsgSented()){
    echo $notifier->getLastError();
}
else{
    echo "Messaggio Telegram inviato con successo.";
}