<?php

namespace App\EventSubscriber;

use App\Entity\Document;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DocumentEventSubscriber.
 */
class DocumentEventSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preRemove,
        ];
    }

    /**
     * @param EntityManagerInterface $manager
     * @param UnitOfWork             $unitOfWork
     * @param EncryptionService      $encryptionService
     */
    public function prePersist(EntityManagerInterface $manager, UnitOfWork $unitOfWork, EncryptionService $encryptionService)
    {
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Document) {
                if (empty($entity->getCryptTag())) {
                    $resultEncryption = $encryptionService->encrypt($entity->getContent());

                    if (!array_key_exists('error', $resultEncryption)) {
                        $entity->setContent($resultEncryption['encryptedContent']);
                        $entity->setCryptTag($resultEncryption['tagCryptage']);

                        $manager->persist($entity);
                    }
                }
            }
        }

        $manager->flush();
    }

    /**
     * @param EntityManagerInterface $manager
     * @param UnitOfWork             $unitOfWork
     * @param EncryptionService      $encryptionService
     */
    public function preRemove(EntityManagerInterface $manager, UnitOfWork $unitOfWork, EncryptionService $encryptionService)
    {
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Document) {
                if (empty($entity->getCryptTag())) {
                    $resultEncryption = $encryptionService->encrypt($entity->getContent());

                    if (!array_key_exists('error', $resultEncryption)) {
                        $entity->setContent($resultEncryption['encryptedContent']);
                        $entity->setCryptTag($resultEncryption['tagCryptage']);

                        $manager->remove($entity);
                    }
                }
            }
        }

        $manager->flush();
    }
}