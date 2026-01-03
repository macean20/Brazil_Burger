<?php
// src/Form/CommandeFilterType.php
namespace App\Form;

use App\Entity\Client;
use App\Entity\Commande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => 'Recherche',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Numéro, client...'
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'Statut',
                'required' => false,
                'choices' => [
                    'Tous les statuts' => '',
                    'En attente' => Commande::ETAT_EN_ATTENTE,
                    'Validée' => Commande::ETAT_VALIDEE,
                    'En préparation' => Commande::ETAT_EN_PREPARATION,
                    'Terminée' => Commande::ETAT_TERMINEE,
                    'En livraison' => Commande::ETAT_EN_LIVRAISON,
                    'Livrée' => Commande::ETAT_LIVREE,
                    'Annulée' => Commande::ETAT_ANNULEE
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('client', EntityType::class, [
                'label' => 'Client',
                'class' => Client::class,
                'choice_label' => function(Client $client) {
                    return $client->getPrenom() . ' ' . $client->getNom() . ' (' . $client->getTelephone() . ')';
                },
                'required' => false,
                'placeholder' => 'Tous les clients'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // Pour avoir des paramètres GET propres
    }
}