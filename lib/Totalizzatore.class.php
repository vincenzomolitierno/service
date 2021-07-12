<?php
/**
 * Created by PhpStorm.
 * User: massimo.brunale
 * Date: 17/05/2021
 * Time: 13:29
 */
require_once "Configuration.class.php";

class Totalizzatore{

    public static function aggiornaContatori($colonnina, $litri){
        try{
            $string = file_get_contents(Configuration::FILE_CONTATORI);
            $array_contatori = json_decode($string, true);
            $array_contatori["colonnine"][$colonnina]["litri_erogati"] += $litri;
            $data_attuale = new DateTime(null, new DateTimeZone('Europe/Rome'));
            $array_contatori["colonnine"][$colonnina]["data_aggiornamento"] = $data_attuale->format("Y-m-d H:i:s");
            $json_contatori = json_encode($array_contatori);

            $myfile = fopen(Configuration::FILE_CONTATORI, "w");
            fwrite($myfile, $json_contatori);
            fclose($myfile);

            return $array_contatori["colonnine"][$colonnina]["litri_erogati"];

        }catch (Exception $e){}
    }

    public static function leggiContatori(){
        $dati = "";
        try{
            $string = file_get_contents(Configuration::FILE_CONTATORI);
            $array_contatori = json_decode($string, true);

            foreach ( $array_contatori["colonnine"] as $key => $arrValue) {
                $dati .= "id_colonnina:".$key." - litri erogati:".$arrValue["litri_erogati"]." - data_aggiornamento:".$arrValue["data_aggiornamento"]."</br>";
            }

        }catch (Exception $e){}

        return $dati;
    }
}