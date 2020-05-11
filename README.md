# nuliga-club-php
Unsere Implementierung für den Zugriff auf die nuPortalRS Spielbetriebdaten.

## nuPortalRS API
https://hbde-portal.liga.nu/rs/documentation/

### Authentifizierung am NuPortal
https://www.bhv-online.de/filemanager/BHV/Daten/Service%20und%20Downloads/Nuliga/HTD_OAuth2_ZugriffaufnuPortalRS_200918_1144.pdf

## Dateien

### credentials.php
Datei mit den Zugangsdaten des Vereins für das nuPortal

### functions.php
Funktionssammlung zur Anmeldung, Token Anfrage, Token Update etc. Mit diesen Funktione wird auch der aktuelle Token in der Datei accesstoken.php abgelegt und genutzt solange er gültig ist

### getteams.php
Abfrage aller Mannschaften eines Vereins die in der aktuellen Runde aktiv sind

### getranking.php
Abfrage der aktuellen Tabellen aller Ligen der Mannschaften

### getschedule.php
Abfrage des aktuellen Spielplans, die Spiele mit Ergebniss der letzten 2 Wochen und die geplanten Spiel der kommenden 2 Wochen

### getspielplan.php
Abfrage des kompletten Saison Spielplans aller Teams
