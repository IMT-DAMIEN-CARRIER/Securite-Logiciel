<?php

namespace App\DataFixtures;

use App\Entity\Document;
use App\Entity\LibellePermission;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\DocumentCreatorRightsService;
use App\Service\EncryptionService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    const ROGER = 'roger';
    const ADMIN = 'admin';
    const ALICE = 'alice';
    const BOB = 'bob';

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var DocumentCreatorRightsService
     */
    private DocumentCreatorRightsService $documentCreatorRightsService;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * @var EncryptionService
     */
    private EncryptionService $encryptionService;

    /**
     * AppFixtures constructor.
     *
     * @param UserRepository               $userRepository
     * @param DocumentCreatorRightsService $documentCreatorRightsService
     * @param UserPasswordEncoderInterface $encoder
     * @param EncryptionService            $encryptionService
     */
    public function __construct(UserRepository $userRepository, DocumentCreatorRightsService $documentCreatorRightsService, UserPasswordEncoderInterface $encoder, EncryptionService $encryptionService)
    {
        $this->userRepository = $userRepository;
        $this->documentCreatorRightsService = $documentCreatorRightsService;
        $this->encoder = $encoder;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('Fr-fr');
        $users = [];

        // On crée les 2 rôles
        $adminRole = (new Role())
            ->setTitle(Role::ROLE_ADMIN);

        $manager->persist($adminRole);

        $userRole = (new Role())
            ->setTitle(Role::ROLE_USER);

        $manager->persist($userRole);

        //On crée les 4 libellePermissions
        $read = (new LibellePermission())
            ->setTitle(LibellePermission::READ);
        $manager->persist($read);

        $delete = (new LibellePermission())
            ->setTitle(LibellePermission::DELETE);
        $manager->persist($delete);

        $edit = (new LibellePermission())
            ->setTitle(LibellePermission::EDIT);
        $manager->persist($edit);

        $manager->flush();

        // On crée les 4 utilisateurs : admin, alice, bob, roger.
        $admin = $this->createUser(self::ADMIN, $adminRole);
        $manager->persist($admin);

        $alice = $this->createUser(self::ALICE, $userRole);
        $manager->persist($alice);
        $users[] = $alice;

        $bob = $this->createUser(self::BOB, $userRole);
        $manager->persist($bob);
        $users[] = $bob;

        $roger = $this->createUser(self::ROGER, $userRole);
        $manager->persist($roger);
        $users[] = $roger;

        for ($i = 1; $i <= 10; $i++) {
            /** @var User $user */
            $user = $users[mt_rand(0, count($users) - 1)];

            $document = (new Document())
                ->setUser($user)
                ->setName('file'.$i.'.'.$faker->fileExtension);

            $resultEncryption = $this->encryptionService->encrypt($faker->text());

            if (!array_key_exists('error', $resultEncryption)) {
                $document->setContent($resultEncryption['encryptedContent']);
                $document->setCryptTag($resultEncryption['tagCryptage']);
            }

            $document = $this->documentCreatorRightsService->grantAllRightOnDocumentToUser($document, $user);

            if (self::ADMIN !== $user->getLogin() && self::ROGER !== $user->getLogin()) {
                /**
                 * On va maintenant définir les droits de manière aléatoire sur le document.
                 * De manière arbitraire et pour simplifer on va tirer un rand entre 1 et 3 :
                 * Si 1 : On donne GET
                 * Si 2 : On donne GET et EDIT
                 * Si 3 : On donne GET, EDIT et DELETE.
                 *
                 * Volontairement, on ne donne aucun droit à Roger sur les documents de Bob et Alice.
                 */

                $rand = mt_rand(1, 3);

                $userBis = $bob;

                if (self::BOB === $user->getLogin()) {
                    $userBis = $alice;
                }

                switch ($rand) {
                    case 1:
                        $readPermission = (new Permission())
                            ->setLibellePermission($this->documentCreatorRightsService->getPermission())
                            ->setDocument($document)
                            ->setUser($userBis);

                        $manager->persist($readPermission);

                        break;
                    case 2:
                        $readPermission = (new Permission())
                            ->setLibellePermission($this->documentCreatorRightsService->getPermission())
                            ->setDocument($document)
                            ->setUser($userBis);

                        $manager->persist($readPermission);

                        $editPermission = (new Permission())
                            ->setLibellePermission(
                                $this->documentCreatorRightsService
                                    ->getPermission(LibellePermission::EDIT)
                            )
                            ->setUser($userBis)
                            ->setDocument($document);

                        $manager->persist($editPermission);
                        break;
                    case 3:
                        $document = $this->documentCreatorRightsService
                            ->grantAllRightOnDocumentToUser($document, $userBis);
                        break;
                }
            }

            $manager->persist($document);
        }

        $manager->flush();
    }

    /**
     * @param string $login
     * @param Role   $role
     *
     * @return User
     */
    private function createUser(string $login, Role $role): User
    {
        $user = (new User)
            ->setLogin($login)
            ->addUserRoles($role);

        $user
            ->setHash($this->encoder->encodePassword($user, $login));

        return $user;
    }
}
