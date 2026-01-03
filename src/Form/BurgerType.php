<?php
// src/Form/BurgerType.php
namespace App\Form;

use App\Entity\Burger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class BurgerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du burger',
                'attr' => [
                    'placeholder' => 'Ex: Brasil Classic',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description du burger...',
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (FCFA)',
                'attr' => [
                    'placeholder' => 'Ex: 2500',
                    'class' => 'form-control',
                    'step' => '100'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Positive(['message' => 'Le prix doit être positif'])
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du burger',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, WebP)',
                    ])
                ]
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Burger::class,
        ]);
    }
}