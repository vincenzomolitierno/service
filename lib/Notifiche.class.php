<?php

/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 21/05/2021
 * Time: 14:33
 */
require_once("Configuration.class.php");
require_once("Notifier/Notifier.class.php");

class Notifiche
{
    public static function inviaNotifiche($text){

        if(Configuration::SEND_EMAIL_ON_ERROR || Configuration::SEND_TELEGRAM_MSG_ON_ERROR){
            $notifier = new Notifier();
            $subject = Configuration::SUBJECT_PREFIX." - From ".basename($_SERVER['PHP_SELF']);
            $subject_for_telegram = Configuration::SUBJECT_PREFIX;
            $message = "(Client ".$_SERVER['REMOTE_ADDR'].") - From ".basename($_SERVER['PHP_SELF'])." ".$text;

            if(Configuration::SEND_EMAIL_ON_ERROR){
                $notifier->sendMail(NotifierConfiguration::FROM, Configuration::$rcpt, $subject, $message);
                if(!$notifier->isEmailSented()){
                    echo $notifier->getLastError();
                }
            }
            if(Configuration::SEND_TELEGRAM_MSG_ON_ERROR){
                $notifier->sendTelegramMessage(Configuration::TELEGRAM_CHAT_ID_AUTORIMESSA, $subject_for_telegram."-".$message);
            }
        }

    }
}