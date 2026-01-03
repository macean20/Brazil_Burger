<?php
// src/Repository/ComplementRepository.php
namespace App\Repository;

use App\Entity\Complement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Complement>
 */
class ComplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Complement::class);
    }

    /**
     * Trouver les compléments disponibles par type
     */
    public function findDisponiblesByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->andWhere('c.disponible = :disponible')
            ->setParameter('type', $type)
            ->setParameter('disponible', true)
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver toutes les boissons disponibles
     */
    public function findBoissonsDisponibles(): array
    {
        return $this->findDisponiblesByType('BOISSON');
    }

    /**
     * Trouver toutes les frites disponibles
     */
    public function findFritesDisponibles(): array
    {
        return $this->findDisponiblesByType('FRITE');
    }

    /**
     * Compter les compléments par type
     */
    public function countByType(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.type, COUNT(c.id) as total')
            ->groupBy('c.type')
            ->getQuery()
            ->getResult();
    }

    /**
     * Rechercher des compléments
     */
    public function search(string $searchTerm, ?string $type = null): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($searchTerm) {
            $qb->andWhere('c.nom LIKE :search')
               ->setParameter('search', '%' . $searchTerm . '%');
        }

        if ($type) {
            $qb->andWhere('c.type = :type')
               ->setParameter('type', $type);
        }

        return $qb->orderBy('c.type', 'ASC')
                  ->addOrderBy('c.nom', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Obtenir les compléments les plus utilisés dans les menus
     */
    public function findMostUsedInMenus(int $limit = 5): array
    {
        // Compléments utilisés comme boisson
        $boissons = $this->createQueryBuilder('c')
            ->select('c, COUNT(m.id) as usage_count')
            ->innerJoin('c.menusBoisson', 'm')
            ->andWhere('c.type = :type_boisson')
            ->setParameter('type_boisson', 'BOISSON')
            ->groupBy('c.id')
            ->orderBy('usage_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        // Compléments utilisés comme frite
        $frites = $this->createQueryBuilder('c')
            ->select('c, COUNT(m.id) as usage_count')
            ->innerJoin('c.menusFrite', 'm')
            ->andWhere('c.type = :type_frite')
            ->setParameter('type_frite', 'FRITE')
            ->groupBy('c.id')
            ->orderBy('usage_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return [
            'boissons' => $boissons,
            'frites' => $frites
        ];
    }

    /**
     * Vérifier si un complément peut être supprimé
     */
    public function canDelete(Complement $complement): bool
    {
        // Vérifier si utilisé dans des menus
        $menusCount = $this->createQueryBuilder('c')
            ->select('COUNT(m.id)')
            ->leftJoin('c.menusBoisson', 'm')
            ->orWhere(':complement MEMBER OF c.menusFrite')
            ->setParameter('complement', $complement)
            ->getQuery()
            ->getSingleScalarResult();

        return $menusCount == 0;
    }

    /**
     * Sauvegarder un complément
     */
    public function save(Complement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un complément
     */
    public function remove(Complement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}