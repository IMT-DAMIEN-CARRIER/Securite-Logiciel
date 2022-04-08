<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;

/**
 * Class PermissionsPermissionService.
 */
class PermissionsPermissionService
{
    /**
     * @param User   $user
     * @param array  $permissions
     * @param array  $results
     *
     * @return array
     */
    public function generateArrayResults(User $user, array $permissions, array $results): array
    {
        /** @var Permission $permission */
        foreach ($permissions as $permission) {
            if ($this->canDoToPermission($user, $permission)) {
                $results[] = $permission;
            }
        }

        return $results;
    }

    /**
     * @param User       $user
     * @param Permission $permission
     *
     * @return bool
     */
    private function canDoToPermission(User $user, Permission $permission): bool
    {
        $canDo = false;

        if ($permission->getDocument()->getUser() === $user && $permission->getUser() !== $user) {
            $canDo = true;
        }

        return $canDo;
    }
}