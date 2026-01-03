<?php
// src/Service/Livraison/AffectationService.php
namespace App\Service\Livraison;

use App\Entity\Commande;
use App\Entity\Livreur;
use App\Entity\Zone;
use App\Repository\CommandeRepository;
use App\Repository\LivreurRepository;
use App\Service\Commande\CommandeStateMachine;

class AffectationService
{
    private CommandeRepository $commandeRepository;
    private LivreurRepository $livreurRepository;
    private LivreurService $livreurService;
    private CommandeStateMachine $stateMachine;

    public function __construct(
        CommandeRepository $commandeRepository,
        LivreurRepository $livreurRepository,
        LivreurService $livreurService,
        CommandeStateMachine $stateMachine
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->livreurRepository = $livreurRepository;
        $this->livreurService = $livreurService;
        $this->stateMachine = $stateMachine;
    }

    /**
     * Affecter un livreur à une commande
     */
    public function affecterLivreur(Commande $commande, Livreur $livreur): bool
    {
        // Vérifier que la commande peut être livrée
        if (!$this->stateMachine->canDeliver($commande)) {
            return false;
        }

        // Vérifier que le livreur est disponible
        if (!$livreur->isDisponible()) {
            return false;
        }

        // Vérifier que la commande a une zone
        if (!$commande->getZone()) {
            return false;
        }

        // Affecter le livreur
        $commande->setLivreur($livreur);
        
        // Changer l'état de la commande
        $this->stateMachine->applyTransition($commande, Commande::ETAT_EN_LIVRAISON);

        $this->commandeRepository->save($commande, true);

        return true;
    }

    /**
     * Affecter automatiquement des livreurs aux commandes par zone
     */
    public function affecterLivreursParZone(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'details' => []
        ];

        // Récupérer les commandes prêtes pour livraison (terminées)
        $commandes = $this->commandeRepository->createQueryBuilder('c')
            ->andWhere('c.etat = :etat_terminee')
            ->setParameter('etat_terminee', Commande::ETAT_TERMINEE)
            ->andWhere('c.zone IS NOT NULL')
            ->andWhere('c.livreur IS NULL')
            ->orderBy('c.dateCommande', 'ASC')
            ->getQuery()
            ->getResult();

        // Grouper les commandes par zone
        $commandesParZone = [];
        foreach ($commandes as $commande) {
            $zoneId = $commande->getZone()->getId();
            if (!isset($commandesParZone[$zoneId])) {
                $commandesParZone[$zoneId] = [];
            }
            $commandesParZone[$zoneId][] = $commande;
        }

        // Affecter des livreurs par zone
        foreach ($commandesParZone as $zoneId => $commandesZone) {
            $livreursDisponibles = $this->livreurService->getAvailableLivreurs();
            
            if (empty($livreursDisponibles)) {
                $results['failed'] += count($commandesZone);
                $results['details'][] = [
                    'zone' => $commandesZone[0]->getZone()->getNom(),
                    'message' => 'Aucun livreur disponible',
                    'commandes' => count($commandesZone)
                ];
                continue;
            }

            // Affecter les commandes aux livreurs disponibles
            $livreurIndex = 0;
            foreach ($commandesZone as $commande) {
                $livreur = $livreursDisponibles[$livreurIndex % count($livreursDisponibles)];
                
                if ($this->affecterLivreur($commande, $livreur)) {
                    $results['success']++;
                    
                    $results['details'][] = [
                        'zone' => $commande->getZone()->getNom(),
                        'commande' => $commande->getNumeroCommande(),
                        'livreur' => $livreur->getPrenom() . ' ' . $livreur->getNom(),
                        'message' => 'Affectation réussie'
                    ];
                } else {
                    $results['failed']++;
                    
                    $results['details'][] = [
                        'zone' => $commande->getZone()->getNom(),
                        'commande' => $commande->getNumeroCommande(),
                        'message' => 'Échec de l\'affectation'
                    ];
                }
                
                $livreurIndex++;
            }
        }

        return $results;
    }

    /**
     * Libérer un livreur d'une commande
     */
    public function libererLivreur(Commande $commande): bool
    {
        if (!$commande->getLivreur()) {
            return false;
        }

        $commande->setLivreur(null);
        $this->commandeRepository->save($commande, true);

        return true;
    }

    /**
     * Obtenir les commandes par livreur
     */
    public function getCommandesParLivreur(?Livreur $livreur = null): array
    {
        $qb = $this->commandeRepository->createQueryBuilder('c')
            ->andWhere('c.livreur IS NOT NULL');

        if ($livreur) {
            $qb->andWhere('c.livreur = :livreur')
               ->setParameter('livreur', $livreur);
        }

        return $qb->orderBy('c.dateCommande', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Obtenir les commandes par zone
     */
    public function getCommandesParZone(?Zone $zone = null): array
    {
        $qb = $this->commandeRepository->createQueryBuilder('c')
            ->andWhere('c.zone IS NOT NULL')
            ->andWhere('c.livreur IS NULL')
            ->andWhere('c.etat = :etat_terminee')
            ->setParameter('etat_terminee', Commande::ETAT_TERMINEE);

        if ($zone) {
            $qb->andWhere('c.zone = :zone')
               ->setParameter('zone', $zone);
        }

        return $qb->orderBy('c.zone', 'ASC')
                  ->addOrderBy('c.dateCommande', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Obtenir les statistiques d'affectation
     */
    public function getAffectationStats(): array
    {
        // Commandes sans livreur
        $commandesSansLivreur = $this->commandeRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.livreur IS NULL')
            ->andWhere('c.etat = :etat_terminee')
            ->setParameter('etat_terminee', Commande::ETAT_TERMINEE)
            ->getQuery()
            ->getSingleScalarResult();

        // Commandes en livraison
        $commandesEnLivraison = $this->commandeRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.etat = :etat_livraison')
            ->setParameter('etat_livraison', Commande::ETAT_EN_LIVRAISON)
            ->getQuery()
            ->getSingleScalarResult();

        // Livreurs disponibles
        $livreursDisponibles = $this->livreurRepository->count(['disponible' => true]);

        // Zones avec commandes en attente
        $zonesAvecCommandes = $this->commandeRepository->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.zone)')
            ->andWhere('c.livreur IS NULL')
            ->andWhere('c.etat = :etat_terminee')
            ->setParameter('etat_terminee', Commande::ETAT_TERMINEE)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'commandes_sans_livreur' => (int) $commandesSansLivreur,
            'commandes_en_livraison' => (int) $commandesEnLivraison,
            'livreurs_disponibles' => (int) $livreursDisponibles,
            'zones_avec_commandes' => (int) $zonesAvecCommandes,
            'taux_affectation' => $this->calculerTauxAffectation()
        ];
    }

    /**
     * Calculer le taux d'affectation
     */
    private function calculerTauxAffectation(): float
    {
        // Commandes terminées avec livreur
        $commandesAffectees = $this->commandeRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.livreur IS NOT NULL')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('etats', [Commande::ETAT_TERMINEE, Commande::ETAT_EN_LIVRAISON, Commande::ETAT_LIVREE])
            ->getQuery()
            ->getSingleScalarResult();

        // Total des commandes terminées
        $totalCommandes = $this->commandeRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('etats', [Commande::ETAT_TERMINEE, Commande::ETAT_EN_LIVRAISON, Commande::ETAT_LIVREE])
            ->getQuery()
            ->getSingleScalarResult();

        if ($totalCommandes == 0) {
            return 0.0;
        }

        return round(($commandesAffectees / $totalCommandes) * 100, 2);
    }

    /**
     * Optimiser l'affectation des livreurs
     */
    public function optimiserAffectation(): array
    {
        $optimizations = [];

        // 1. Regrouper les commandes par zone
        $commandesParZone = $this->getCommandesParZone();
        
        // 2. Pour chaque zone, essayer de grouper les commandes par livreur
        $groupedByZone = [];
        foreach ($commandesParZone as $commande) {
            $zoneId = $commande->getZone()->getId();
            if (!isset($groupedByZone[$zoneId])) {
                $groupedByZone[$zoneId] = [
                    'zone' => $commande->getZone(),
                    'commandes' => []
                ];
            }
            $groupedByZone[$zoneId]['commandes'][] = $commande;
        }

        // 3. Proposer des optimisations
        $livreursDisponibles = $this->livreurService->getAvailableLivreurs();
        
        foreach ($groupedByZone as $zoneData) {
            $commandesCount = count($zoneData['commandes']);
            
            if ($commandesCount > 0) {
                $livreursNecessaires = ceil($commandesCount / 3); // 3 commandes max par livreur
                
                $optimizations[] = [
                    'zone' => $zoneData['zone']->getNom(),
                    'commandes_en_attente' => $commandesCount,
                    'livreurs_disponibles' => count($livreursDisponibles),
                    'livreurs_necessaires' => $livreursNecessaires,
                    'recommandation' => $this->genererRecommandation($commandesCount, count($livreursDisponibles))
                ];
            }
        }

        return $optimizations;
    }

    /**
     * Générer une recommandation d'affectation
     */
    private function genererRecommandation(int $commandesCount, int $livreursDisponibles): string
    {
        if ($commandesCount == 0) {
            return 'Aucune commande en attente';
        }

        if ($livreursDisponibles == 0) {
            return 'Aucun livreur disponible';
        }

        $ratio = $commandesCount / $livreursDisponibles;

        if ($ratio > 3) {
            return 'Assigner plusieurs livreurs à cette zone (trop de commandes)';
        } elseif ($ratio > 2) {
            return 'Assigner un livreur dédié à cette zone';
        } else {
            return 'Assigner un livreur pour plusieurs zones';
        }
    }
}