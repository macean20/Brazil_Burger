<?php
// src/Form/MenuType.php
namespace App\Form;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Entity\Complement;
use App\Repository\ComplementRepository;
use App\Repository\BurgerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du menu',
                'attr' => [
                    'placeholder' => 'Ex: Menu Brasil Classic',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description du menu...',
                    'class' => 'form-control',
                    'rows' => 3,
                ],
            ])

            ->add('burger', EntityType::class, [
                'label' => 'Burger',
                'class' => Burger::class,
                'placeholder' => '-- Choisir un burger --',
                'query_builder' => function (BurgerRepository $repo) {
                    return $repo->createQueryBuilder('b')
                        ->where('b.disponible = true')
                        ->orderBy('b.nom', 'ASC');
                },
                'choice_label' => fn (Burger $burger) =>
                    $burger->getNom().' ('.$burger->getPrix().' FCFA)',

                // ✅ IMPORTANT : data-prix sur chaque option
                'choice_attr' => fn (Burger $burger) => [
                    'data-prix' => (string) $burger->getPrix(),
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un burger']),
                ],
            ])

            ->add('boisson', EntityType::class, [
                'label' => 'Boisson',
                'class' => Complement::class,
                'placeholder' => '-- Choisir une boisson --',
                'query_builder' => function (ComplementRepository $repo) {
                    return $repo->createQueryBuilder('c')
                        ->where('c.type = :type')
                        ->andWhere('c.disponible = true')
                        ->setParameter('type', Complement::TYPE_BOISSON)
                        ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => fn (Complement $c) =>
                    $c->getNom().' ('.$c->getPrix().' FCFA)',

                // ✅ IMPORTANT
                'choice_attr' => fn (Complement $c) => [
                    'data-prix' => (string) $c->getPrix(),
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une boisson']),
                ],
            ])

            ->add('frite', EntityType::class, [
                'label' => 'Frites',
                'class' => Complement::class,
                'placeholder' => '-- Choisir des frites --',
                'query_builder' => function (ComplementRepository $repo) {
                    return $repo->createQueryBuilder('c')
                        ->where('c.type = :type')
                        ->andWhere('c.disponible = true')
                        ->setParameter('type', Complement::TYPE_FRITE)
                        ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => fn (Complement $c) =>
                    $c->getNom().' ('.$c->getPrix().' FCFA)',

                // ✅ IMPORTANT
                'choice_attr' => fn (Complement $c) => [
                    'data-prix' => (string) $c->getPrix(),
                ],

                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir des frites']),
                ],
            ])

            ->add('prixTotal', NumberType::class, [
                'label' => 'Prix total (FCFA)',
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true,
                    'id' => 'menu_prix_total',
                ],
                'required' => true,
            ])

            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
