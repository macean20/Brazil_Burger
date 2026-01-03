<?php
// src/Repository/ZoneRepository.php
namespace App\Repository;

use App\Entity\Zone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Zone>
 */
class ZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    /**
     * Trouver toutes les zones avec leurs quartiers
     */
    public function findAllWithQuartiers(): array
    {
        return $this->createQueryBuilder('z')
            ->leftJoin('z.quartiers', 'q')
            ->addSelect('q')
            ->orderBy('z.nom', 'ASC')
            ->addOrderBy('q.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver une zone avec ses quartiers
     */
    public function findWithQuartiers($id): ?Zone
    {
        return $this->createQueryBuilder('z')
            ->leftJoin('z.quartiers', 'q')
            ->addSelect('q')
            ->andWhere('z.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Rechercher des zones
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.nom LIKE :search OR z.description LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('z.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les zones les plus actives (avec le plus de commandes)
     */
    public function findMostActiveZones(int $limit = 5): array
    {
        return $this->createQueryBuilder('z')
            ->select('z, COUNT(c.id) as nb_commandes, SUM(c.montantTotal) as total_revenue')
            ->leftJoin('z.commandes', 'c')
            ->andWhere('c.etat != :etat_annule')
            ->setParameter('etat_annule', 'ANNULEE')
            ->groupBy('z.id')
            ->orderBy('nb_commandes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les statistiques des zones
     */
    public function getStats(): array
    {
        $stats = $this->createQueryBuilder('z')
            ->select('
                COUNT(z.id) as total_zones,
                AVG(z.prixLivraison) as prix_moyen,
                MIN(z.prixLivraison) as prix_min,
                MAX(z.prixLivraison) as prix_max,
                SUM(z.prixLivraison) as total_potentiel
            ')
            ->getQuery()
            ->getSingleResult();

        // Nombre total de quartiers
        $totalQuartiers = $this->createQueryBuilder('z')
            ->select('COUNT(q.id)')
            ->leftJoin('z.quartiers', 'q')
            ->getQuery()
            ->getSingleScalarResult();

        $stats['total_quartiers'] = (int) $totalQuartiers;
        $stats['quartiers_par_zone_moyen'] = $stats['total_zones'] > 0 
            ? round($stats['total_quartiers'] / $stats['total_zones'], 2) 
            : 0;

        return $stats;
    }

    /**
     * Trouver les zones avec commandes en attente de livraison
     */
    public function findZonesWithPendingDeliveries(): array
    {
        return $this->createQueryBuilder('z')
            ->select('z, COUNT(c.id) as commandes_en_attente')
            ->innerJoin('z.commandes', 'c')
            ->andWhere('c.etat = :etat_terminee')
            ->andWhere('c.livreur IS NULL')
            ->setParameter('etat_terminee', 'TERMINEE')
            ->groupBy('z.id')
            ->having('commandes_en_attente > 0')
            ->orderBy('commandes_en_attente', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifier si une zone peut être supprimée
     */
    public function canDelete(Zone $zone): bool
    {
        // Vérifier si la zone a des commandes associées
        $commandesCount = $this->createQueryBuilder('z')
            ->select('COUNT(c.id)')
            ->leftJoin('z.commandes', 'c')
            ->andWhere('z.id = :zone')
            ->setParameter('zone', $zone->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return $commandesCount == 0;
    }

    /**
     * Sauvegarder une zone
     */
    public function save(Zone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer une zone
     */
    public function remove(Zone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}