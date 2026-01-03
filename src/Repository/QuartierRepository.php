<?php
// src/Repository/QuartierRepository.php
namespace App\Repository;

use App\Entity\Quartier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quartier>
 */
class QuartierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quartier::class);
    }

    /**
     * Trouver les quartiers d'une zone
     */
    public function findByZone($zoneId): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.zone = :zone')
            ->setParameter('zone', $zoneId)
            ->orderBy('q.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver un quartier par nom dans une zone
     */
    public function findByNameInZone(string $nom, $zoneId): ?Quartier
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.nom = :nom')
            ->andWhere('q.zone = :zone')
            ->setParameter('nom', $nom)
            ->setParameter('zone', $zoneId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Rechercher des quartiers
     */
    public function search(string $searchTerm, $zoneId = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->innerJoin('q.zone', 'z');

        if ($searchTerm) {
            $qb->andWhere('q.nom LIKE :search OR z.nom LIKE :search')
               ->setParameter('search', '%' . $searchTerm . '%');
        }

        if ($zoneId) {
            $qb->andWhere('q.zone = :zone')
               ->setParameter('zone', $zoneId);
        }

        return $qb->orderBy('z.nom', 'ASC')
                  ->addOrderBy('q.nom', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Compter les quartiers par zone
     */
    public function countByZone(): array
    {
        return $this->createQueryBuilder('q')
            ->select('z.nom as zone_nom, COUNT(q.id) as nb_quartiers')
            ->innerJoin('q.zone', 'z')
            ->groupBy('z.id')
            ->orderBy('nb_quartiers', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les quartiers avec le plus de commandes
     */
    public function findMostActiveQuartiers(int $limit = 10): array
    {
        return $this->createQueryBuilder('q')
            ->select('q, COUNT(c.id) as nb_commandes')
            ->innerJoin('q.zone', 'z')
            ->leftJoin('z.commandes', 'c')
            ->andWhere('c.etat != :etat_annule')
            ->setParameter('etat_annule', 'ANNULEE')
            ->groupBy('q.id')
            ->orderBy('nb_commandes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifier si un quartier existe déjà dans une zone
     */
    public function existsInZone(string $nom, $zoneId): bool
    {
        $count = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->andWhere('q.nom = :nom')
            ->andWhere('q.zone = :zone')
            ->setParameter('nom', $nom)
            ->setParameter('zone', $zoneId)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Sauvegarder un quartier
     */
    public function save(Quartier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un quartier
     */
    public function remove(Quartier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}