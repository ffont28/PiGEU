# PiGEU
La PIattaforma per la Gestione degli Esami Universitari, d'ora in poi 
chiamata PiGEU è una soluzione Software per gestire la realtà universitaria
nella sua complessità.

## Installazione
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
Occorrerà copiare in ```/etc/apache2/sites-available``` e anche in ```/etc/apache2/sites-enabled```
il file ```pigeu.conf``` presente nella directory ```/install/``` di questo repository
4. Dopo aver riavviato il server apache2 con il seguente comando
```bash
sudo service apache2 restart
``` 
PiGEU sarà operativo sulla macchina.

5. Controllare la corretta installazione e funzionamento di PiGEU digitando
nel browser [http://localhost](http://localhost)