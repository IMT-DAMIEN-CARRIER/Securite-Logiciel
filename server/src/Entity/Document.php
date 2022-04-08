<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DocumentRepository;
use App\Service\EncryptionService;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}}
 *     },
 *     collectionOperations={
 *          "get",
 *          "post",
 *          "get_delete" = {
 *              "method": "GET",
 *              "path": "/documents/get_delete",
 *          },
 *          "get_update" = {
 *              "method": "GET",
 *              "path": "/documents/get_update"
 *          },
 *          "get_download" = {
 *              "method": "GET",
 *              "path": "/documents/get_download"
 *          },
 *      },
 *     itemOperations={
 *          "get",
 *          "put",
 *          "delete",
 *          "get_download" = {
 *              "method": "GET",
 *              "path": "/documents/get_download/{id}"
 *          }
 *      }
 *)
 *
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Un autre document possède déjà ce nom. Merci de le modifier."
 * )
 */
class Document
{
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
     *
     * @Assert\Length(
     *     min="5",
     *     max="255",
     *     minMessage="Le nom doit faire plus de 5 caractères !",
     *     maxMessage="Le nom ne peut pas faire plus de 255 caractères !"
     * )
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug;

    /**
     * @Groups("read")
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @Groups("read")
     *
     * @ORM\OneToMany(targetEntity=Permission::class, mappedBy="document", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private Collection $permissions;

    /**
     * @Groups("read")
     *
     * @ORM\Column(type="text")
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $cryptTag = null;

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    /**
     * Renvoie true si l'utilisateur peut supprimer le document, false sinon.
     *
     * @param User $user
     *
     * @return bool
     */
    public function canDeleteDocument(User $user): bool
    {
        $canDelete = false;

        /** @var Permission $permission */
        foreach ($this->getPermissions() as $permission) {
            if ($permission->getUser() === $user &&
                LibellePermission::DELETE === $permission->getLibellePermission()->getTitle()
            ) {
                $canDelete = true;
            }
        }

        return $canDelete;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Document
     */
    public function setName(string $name): Document
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Document
     */
    public function setSlug(string $slug): Document
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Document
     */
    public function setUser(User $user): Document
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection
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
            $permission->setDocument($this);
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
            if ($permission->getDocument() === $this) {
                $permission->setDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return Document
     */
    public function setContent(?string $content): Document
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCryptTag(): ?string
    {
        return $this->cryptTag;
    }

    /**
     * @param string|null $cryptTag
     *
     * @return Document
     */
    public function setCryptTag(?string $cryptTag): Document
    {
        $this->cryptTag = $cryptTag;

        return $this;
    }
}
