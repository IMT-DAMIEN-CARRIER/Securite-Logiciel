<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Document;
use App\Entity\LibellePermission;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Service\DocumentCreatorRightsService;
use App\Service\EncryptionService;
use Doctrine\Migrations\Configuration\Exception\FileNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Security;

/**
 * Class DocumentDataPersister.
 */
class DocumentDataPersister implements ContextAwareDataPersisterInterface
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
     * @var DocumentCreatorRightsService
     */
    private DocumentCreatorRightsService $documentCreatorRightsService;

    /**
     * @var EncryptionService
     */
    private EncryptionService $encryptionService;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * DocumentDataPersister constructor.
     *
     * @param Security                     $security
     * @param EntityManagerInterface       $manager
     * @param DocumentCreatorRightsService $documentCreatorRightsService
     * @param EncryptionService            $encryptionService
     * @param LoggerInterface              $logger
     */
    public function __construct(Security $security, EntityManagerInterface $manager, DocumentCreatorRightsService $documentCreatorRightsService, EncryptionService $encryptionService, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->documentCreatorRightsService = $documentCreatorRightsService;
        $this->encryptionService = $encryptionService;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Document;
    }

    /**
     * @inheritDoc
     */
    public function persist($data, array $context = [])
    {
        if (!in_array(Role::ROLE_ADMIN, $this->getCurrentUser()->getRoles())) {
            /** @var Document $document */
            $document = $data;

            if (empty($document->getId())) {
                $document = $this->documentCreatorRightsService
                    ->grantAllRightOnDocumentToUser($document, $this->getCurrentUser());
                $document->setUser($this->getCurrentUser());

                $resultEncryption = $this->encryptionService->encrypt($document->getContent());

                $this->logger->debug('Debut encryption', ['cause' => $resultEncryption]);

                if (!array_key_exists('error', $resultEncryption)) {
                    $document->setContent($resultEncryption['encryptedContent']);
                    $document->setCryptTag($resultEncryption['tagCryptage']);
                } else {
                    throw new InvalidArgumentException();
                }
            }

            if (!empty($document->getId()) && $this->canDo($document, LibellePermission::EDIT)) {
                $resultEncryption = $this->encryptionService->encrypt($document->getContent());

                $this->logger->debug('Debut encryption', ['cause' => $resultEncryption]);

                if (!array_key_exists('error', $resultEncryption)) {
                    $document->setContent($resultEncryption['encryptedContent']);
                    $document->setCryptTag($resultEncryption['tagCryptage']);
                } else {
                    throw new InvalidArgumentException();
                }
            }

            $this->manager->persist($document);
            $this->manager->flush();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        /** @var Document $document */
        $document = $data;

        $this->logger->debug('document pour delete : ', [$document->getId()]);

        if (!in_array(Role::ROLE_ADMIN, $this->getCurrentUser()->getRoles()) &&
            $this->canDo($document, LibellePermission::DELETE)
        ) {
            $this->logger->debug('ouf on est sauvé');
            $this->manager->remove($document);
            $this->manager->flush();
        } else {
            throw new FileNotFound('Vous ne pouvez pas supprimer ce document.');
        }
    }

    /**
     * @param Document $document
     * @param string   $permissionLibelle
     *
     * @return bool
     */
    private function canDo(Document $document, string $permissionLibelle): bool
    {
        $canPersist = false;

        /** @var Permission $permission */
        foreach ($document->getPermissions() as $permission) {
            if ($permission->getUser() === $this->getCurrentUser() &&
                $permissionLibelle === $permission->getLibellePermission()->getTitle()
            ) {
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