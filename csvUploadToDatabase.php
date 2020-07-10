<?php

// Dieses Script liest die Daten aus der .tsv Datei aus und spielt sie
// entsprechend der Metadaten aus der .json Datei in die
// Elasticsearch-Datenbank ein.
// Siehe auch config.php für Pfade, Parameter usw.

error_reporting(E_ALL);                                           // ggf. Debugging

require 'vendor/autoload.php';                                    // Composer
include 'checkData.php';                                          // Daten überprüfen
use Elasticsearch\ClientBuilder;                                  // Elasticsearch client

////////////////////////////////////////////////////////////////////////////////

function csvUploadToDatabaseFUN($filename){
  include 'config.php';                                           // Konfiguration

  $debug=false;                                                   // Textausgabe

  // Nur Dateinamen ohne z.B. Pfade erlaubt:
  $filename = preg_replace("/\.[^.]+$/", "", $filename);          // nur echte Dateinamen
  if ($filename=='nothing selected') {
    return;
  }//if
  $TSVfilename=$filename.'.tsv';                                  // .. für .tsv Datei
  $JSONfilename=$filename.'.json';                                // .. für .json Datei
  $gridTSVfilename=$filename.'_6.tsv';                            // .. tsv mit Grid Daten

  // Elasticsearch client erzeugen:
  $client = ClientBuilder::create()->setHosts([$hostname])->build();

  // Metadaten laden:
  $metaData = file_get_contents($dataJSONPathName.$JSONfilename);
  $metaData = json_decode($metaData, true);

  // Column descriptions laden:
  $clmDescr = file_get_contents($colDescrTSVPath);
  $clmDescr = json_decode($clmDescr, true);
  $clmNames = array_column($clmDescr, 'id');
  $clmTypes = array_column($clmDescr, 'type');

  // CSV Reader Session starten und ggf. den Index löschen/neu anlegen:
  require_once('CSVReader.class.php');                            // zum Lesen .tsv
  session_start();
  // wenn CSVReader noch nicht angelegt, neu anlegen:
  if (!isset($_SESSION['csv'])) {
    $_SESSION['csv']=new CSVReader($dataTSVPathName.$TSVfilename, '\t');
    $_SESSION['csvGrid']=new CSVReader($gridTSVPathName.$gridTSVfilename, '\t');
    // ggf. Elasticsearch Datenbank bereinigen:
    $params = ['index' => $prefix.$filename];
    if ($client->indices()->exists($params)) {                    // index vorhanden?
      $client->indices()->delete($params);                        // .. löschen
    }//if exists
  // Index neu erstellen, Indexname, Felder und Typen:
  $params=[];
  $params['index']=$prefix.$filename;
  for ($i=0;$i<sizeof($metaData['columns']);$i++) {               // variable Einträge
    $index=array_search($metaData['columns'][$i], $clmNames, $strict=true);
    if ($index) {                                                 // Typ gefunden?
      $type=$typesDict[$clmTypes[$index]];                        // Korrektur des Typ
    } else {
      $type=$typesDict['default'];
    }//if-else index
      $params['body']['mappings']['my_type']['properties']
        [$metaData['columns'][$i]]['type']=$type;
      if ($type=='date') {                                        // ggf. Datumsformate
        $params['body']['mappings']['my_type']['properties']
        [$metaData['columns'][$i]]['format']=implode('||', $dateFormats);
      }//if date
    }//for i
  for ($i=0;$i<sizeof($metaData['constants']);$i++) {             // konstante Einträge
    $index=array_search($metaData['constants'][$i], $clmNames, $strict=true);
    if ($index) {                                                 // Typ gefunden?
      $type=$typesDict[$clmTypes[$index]];                        // Korrektur des Typ
    } else {
      $type=$typesDict['default'];
    }//if-else index
    $params['body']['mappings']['my_type']['properties']
      [$metaData['constants'][$i]['id']]['type']=$type;
      if ($type=='date') {                                        // ggf. Datumsformate
        $params['body']['mappings']['my_type']['properties']
        [$metaData['constants'][$i]['id']]['format']=implode('||', $dateFormats);
      }//if date
    }//for i
  $params['body']['mappings']['my_type']['properties'][$grid5FieldName]['type']='text';
  $params['body']['mappings']['my_type']['properties'][$grid5FieldName]['term_vector']='yes';
  $params['body']['mappings']['my_type']['properties'][$grid5FieldName]['fielddata']='true';
  $params['body']['mappings']['my_type']['properties'][$grid5FieldName]['analyzer']='german';

  $response = $client->indices()->create($params);                // Index neu erstellen
  }//if !isset
  $csv=$_SESSION['csv'];                                          // shortcut erstellen
  $csvGrid=$_SESSION['csvGrid'];                                  // shortcut erstellen

  // finden der Indizes zum Überprüfen der Daten in checkRow():
  $checkRowInfo['start0']=array_search('starttime0', $metaData['columns'], $strict=true);
  $checkRowInfo['stop0']=array_search('stoptime0', $metaData['columns'], $strict=true);
  $checkRowInfo['start1']=array_search('starttime1', $metaData['columns'], $strict=true);
  $checkRowInfo['stop1']=array_search('stoptime1', $metaData['columns'], $strict=true);
  $checkRowInfo['top0']=array_search('top0', $metaData['columns'], $strict=true);
  $checkRowInfo['mid0']=array_search('mid0', $metaData['columns'], $strict=true);
  $checkRowInfo['bottom0']=array_search('bottom0', $metaData['columns'], $strict=true);
  $checkRowInfo['chlorophyllmax0']=array_search('chlorophyllmax0', $metaData['columns'], $strict=true);
  $checkRowInfo['chlorophyllmin0']=array_search('chlorophyllmin0', $metaData['columns'], $strict=true);
  $checkRowInfo['sedimentmin0']=array_search('sedimentmin0', $metaData['columns'], $strict=true);
  $checkRowInfo['sedimentmax0']=array_search('sedimentmax0', $metaData['columns'], $strict=true);
  $checkRowInfo['gelbstoffmin0']=array_search('gelbstoffmin0', $metaData['columns'], $strict=true);
  $checkRowInfo['gelbstoffmax0']=array_search('gelbstoffmax0', $metaData['columns'], $strict=true);

  // leeren "body" für die Kommunikation mit Elasticsearch erstellen, für params:
  $body=[];
  for ($i=0;$i<sizeof($metaData['columns']);$i++) {               // variable Einträge
    $body[$metaData['columns'][$i]]='null';
  }//for
  $checkRowInfo['rowSize']=sizeof($body);                         // für CheckRow()
  for ($i=0;$i<sizeof($metaData['constants']);$i++) {             // konstante Einträge
    $body[$metaData['constants'][$i]['id']]=$metaData['constants'][$i]['value'];
  }//for
  $body[$grid5FieldName]='null';

  // Daten aus .tsv auslesen, elementweise, und in Elasticsearch speichern:
  print("Start import to database at index: ".$csv->key()."<br />");
  $params=[];                                                     // für Übergabe an ES
  while ($csv->valid()) {                                         // für jede Zeile
    $row=$csv->current();                                         // eine Zeile lesen
    $gridRow=$csvGrid->current();
    $csv->next();
    $csvGrid->next();
    $gridRow=str_replace(',',' ',$gridRow);
    for ($i=0;$i<sizeof($row);$i++) {                             // für jede Spalte
      $body[$metaData['columns'][$i]]=$row[$i];                   // "body" befüllen
    }//for
    $body[$grid5FieldName]=$gridRow[2];
    $checkPassed=checkRow($csv->key(), $row, $metaData, $checkRowInfo);
    if ($checkPassed) {                                           // Zeile OK?
      $params['body'][] = ['index' => ['_index' => $prefix.$filename,
                                      '_type' => 'my_type',
                                      '_id' => $filename.' '.$csv->key(),]];
      $params['body'][] = $body;
    }//if
    if ($csv->key()%$numOfLinesToImportPerBulkUpload==0) {        // Packete pro Upload
      if (sizeof($params)>0) {
        $client->bulk($params);
      }//if
      $params=[];
    }//if
  }//while

  if (sizeof($params)>0) {
    $client->bulk($params);
  }//if

  return 'Upload finished';
  session_write_close();
}//function
