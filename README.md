CCEC REST SERVER

(versione Ver 1.7 11/06/2021)

Risolto bug ping in index.php.
Estesi i parametri di configurazione Notifiche in Configuration.class.php. Nuove funzionalita di test delle Notifiche; testMail, testTelegram, testTelegramChatId.
###################

(versione Ver 1.6 31-05-2021)

Rivisto e ampliato il sistema di notifiche.
Implementato contatore di ritrasmissione nel file "rifornimenti_da_ritrasmettere".
Inserito il parametro Configuration::NUMERO_MASSIMO_RITRASMISSIONI = 5.

###################

(versione Ver 1.5 25-05-2021)

Modificata logica di comunicazione con il CCEC in caso di Rifornimento. Il RestServer analizza la stringa rifornimento ricevuta e
risponde immediatamente con:

 * - 403 FORBIDDEN se il client chiamante non è autorizzato
 * - 500 INTERNAL SERVER ERROR se la stringa rifornimento ricevuta non è valida (vuota o con un numero di campi inatteso)
 * - 200 OK in caso di stringa rifornimento ricevuta correttamente

 Successivamente avviene l'interfacciamento con il GAC. Eventuali inconvenienti in questa fase vengono gestiti con il
 file "rifornimenti_da_ritrasmettere.txt"

###################

(versione Ver 1.4 21-05-2021)

Aggiunte le Notifiche via email e via telegram, configurabili in lib/Notifier/NotifierConfiguration.class.php
e  in Configuration.class.php:
      -SEND_EMAIL_ON_ERROR
      -SEND_TELEGRAM_MSG_ON_ERROR


###################

(versione Ver 1.3 19-05-2021)


Spostati nella root principale i file "totalizzatori.json", "rifornimenti_da_ritrasmettere.txt" e "log.txt".

Nuova classe RestWrapper per le chiamate rest alle API mediante l'utilizzo della classe RestCurl

Rimosso codice di errore "99999".

Ai fini di una corretta comunicazione degli esiti delle chiamate, il Rest SERVER restituisce sempre un json creato dal Rest Server.
In caso di errori dipendenti dal Rest Server viene inviato l'header "500 Internal Server Error".
In caso di non autorizzazione del client viene inviato l'header" 403 Forbidden".
In tutti gli altri casi viene restituito l'header originale proveniente dalla API interrogata dal Rest Server.

###################


(versione Ver 1.2 17-05-2021)

Creata pagina "index.php" che offre un cruscotto di monitoraggio dei dispositivi CCEC.

Introdotto il file "totalizzatori.json" in cui sono memorizzati i contatori delle varie colonnine.

Tutte le operazioni effettuate sono loggate nel file "log.txt". Eventuali rifornimenti non trasmessi con successo sono
memorizzati nel file "rifornimenti_da_ritrasmettere.txt". I tentativi di ritrasmissione avvengono ad ogni nuovo rifornimento e senza
modificare i contatori.

Introdotto un codice di errore "99999" per identificare errori riportati dal wrapper.

Introdotta autenticazione client. Client non autorizzati non potranno effettuare chiamate al server.

###############

(versione 1.0 18.03.2021)

Dipendenze:
- Php 7.3.5 con Curl

Descrizione:

In questa prima fase il server da la possibilità di registrare su GACWEB il rifornimento effettuato
e di verificare se una tessera ATe è valida.

Funzionalità offerte:

- GET GetAssociazioneByRfidCode
  Ricerca tessera ATe, inserendo come chiave di ricerca il codice identificativo

  - restituisce l'esito della chiamata all'omonima chiamata GET http://wbpate-test.dipvvf.it/GetAssociazioneByRfidCode

    Parametri: rfidCode

    Risposta:
    {
      "codiceRfid": "63CFE34D",
      "codiceFiscale": "BRBGNN80S18F839Z",
      "cognome": "BARBATO",
      "nome": "GIOVANNI SALVATORE",
      "validita": {
        "da": "2017-10-24T22:00:00Z",
        "a": "2027-09-06T22:00:00Z"
      }
    }

    Codici esiti: si rimanda allo swaggeer http://wbpate-test.dipvvf.it/swagger/index.html

- POST Rifornimento
  Effettua il rifornimento del mezzo

  - restituisce l'esito della chiamata all'omonima chiamata PUT https://gacweb-test.dipvvf.it/gac-servizi/integrazione/RilevamentiAutomatici/Rifornimento

    Parametri: stringa del rifornimento proveniente dal CCEC. Es: 63cfe34d;00001;D;0.00;1234;000;SA1001
                                                               * - 63cfe34d = Id del tesserino ATE
                                                               * - 00001 = IdMezzo (Targa o tag)
                                                               * - D = Diesel (in caso di Benzina B)
                                                               * - 0.00 sono i litri erogati
                                                               * - 1234 Km (ultime 4 cifre del conta km)
                                                               * - 000 = Autenticato OK (111 se id badge non viene riconosciuto)
                                                               * - SA1001 un codice associato alla sede

    Risposta:
    {
      "codiceEsito": "200",
      "descrizioneEsito": "Rifornimento effettuato con successo",
      "errore": false
    }

     Codici esiti: si rimanda allo swaggeer https://gacweb-test.dipvvf.it/gac-servizi/swagger/#/
