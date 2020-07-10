# Vis-1 - Übung zur Vorlesung Visualisierung

## Aufgabe 1 - Einlesen der Daten

#### a) Konzept
Als Datenbank wird Elasticsearch verwendet. Die Bezeichnung der Indizes in der Datenbank ist wie der Dateiname der **.tsv** Quelldatei ohne die Endung (z.B. **m\_vegetation\_index**). Als ID für die Dokumente wird der Dateiname ohne Endung plus die Zeilennummer in der Quelldatei verwendet (z.B. **m\_vegetation\_index123**). Die Namen der Felder ergeben sich aus den Spaltennamen (aus der **.json** Datei).

##### Begründung für das Konzept:
ElasticSearch wird als Datenbank gewählt, weil diese Datenbank auch bei großen Datenmengen relativ schnell ist, und die Ablage der Daten relativ einfach ist (Indizes und Dokumente). Bezüglich der weiteren Verwendung der Daten ist man bei der Verwendung von Elasticsearch sehr flexibel, es müssen wenige Entscheidungen vorweg getroffen werden (im Gegensatz zur Strukturierung einer relational Datenbank z.B.). Die Daten werden z.B. bisher in verschiedenen Indizes (zu verschiedenen Quelldateien) abgelegt, jedoch kann dies später sehr leicht geändert werden.
Die Verwendung von php (im Gegensatz zB. zu JavaScript) ergibt sich aus dem (momentanen) Wissenstand des Teams.

##### Es werden in die Datenbank eingelesen:
  - Die Konstanten (aus der **.json** Datei)
  - Die Spalteneinträge (aus der **.tsv** Datei)
Die Daten werden über ein php script in die Elasticsearch Datenbank geladen. Zum Auslesen aus der **.tsv** Datei wird die **CSVReader.class** verwendet. Dann werden die Daten über ein API (Elasticsearch Client) an die Datenbank gesendet. Dabei wird immer nach (zB. 1000) Zeilen unterbrochen (kann vom Bediener fortgesetzt werden).

##### Die Daten werden vor dem Versandt an Elasticsearch überprüft auf:
  - Fehlende Einträge (Anzahl der Einträge pro Zeile der Quelldatei)
  - Handelt es sich bei den Einträgen **starttime0**, **stoptime0**, **starttime1**, **stoptime1** um Datumseinträge?
  - Sind die Datumseinträge plausibel (Zeitliche Abfolge von start und stop)
  - Sind die Extremwerte korrekt eingetragen, z.B. sind Minimalwerte chemischer und limnologischer Bestandteile (Chlorophyll, Gelbstoff, Sediment) und Höhenangaben kleiner/gleich dem Maximalwert

##### Ablage der Daten:
Die Datensätze welche zur Verfügung gestellt werden sollen müssen im Ordner **htdocs/uploads** liegen. Es müssen beide Dateien, **.tsv** und **.json**, dort vorhanden sein.

##### Benutzerinterface:
Dem Benutzer wird eine Auswahl von Datensätzen und deren letztes Änderungsdatum angezeigt. Der Benutzer kann über Buttons:
  - Einen Datensatz auswählen (**.tsv** und **.json** Datei müssen jeweils beide im Ordner **htdocs/uploads** liegen)
  - Die Daten aus dem ausgewählten Datensatz in die Datenbank laden
  - Die Daten aus dem ausgewählten Datensatz aus der Datenbank löschen
  - Die gesamte Datenbank löschen (alle Indizes, alle Dokumente)

#### b) Scripte/Dateien
Das Gesamtsystem besteht bisher aus folgenden Dateien:
  - **Webinterface.php:**             HMI
  - **style.css:**                    HMI
  - **CSVReader.class.php:**          Auslesen der .tsv Dateien
  - **csvUploadToDatabase.php:**      Einlesen in die Datenbank
  - **checkData.php:**                Prüfen der Daten
  - **deleteIndex.php:**              Löschen von Indizes
  - **deleteWholeDatabase.php:**      Löschen aller Indizes
  - **reset.php:**                    Zurücksetzen
  - **composer.json**

#### c) Bedienung
0) Vor dem ersten Start muss im Verzeichnis **htdocs** der Befehl **composer install** ausgeführt werden.  
1) Starte die Elasticsearch Datenbank  
2) Starte **Webinterface.php** im Browser und wähle eine **.tsv** Datei, die in die Datenbank hochgeladen werden soll _(Select Data File)_.  
3) Ist eine **.tsv** Datei ausgewählt worden, kann diese in die Datenbank hochgeladen werden _(Upload to database)_.  
4) Ausgewählte Dateien können auch aus der Datenbank gelöscht werden _(Remove from database)_  
5) Die Datenbank kann komplett bereinigt werden _(Flush database)_  


## Aufgabe 2 - API Design und Abfrage

Für diesen Aufgabenteil wurde eine API erstellt, die es erlaubt auf die zuvor eingelesenen und zu Elasticsearch hochgeladenen tsv-Dateien zuzugreifen. Anhand AJAX können durch eine Filterauswahl (Filter: Sensor, Mission, Region in **AJAXDataDownloadFromES.php**) die gewünschten Dateieinträge abgefragt werden, welche dann aus der Datenbank ausgelesen (**downloadFromES.php**) und als Tabelle ausgegeben werden. Die Dropdown-Auswahlliste **AJAXDataDownloadFromES.php** liest automatisch aus dem Ordner filter_options die Auswahllisten aus.  
In der Tabelle werden die Spalten ID, Sensor, Mission, Region, Start- und Stopzeitpunkt, Ausfallsicherheit und Qualität ausgegeben. Wurde eine Tabelle selektiert, die eine dieser Spalten nicht besitzen sollte, wird stattdessen der Wert 'NA' ausgegeben.  

Da der Zugriff nur lesend möglich sein soll, um unerlaubte Änderungen zu vermeiden, wurden zwei Benutzer angelegt, die zuvor im Kibana-Management erzeugt werden müssen:  
- nur lesender Zugriff für die Filterauswahl **downloadFromES.php**:  
      Name: userRead  
      Passwort: password  
- lesender und schreibender Zugriff für sowohl **Webinterface.php** als auch **downloadFromES.php**:  
      Name: user  
      Passwort: password  
