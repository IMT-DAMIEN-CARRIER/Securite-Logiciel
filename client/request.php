<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

const URL_DOCUMENT = '/api/documents';
const URL_PERMISSION = '/api/permissions';
const URL_USERS = '/api/users';
const URL_LIBELLE_PERMISSIONS = '/api/libelle_permissions';
const URL_LOGIN = '/api/login_check';

const METHOD_GET = 'GET';
const METHOD_POST = 'POST';
const METHOD_PUT = 'PUT';
const METHOD_DELETE = 'DELETE';

/**
 * @param string      $jwt
 * @param string      $method
 * @param string      $ressource_location
 * @param string|null $data
 *
 * @return bool|string
 */
function requeteApi(string $jwt, string $method, string $ressource_location, ?string $data = null)
{
    $curl = curl_init(URL.$ressource_location);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        [
            'accept: application/json',
            'Authorization: Bearer '.$jwt,
            'Content-Type: application/json',
        ]
    );
    $response = curl_exec($curl);

    curl_close($curl);


    return $response;
}

/**
 * @param string $data
 *
 * @return bool|string
 */
function requeteAuthApi(string $data)
{
    /** Premier curl pour récupéré le token pour se connecter à API Platform. */
    $ch = curl_init(URL.URL_LOGIN);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            'Content-Type: application/json',
            'Content-Length: '.strlen($data),
        ]
    );

    return curl_exec($ch);
}
?>
