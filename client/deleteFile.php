<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string $jwtToken
 * @param int    $idFile
 */
function deleteFile(string $jwtToken, int $idFile)
{
    $arrayResult = json_decode(requeteApi($jwtToken, METHOD_DELETE, URL_DOCUMENT.'/'.$idFile));
    if (empty($arrayResult)) {
        echo PHP_EOL.'Le fichier à bien été supprimé'.PHP_EOL;
    } else {
        $stringError = 'Le fichier que vous tentez de supprimer n\'existe pas';
        $stringError .= ' ou vous n\'avez pas les droits de suppression sur ce fichier.';

        echo PHP_EOL.$stringError.PHP_EOL;
    }

    echo PHP_EOL.SEPARATOR.PHP_EOL.PHP_EOL;
}