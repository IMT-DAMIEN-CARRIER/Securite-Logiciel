<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\Service;

use App\Entity\Document;
use App\Entity\LibellePermission;
use App\Entity\Permission;
use App\Entity\User;
use App\Repository\LibellePermissionRepository;

/**
 * Class DocumentCreatorRightsService.
 */
class DocumentCreatorRightsService
{
    /**
     * @var LibellePermissionRepository
     */
    private LibellePermissionRepository $libellePermissionRepository;

    /**
     * DocumentCreatorRightsService constructor.
     *
     * @param LibellePermissionRepository $libellePermissionRepository
     */
    public function __construct(LibellePermissionRepository $libellePermissionRepository)
    {
        $this->libellePermissionRepository = $libellePermissionRepository;
    }

    /**
     * @param Document $document
     * @param User     $user
     *
     * @return Document
     */
    public function grantAllRightOnDocumentToUser(Document $document, User $user): Document
    {
        $read = (new Permission())
            ->setUser($user)
            ->setDocument($document)
            ->setLibellePermission($this->getPermission());

        $document->addPermission($read);

        $delete = (new Permission())
            ->setUser($user)
            ->setDocument($document)
            ->setLibellePermission($this->getPermission(LibellePermission::DELETE));

        $document->addPermission($delete);

        $edit = (new Permission())
            ->setUser($user)
            ->setDocument($document)
            ->setLibellePermission($this->getPermission(LibellePermission::EDIT));

        $document->addPermission($edit);

        return $document;
    }

    /**
     * @param string $permission
     *
     * @return LibellePermission
     */
    public function getPermission(string $permission = LibellePermission::READ): LibellePermission
    {
        switch ($permission) {
            case LibellePermission::DELETE:
                $result = $this->libellePermissionRepository->findOneBy(['title' => LibellePermission::DELETE]);

                break;
            case LibellePermission::EDIT:
                $result = $this->libellePermissionRepository->findOneBy(['title' => LibellePermission::EDIT]);

                break;
            default:
                $result = $this->libellePermissionRepository->findOneBy(['title' => LibellePermission::READ]);

                break;
        }

        /** @var LibellePermission $libellePermission */
        $libellePermission = $result;

        return $libellePermission;
    }
}