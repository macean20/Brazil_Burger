<?php
// src/Form/ComplementType.php
namespace App\Form;

use App\Entity\Complement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du complément',
                'attr' => [
                    'placeholder' => 'Ex: Coca-Cola, Frites Standard',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire'])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Boisson' => Complement::TYPE_BOISSON,
                    'Frite' => Complement::TYPE_FRITE
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type est obligatoire'])
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (FCFA)',
                'attr' => [
                    'placeholder' => 'Ex: 500',
                    'class' => 'form-control',
                    'step' => '50'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Positive(['message' => 'Le prix doit être positif'])
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
            'data_class' => Complement::class,
        ]);
    }
}