<?php

// Elasticsearch Client erstellen:
require 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;

function deleteAllFUN()
{
    include 'config.php';                                       // Konfiguration

    $debug=false;

    // Elasticsearch Client erzeugen:
    if ($debug) {
        print('hostname: '.$hostname.'<br />');
    }
    $client = ClientBuilder::create()->setHosts([$hostname])->build();
    if ($debug) {
        print("Client created: ".$hostname."<br />");
    }

    $params = ['index' => '*',];

    if ($debug) {
        print("before deletion"."<br />");
    }
    $response = $client->indices()->delete($params);
    if ($debug) {
        print('All deleted'.'<br />');
    }

    return "Everything deleted from database";
}//function
