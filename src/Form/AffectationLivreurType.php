<?php
// src/Form/AffectationLivreurType.php
namespace App\Form;

use App\Entity\Livreur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffectationLivreurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('livreur', EntityType::class, [
                'label' => 'Livreur',
                'class' => Livreur::class,
                'choice_label' => function(Livreur $livreur) {
                    return $livreur->getPrenom() . ' ' . $livreur->getNom() . 
                           ' (' . $livreur->getTelephone() . ')' . 
                           ($livreur->isDisponible() ? ' ✅' : ' ❌');
                },
                'placeholder' => '-- Sélectionner un livreur --',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('l')
                        ->andWhere('l.disponible = true')
                        ->orderBy('l.nom', 'ASC')
                        ->addOrderBy('l.prenom', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}