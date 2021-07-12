<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 21/05/2021
 * Time: 15:41
 */

require_once "NotifierConfiguration.class.php";
require_once "PHPMailer.php";
require_once "SMTP.php";
require_once "Exception.php";
require_once "TelegramBotApi.class.php";

class Notifier{

    private $emailSented;
    private $telegramMsgSented;
    private $lastError;

    public function  __construct(){
        $this->setEmailSented(false);
        $this->setTelegramMsgSented(false);
        $this->setLastError("");
    }

    /**
     * @param $from_name string
     * @param $rcpt array
     * @param $subject string
     * @param $message string
     */
    public function sendMail($from_name, $rcpt, $subject, $message){
        $this->setEmailSented(false);
        $this->setLastError("");

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = NotifierConfiguration::SMTP_INTERNO;
            $mail->SMTPAuth = NotifierConfiguration::SMTP_AUTH;
            $mail->AuthType = NotifierConfiguration::SMTP_AUTH_TYPE;
            $mail->Port = NotifierConfiguration::SMTP_PORT;
            $mail->Username = NotifierConfiguration::SMTP_USERNAME;
            $mail->Password = NotifierConfiguration::SMTP_PASSWORD;
            $mail->From = NotifierConfiguration::FROM;
            $mail->FromName = $from_name;
            $mail->Priority = 3;

            $mail->Subject = $subject;
            $mail->Body = $message;

            if(!empty($rcpt)){
                foreach ($rcpt as $key=>$value){
                    $mail->addAddress($value);
                }

                if (!$mail->send()) {
                    $this->setEmailSented(false);
                    $this->setLastError("mailer - ".$subject." NON inviata: ".$mail->ErrorInfo);
                } else {
                    $this->setEmailSented(true);
                    $this->setLastError("");
                }
            }

        }catch(Exception $e){
            $this->setEmailSented(false);
            $this->setLastError("mailer - ".$subject." NON inviata: ".$e->getMessage());
        }
    }

    /**
     * @param $chat_id integer
     * @param $message string
     */
    public function sendTelegramMessage($chat_id, $message){
        $this->setTelegramMsgSented(false);
        $this->setLastError("");

        $TelegramBotApi = new TelegramBotApi(NotifierConfiguration::TELEGRAM_BOT_URL, NotifierConfiguration::PROXY, NotifierConfiguration::PROXY_PORT, NotifierConfiguration::PROXY_CREDENTIALS);
        $arrEsito = $TelegramBotApi->sendMessage($chat_id, $message);

        if ($arrEsito["esito"]){
            $this->setTelegramMsgSented(true);
            $this->setLastError("");
        }
        else{
            $this->setTelegramMsgSented(false);
            $this->setLastError($arrEsito["descrizione"]);
        }
    }

    /**
     * @return boolean
     */
    public function isEmailSented()
    {
        return $this->emailSented;
    }

    /**
     * @param boolean $emailSented
     */
    public function setEmailSented($emailSented)
    {
        $this->emailSented = $emailSented;
    }

    /**
     * @return boolean
     */
    public function isTelegramMsgSented()
    {
        return $this->telegramMsgSented;
    }

    /**
     * @param boolean $telegramMsgSented
     */
    public function setTelegramMsgSented($telegramMsgSented)
    {
        $this->telegramMsgSented = $telegramMsgSented;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param string $lastError
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;
    }


}

/** STUB **/

/*
$destinatari[] = 'informatica.novara@vigilfuoco.it';
Notifier::sendMail("BOT NOVARA", $destinatari, "PROVA NOTIFIER", "CORPO DELL PROVA");

Notifier::sendTelegramMessage(CHAT_ID_UFFICIO_INFORMATICA, "PROVA NOTIFIER");
*/