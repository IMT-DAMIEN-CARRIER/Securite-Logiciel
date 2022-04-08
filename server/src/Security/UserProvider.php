<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider.
 */
class UserProvider implements UserProviderInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * UserProvider constructor.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @internal param Client $httpClient
     * @internal param UserOptionService $userOptionService
     * @internal param ProjectService $projectService
     * @internal param SessionService $sessionService
     * @internal param Session $session
     * @internal param UserOptionService $userOptionsService
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        return $this->entityManager->createQueryBuilder()
            ->where(User::ALIAS.'login = :login')
            ->setParameter('login', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }
}
