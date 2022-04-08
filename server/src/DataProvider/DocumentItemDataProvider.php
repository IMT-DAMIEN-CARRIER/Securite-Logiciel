<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Document;
use App\Entity\LibellePermission;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\DocumentRepository;
use App\Service\DocumentPermissionService;
use App\Service\EncryptionService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class DocumentItemDataProvider.
 */
class DocumentItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    const GET = 'get';
    const GET_DOWNLOAD = 'get_download';

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var DocumentRepository
     */
    private DocumentRepository $documentRepository;

    /**
     * @var DocumentPermissionService
     */
    private DocumentPermissionService $documentReadPermissionService;

    /**
     * @var EncryptionService
     */
    private EncryptionService $encryptionService;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * DocumentItemDataProvider constructor.
     *
     * @param Security                  $security
     * @param DocumentRepository        $documentRepository
     * @param DocumentPermissionService $documentReadPermissionService
     * @param EncryptionService         $encryptionService
     * @param LoggerInterface           $logger
     */
    public function __construct(Security $security, DocumentRepository $documentRepository, DocumentPermissionService $documentReadPermissionService, EncryptionService $encryptionService, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->documentRepository = $documentRepository;
        $this->documentReadPermissionService = $documentReadPermissionService;
        $this->encryptionService = $encryptionService;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (self::GET_DOWNLOAD === $operationName && in_array(Role::ROLE_ADMIN, $user->getRoles())) {
            return null;
        }

        $document = $this->documentRepository->find($id);

        if (!empty($document->getCryptTag())) {
            $decryptedContent = $this->encryptionService->decrypt($document);

            if (false === $decryptedContent) {
                throw new \ErrorException();
            }

            $document->setContent($decryptedContent);
            $document->setCryptTag(null);
        }

        if (in_array(Role::ROLE_ADMIN, $user->getRoles())) {
            return $document;
        }

        if ($document->getUser() === $user) {
            return $document;
        }

        $canRead = $this->documentReadPermissionService
            ->canDoToDocument($user, $document, LibellePermission::READ);

        if (!$canRead) {
            return null;
        }

        return $document;
    }

    /**
     * @param string      $resourceClass
     * @param string|null $operationName
     * @param array       $context
     *
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Document::class === $resourceClass;
    }
}