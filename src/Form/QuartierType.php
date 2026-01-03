<?php

namespace App\Form;

use App\Entity\Quartier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class QuartierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du quartier',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Plateau'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du quartier est obligatoire']),
                    new Assert\Length(['max' => 100])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quartier::class,
        ]);
    }
}