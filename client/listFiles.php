<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string      $jwt
 * @param string|null $method
 */
function listFiles(string $jwt, string $method = null)
{
    $url = URL_DOCUMENT;

    if (null !== $method) {
        $url = $url.$method;
    }

    $retour = requeteApi($jwt, METHOD_GET, $url);

    $arrayResults = json_decode($retour);

    $listFiles = [];

    if (!empty($arrayResults)) {
        echo "\t".'0 : Pour sortir'.PHP_EOL;

        foreach ($arrayResults as $document) {
            if (isset($document->id)) {
                array_push($listFiles, $document->id);
                echo "\t".$document->id.' - '.$document->name.PHP_EOL;
            }
        }
    }

    return $listFiles;
}