<?php

error_reporting(E_ALL);                                           // ggf. Debugging

require 'vendor/autoload.php';                                    // Composer
include 'checkData.php';                                          // Daten überprüfen
use Elasticsearch\ClientBuilder;                                  // Elasticsearch client

////////////////////////////////////////////////////////////////////////////////
  include 'config.php';

  $debug=false;                                                   // Textausgabe

  $filename='_grid_5.tsv';

  // Elasticsearch client erzeugen:
  $client = ClientBuilder::create()->setHosts([$hostname])->build();

  // CSV Reader Session starten und ggf. den Index löschen/neu anlegen:
  require_once('CSVReader.class.php');                            // zum Lesen .tsv
  session_start();
  // wenn CSVReader noch nicht angelegt, neu anlegen:
  if (!isset($_SESSION['csv2'])) {
    $_SESSION['csv2']=new CSVReader($gridPathName.$filename, '\t');
    // ggf. Elasticsearch Datenbank bereinigen:
    $params = ['index' => $grid5Idx];
    if ($client->indices()->exists($params)) {                    // index vorhanden?
      $client->indices()->delete($params);                        // .. löschen
    }//if exists
    // Index neu erstellen, Indexname, Felder und Typen:
    $params=[];
    $params['index']=$grid5Idx;
    $params['body']['mappings'][$prefix.'grid_type']['properties'][$polyFieldName]['type']='text';
    $response = $client->indices()->create($params);                // Index neu erstellen
  }//if !isset
  $csv=$_SESSION['csv2'];                                          // shortcut erstellen

  // leeren "body" für die Kommunikation mit Elasticsearch erstellen, für params:
  $body=[];
  $body[$polyFieldName]='null';

  // Daten aus .tsv auslesen, elementweise, und in Elasticsearch speichern:
  $params=[];                                                     // für Übergabe an ES
  while ($csv->valid()) {                                         // für jede Zeile
    $row=$csv->current();                                         // eine Zeile lesen
    $csv->next();

    $body[$polyFieldName]=$row[1];
    $params['body'][] = ['index' => ['_index' => $grid5Idx,
                                      '_type' => $prefix.'grid_type',
                                      '_id' => $grid5IDPrefix.$row[0]]];
    $params['body'][] = $body;

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

  unset($_SESSION['csv2']);
  session_write_close();
  print("Upload of grid data finished"."<br />");
