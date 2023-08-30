# PiGEU
La PIattaforma per la Gestione degli Esami Universitari, d'ora in poi 
chiamata PiGEU è una soluzione Software per gestire la realtà universitaria
nella sua complessità.

## Composizione del presente repository
Il presente repository contiene diverse cartelle contenenti file:
- La directory ```SQL``` contiene file in formato ```.sql``` con le funzioni e trigger e viste implementate nella base di dati
- La directory ```manuali``` contiene in formato ```.tex``` e anche in formato ```.pdf``` i diversi manuali tra cui:
  - manuale docente
  - manuale studente
  - manuale segreteria
  - descrizione tecnica dell'intero sistema PiGEU
- La directory ```dump di PiGEU``` contiene i dump periodici della base di dati, indicanti ciascuno la data di realizzazione del file di backup
- La directory ```studente``` contiene i file ```.php``` inerenti la parte di PiGEU che riguarda l'utenza *studente*
- La directory ```docente``` contiene i file ```.php``` inerenti la parte di PiGEU che riguarda l'utenza *docente*
- La directory ```segreteria``` contiene i file ```.php``` inerenti la parte di PiGEU che riguarda l'utenza *segreteria*
Le altre directory non specificate in questo elenco non godono di particolare rilevanza al fine della comprensione per l'utilizzo di PiGEU

## Installazione sulla macchina SERVER
### Requisiti
Per usufruire delle potenzialità di PiGEU è necessario avere i seguenti requisiti:
- Sistema operativo Linux (lo sviluppo e il testing sono stati fatti su UBUNTU)
- GIT
- Apache2
- PHP

### Procedura
1. Anzitutto è necessario, dopo avere installato le dipendenze sopra indicate, 
posizionarsi nella directory ```/var/www/```con il seguente comando
```bash
cd /var/www/
```
2. Il secondo passo per continuare la procedura di installazione è clonare il presente repository 
```bash
git clone https://github.com/ffont28/PiGEU.git
```
3. Dopo aver clonato il presente repository occorre impostare il Virtual Host affinchè
PiGEU sia raggiungibile da GUI Web.
Occorrerà copiare in ```/etc/apache2/sites-available``` il file ```pigeu.conf``` presente nella directory```/install/``` di questo repository
4. Successivamente si deve abilitare la configurazione presente nel file ```pigeu.conf``` appena copiato, con il seguente comando
```bash
cd /etc/apache2/sites-available
sudo a2ensite pigeu.conf
```  
5. Dopo aver riavviato il server apache2 con il seguente comando
```bash
systemctl reload apache2
``` 
PiGEU sarà operativo sulla macchina server.

6. Controllare la corretta installazione e funzionamento di PiGEU digitando
nel browser [http://localhost](http://localhost)
