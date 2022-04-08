<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string $jwtToken
 */
function getUser(string $jwtToken)
{
    $retour = requeteApi($jwtToken, METHOD_GET, URL_USERS);

    $arrayResults = json_decode($retour);

    $listUser = [];

    if (!empty($arrayResults)) {
        echo "\t".'0 : Pour sortir'.PHP_EOL;

        foreach ($arrayResults as $user) {
            array_push($listUser, $user->id);
            echo "\t".'ID : '.$user->id.' - '.$user->login.PHP_EOL;
        }
    }

    return $listUser;
}
