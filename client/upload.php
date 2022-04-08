<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string $jwtToken
 * @param int    $choice
 */
function uploadFile(string $jwtToken, $choice = CHOICE_CREATE_FILE)
{
    switch($choice){
        case CHOICE_UPDATE_FILE:
            $fileList = editableFile($jwtToken);

            if ($fileList == null){
                break;
            }

            $id = selectFileToEdit($fileList);

            if ('0' === $id) {
                break;
            }

            if ('-1' === $id){
                echo 'L\'ID renseigné n\'est pas correct'.PHP_EOL;
            } else {
                putFile($jwtToken, $id);
            }

            break;
        case CHOICE_CREATE_FILE:
            postFile($jwtToken);

            break;
    }
}

/**
 * @param array $fileList
 *
 * @return string|null
 */
function selectFileToEdit(array $fileList): ?string
{
    echo PHP_EOL.'ID du fichier que vous voulez écraser : ';
    $idFichier = trim(fgets(STDIN));
    echo PHP_EOL;
    foreach ($fileList as $file) {

        if ((int) $idFichier === $file->id) {
            return $idFichier;
        }
    }

    return '-1';
}

/**
 * @param string $jwt
 * @param string $id
 */
function putFile(string $jwt, string $id)
{
    echo'Veuillez indiquer le chemin du fichier qui va écraser le précédent : ';
    $pathFile = trim(fgets(STDIN));
    echo PHP_EOL;

    if (!file_exists($pathFile)){
        echo 'Le fichier '.$pathFile.' n\'existe pas'.PHP_EOL;
    } else {

        if (is_dir($pathFile)) {
            echo 'Vous avez sélectionné un répertoire et non un fichier'.PHP_EOL;
            return;
        }
        $data = ['content' => file_get_contents($pathFile)];

        $result = json_decode(
            requeteApi($jwt, METHOD_PUT, URL_DOCUMENT.'/'.$id, json_encode($data))
        );

        if (!empty($result)) {
            echo 'Fichier correctement mis à jour'.PHP_EOL;
        }
    }
}

/**
 * @param string $jwt
 */
function postFile(string $jwt)
{
    echo PHP_EOL;
    echo 'Veuillez indiquer le chemin du fichier à upload : ';
    $pathFile = trim(fgets(STDIN));
    echo PHP_EOL;

    if (!file_exists($pathFile)){
        echo 'Le fichier '.$pathFile.' n\'existe pas'.PHP_EOL;
    } else if (is_dir($pathFile)) {
        echo 'Vous avez sélectionné un répertoire et non un fichier'.PHP_EOL;
    } else {
        $data = ['name' => basename($pathFile), 'content' => file_get_contents($pathFile)];
        $result = json_decode(
            requeteApi($jwt, METHOD_POST, URL_DOCUMENT, json_encode($data))
        );

        if (!isset($result->id)) {
            echo 'Une erreur est survenue : votre document n\'a pas été créé, vous n\'êtes peut-être pas autorisés à réaliser cette action'.PHP_EOL;
        } else {
            echo 'Votre document a été enregistré avec succès.'.PHP_EOL;
        }
    }
}

/**
 * @param string $jwtToken
 *
 * @return array|null
 */
function editableFile(string $jwtToken): ?array
{
    $arrayResult = json_decode(requeteApi($jwtToken, METHOD_GET, URL_DOCUMENT.URL_GET_UPDATE));

    $result = [];

    if (empty($arrayResult)) {
        echo 'Vous n\'avez accès a aucun document en édition.'.PHP_EOL;

        return null;
    } elseif (isset($arrayResult->title) && $arrayResult->title == 'An error occurred'){
        echo 'Une erreur côté serveur est survenue.'.PHP_EOL;

        return null;
    } else {
        foreach ($arrayResult as $document) {
            if (!empty($document->id)){
                echo "\t".'ID : '.$document->id.' - '.$document->name.PHP_EOL;
                $result[] = $document;
            }
        }
    }

    return $result;
}