<?php
// src/Repository/ClientRepository.php
namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Trouver un client par email
     */
    public function findByEmail(string $email): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouver un client par téléphone
     */
    public function findByTelephone(string $telephone): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.telephone = :telephone')
            ->setParameter('telephone', $telephone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Rechercher des clients
     */
    public function search(string $searchTerm): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom LIKE :search OR c.prenom LIKE :search OR c.telephone LIKE :search OR c.email LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les clients les plus actifs
     */
    public function findTopClients(int $limit = 10): array
    {
        return $this->createQueryBuilder('c')
            ->select('c, COUNT(cmd.id) as nb_commandes, SUM(cmd.montantTotal) as total_depense')
            ->leftJoin('c.commandes', 'cmd')
            ->andWhere('cmd.etat != :etat_annule')
            ->setParameter('etat_annule', 'ANNULEE')
            ->groupBy('c.id')
            ->orderBy('nb_commandes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les statistiques des clients
     */
    public function getStats(): array
    {
        $stats = $this->createQueryBuilder('c')
            ->select('
                COUNT(c.id) as total_clients,
                COUNT(DISTINCT DATE(c.createdAt)) as nouveaux_par_jour_moyen,
                MAX(c.createdAt) as dernier_inscrit
            ')
            ->getQuery()
            ->getSingleResult();

        // Clients ayant commandé aujourd'hui
        $clientsAujourdhui = $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.id)')
            ->innerJoin('c.commandes', 'cmd')
            ->andWhere('DATE(cmd.dateCommande) = CURRENT_DATE()')
            ->getQuery()
            ->getSingleScalarResult();

        $stats['clients_aujourdhui'] = (int) $clientsAujourdhui;

        return $stats;
    }

    /**
     * Sauvegarder un client
     */
    public function save(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un client
     */
    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 