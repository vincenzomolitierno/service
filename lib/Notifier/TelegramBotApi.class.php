<?php
/**
 * https://core.telegram.org/bots/api
 *
 * @author  Massimo Brunale <massimo.brunale@gmail.com>
 */


class TelegramBotApi{

    private $bot_url;
    private $proxy;
    private $proxy_port;
    private $proxy_credentials;

    public function  __construct($bot_url, $proxy, $proxy_port, $proxy_credentials){
        $this->setBotUrl($bot_url);
        $this->setProxy($proxy);
        $this->setProxyPort($proxy_port);
        $this->setProxyCredentials($proxy_credentials);
    }

    private function send_request($request_url){
        $arrResult = array();

        $url = $this->getBotUrl()."/".$request_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PROXY, $this->getProxy()); //your proxy url
        curl_setopt($ch, CURLOPT_PROXYPORT, $this->getProxyPort()); // your proxy port number
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->getProxyCredentials());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        curl_close($ch);

        // Check if any error occurred
        if(!empty($err))
        {
            $arrResult["esito"] = false;
            $arrResult["descrizione"] = 'Curl error: ' . $err . "-".$errmsg;
            $arrResult["dati_restituiti"] = "";
        }
        else{
            $arrResult["esito"] = true;
            $arrResult["descrizione"] = "Comando inviato con successo.";
            $arrResult["dati_restituiti"] = $result;
        }

        return $arrResult;
    }

    /**
     * sendMessage - Use this method to send text messages. On success, the sent Message is returned
     */

    /**
     * @param $chat_id integer
     * @param $message string
     * @return array
     */
    public function sendMessage($chat_id, $message){

        $data = array(
            'chat_id' => $chat_id,
            'text' => $message
        );

        $request_url = "sendMessage?".http_build_query($data);
        $arrResult = $this->send_request($request_url);
        return $arrResult;
    }

    public function GetUpdates($offset){
        return $this->send_request("GetUpdates?offset=".$offset);
    }

    /**
     * @return mixed
     */
    public function getBotUrl()
    {
        return $this->bot_url;
    }

    /**
     * @param mixed $bot_url
     */
    public function setBotUrl($bot_url)
    {
        $this->bot_url = $bot_url;
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param mixed $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return mixed
     */
    public function getProxyPort()
    {
        return $this->proxy_port;
    }

    /**
     * @param mixed $proxy_port
     */
    public function setProxyPort($proxy_port)
    {
        $this->proxy_port = $proxy_port;
    }

    /**
     * @return mixed
     */
    public function getProxyCredentials()
    {
        return $this->proxy_credentials;
    }

    /**
     * @param mixed $proxy_credentials
     */
    public function setProxyCredentials($proxy_credentials)
    {
        $this->proxy_credentials = $proxy_credentials;
    }



}


?>