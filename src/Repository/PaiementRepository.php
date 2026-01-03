<?php
// src/Repository/PaiementRepository.php
namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    /**
     * Obtenir les recettes du jour
     */
    public function getRecettesAujourdhui(): float
    {
        $start = (new \DateTimeImmutable('today'))->setTime(0, 0, 0);
        $end   = $start->modify('+1 day');

        $result = $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.montant), 0) as total')
            ->join('p.commande', 'c')
            ->andWhere('c.dateCommande >= :start AND c.dateCommande < :end')
            ->andWhere('p.statut = :statut_reussi')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('statut_reussi', Paiement::STATUT_REUSSI)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }


    /**
     * Sauvegarder un paiement
     */
    public function save(Paiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un paiement
     */
    public function remove(Paiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}