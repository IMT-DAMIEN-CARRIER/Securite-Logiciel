<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DocumentType.
 */
class DocumentType extends ApplicationType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                self::getConfiguration(
                    'Name',
                    'Entrez un nom pour votre document'
                )
            )
            ->add(
                'slug',
                TextType::class,
                self::getConfiguration(
                    'Adresse Web',
                    'Adresse Web (automatique)',
                    [
                        'required' => false,
                    ]
                )
            )
            ->add(
                'content',
                TextType::class,
                self::getConfiguration(
                    'Contenu du fichier',
                    'Entrez le contenu de votre fichier',
                )
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
                    'label' => $options['buttonLabel'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Document::class,
            ]
        );

        $resolver->setRequired(['buttonLabel']);
        $resolver->setAllowedTypes('buttonLabel', 'string');
    }
}
