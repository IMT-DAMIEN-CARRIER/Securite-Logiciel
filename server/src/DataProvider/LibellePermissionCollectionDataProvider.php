<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\LibellePermission;
use App\Repository\LibellePermissionRepository;

/**
 * Class LibellePermissionCollectionDataProvider.
 */
class LibellePermissionCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var LibellePermissionRepository
     */
    private LibellePermissionRepository $libellePermissionRepository;

    /**
     * LibellePermissionCollectionDataProvider constructor.
     *
     * @param LibellePermissionRepository $libellePermissionRepository
     */
    public function __construct(LibellePermissionRepository $libellePermissionRepository)
    {
        $this->libellePermissionRepository = $libellePermissionRepository;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->libellePermissionRepository->findAll();
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
        return LibellePermission::class === $resourceClass;
    }
}