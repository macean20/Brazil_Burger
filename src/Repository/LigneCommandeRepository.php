<?php
// src/Repository/LigneCommandeRepository.php
namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    /**
     * Trouver les lignes de commande d'une commande
     */
    public function findByCommande($commandeId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.commande = :commande')
            ->setParameter('commande', $commandeId)
            ->orderBy('l.typeItem', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les statistiques des produits vendus
     */
    public function getVentesParProduit(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): array
    {
        return $this->createQueryBuilder('l')
            ->select('
                CASE 
                    WHEN l.typeItem = :type_burger THEN b.nom
                    WHEN l.typeItem = :type_menu THEN m.nom
                END as nom_produit,
                l.typeItem as type_produit,
                SUM(l.quantite) as total_quantite,
                SUM(l.sousTotal) as total_chiffre_affaires
            ')
            ->leftJoin('l.burger', 'b')
            ->leftJoin('l.menu', 'm')
            ->innerJoin('l.commande', 'c')
            ->andWhere('c.dateCommande BETWEEN :debut AND :fin')
            ->andWhere('c.etat != :etat_annule')
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin)
            ->setParameter('type_burger', 'BURGER')
            ->setParameter('type_menu', 'MENU')
            ->setParameter('etat_annule', 'ANNULEE')
            ->groupBy('l.typeItem, COALESCE(b.id, m.id)')
            ->orderBy('total_quantite', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les ventes quotidiennes
     */
    public function getVentesQuotidiennes(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('l')
            ->select('
                DATE(c.dateCommande) as date_vente,
                SUM(l.quantite) as total_quantite,
                SUM(l.sousTotal) as total_chiffre_affaires,
                COUNT(DISTINCT c.id) as nb_commandes
            ')
            ->innerJoin('l.commande', 'c')
            ->andWhere('DATE(c.dateCommande) = :date')
            ->andWhere('c.etat != :etat_annule')
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('etat_annule', 'ANNULEE')
            ->groupBy('date_vente')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Sauvegarder une ligne de commande
     */
    public function save(LigneCommande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer une ligne de commande
     */
    public function remove(LigneCommande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}