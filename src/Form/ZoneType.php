<?php

namespace App\Form;

use App\Entity\Zone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la zone',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Dakar Plateau'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length(['max' => 100])
                ]
            ])
            ->add('prixLivraison', MoneyType::class, [
                'label' => 'Prix de livraison',
                'currency' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '1000'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix est obligatoire']),
                    new Assert\Positive(['message' => 'Le prix doit être positif'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Description de la zone...'
                ]
            ])
            ->add('quartiers', CollectionType::class, [
                'entry_type' => QuartierType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Quartiers',
                'attr' => [
                    'class' => 'quartiers-collection'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zone::class,
        ]);
    }
}