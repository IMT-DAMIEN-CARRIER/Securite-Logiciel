<?php

namespace App\Form;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AddRoleUserType.
 */
class AddRoleUserType extends AbstractType
{
    /**
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * AddRoleUserType constructor.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'userRoles',
                EntityType::class,
                [
                    'choices' => $this->roleRepository->findAll(),
                    'multiple' => true,
                    'required' => true,
                    'class' => Role::class,
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-success',
                    ],
                    'label' => 'Confirmer l\'ajout de rôles',
                ]
            );
    }
}
