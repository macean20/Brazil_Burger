<?php
// src/Service/Commande/CommandeFilterService.php
namespace App\Service\Commande;

use App\Repository\CommandeRepository;
use Doctrine\ORM\QueryBuilder;

class CommandeFilterService
{
    private CommandeRepository $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        $this->commandeRepository = $commandeRepository;
    }

    /**
     * Appliquer les filtres aux commandes
     */
    public function applyFilters(QueryBuilder $qb, array $filters): void
    {
        // Filtre par recherche
        if (!empty($filters['search'])) {
            $this->applySearchFilter($qb, $filters['search']);
        }

        // Filtre par statut
        if (!empty($filters['etat'])) {
            $this->applyEtatFilter($qb, $filters['etat']);
        }

        // Filtre par date
        if (!empty($filters['date'])) {
            $this->applyDateFilter($qb, $filters['date']);
        }

        // Filtre par client
        if (!empty($filters['client'])) {
            $this->applyClientFilter($qb, $filters['client']);
        }

        // Filtre par zone
        if (!empty($filters['zone'])) {
            $this->applyZoneFilter($qb, $filters['zone']);
        }

        // Filtre par livreur
        if (!empty($filters['livreur'])) {
            $this->applyLivreurFilter($qb, $filters['livreur']);
        }

        // Filtre par type (livraison/sur place)
        if (!empty($filters['type'])) {
            $this->applyTypeFilter($qb, $filters['type']);
        }

        // Tri
        $this->applySorting($qb, $filters['sort'] ?? 'date_desc');
    }

    /**
     * Appliquer le filtre de recherche
     */
    private function applySearchFilter(QueryBuilder $qb, string $search): void
    {
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->like('c.numeroCommande', ':search'),
            $qb->expr()->like('client.nom', ':search'),
            $qb->expr()->like('client.prenom', ':search'),
            $qb->expr()->like('client.telephone', ':search')
        ))
        ->setParameter('search', '%' . $search . '%');
    }

    /**
     * Appliquer le filtre par statut
     */
    private function applyEtatFilter(QueryBuilder $qb, string $etat): void
    {
        $qb->andWhere('c.etat = :etat')
           ->setParameter('etat', $etat);
    }

    /**
     * Appliquer le filtre par date
     */
    private function applyDateFilter(QueryBuilder $qb, $date): void
    {
        if ($date instanceof \DateTimeInterface) {
            $qb->andWhere('DATE(c.dateCommande) = :date')
               ->setParameter('date', $date->format('Y-m-d'));
        } elseif (is_string($date)) {
            $qb->andWhere('DATE(c.dateCommande) = :date')
               ->setParameter('date', $date);
        }
    }

    /**
     * Appliquer le filtre par client
     */
    private function applyClientFilter(QueryBuilder $qb, $client): void
    {
        if (is_numeric($client) || is_string($client)) {
            $qb->andWhere('client.id = :clientId')
               ->setParameter('clientId', $client);
        }
    }

    /**
     * Appliquer le filtre par zone
     */
    private function applyZoneFilter(QueryBuilder $qb, $zone): void
    {
        if (is_numeric($zone) || is_string($zone)) {
            $qb->andWhere('zone.id = :zoneId')
               ->setParameter('zoneId', $zone);
        }
    }

    /**
     * Appliquer le filtre par livreur
     */
    private function applyLivreurFilter(QueryBuilder $qb, $livreur): void
    {
        if (is_numeric($livreur) || is_string($livreur)) {
            $qb->andWhere('livreur.id = :livreurId')
               ->setParameter('livreurId', $livreur);
        }
    }

    /**
     * Appliquer le filtre par type
     */
    private function applyTypeFilter(QueryBuilder $qb, string $type): void
    {
        if ($type === 'livraison') {
            $qb->andWhere('c.zone IS NOT NULL');
        } elseif ($type === 'sur_place') {
            $qb->andWhere('c.zone IS NULL');
        }
    }

    /**
     * Appliquer le tri
     */
    private function applySorting(QueryBuilder $qb, string $sort): void
    {
        switch ($sort) {
            case 'date_asc':
                $qb->orderBy('c.dateCommande', 'ASC');
                break;
            case 'montant_desc':
                $qb->orderBy('c.montantTotal', 'DESC');
                break;
            case 'montant_asc':
                $qb->orderBy('c.montantTotal', 'ASC');
                break;
            case 'numero_desc':
                $qb->orderBy('c.numeroCommande', 'DESC');
                break;
            default: // date_desc
                $qb->orderBy('c.dateCommande', 'DESC');
        }
    }

    /**
     * Obtenir les options de filtre disponibles
     */
    public function getFilterOptions(): array
    {
        return [
            'etats' => [
                '' => 'Tous les statuts',
                'EN_ATTENTE' => 'En attente',
                'VALIDEE' => 'Validée',
                'EN_PREPARATION' => 'En préparation',
                'TERMINEE' => 'Terminée',
                'EN_LIVRAISON' => 'En livraison',
                'LIVREE' => 'Livrée',
                'ANNULEE' => 'Annulée'
            ],
            'types' => [
                '' => 'Tous les types',
                'livraison' => 'Livraison',
                'sur_place' => 'Sur place'
            ],
            'sort_options' => [
                'date_desc' => 'Date (récent)',
                'date_asc' => 'Date (ancien)',
                'montant_desc' => 'Montant (décroissant)',
                'montant_asc' => 'Montant (croissant)',
                'numero_desc' => 'Numéro commande'
            ],
            'periodes' => [
                'today' => 'Aujourd\'hui',
                'yesterday' => 'Hier',
                'this_week' => 'Cette semaine',
                'last_week' => 'Semaine dernière',
                'this_month' => 'Ce mois',
                'last_month' => 'Mois dernier'
            ]
        ];
    }

    /**
     * Appliquer une période prédéfinie
     */
    public function applyPeriodFilter(QueryBuilder $qb, string $period): void
    {
        $now = new \DateTime();
        
        switch ($period) {
            case 'today':
                $qb->andWhere('DATE(c.dateCommande) = CURRENT_DATE()');
                break;
                
            case 'yesterday':
                $yesterday = (new \DateTime())->modify('-1 day');
                $qb->andWhere('DATE(c.dateCommande) = :yesterday')
                   ->setParameter('yesterday', $yesterday->format('Y-m-d'));
                break;
                
            case 'this_week':
                $startOfWeek = (new \DateTime())->modify('monday this week');
                $qb->andWhere('c.dateCommande >= :startOfWeek')
                   ->setParameter('startOfWeek', $startOfWeek);
                break;
                
            case 'last_week':
                $startOfLastWeek = (new \DateTime())->modify('monday last week');
                $endOfLastWeek = (new \DateTime())->modify('sunday last week');
                $qb->andWhere('c.dateCommande BETWEEN :start AND :end')
                   ->setParameter('start', $startOfLastWeek)
                   ->setParameter('end', $endOfLastWeek);
                break;
                
            case 'this_month':
                $startOfMonth = (new \DateTime())->modify('first day of this month');
                $qb->andWhere('c.dateCommande >= :startOfMonth')
                   ->setParameter('startOfMonth', $startOfMonth);
                break;
                
            case 'last_month':
                $startOfLastMonth = (new \DateTime())->modify('first day of last month');
                $endOfLastMonth = (new \DateTime())->modify('last day of last month');
                $qb->andWhere('c.dateCommande BETWEEN :start AND :end')
                   ->setParameter('start', $startOfLastMonth)
                   ->setParameter('end', $endOfLastMonth);
                break;
        }
    }
}