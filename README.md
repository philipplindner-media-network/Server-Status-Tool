## Willkommen auf der Projekt seite von "Server Status Tool"

Dies ist eine Tool was ermöglich ohne Großen aufwan Server zu überwachen.

### Instalation

Alle Dateten Herunterladen, oder nur der ServerTool_v2.zip Datei. Die index.php und hddtemp.sh Datei auf den Server Hochladen in eine Ordener Names "sst" (muss Erstelt weden und Schreibrechet (777) haben). Dann die Datei hddtemp.sh mit Folgdenden Befel Bearbeiten:
```markdown
chmod +x hddtemp.sh
```
Des weiteren weden volgden Programm Benötigt (Bitte suchen sei für Ihr System die passenden Befele zur Instalation aus aus).

- hddtemp
- lm-sensors
- sudo

Für Aktuelle Temperatoren der HDD's und SSD's muss noch eine Crontab erstelt werden
```markdown
crontab -e

*/5 * * * * sudo sh /var/www/html/sst/hddtemp.sh
```



### Support or Contact

Für Hilfe und Anmeldungen bitte per mail an post[at]philipp-lindner[dot]de
