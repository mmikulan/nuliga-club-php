# nuliga-club-php
Unsere Implementierung der nuPortalRS API in PHP für den Zugriff auf die nuLiga Spielbetriebsdaten mit der wir die aktuellen Spiel sowie Tabellenstände auf die Webseite des TSV Schleißheims einbauen (siehe https://schleissheim-handball.de/mannschaften/1-damen/)

## nuPortalRS API
https://hbde-portal.liga.nu/rs/documentation/

### Authentifizierung am NuPortal
https://www.bhv-online.de/filemanager/BHV/Daten/Service%20und%20Downloads/Nuliga/HTD_OAuth2_ZugriffaufnuPortalRS_200918_1144.pdf

## Dateien

### credentials.php
Datei mit den nuPortal Zugangsdaten des Vereins sowie weitere Konfigurationsdaten

### functions.php
Funktionssammlung zur Anmeldung, Token Anfrage, Token Update etc. Mit diesen Funktionen wird auch der aktuelle Token in der Datei accesstoken.php abgelegt und genutzt solange er gültig ist

### getteams.php
Abfrage aller Mannschaften eines Vereins die in der aktuellen Runde aktiv sind. Das Result findet sich in den Dateien nuliga_teams.json (Ligainformationen, Altersklasse, etc. pro Team) und nuliga_mapping.json (Altersklasse zu nuLiga Team Id)

### getranking.php
Abfrage der aktuellen Tabellen aller Ligen der Mannschaften. Das Resultat findet sich in der Datei nuliga_ranking.json (pro Team die Liga und entsprechende aktuelle Tabelleninformationen)

### getschedule.php
Abfrage des aktuellen Spielplans, die Spiele mit Ergebniss der letzten 2 Wochen und die geplanten Spiel der kommenden 2 Wochen. Das Resultat findet sich in den Dateien nuliga_schedule.json (die Liste aller Spiele des Vereins im Zeitraum), nuliga_schedule_team.json (die Liste der Spiele pro Team) und nuliga_schedule_today.json (nur die Spiele des aktuellen Tages z.B. für Newsticker)

### getspielplan.php
Abfrage des kompletten Saison Spielplans aller Teams. Das Resultat findet sich in der Datei nuliga_spielplan.json (alle Spiele des Vereins in der aktuellen Saison)

### nutest.php
Testabfrage um zu prüfen ob die Zugangsdaten korrekt sind. Es werden keine Dateien angelegt.

### Konfiguration
Die nötigen Anpassungen begrenzen sich auf die Datei credentials.php
#### clientid - Die Nutzerkennung welche vom Verband bzw. nuLiga vergeben werden
#### clientsecret - Das Passwort für den Zugang (kommt auch vom Verband bzw. nuLiga)
#### scope - muss aktuell auf "club" gesetzt sein
#### nuligateamid - Id des Vereins (nicht die club Nr in den nuLiga Abfragen, sondern die Id welche auf der nuLiga Seite des Vereins unter V.Nr. steht)
#### nuligawebdir - das Verzeichnis wo die Resultat Dateien relativ zur Skriptausführung abgespeichert werden. 
In dem nuligawebdir Verzeichnis und dem eigentlichen Skriptverzeichen werden Schreibrechte benötigt.

## Viel Spass bei der Nutzung ...
