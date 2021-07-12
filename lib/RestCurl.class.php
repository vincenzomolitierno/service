<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 18/03/2021
 * Time: 12:56
 */

class RestCurl{

    public static function getCurlOption($method, $authorization = "", $json_to_send = "", $key = ""){
        $headers = array();

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );

        if($method == "POST" || $method == "PUT"){
            $options[CURLOPT_POSTFIELDS] = $json_to_send;
            $headers[] = "Content-Type: application/json";
            if ($method == 'POST'){
                $options[CURLOPT_POST] = 1;
            }
            elseif($method == 'PUT'){
                $options[CURLOPT_CUSTOMREQUEST] = "PUT";
            }
        }

        if(!empty($authorization)){
            $headers[] = "Authorization: ".$authorization;
        }

        // patch Vincenzo 11/07/2021
        if(!empty($key)){
            $headers[] = "X-Api-Key: ".$key;
        }

        if(!empty($headers)){
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        return $options;
    }

}