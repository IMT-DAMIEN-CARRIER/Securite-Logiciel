<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "post",
 *          "get_delete" = {
 *              "method": "GET",
 *              "path": "/permissions/get_delete",
 *          },
 *      },
 *     itemOperations={"get", "delete"}
 * )
 *
 * @ORM\Entity(repositoryClass=PermissionRepository::class)
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Document::class, inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Document $document;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=LibellePermission::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private LibellePermission $libellePermission;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @param Document $document
     *
     * @return Permission
     */
    public function setDocument(Document $document): Permission
    {
        $this->document = $document;

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
     * @return Permission
     */
    public function setUser(User $user): Permission
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return LibellePermission
     */
    public function getLibellePermission(): LibellePermission
    {
        return $this->libellePermission;
    }

    /**
     * @param LibellePermission $libellePermission
     *
     * @return Permission
     */
    public function setLibellePermission(LibellePermission $libellePermission): Permission
    {
        $this->libellePermission = $libellePermission;

        return $this;
    }
}
