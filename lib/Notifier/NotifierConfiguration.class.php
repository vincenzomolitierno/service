<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 14/05/2021
 * Time: 13:25
 */

class NotifierConfiguration{

    /**** Mailer *******************/
    const SMTP_INTERNO = 'smtp.interno.it';
    const SMTP_AUTH = true;
    const SMTP_AUTH_TYPE = 'LOGIN';
    const SMTP_PORT = '25';
    const SMTP_USERNAME = 'comando.novara@vigilfuoco.it';
    const SMTP_PASSWORD = 'Comnov2012*';
    const FROM = 'comando.novara@vigilfuoco.it';

    /**** telegram *******************/
    const TELEGRAM_API_URL = "https://api.telegram.org";
    const TELEGRAM_BOT_TOKEN = "bot1728341779:AAHa9DM5mtT_ggw51MQwQq-DeNiR7qII1vU";
    const TELEGRAM_BOT_URL = self::TELEGRAM_API_URL."/".self::TELEGRAM_BOT_TOKEN;

    /**** proxy *******************/
    const PROXY = "virtualproxy.no.dipvvf.it";
    const PROXY_PORT = "3128";
    const PROXY_CREDENTIALS = "dike:Ldap101177";

}
