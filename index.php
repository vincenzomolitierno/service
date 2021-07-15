<?php

require_once("lib/Configuration.class.php");
require_once ("lib/Log.class.php");
require_once ("lib/Totalizzatore.class.php");

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="<?php echo Configuration::REFRESH_INDEX_PAGE?>" />
    <title>Server REST CCEC - <?php echo Configuration::VERSIONE_REST_SERVER; ?></title>
    <style>
        .up{
            color: green;
        }

        .down{
            color: red;
        }
    </style>
</head>
<body>

<?php

phpinfo();
    // echo "Server CCEC<hr>";

    // echo "Benvenuto, in questa pagina puoi verificare lo stato dei dispositivi e dei contatori colonnina. <hr>";

    // $data_verifica = new DateTime("Europe/Rome");
    // echo "Ultimo controllo: ".$data_verifica->format("d-m-Y h:m:s")." (il controllo viene fatto ogni ".Configuration::REFRESH_INDEX_PAGE." secondi)<br/><br/>";

    // echo "<table><tr><td><img src='img/ccec.png'/></td><td>";

    // foreach (Configuration::$ip_dispositivi as $key=>$value){
    //     if($value != "::1" && $value != "127.0.0.1"){
    //         $status = "dispositivo ".($key+1).", ip=".$value." - stato: ";

    //         $output_della_shell = shell_exec('ping -c 1 '.$value);

    //         if(strpos($output_della_shell, "1 received") !== false){
    //             $status .=  "<span class='up'> UP (".$result_ping.") </span>";
    //         }
    //         else{
    //             $status .= "<span class='down'> DOWN </span>";
    //         }
    //         echo $status."</br>";
    //     }
    // }
    // echo "</td></tr>";

    // echo "<tr><td><img src='img/colonnina.png'/></td><td>";

    // echo Totalizzatore::leggiContatori();

    // echo "</td></tr></table>";

    // echo "<hr>".Configuration::VERSIONE_REST_SERVER;

?>

</body>
</html>

