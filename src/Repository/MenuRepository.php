<?php
// src/Repository/MenuRepository.php
namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /**
     * Trouver les menus disponibles
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('m.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les menus par burger
     */
    public function findByBurger($burgerId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.burger = :burger')
            ->setParameter('burger', $burgerId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Sauvegarder un menu
     */
    public function save(Menu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un menu
     */
    public function remove(Menu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}