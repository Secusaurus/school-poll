# Eltern-Beteiligungs-Umfragen-Tool

Diese php-basierte Webseite ermöglicht es, einfache Umfragen zu gestalten und auszuwerten.
Dabei wird im Sinne des "Gruppendrucks" angezeigt, wie hoch die Beteiligung aktuell ist und welche Klassen bereits mitgewirkt haben.


## Konfiguration

Es ist eine SQL-Datenbank nötig.

1. In `./config/config.php` die entsprechenden Credentials und das Admin-Kennwort anpassen
2. Per php (z.B. über den Browser) `./config/install.php` aufrufen. Das erstellt alle nötigen Tabellen
3. Die `./config/install.php` löschen
4. Die Datei `./config/lockdown.htaccess` auf dem Webserver in `./config/.htaccess` umbenennen
5. Falls das Repository direkt auf den Webserver gecloned wurde, bitte die Readme- und Lizenz-Dateien entfernen und auch ggf. das .git-Verzeichnis, bevor es öffentlich im Internet steht.

## Betrieb

Die index.php enthält alles, was man braucht. Also einfach per Browser auf die Hauptseite surfen und dann als Admin anmelden, um die erste Umfrage zu erstellen. Im Admin-Dashboard sind dann auch Links

## Disclaimer

- Die Web-App ist minimimalistisch für genau meinen Einsatzzweck gebaut. Wenn Funktionen fehlen oder nicht selbsterklärend sind, dann gerne forken oder Vorschläge per Issue machen
- Die Basis ist mit Unterstützung von Mistral AI entwickelt. Ich habe da schon noch ordentlich nachgearbeitet, will aber nicht ausschließen, dass hier und da AI-Fehler (auch Security-Schlupflöcher) zu finden sind. Bitte entsprechend achtsam verwenden
- Diese Umfrage wird zwangsläufig personenbezogene Daten einsammeln. Unbedingt die Datenschutzerklärung einmal gegenlesen, wenn man das verwendet!