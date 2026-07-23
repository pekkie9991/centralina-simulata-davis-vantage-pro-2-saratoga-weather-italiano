# Console Davis VP2 & Vue - Meteo Limbiate

Questo repository contiene l'integrazione PHP/HTML5 delle console meteorologiche **Davis Vantage Pro 2** e **Davis Vantage Vue** per il sito web di Meteo Limbiate (Villaggio del Sole).

Il sistema è progettato per interfacciarsi con le API WeatherLink v1, sincronizzare i dati meteo in tempo reale e riprodurre l'interfaccia interattiva delle console originali in lingua italiana.

## Caratteristiche principali

* **Interfaccia realistica:** Riproduzione interattiva del layout delle console Vantage Pro 2 (`davconvp2CUmx.php`) e Vantage Vue (`davconvueCUmx.php`).
* **Motore previsionale Davis simulato:** Lo script analizza l'andamento dei sensori fisici (andamento barometrico, vento, ecc.) per comporre frasi previsionali complesse e dinamiche, analogamente alla console fisica.
* **Traduttore modulare in italiano:** Un dizionario integrato traduce le frasi previsionali in lingua italiana in tempo reale, gestendo la punteggiatura e i singoli segmenti di testo.
* **Gestione della cache e delle risorse:** Memorizzazione dei dati in cache locale (`realtime-x.txt`) per rispettare i limiti di frequenza delle chiamate API di WeatherLink.
* **Supporto diagnostico:** Generazione automatica di log di errore (`weatherlink_error_debug.txt`) e risposte grezze (`weatherlink_raw_debug.json`) per facilitare la risoluzione di eventuali anomalie di rete.

## Requisiti

* Server web con supporto **PHP 7.4 o superiore**.
* Estensione **cURL** abilitata in PHP (o abilitazione alle connessioni Server-to-Server se ospitato su piattaforme come Altervista).
* Account WeatherLink con credenziali API v1 (DID, Password, API Token).

## Struttura dei File

* `davconvp2CUmx.php` / `davconvueCUmx.php`: Pagine di avvio rispettivamente per la console Vantage Pro 2 e Vantage Vue.
* `davconvp2CUmx-inc.php` / `davconvueCUmx-inc.php`: Struttura del layout HTML/CSS interno delle console.
* `weatherlink_sync.php`: Script di backend che gestisce le richieste API a WeatherLink, la cache locale e il motore di traduzione.
* `Settings.php`: File di configurazione generale con unità di misura e fuso orario locale.
* `/davcon`: Directory contenente i fogli di stile (CSS), gli script per la gestione dei grafici (Flot JS), lo scroller del testo e le immagini delle console.

## Installazione e Configurazione

1. **Abilitazione Server-to-Server (per Altervista):**
   Se il sito è ospitato su Altervista, assicurati di abilitare le connessioni Server-to-Server dal pannello di controllo dell'hosting (*Impostazioni* -> *Server to Server* -> *Attiva*).
   
2. **Configurazione credenziali:**
   Apri il file `weatherlink_sync.php` e inserisci le tue credenziali WeatherLink nei rispettivi campi:
   ```php
   $WL_DID   = "IL_TUO_DID";
   $WL_PASS  = "LA_TUA_PASSWORD";
   $WL_TOKEN = "IL_TUO_API_TOKEN";
