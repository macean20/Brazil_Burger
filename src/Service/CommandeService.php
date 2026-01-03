<?php
// src/Service/CommandeService.php
namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommandeService
{
    private EntityManagerInterface $entityManager;
    private CommandeRepository $commandeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommandeRepository $commandeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->commandeRepository = $commandeRepository;
    }

    /**
     * Changer le statut d'une commande
     */
    public function changerStatut(Commande $commande, string $nouvelEtat): bool
    {
        if ($this->isTransitionValide($commande->getEtat(), $nouvelEtat)) {
            $commande->setEtat($nouvelEtat);
            $this->entityManager->flush();
            return true;
        }
        
        return false;
    }

    /**
     * Vérifier si la transition d'état est valide
     */
    private function isTransitionValide(string $etatActuel, string $nouvelEtat): bool
    {
        $transitionsValides = [
            Commande::ETAT_EN_ATTENTE => [Commande::ETAT_VALIDEE, Commande::ETAT_ANNULEE],
            Commande::ETAT_VALIDEE => [Commande::ETAT_EN_PREPARATION, Commande::ETAT_ANNULEE],
            Commande::ETAT_EN_PREPARATION => [Commande::ETAT_TERMINEE, Commande::ETAT_ANNULEE],
            Commande::ETAT_TERMINEE => [Commande::ETAT_EN_LIVRAISON, Commande::ETAT_LIVREE],
            Commande::ETAT_EN_LIVRAISON => [Commande::ETAT_LIVREE],
        ];
        
        return in_array($nouvelEtat, $transitionsValides[$etatActuel] ?? []);
    }

    /**
     * Annuler une commande
     */
    public function annulerCommande(Commande $commande): bool
    {
        if ($commande->canBeCanceled()) {
            $commande->setEtat(Commande::ETAT_ANNULEE);
            $this->entityManager->flush();
            return true;
        }
        
        return false;
    }

    /**
     * Calculer le montant total d'une commande
     */
    public function calculerMontantTotal(Commande $commande): float
    {
        $total = 0;
        
        foreach ($commande->getLigneCommandes() as $ligne) {
            $total += (float) $ligne->getSousTotal();
        }
        
        // Ajouter le prix de livraison si la zone existe
        if ($commande->getZone()) {
            $total += (float) $commande->getZone()->getPrixLivraison();
        }
        
        return $total;
    }

    /**
     * Filtrer les commandes
     */
    public function filtrerCommandes(array $filters): array
    {
        return $this->commandeRepository->filter($filters);
    }
}