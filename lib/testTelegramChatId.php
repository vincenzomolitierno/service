<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 09/06/2021
 * Time: 21:33
 */

require_once("Notifier/NotifierConfiguration.class.php");
require_once("Notifier/TelegramBotApi.class.php");

$TelegramBotApi = new TelegramBotApi(NotifierConfiguration::TELEGRAM_BOT_URL, NotifierConfiguration::PROXY, NotifierConfiguration::PROXY_PORT, NotifierConfiguration::PROXY_CREDENTIALS);
print_r($TelegramBotApi->GetUpdates(1));