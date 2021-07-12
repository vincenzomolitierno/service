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
$subject = Configuration::SUBJECT_PREFIX." - test";
$message = "test";

$notifier->sendMail(NotifierConfiguration::FROM, Configuration::$rcpt, $subject, $message);
if(!$notifier->isEmailSented()){
    echo $notifier->getLastError();
}
else{
    echo "Email inviata con successo.";
}
