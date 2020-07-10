<?php

////// Elasticsearch user, ! DARF NICHT SUPERUSER SEIN !:
$user='user';
$password='password';
// Adresse von Elasticsearch:
$hostname='http://'.$user.':'.$password.'@localhost:9200';

////// Elasticsearch zweiter user, der nur lesen darf, ! DARF NICHT SUPERUSER SEIN !:
$userRead='user';
$passwordRead='password';
// Adresse von Elasticsearch:
$hostnameRead='http://'.$userRead.':'.$passwordRead.'@localhost:9200';

// Anzahl Zeilen welche in einem Durchgang importiert werden sollen:
$numOfLinesToImportPerButtonPress=50000;
$numOfLinesToImportPerBulkUpload=5000;
// Pfade zu den .tsv und .json Dateien:
$dataTSVPathName=__DIR__ . DIRECTORY_SEPARATOR .'data'.DIRECTORY_SEPARATOR;
$gridTSVPathName=__DIR__ . DIRECTORY_SEPARATOR .'gridData'.DIRECTORY_SEPARATOR;
$dataJSONPathName=__DIR__ . DIRECTORY_SEPARATOR .'meta'.DIRECTORY_SEPARATOR;
$gridPathName=__DIR__ . DIRECTORY_SEPARATOR .'grid'.DIRECTORY_SEPARATOR;
// Pfad zu _columnDescription.json
$colDescrTSVPath=__DIR__ . DIRECTORY_SEPARATOR .'filter_options'.DIRECTORY_SEPARATOR.'_columnDescription.json';
// Dictionary for types:
$typesDict=[
              'Text'=>'keyword',
              'Date'=>'date',
              'Identifier'=>'keyword',
              'GeoObject'=>'keyword',
              'Integer'=>'integer',
              'Double'=>'double',
              'Character'=>'char',
              'Identifier'=>'keyword',
              'default'=>'keyword'
];
// Formate für Felder vom Typ "date":
$dateFormats=['yyyy-MM-dd HH:mm:ss.SSS',
              'yyyy-MM-dd HH:mm:ss',
              'yyyy-MM-dd||epoch_millis'];
// prefix für Indexnamen aller Daten (außer Grid-Index):
$prefix='vis01_';
// Feldname für die Grid-Kürzel:
$grid5FieldName='gridIDs';
// Indexname für Grid-5-Daten:
$grid5Idx='vis01_grid5idx'; // $prefix wird nich autom. vorangestellt
// Prefix für die IDs der Dokumente der Grid-5-Daten:
$grid5IDPrefix='vis01_grid5_'; // $prefix wird nicht autom. vorangestellt
// Feldname für die Polygone in den Grid-5-Daten:
$polyFieldName='Poly';
