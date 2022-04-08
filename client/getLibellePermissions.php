<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string      $jwtToken
 * @param string|null $url
 *
 * @return mixed
 */
function getLibellePermission(string $jwtToken, ?string $url = null)
{
    if (empty($url)) {
        $url = URL_LIBELLE_PERMISSIONS;
    }

    return json_decode(requeteApi($jwtToken, METHOD_GET, $url));
}
