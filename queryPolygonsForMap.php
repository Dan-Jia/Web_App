
<?php

error_reporting(E_ALL);                                   // ggf. Debugging

require 'vendor/autoload.php';                            // Composer
use Elasticsearch\ClientBuilder;                          // Elasticsearch client

include 'config.php';                                     // Konfiguration

$client = ClientBuilder::create()->setHosts([$hostname])->build();

$params = [
    'type' => 'my_type',
    'index' => $prefix.'*',
    'size' => 0,
    'body' => [
      'aggs' => [
        'gridIDs'=> ['terms' => ['field' => $grid5FieldName, 'size' => 2000]],
      ],
    'query' => [
        'bool' => [
            'must' => [
              ['match' => ['sensor0' => $_REQUEST['sensor0']]],
          ],//must
            'filter' => [
              ['range' => ['starttime1' => [  'gte' => substr($_REQUEST['starttime'],0,10),
                                              'lte' => substr($_REQUEST['stoptime'],0,10),
                                              'format' => 'yyyy-MM-dd']]
              ]//range
          ]//filter
        ]//bool
      ]//query
    ]//body
  ];//params

$response = $client->search($params);

$result=$response['aggregations']['gridIDs']['buckets'];
$resultKeys=[];
for($i=0;$i<sizeof($result);$i++)$resultKeys[$i]=strtoupper(substr($result[$i]['key'],0,6));
$resultKeys=array_values(array_unique($resultKeys));

$polys=[];

for($i=0;$i<sizeof($resultKeys);$i++){

  $params = [
    'type' => $prefix.'grid_type',
    'index' => $grid5Idx,
    'size' => 1,
    'body' => [
    'query' => [
        'bool' => [
            'must' => [
                ['match' => ['_id' => $grid5IDPrefix.$resultKeys[$i]]],
          ]//must
        ]//bool
      ]//query
    ]//body
  ];//params

  $response = $client->search($params);
  if(isset($response['hits']['hits'][0]['_source'][$polyFieldName]))$polys[$i]=$response['hits']['hits'][0]['_source'][$polyFieldName];
}//for

echo json_encode($polys);

$f=fopen('polygons.csv','w');
fputcsv($f, $polys, ';');
