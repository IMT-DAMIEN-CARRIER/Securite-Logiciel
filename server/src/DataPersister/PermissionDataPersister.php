<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Document;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

/**
 * Class PermissionDataPersister
 */
class PermissionDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * @var EncryptionService
     */
    private EncryptionService $encryptionService;

    /**
     * PermissionDataPersister constructor.
     *
     * @param Security $security
     * @param EntityManagerInterface $manager
     * @param EncryptionService $encryptionService
     */
    public function __construct(
        Security $security,
        EntityManagerInterface $manager,
        EncryptionService $encryptionService
    ) {
        $this->security = $security;
        $this->manager = $manager;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @inheritDoc
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Permission;
    }

    /**
     * @inheritDoc
     */
    public function persist($data, array $context = [])
    {
        if (!in_array(Role::ROLE_ADMIN, $this->getCurrentUser()->getRoles())) {
            /** @var Permission $permission */
            $permission = $data;
            $document = $permission->getDocument();
            $userToAddPerm = $permission->getUser();

            if (empty($document->getCryptTag())) {
                $resultEncryption = $this->encryptionService->encrypt($document->getContent());

                if (!array_key_exists('error', $resultEncryption)) {
                    $document->setContent($resultEncryption['encryptedContent']);
                    $document->setCryptTag($resultEncryption['tagCryptage']);
                }

                $permission->setDocument($document);

                $this->manager->persist($document);
            }

            if (!in_array(Role::ROLE_SUPER_ADMIN, $userToAddPerm->getRoles())) {
                if ($document->getUser() === $this->getCurrentUser()) {
                    $persist = true;

                    /** @var Permission $perm */
                    foreach ($document->getPermissions() as $perm) {
                        if ($perm->getLibellePermission() === $permission->getLibellePermission() &&
                            $perm->getUser() === $userToAddPerm
                        ) {
                            $persist = false;
                        }
                    }

                    if ($persist) {
                        $this->manager->persist($permission);
                    }
                }
            }

            $this->manager->flush();
        } else {
            throw new NotFoundHttpException();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        /** @var Permission $perm */
        $perm = $data;

        if (!in_array(Role::ROLE_ADMIN, $this->getCurrentUser()->getRoles()) &&
            $this->canDo($perm->getDocument())
        ) {
            $this->manager->remove($perm);
            $this->manager->flush();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @param Document $document
     *
     * @return bool
     */
    private function canDo(Document $document): bool
    {
        $canPersist = false;

        /** @var Permission $permission */
        foreach ($document->getPermissions() as $permission) {
            if ($permission->getUser() === $this->getCurrentUser()) {
                $canPersist = true;
            }
        }

        return $canPersist;
    }

    /**
     * @return User
     */
    private function getCurrentUser(): User
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $user;
    }
}