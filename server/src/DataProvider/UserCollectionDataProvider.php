<?php
/**
 * Property of Damien Carrier, Benoit Perrier, Clément Savinaud.
 */

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserCollectionDataProvider.
 */
class UserCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * UserCollectionDataProvider constructor.
     *
     * @param UserRepository $userRepository
     * @param Security       $security
     */
    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $users = $this->userRepository->findAll();

        $result = [];

        foreach ($users as $user) {
            if (!in_array(Role::ROLE_SUPER_ADMIN, $user->getRoles()) &&
                !in_array(Role::ROLE_ADMIN, $user->getRoles()) &&
                $user !== $currentUser
            ) {
                $arrayUser = [
                    'id' => $user->getId(),
                    'login' => $user->getLogin(),
                ];

                $result[] = $arrayUser;
            }
        }

        return $result;
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
        return User::class === $resourceClass;
    }
}