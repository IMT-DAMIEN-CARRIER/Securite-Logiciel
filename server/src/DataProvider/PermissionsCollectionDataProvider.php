<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\LibellePermission;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Service\PermissionsPermissionService;
use Symfony\Component\Security\Core\Security;

/**
 * Class PermissionsCollectionDataProvider.
 */
class PermissionsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    const GET_DELETE = 'get_delete';
    const GET_UPDATE = 'get_update';

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var PermissionRepository
     */
    private PermissionRepository $permissionsRepository;

    private PermissionsPermissionService $permissionsPermissionService;

    /**
     * PermissionsCollectionDataProvider constructor.
     *
     * @param Security                     $security
     * @param PermissionRepository         $permissionsRepository
     * @param PermissionsPermissionService $permissionsPermissionService
     */
    public function __construct(Security $security, PermissionRepository $permissionsRepository, PermissionsPermissionService $permissionsPermissionService)
    {
        $this->security = $security;
        $this->permissionsRepository = $permissionsRepository;
        $this->permissionsPermissionService = $permissionsPermissionService;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $permissions = $this->permissionsRepository->findAll();

        if (in_array(Role::ROLE_ADMIN, $user->getRoles())) {
            return $permissions;
        }

        $results = [];

        if (self::GET_DELETE === $operationName) {
            $results = $this->permissionsPermissionService
                ->generateArrayResults($user, $permissions, $results);
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
        return Permission::class === $resourceClass;
    }
}