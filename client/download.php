<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * Cette méthode est utiliser pour télécharger les données liées au document, ainsi que de créer ce document si il existe.
 *
 * @param string      $jwtToken
 * @param int         $id
 * @param string|null $dir Le chemin vers le fichier si nécessaire. Si null sera créer dans /tmp.
 */
function downloadFile(string $jwtToken, int $id, ?string $dir = null)
{
    $result = requeteApi($jwtToken, METHOD_GET, URL_DOCUMENT.'/get_download/'.$id);

    if (empty($dir)) {
        $dir = '/tmp/';
    } else {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    $arrayResults = json_decode($result, true);

    if (empty($arrayResults['name'])) {
        echo PHP_EOL.'Vous n\'avez pas accès à ce document ou il n\'existe pas.'.PHP_EOL;
        exit;
    } else {
        $path = $dir.'/'.$arrayResults['name'];
        $content = $arrayResults['content'];

        file_put_contents($path, $content);

        if (file_exists($dir.'/'.$arrayResults['name'])) {
            echo PHP_EOL;
            echo 'Votre fichier à bien été créer au chemin suivant : '.$dir.'/'.$arrayResults['name'];
            echo PHP_EOL;
        } else {
            echo PHP_EOL;
            echo 'Il y a eu un problème lors de la création de votre fichier.';
            echo PHP_EOL;
            exit;
        }
    }

    echo PHP_EOL.SEPARATOR.PHP_EOL.PHP_EOL;
}
?>