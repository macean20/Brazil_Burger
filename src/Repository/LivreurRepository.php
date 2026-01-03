<?php
// src/Repository/LivreurRepository.php
namespace App\Repository;

use App\Entity\Livreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livreur>
 */
class LivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreur::class);
    }

    /**
     * Trouver les livreurs disponibles
     */
    public function findDisponibles(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('l.nom', 'ASC')
            ->addOrderBy('l.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compter les livreurs disponibles
     */
    public function countDisponibles(): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->andWhere('l.disponible = :disponible')
            ->setParameter('disponible', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Rechercher des livreurs
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.nom LIKE :search OR l.prenom LIKE :search OR l.telephone LIKE :search OR l.email LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('l.nom', 'ASC')
            ->addOrderBy('l.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver un livreur par email
     */
    public function findByEmail(string $email): ?Livreur
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Obtenir les statistiques des livreurs
     */
    public function getStats(): array
    {
        return $this->createQueryBuilder('l')
            ->select('
                COUNT(l.id) as total,
                SUM(CASE WHEN l.disponible = true THEN 1 ELSE 0 END) as disponibles,
                SUM(CASE WHEN l.disponible = false THEN 1 ELSE 0 END) as indisponibles
            ')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Sauvegarder un livreur
     */
    public function save(Livreur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un livreur
     */
    public function remove(Livreur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}