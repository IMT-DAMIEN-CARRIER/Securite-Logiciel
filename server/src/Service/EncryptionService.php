<?php

namespace App\Service;


use App\Entity\Document;

class EncryptionService
{
    private const CYPHER = 'aes-256-gcm';

    /**
     * @var string
     */
    private string $privateKey;

    /**
     * @var string
     */
    private string $ivEncryptSsl;

    /**
     * EncryptionService constructor.
     *
     * @param string $privateKey
     * @param string $ivEncryptSsl
     */
    public function __construct(string $privateKey, string $ivEncryptSsl)
    {
        $this->privateKey = $privateKey;
        $this->ivEncryptSsl = $ivEncryptSsl;
    }

    /**
     * Méthode public pour le chiffrement des documents.
     *
     * @param string $content
     *
     * @return array|null
     */
    public function encrypt(string $content): ?array
    {
        $result = self::finalEncryption($content, $this->privateKey, $this->ivEncryptSsl);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * Méthode public pour le déchiffrement des documents.
     *
     * @param Document $document
     *
     * @return false|string
     */
    public function decrypt(Document $document)
    {
        return self::finalDecryption($document, $this->privateKey, $this->ivEncryptSsl);
    }

    /**
     * @param string $content
     * @param string $privateKey
     * @param string $ivEncryptSsl
     *
     * @return array|null
     */
    public static function finalEncryption(string $content, string $privateKey, string $ivEncryptSsl): ?array
    {
        $result = [];

        $result['encryptedContent'] = openssl_encrypt(
            $content,
            self::CYPHER,
            $privateKey,
            0,
            $ivEncryptSsl,
            $tagCryptage
        );

        $result['tagCryptage'] = $tagCryptage;

        if (false === $result['encryptedContent']) {
            return ['error' => openssl_error_string()];
        }

        return $result;
    }

    /**
     * @param Document $document
     * @param string   $privateKey
     * @param string   $ivEncryptSsl
     *
     * @return false|string
     */
    public static function finalDecryption(Document $document, string $privateKey, string $ivEncryptSsl)
    {
        return openssl_decrypt(
            $document->getContent(),
            self::CYPHER,
            $privateKey,
            0,
            $ivEncryptSsl,
            $document->getCryptTag()
        );
    }
}