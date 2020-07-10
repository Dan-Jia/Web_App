<?php

error_reporting(E_ALL);                                   // ggf. Debugging
require 'vendor/autoload.php';                            // Composer
use Elasticsearch\ClientBuilder;                          // Elasticsearch client

function createFilterList($listName){

include 'config.php';

$client = ClientBuilder::create()->setHosts([$hostname])->build();

$params = [
    'type' => 'my_type',
    'index' => 'vis01_*',
    'size' => 0,
    'body' => [
      'aggs' => [
        'missions'=> ['terms' => ['field' => $listName, 'size' => 2000]],
      ]//aggs
    ]//body
  ];//params

$response = $client->search($params);
$response = $response['aggregations']['missions']['buckets'];

$missionList=[];
for($i=0;$i<sizeof($response);$i++){
  $missionList[$i]=utf8_encode($response[$i]['key']);
}

return $missionList;
}
