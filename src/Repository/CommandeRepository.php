<?php
// src/Repository/CommandeRepository.php
namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    private function getTodayRange(): array
    {
        $start = (new \DateTimeImmutable('today'))->setTime(0, 0, 0);
        $end   = $start->modify('+1 day');
        return [$start, $end];
    }

    /**
     * Trouver les commandes par statut
     */
    public function findByEtat(string $etat): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.etat = :etat')
            ->setParameter('etat', $etat)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les commandes du jour
     */
    public function findToday(): array
    {
        [$start, $end] = $this->getTodayRange();

        return $this->createQueryBuilder('c')
            ->andWhere('c.dateCommande >= :start AND c.dateCommande < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Commandes en cours du jour
     */
    public function countEnCoursAujourdhui(): int
    {
        [$start, $end] = $this->getTodayRange();

        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.dateCommande >= :start AND c.dateCommande < :end')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('etats', [
                Commande::ETAT_EN_ATTENTE,
                Commande::ETAT_VALIDEE,
                Commande::ETAT_EN_PREPARATION,
                Commande::ETAT_TERMINEE,
                Commande::ETAT_EN_LIVRAISON
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Commandes validées du jour
     */
    public function countValideesAujourdhui(): int
    {
        [$start, $end] = $this->getTodayRange();

        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.dateCommande >= :start AND c.dateCommande < :end')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('etats', [
                Commande::ETAT_VALIDEE,
                Commande::ETAT_EN_PREPARATION,
                Commande::ETAT_TERMINEE,
                Commande::ETAT_EN_LIVRAISON,
                Commande::ETAT_LIVREE
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Commandes annulées du jour
     */
    public function countAnnuleesAujourdhui(): int
    {
        [$start, $end] = $this->getTodayRange();

        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.dateCommande >= :start AND c.dateCommande < :end')
            ->andWhere('c.etat = :etat')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('etat', Commande::ETAT_ANNULEE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Commandes par heure aujourd'hui (PostgreSQL / Neon)
     */
    public function getCommandesParHeureAujourdhui(): array
    {
        [$start, $end] = $this->getTodayRange();

        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT EXTRACT(HOUR FROM date_commande) AS heure, COUNT(id) AS nb_commandes
            FROM commande
            WHERE date_commande >= :start AND date_commande < :end
            GROUP BY heure
            ORDER BY heure ASC
        ";

        $rows = $conn->fetchAllAssociative($sql, [
            'start' => $start->format('Y-m-d H:i:s'),
            'end'   => $end->format('Y-m-d H:i:s'),
        ]);

        $labels = [];
        $data = [];
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['heure']] = (int)$r['nb_commandes'];
        }

        for ($i = 8; $i <= 22; $i++) {
            $labels[] = sprintf('%02dh', $i);
            $data[] = $map[$i] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Filtrer les commandes
     */
    public function filter(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->addSelect('client')
            ->orderBy('c.dateCommande', 'DESC');

        if (!empty($filters['search'])) {
            $qb->andWhere('c.numeroCommande LIKE :search OR client.nom LIKE :search OR client.prenom LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['etat'])) {
            $qb->andWhere('c.etat = :etat')
               ->setParameter('etat', $filters['etat']);
        }

        // ✅ CORRECTION : Filtre date avec gestion de différents types
        if (!empty($filters['date'])) {
            $date = $filters['date'];
            
            // Si c'est déjà un objet DateTime, le convertir en DateTimeImmutable
            if ($date instanceof \DateTime) {
                $date = \DateTimeImmutable::createFromMutable($date);
            }
            // Si c'est une chaîne, la parser
            elseif (is_string($date)) {
                $parsedDate = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
                $date = $parsedDate !== false ? $parsedDate : null;
            }
            // Si c'est déjà DateTimeImmutable, on garde tel quel
            elseif (!($date instanceof \DateTimeImmutable)) {
                $date = null;
            }
            
            // Si la conversion a réussi
            if ($date instanceof \DateTimeImmutable) {
                $start = $date->setTime(0, 0, 0);
                $end = $start->modify('+1 day');

                $qb->andWhere('c.dateCommande >= :startDate')
                   ->andWhere('c.dateCommande < :endDate')
                   ->setParameter('startDate', $start)
                   ->setParameter('endDate', $end);
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function save(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
