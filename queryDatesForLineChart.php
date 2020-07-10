
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
        'date'=> ['date_histogram'=>[
                      'field'=>'starttime1',
                      'interval'=>'day',
                      'format'=>'yyyy-MM-dd',
                    ]
        ],
      ],
    'query' => [
        'bool' => [
            'must' => [
              ['match' => ['region1' => $_REQUEST['region']]],
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

$result1=$response['aggregations']["date"]["buckets"];

$params = [
    'type' => 'my_type',
    'index' => 'vis01_*',
    'size' => 0,
    'body' => [
      'aggs' => [
        'date'=> ['date_histogram'=>[
                      'field'=>'starttime1',
                      'interval'=>'day',
                      'format'=>'yyyy-MM-dd',
                    ]
        ],
      ],
    'query' => [
        'bool' => [
            'must' => [
              ['match' => ['region0' => $_REQUEST['region']]],
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

$result2=$response['aggregations']["date"]["buckets"];

for($i=0;$i<sizeof($result1);$i++){
  for($ii=0;$ii<sizeof($result2);$ii++){
    if($result2[$ii]['key']==$result1[$i]['key']){
      $result1[$i]['doc_count']+=$result2[$ii]['doc_count'];
      $result2[$ii]['key']='none';
    }
  }
}
$j=sizeof($result1);
for($i=0;$i<sizeof($result2);$i++){
    if($result2[$i]['key']!='none'){
      $result1[$j]['key']=$result2[$i]['key'];
      $result1[$j]['doc_count']=$result2[$i]['doc_count'];
      $result1[$j]['key_as_string']=$result2[$i]['key_as_string'];
      $j+=1;
    }
}
for($i=0;$i<sizeof($result1);$i++)$result1[$i]['key_as_string']=substr($result1[$i]['key_as_string'],0,10);

echo json_encode($result1);

$f=fopen('lineChart.csv','w');
for($i=0;$i<sizeof($result1);$i++)fputcsv($f, $result1[$i], ';');
