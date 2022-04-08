#!/usr/bin/php -q
<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */
include_once 'request.php';
include_once 'authentification.php';
include_once 'upload.php';
include_once 'download.php';
include_once 'listFiles.php';
include_once 'deleteFile.php';
include_once 'getUser.php';
include_once 'getLibellePermissions.php';
include_once 'listPermissions.php';
include_once 'deletePermission.php';
include_once 'addPermission.php';

const CHOICE_CREATE_FILE = 1;
const CHOICE_UPDATE_FILE = 2;
const URL = 'https://securite-app.damscrr.fr';

const URL_GET_DELETE = '/get_delete';
const URL_GET_UPDATE = '/get_update';

$jwt = null;

const SEPARATOR = '----------------------------------------------';

do {
    echo 'Authentification ...'.PHP_EOL;
    echo 'Login : ';
    $login = trim(fgets(STDIN));
    echo 'Password : ';
    system('stty -echo'); // Cache l'input de l'utilisateur
    $password = trim(fgets(STDIN));
    system('stty echo'); // Arrete de cacher les inputs utilisateur
    echo PHP_EOL;
    $jwt = authentification($login, $password);
} while (null === $jwt);

$exit = false;

do {
    echo PHP_EOL.SEPARATOR.PHP_EOL.PHP_EOL;
    echo 'Liste des actions possibles :'.PHP_EOL.PHP_EOL;
    echo "\t".'1)  Lister les fichiers accessibles'.PHP_EOL;
    echo "\t".'2)  Lister les permissions consultables'.PHP_EOL;
    if ($login != 'admin'){
        echo "\t".'3)  Créer un fichier'.PHP_EOL;
        echo "\t".'4)  Télécharger un fichier'.PHP_EOL;
        echo "\t".'5)  Supprimer un fichier'.PHP_EOL;
        echo "\t".'6)  Modifer un fichier'.PHP_EOL;
        echo "\t".'7)  Liste des utilisateurs'.PHP_EOL;
        echo "\t".'8)  Liste des libellés pour les permissions'.PHP_EOL;
        echo "\t".'9)  Supprimer une permission'.PHP_EOL;
        echo "\t".'10) Ajouter une permission'.PHP_EOL;
    }
    echo "\t".'0)  Sortir de l\'application'.PHP_EOL;
    echo PHP_EOL.'Entrez le numéro de l\'action à réaliser (N\'importe quelle autre touche pour sortir) : ';
    $choix = trim(fgets(STDIN));

    switch ($choix) {
        case '1':
            echo PHP_EOL.'Liste des documents accessibles :'.PHP_EOL.PHP_EOL;
            $fileList = listFiles($jwt);

            break;
        case '2':
            echo 'Voici la liste des permissions que vous pouvez consulter : '.PHP_EOL.PHP_EOL;
            $liste_permissions = listPermissions($jwt, '/get_delete');

            break;
        case '3':
            if ($login == 'admin'){
                break;
            }
            echo PHP_EOL.'Bienvenue dans la création de fichier !'.PHP_EOL.PHP_EOL;
            uploadFile($jwt);

            break;
        case '4':
            if ($login == 'admin'){
                break;
            }
            echo PHP_EOL.'Voici la liste des documents téléchargeable :'.PHP_EOL.PHP_EOL;
            $fileList = listFiles($jwt, '/get_download');

            if (empty($fileList)) {
                echo 'Vous ne pouvez télécharger aucun fichier.'.PHP_EOL;
                break;
            }

            echo PHP_EOL;
            echo 'Download un fichier : ';
            $idFile = trim(fgets(STDIN));
            echo PHP_EOL;

            if ('0' === $idFile) {
                break;
            }

            if (!in_array($idFile, $fileList)) {
                echo 'Le document mentionné ne fais pas parti des documents que vous pouvez télécharger'.PHP_EOL;
                break;
            }

            echo PHP_EOL;
            $stringPrompt = 'Où voulez vous l\'enregistrer (chemin vers le dossier de destination sans le "/" ) la fin) ';
            $stringPrompt .= '(Si vide sera enregistré dans /tmp) : ';

            echo $stringPrompt;
            $dir = trim(fgets(STDIN));
            echo PHP_EOL;

            downloadFile($jwt, $idFile, $dir);

            break;
        case '5':
            if ($login == 'admin'){
                break;
            }
            echo PHP_EOL.'Voici la liste des documents que vous pouvez supprimer :'.PHP_EOL.PHP_EOL;
            $fileList = listFiles($jwt, URL_GET_DELETE);

            echo PHP_EOL;
            echo 'Donner l\'id du fichier à supprimer : ';
            $idFile = trim(fgets(STDIN));
            echo PHP_EOL;

            if ('0' === $idFile) {
                break;
            }

            if (!in_array($idFile, $fileList)) {
                echo 'L\'ID mentionné ne fais pas parti des documents que vous pouvez supprimer'.PHP_EOL;
                break;
            }

            deleteFile($jwt, $idFile);

            break;
        case '6':
            if ($login == 'admin'){
                break;
            }
            echo 'Bienvenue dans la zone d\'édition de fichier !'.PHP_EOL.PHP_EOL;
            uploadFile($jwt, CHOICE_UPDATE_FILE);

            break;
        case '7':
            if ($login == 'admin'){
                break;
            }
            echo 'Voici la liste des autres utilisateurs présents sur le site : '.PHP_EOL.PHP_EOL;
            $userList = getUser($jwt);

            break;
        case '8':
            if ($login == 'admin'){
                break;
            }
            echo 'Voici la liste des libellées concernant les permissions : '.PHP_EOL.PHP_EOL;
            $arrayResults = getLibellePermission($jwt);

            if (!empty($arrayResults)) {
                echo "\t".'0 : Pour sortir'.PHP_EOL;

                foreach ($arrayResults as $libellePermission) {
                    echo "\t".$libellePermission->id.' - '.$libellePermission->title.PHP_EOL;
                }
            }

            break;
        case '9':
            if ($login == 'admin'){
                break;
            }
            echo 'Voici la liste des permissions que vous pouvez supprimer : '.PHP_EOL.PHP_EOL;
            $liste_permissions = listPermissions($jwt, '/get_delete');

            echo PHP_EOL;
            echo 'Donner l\'id de la permission à supprimer : ';
            $idPermission = trim(fgets(STDIN));
            echo PHP_EOL;

            if ('0' === $idPermission) {
                break;
            }

            if (!in_array($idPermission, $liste_permissions)) {
                echo 'L\'ID mentionné ne fais pas parti des permissions que vous pouvez supprimer'.PHP_EOL;
                break;
            }

            deletePermission($jwt, $idPermission);

            break;
        case '10':
            if ($login == 'admin'){
                break;
            }
            echo 'Bienvenue dans la zone de création de permission.'.PHP_EOL.PHP_EOL;

            addPermissions($jwt);

            break;
        case '0':
            $exit = true;

            break;
        default:
            echo 'Veuillez choisir une option valide'.PHP_EOL.PHP_EOL;

            break;
    }
} while (!$exit);
