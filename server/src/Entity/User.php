<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}}
 *     },
 *     collectionOperations={"get"={"method"="GET"}},
 *     itemOperations={"get"={"method"="GET"}}
 * )
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(
 *     fields={"login"},
 *     message="Un autre utilisateur s'est déjà inscrit avec celogin, merci d'en choisir un autre."
 * )
 */
class User implements UserInterface
{
    const ALIAS = 'user';

    /**
     * @Groups("read")
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Groups("read")
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $login;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="3",
     *     minMessage="Votre mot de passe doit faire au moins 6 caractères.",
     *     max="255",
     *     maxMessage="Votre mot de passe ne doit pas dépasser 255 caractères."
     * )
     */
    private string $hash;

    /**
     * @var string
     *
     * @Assert\EqualTo(
     *     propertyPath="hash",
     *     message="Les deux mot de passes ne correspondent pas."
     * )
     */
    private string $passwordConfirm = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $publicKey = null;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class)
     */
    private Collection $userRoles;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $documents;

    /**
     * @ORM\OneToMany(targetEntity=Permission::class, mappedBy="user", orphanRemoval=true)
     */
    private Collection $permissions;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return User
     */
    public function setLogin(string $login): User
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     *
     * @return User
     */
    public function setHash(string $hash): User
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordConfirm(): string
    {
        return $this->passwordConfirm;
    }

    /**
     * @param string $passwordConfirm
     *
     * @return User
     */
    public function setPasswordConfirm(string $passwordConfirm): User
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     *
     * @return User
     */
    public function setPublicKey(string $publicKey): User
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addUserRoles(Role $role): self
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles[] = $role;
        }

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeUserRoles(Role $role): self
    {
        $this->userRoles->removeElement($role);

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param Document $document
     *
     * @return $this
     */
    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setUser($this);
        }

        return $this;
    }

    /**
     * @param Document $document
     *
     * @return $this
     */
    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUser() === $this) {
                $document->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->setUser($this);
        }

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @return $this
     */
    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getUser() === $this) {
                $permission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return array_merge(
            ['ROLE_USER'],
            self::getUserRoles()->map(function (Role $role) {
                return $role->getTitle();
            })->toArray()
        );
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        return $this->hash;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->login;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
}
