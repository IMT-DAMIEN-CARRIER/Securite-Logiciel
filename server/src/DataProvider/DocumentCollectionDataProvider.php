<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Document;
use App\Entity\LibellePermission;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\DocumentRepository;
use App\Service\DocumentPermissionService;
use Symfony\Component\Security\Core\Security;

/**
 * Class DocumentCollectionDataProvider.
 */
class DocumentCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    const GET_DELETE = 'get_delete';
    const GET_UPDATE = 'get_update';
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
    private DocumentPermissionService $documentPermissionService;

    /**
     * DocumentCollectionDataProvider constructor.
     *
     * @param Security                  $security
     * @param DocumentRepository        $documentRepository
     * @param DocumentPermissionService $documentPermissionService
     */
    public function __construct(Security $security, DocumentRepository $documentRepository, DocumentPermissionService $documentPermissionService)
    {
        $this->security = $security;
        $this->documentRepository = $documentRepository;
        $this->documentPermissionService = $documentPermissionService;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $documents = $this->documentRepository->findAll();

        $results = [];

        switch ($operationName) {
            case self::GET_DELETE:
                $results = $this->documentPermissionService
                    ->generateArrayResult($user, $documents, LibellePermission::DELETE, $results);

                break;
            case self::GET_UPDATE:
                $results = $this->documentPermissionService
                    ->generateArrayResult($user, $documents, LibellePermission::EDIT, $results);

                break;
            default:
                if (self::GET_DOWNLOAD === $operationName && in_array(Role::ROLE_ADMIN, $user->getRoles())) {
                    return $results;
                }

                if (in_array(Role::ROLE_ADMIN, $user->getRoles())) {
                    return $documents;
                }

                $results = $this->documentPermissionService
                    ->generateArrayResult($user, $documents, LibellePermission::READ, $results);

                break;
        }

        return $results;
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