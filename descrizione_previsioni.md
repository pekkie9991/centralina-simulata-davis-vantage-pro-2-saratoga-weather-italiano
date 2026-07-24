# Algoritmo Previsionale Davis (Simulatore v6 - Convective & Extreme)

Questo repository utilizza un algoritmo previsionale personalizzato in PHP ad alta fedeltà (**Davis Forecast Engine v6**) all'interno del file `weatherlink_sync_v2.php`. 

Dato che i server di WeatherLink (tramite le API v1) non trasmettono la stringa di testo calcolata localmente dalla console fisica, questo motore simula l'intelligenza originale Davis incrociando i dati istantanei dei sensori per comporre in tempo reale oltre **120 combinazioni uniche** di previsioni meteorologiche in lingua italiana.

---

## Parametri utilizzati dall'algoritmo
Il motore di calcolo analizza contemporaneamente i seguenti dati inviati dai tuoi sensori fisici ogni 5 minuti:
*   **Pressione Atmosferica (`pressure_tendency_string`):** Determina l'andamento barometrico primario (salita rapida, salita lenta, stabile, discesa lenta, discesa rapida).
*   **Umidità Relativa Esterna (`relative_humidity`):** Essenziale per calcolare se c'è abbastanza vapore nell'aria per condensare nubi, nebbia o generare pioggia reale.
*   **Temperatura Esterna (`temp_c`):** Utilizzata per differenziare tra scenari invernali (neve) ed estivi (afa, temporali o grandine).
*   **Velocità e Raffiche del Vento (`wind_ten_min_avg_mph` / `wind_ten_min_gust_mph`):** Usati per stimare l'indice di raffreddamento e per le allerte burrasca.
*   **Pioggia Giornaliera e Intensità Oraria (`rain_day_in` / `rain_rate_in_per_hr`):** Permette di generare le diciture di "Schiarite" se la pioggia è terminata e la pressione risale.

---

## Elenco e Logiche di tutti gli Scenari Implementati

### 1. Salita Rapida della Pressione (`Rising Rapidly`)
Rappresenta l'ingresso deciso dell'alta pressione.
*   **Regola standard:** Genera *"Prevalentemente sereno con variazioni minime di temperatura."*
*   **Regola Post-Pioggia:** Se i sensori rilevano che oggi ha piovuto ma ora l'intensità è zero, genera la previsione di miglioramento: **"Schiarite e più fresco."** (*Clearing and cooler*).
*   **Vento:** Se il vento medio o le raffiche sono elevati, aggiunge **"Ventoso."** (*Windy*).

### 2. Salita Lenta della Pressione (`Rising Slowly`)
Rappresenta una lenta stabilizzazione o l'ingresso di aria fresca post-frontale.
*   **Regola standard:** Genera *"Parzialmente nuvoloso e più fresco."*
*   **Regola Post-Pioggia:** Se ha piovuto di recente, genera **"Schiarite e più fresco."**.

### 3. Pressione Stabile (`Steady`)
Rappresenta stazionarietà barica.
*   **Regola standard:** Genera *"Parzialmente nuvoloso con variazioni minime di temperatura."*
*   **Regola Nebbia/Foschia:** Se l'umidità è estremamente alta ($\ge 90\%$) e il vento è quasi assente ($< 5\text{ km/h}$), aggiunge automaticamente: **"Possibile nebbia o foschia."** (*Misty or foggy conditions possible*).

### 4. Discesa Lenta della Pressione (`Falling Slowly`)
Rappresenta il lento avvicinamento di una perturbazione.
*   **Aria Secca (Umidità < 55%):** L'aria non contiene abbastanza umidità per condensare pioggia. Genera: **"Nubi in aumento con variazioni minime di temperatura."** (*Increasing clouds with little temperature change*).
*   **Aria Umida (Umidità $\ge$ 55%):**
    *   *Se la temperatura è gelida ($\le 3^\circ\text{C}$):* Genera *"Prevalentemente nuvoloso con variazioni minime di temperatura. Neve possibile."*
    *   *Se la temperatura è fredda (tra $3^\circ\text{C}$ e $6^\circ\text{C}$):* Genera *"Prevalentemente nuvoloso con variazioni minime di temperatura. Possibile pioggia e/o neve."*
    *   *Se la temperatura è normale ($> 6^\circ\text{C}$):* Genera *"Prevalentemente nuvoloso con variazioni minime di temperatura. Possibili precipitazioni entro 6 ore."*

### 5. Discesa Rapida della Pressione (`Falling Rapidly`)
Rappresenta l'avvicinamento di una forte perturbazione o di un temporale.
*   **Aria Secca (Umidità < 50%):** Genera *"Prevalentemente nuvoloso con variazioni minime di temperatura. Possibili precipitazioni entro 6 ore."*
*   **Aria Umida (Umidità $\ge$ 50%):**
    *   *Se la temperatura è gelida ($\le 3^\circ\text{C}$):* Genera *"Prevalentemente nuvoloso e più fresco. Neve possibile. Precipitazioni possibili entro 6 ore."*
    *   *Se la temperatura è fredda (tra $3^\circ\text{C}$ e $6^\circ\text{C}$):* Genera *"Prevalentemente nuvoloso e più fresco. Possibile pioggia e neve. Precipitazioni possibili entro 6 ore."*
    *   *Se la temperatura è calda ed estiva ($\ge 24^\circ\text{C}$), l'aria è afosa (umidità $\ge 75\%$) e c'è vento o pioggia forte:* Lo script rileva una cella convettiva instabile (rischio grandine) e genera: **"Prevalentemente nuvoloso e più fresco. Precipitazioni probabili. Forte temporale con possibile grandine e forti venti."**
    *   *Se la temperatura è calda ma senza temporale violento ($\ge 20^\circ\text{C}$ e umidità $\ge 80\%$):* Genera *"Prevalentemente nuvoloso con variazioni minime di temperatura. Precipitazioni probabili. Possibile forte temporale."*

---

## Allerte Fisiche Estreme e di Sicurezza
Oltre al calcolo barometrico, lo script analizza costantemente i dati fisici per iniettare avvisi di sicurezza scorrevoli alla fine del messaggio:
*   **Allerta Burrasca (Gale Warning):** Si attiva se le raffiche registrate superano i **$40\text{ km/h}$** (Aggiunge: *"Forti venti. Allerta burrasca."*).
*   **Allerta Gelo/Brina (Freezing Warning):** Si attiva se la temperatura scende sotto lo zero ($\le 0^\circ\text{C}$) (Aggiunge: *"Possibile gelata o brina."*).
*   **Allerta Wind Chill (Raffreddamento da vento):** Si attiva se la temperatura è sopra lo zero ma il vento gelido fa percepire una temperatura sottozero (Aggiunge: *"Freddo estremo da vento gelido."*).
*   **Allerta Afa Opprimente (Extreme Heat Index):** Si attiva in estate se l'indice di calore percepito a causa dell'umidità supera i **$35^\circ\text{C}$** (Aggiunge: *"Afa opprimente e calore estremo."*).