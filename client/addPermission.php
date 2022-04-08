<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string $jwtToken
 */
function addPermissions(string $jwtToken)
{
    echo PHP_EOL.'Voici la liste de vos documents :'.PHP_EOL;
    $fileList = listFiles($jwtToken);

    echo PHP_EOL.'Choississez un document ou vous voulez ajouter une permission : ';
    $idFile = trim(fgets(STDIN));

    if ('0' === $idFile) {
        return;
    }

    if (!in_array($idFile, $fileList)) {
        echo 'L\'ID mentionné ne fais pas parti des documents sur lesquels vous pouvez ajouter une permission'.PHP_EOL;
        return;
    }

    $urlFile = URL_DOCUMENT.'/'.$idFile;

    echo PHP_EOL.'Voici la liste des utilisateurs à qui vous pouvez ajouter des droits :'.PHP_EOL.PHP_EOL;
    $userList = getUser($jwtToken);

    echo PHP_EOL.'Choississez un utilisateur à qui vous voulez ajouter la permission : ';
    $idUser = trim(fgets(STDIN));

    if ('0' === $idUser) {
        return;
    }

    if (!in_array($idUser, $userList)) {
        echo 'L\'ID mentionné ne fais pas parti des utilisateurs auquels vous pouvez donner des permissions'.PHP_EOL;
        return;
    }

    $urlUser = URL_USERS.'/'.$idUser;

    echo PHP_EOL.'Voici la liste des droits pouvant être ajoutés :'.PHP_EOL;
    $arrayResults = getLibellePermission($jwtToken);
    $listLibellePermission = [];

    if (!empty($arrayResults)) {
        echo "\t".'0 : Pour sortir'.PHP_EOL;

        foreach ($arrayResults as $libellePermission) {
            array_push($listLibellePermission, $libellePermission->id);
            echo "\t".$libellePermission->id.' - '.$libellePermission->title.PHP_EOL;
        }
    }

    echo PHP_EOL.'Choississez un type de permission à ajouter à cet utilisateur : ';
    $idLibellePermission = trim(fgets(STDIN));

    if ('0' === $idLibellePermission) {
        return;
    }

    if (!in_array($idLibellePermission, $listLibellePermission)) {
        echo 'L\'ID mentionné n\'appartient à aucun libelle depermission disponible'.PHP_EOL;
        return;
    }

    $urlLibellePermission = URL_LIBELLE_PERMISSIONS.'/'.$idLibellePermission;

    $arrayData = [
        'document' => $urlFile,
        'user' => $urlUser,
        'libellePermission' => $urlLibellePermission,
    ];

    $result = json_decode(
        requeteApi(
            $jwtToken,
            METHOD_POST,
            URL_PERMISSION,
            json_encode($arrayData)
        )
    );

    if (!isset($result->id)) {
        if ($result->id == NULL){
            echo 'La permission que vous tentez d\'ajouter existe déjà.'.PHP_EOL;
        } else {
            echo 'Une erreur est survenue : '.$result->violations[0]->message.PHP_EOL;
        }
    } else {
        echo 'Votre permission a bien été enregistré avec succès'.PHP_EOL;
    }
}