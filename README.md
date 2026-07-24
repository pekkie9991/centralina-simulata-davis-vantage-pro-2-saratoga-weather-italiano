
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

<img width="708" height="763" alt="Screenshot 2026-07-23 163807" src="https://github.com/user-attachments/assets/a8f6fb5f-e988-4acd-a269-22404f68a77c" /> 

Algoritmo Previsionale Davis (Simulatore v6 - Convective & Extreme)
Questo repository utilizza un algoritmo previsionale personalizzato in PHP ad alta fedeltà (Davis Forecast Engine v6) all'interno del file weatherlink_sync_v2.php.

Dato che i server di WeatherLink (tramite le API v1) non trasmettono la stringa di testo calcolata localmente dalla console fisica, questo motore simula l'intelligenza originale Davis incrociando i dati istantanei dei sensori per comporre in tempo reale oltre 120 combinazioni uniche di previsioni meteorologiche in lingua italiana.

Parametri utilizzati dall'algoritmo
Il motore di calcolo analizza contemporaneamente i seguenti dati inviati dai tuoi sensori fisici ogni 5 minuti:

Pressione Atmosferica (pressure_tendency_string): Determina l'andamento barometrico primario (salita rapida, salita lenta, stabile, discesa lenta, discesa rapida).
Umidità Relativa Esterna (relative_humidity): Essenziale per calcolare se c'è abbastanza vapore nell'aria per condensare nubi, nebbia o generare pioggia reale.
Temperatura Esterna (temp_c): Utilizzata per differenziare tra scenari invernali (neve) ed estivi (afa, temporali o grandine).
Velocità e Raffiche del Vento (wind_ten_min_avg_mph / wind_ten_min_gust_mph): Usati per stimare l'indice di raffreddamento e per le allerte burrasca.
Pioggia Giornaliera e Intensità Oraria (rain_day_in / rain_rate_in_per_hr): Permette di generare le diciture di "Schiarite" se la pioggia è terminata e la pressione risale.
Elenco e Logiche di tutti gli Scenari Implementati
1. Salita Rapida della Pressione (Rising Rapidly)
Rappresenta l'ingresso deciso dell'alta pressione.

Regola standard: Genera "Prevalentemente sereno con variazioni minime di temperatura."
Regola Post-Pioggia: Se i sensori rilevano che oggi ha piovuto ma ora l'intensità è zero, genera la previsione di miglioramento: "Schiarite e più fresco." (Clearing and cooler).
Vento: Se il vento medio o le raffiche sono elevati, aggiunge "Ventoso." (Windy).
2. Salita Lenta della Pressione (Rising Slowly)
Rappresenta una lenta stabilizzazione o l'ingresso di aria fresca post-frontale.

Regola standard: Genera "Parzialmente nuvoloso e più fresco."
Regola Post-Pioggia: Se ha piovuto di recente, genera "Schiarite e più fresco.".
3. Pressione Stabile (Steady)
Rappresenta stazionarietà barica.

Regola standard: Genera "Parzialmente nuvoloso con variazioni minime di temperatura."
Regola Nebbia/Foschia: Se l'umidità è estremamente alta (
≥
90
) e il vento è quasi assente (
<
5
 km/h
), aggiunge automaticamente: "Possibile nebbia o foschia." (Misty or foggy conditions possible).
4. Discesa Lenta della Pressione (Falling Slowly)
Rappresenta il lento avvicinamento di una perturbazione.

Aria Secca (Umidità < 55%): L'aria non contiene abbastanza umidità per condensare pioggia. Genera: "Nubi in aumento con variazioni minime di temperatura." (Increasing clouds with little temperature change).
Aria Umida (Umidità 
≥
 55%):
Se la temperatura è gelida ($\le 3^\circ\text{C}$): Genera "Prevalentemente nuvoloso con variazioni minime di temperatura. Neve possibile."
Se la temperatura è fredda (tra $3^\circ\text{C}$ e $6^\circ\text{C}$): Genera "Prevalentemente nuvoloso con variazioni minime di temperatura. Possibile pioggia e/o neve."
Se la temperatura è normale ($> 6^\circ\text{C}$): Genera "Prevalentemente nuvoloso con variazioni minime di temperatura. Possibili precipitazioni entro 6 ore."
5. Discesa Rapida della Pressione (Falling Rapidly)
Rappresenta l'avvicinamento di una forte perturbazione o di un temporale.

Aria Secca (Umidità < 50%): Genera "Prevalentemente nuvoloso con variazioni minime di temperatura. Possibili precipitazioni entro 6 ore."
Aria Umida (Umidità 
≥
 50%):
Se la temperatura è gelida ($\le 3^\circ\text{C}$): Genera "Prevalentemente nuvoloso e più fresco. Neve possibile. Precipitazioni possibili entro 6 ore."
Se la temperatura è fredda (tra $3^\circ\text{C}$ e $6^\circ\text{C}$): Genera "Prevalentemente nuvoloso e più fresco. Possibile pioggia e neve. Precipitazioni possibili entro 6 ore."
Se la temperatura è calda ed estiva ($\ge 24^\circ\text{C}$), l'aria è afosa (umidità $\ge 75%$) e c'è vento o pioggia forte: Lo script rileva una cella convettiva instabile (rischio grandine) e genera: "Prevalentemente nuvoloso e più fresco. Precipitazioni probabili. Forte temporale con possibile grandine e forti venti."
Se la temperatura è calda ma senza temporale violento ($\ge 20^\circ\text{C}$ e umidità $\ge 80%$): Genera "Prevalentemente nuvoloso con variazioni minime di temperatura. Precipitazioni probabili. Possibile forte temporale."
Allerte Fisiche Estreme e di Sicurezza
Oltre al calcolo barometrico, lo script analizza costantemente i dati fisici per iniettare avvisi di sicurezza scorrevoli alla fine del messaggio:

Allerta Burrasca (Gale Warning): Si attiva se le raffiche registrate superano i 
40
 km/h
 (Aggiunge: "Forti venti. Allerta burrasca.").
Allerta Gelo/Brina (Freezing Warning): Si attiva se la temperatura scende sotto lo zero (
≤
0
∘
C
) (Aggiunge: "Possibile gelata o brina.").
Allerta Wind Chill (Raffreddamento da vento): Si attiva se la temperatura è sopra lo zero ma il vento gelido fa percepire una temperatura sottozero (Aggiunge: "Freddo estremo da vento gelido.").
Allerta Afa Opprimente (Extreme Heat Index): Si attiva in estate se l'indice di calore percepito a causa dell'umidità supera i 
35
∘
C
 (Aggiunge: "Afa opprimente e calore estremo.").
