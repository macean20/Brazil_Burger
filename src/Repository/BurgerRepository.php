<?php
// src/Repository/BurgerRepository.php
namespace App\Repository;

use App\Entity\Burger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Burger>
 */
class BurgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Burger::class);
        }
    /**
     * Trouver les burgers disponibles
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les top burgers vendus aujourd'hui
     */
    public function findTopVendusAujourdhui(int $limit = 5): array
        {
            $start = (new \DateTimeImmutable('today'))->setTime(0, 0, 0);
            $end   = $start->modify('+1 day');

            return $this->createQueryBuilder('b')
                ->select('b.nom AS nom, SUM(lc.quantite) AS quantite_vendue, SUM(lc.sousTotal) AS chiffre_affaires')
                ->innerJoin('b.ligneCommandes', 'lc')
                ->innerJoin('lc.commande', 'c')
                ->andWhere('c.dateCommande >= :start AND c.dateCommande < :end')
                ->andWhere('c.etat != :etat_annule')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('etat_annule', 'ANNULEE')
                ->groupBy('b.id, b.nom')
                ->orderBy('quantite_vendue', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        }
    /**
     * Rechercher des burgers par nom
     */
    public function searchByName(string $search): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Sauvegarder un burger
     */
    public function save(Burger $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un burger
     */
    public function remove(Burger $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}