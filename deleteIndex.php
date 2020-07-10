<?php

// Elasticsearch Client erstellen:
require 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;

function deleteIndexFUN($filename)
{
    include 'config.php';                                     // Konfiguration

    $debug=false;

    // Elasticsearch client erzeugen:
    if ($debug) {
        print('hostname: '.$hostname.'<br />');
    }
    $client = ClientBuilder::create()->setHosts([$hostname])->build();
    if ($debug) {
        print("Client created: ".$hostname."<br />");
    }

    // Bestimmten Index löschen:
    $fileN = preg_replace("/\.[^.]+$/", "", $filename);
    $params = ['index' => $prefix.$fileN];
    if ($debug) {
        print("before check"."<br />");
    }
    if ($client->indices()->exists($params)) {
        if ($debug) {
            print("before deletion"."<br />");
        }
        $response = $client->indices()->delete($params);         // .. löschen
        return "Index deleted from database";
    } else {
        return $fileN.' not found in database'.'<br />';
    }
}
