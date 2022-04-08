<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
*/

/**
 * @param string      $jwt
 * @param string      $idPermission
*/
function deletePermission(string $jwt, string $idPermission)
{
    $arrayResults = json_decode(requeteApi($jwt, METHOD_DELETE, URL_PERMISSION.'/'.$idPermission));

    if (empty($arrayResults)) {
        echo PHP_EOL.'La permission '.$idPermission.' a été correctement supprimée'.PHP_EOL; 
    } else {
        $stringError = 'La permission que vous tentez de supprimer n\'existe pas';
        $stringError .= ' ou vous n\'avez pas les droits de la supprimer.';

        echo PHP_EOL.$stringError.PHP_EOL;
    }

    echo PHP_EOL.SEPARATOR.PHP_EOL.PHP_EOL;
}

?>