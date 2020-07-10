
<?php

error_reporting(E_ALL);                                   // ggf. Debugging

require 'vendor/autoload.php';                            // Composer
use Elasticsearch\ClientBuilder;                          // Elasticsearch client

include 'config.php';                                     // Konfiguration

$client = ClientBuilder::create()->setHosts([$hostname])->build();


$params = [
    'type' => 'my_type',
    'index' => 'vis01_*',
    'size' => 0,
    'body' => [
      'aggs' => [
          'AggSensorDate'=> [
               'terms' => ['field' => 'sensor0', 'size' => 100],
          ],
       ],
      'query' => [
          'bool' => [
            //  'must' => [
            //      ['match' => ['sensor0' => $_REQUEST['sensor0']]],
            // //     // ['match' => ['mission0' => $_REQUEST['mission0']]],
            // //     ['match' => ['region1' => $_REQUEST['region1']]],
            //  ],//must
              'filter' => [
                ['range' => ['starttime1' => [  'gte' => substr($_REQUEST['starttime'], 0, 10),
                                                'lte' => substr($_REQUEST['stoptime'], 0, 10),
                                                'format' => 'yyyy-MM-dd']]
                ]//range
            ]//filter
          ]//bool
        ]//query
    ],//body
  ];//params

$response = $client->search($params);

$result=$response['aggregations']["AggSensorDate"]["buckets"];

echo json_encode($result);

$f=fopen('barChart.csv','w');
for($i=0;$i<sizeof($result);$i++)fputcsv($f, $result[$i], ';');
