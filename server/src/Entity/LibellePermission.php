<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LibellePermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *     collectionOperations={"get"={"method"="GET"}},
 *     itemOperations={"get"={"method"="GET"}}
 * )
 *
 * @ORM\Entity(repositoryClass=LibellePermissionRepository::class)
 *
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Une autre permission avec le même title existe déjà."
 * )
 */
class LibellePermission
{
    const READ = 'READ';
    const CREATE = 'CREATE';
    const DELETE = 'DELETE';
    const EDIT = 'EDIT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return LibellePermission
     */
    public function setTitle(string $title): LibellePermission
    {
        $this->title = $title;

        return $this;
    }
}
