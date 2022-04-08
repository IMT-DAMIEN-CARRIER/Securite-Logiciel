<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string $jwt
 * @param string $method
 *
 * @return array
 */
function listPermissions(string $jwt, string $method): array
{
    $url = URL_PERMISSION.$method;
    $arrayResult = json_decode(requeteApi($jwt, METHOD_GET, $url));
    $listPermissions = [];

    if (!empty($arrayResult)) {
        echo "\t".'0 : Pour sortir'.PHP_EOL;

        foreach ($arrayResult as $permission) {
            if (isset($permission->id)) {
                array_push($listPermissions, $permission->id);

                $document = getDocument($jwt, $permission->document);
                $user = getUserPermission($jwt, $permission->user);
                $libellePermission = getLibellePermission($jwt, $permission->libellePermission);

                if (!isset($document->id) || !isset($user->id)) {
                    echo 'Il y a eu un soucis lors de la récupération du document ou de l\'utilisateur.'.PHP_EOL;
                    exit;
                }

                $stringResult = "\t".$permission->id.' - Permission '.$libellePermission->title.' donnée à '.$user->login.' sur le fichier '.$document->name;
                $stringResult .= ' (ID '.$document->id.')'.PHP_EOL;
                echo $stringResult;
            }
        }
    } else {
        echo "\t".'Vous n\'avez pas encore donner de permissions sur vos fichiers.'.PHP_EOL;
    }

    return $listPermissions;
}

/**
 * @param string $jwtToken
 * @param string $url
 *
 * @return mixed
 */
function getDocument(string $jwtToken, string $url)
{
    return json_decode(requeteApi($jwtToken, METHOD_GET, $url));
}

/**
 * @param string $jwtToken
 * @param string $url
 *
 * @return mixed
 */
function getUserPermission(string $jwtToken, string $url)
{
    return json_decode(requeteApi($jwtToken, METHOD_GET, $url));
}
