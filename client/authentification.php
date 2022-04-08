<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

/**
 * @param string $login
 * @param string $password
 *
 * @return string|null
 */
function authentification(string $login, string $password): ?string
{
	$data = array('username' => $login, 'password' => $password);

	$tokenResult = json_decode(requeteAuthApi(json_encode($data)));

	/** Si le token récupéré est invalide on arrête le script. */
	if (!isset($tokenResult->token)) {
		echo 'L\'identification à échouée.'.PHP_EOL;

		return null;
	}

//	echo 'Voici le JWT : '.$tokenResult->token.PHP_EOL;

	return $tokenResult->token;
}
?>
