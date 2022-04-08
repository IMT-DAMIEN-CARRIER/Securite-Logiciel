<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\Service;

use App\Entity\Document;
use App\Entity\LibellePermission;
use App\Entity\Permission;
use App\Entity\User;

/**
 * Class DocumentPermissionService.
 */
class DocumentPermissionService
{

    /**
     * @param User   $user
     * @param array  $documents
     * @param string $libellePermission
     * @param array  $results
     *
     * @return array
     */
    public function generateArrayResult(User $user, array $documents, string $libellePermission, array $results): array
    {
        /** @var Document $document */
        foreach ($documents as $document) {
            switch ($libellePermission) {
                case LibellePermission::READ:
                    if ($this->canDoToDocument($user, $document, LibellePermission::READ)) {
                        if (!in_array($document, $results)) {
                            $results[] = $document;
                        }
                    }

                    break;
                case LibellePermission::EDIT:
                    if ($this->canDoToDocument($user, $document, LibellePermission::EDIT)) {
                        if (!in_array($document, $results)) {
                            $results[] = $document;
                        }
                    }

                    break;
                case LibellePermission::DELETE:
                    if ($this->canDoToDocument($user, $document, LibellePermission::DELETE)) {
                        if (!in_array($document, $results)) {
                            $results[] = $document;
                        }
                    }

                    break;
            }
        }

        return $results;
    }

    /**
     * @param User     $user
     * @param Document $document
     * @param string   $libellePermission
     *
     * @return bool
     */
    public function canDoToDocument(User $user, Document $document, string $libellePermission): bool
    {
        $canDo = false;

        /** @var Permission $permission */
        foreach ($document->getPermissions() as $permission) {
            if ($permission->getLibellePermission()->getTitle() === $libellePermission &&
                $permission->getUser() === $user
            ) {
                $canDo = true;
            }
        }

        return $canDo;
    }
}