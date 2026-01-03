<?php
// src/Repository/GestionnaireRepository.php
namespace App\Repository;

use App\Entity\Gestionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Gestionnaire>
 */
class GestionnaireRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gestionnaire::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Gestionnaire) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Trouver un gestionnaire par email
     */
    public function findByEmail(string $email): ?Gestionnaire
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Sauvegarder un gestionnaire
     */
    public function save(Gestionnaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un gestionnaire
     */
    public function remove(Gestionnaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Rechercher des gestionnaires
     */
    public function search(string $search): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.nom LIKE :search OR g.prenom LIKE :search OR g.email LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('g.nom', 'ASC')
            ->addOrderBy('g.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}