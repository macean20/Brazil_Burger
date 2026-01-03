<?php
// src/Service/Complement/ComplementService.php
namespace App\Service\Complement;

use App\Entity\Complement;
use App\Repository\ComplementRepository;

class ComplementService
{
    private ComplementRepository $complementRepository;

    public function __construct(ComplementRepository $complementRepository)
    {
        $this->complementRepository = $complementRepository;
    }

    /**
     * Créer un nouveau complément
     */
    public function createComplement(array $data): Complement
    {
        $complement = new Complement();
        $complement->setNom($data['nom'] ?? '');
        $complement->setType($data['type'] ?? Complement::TYPE_BOISSON);
        $complement->setPrix($data['prix'] ?? 0);
        $complement->setDisponible($data['disponible'] ?? true);

        $this->complementRepository->save($complement, true);

        return $complement;
    }

    /**
     * Mettre à jour un complément
     */
    public function updateComplement(Complement $complement, array $data): Complement
    {
        $complement->setNom($data['nom'] ?? $complement->getNom());
        $complement->setType($data['type'] ?? $complement->getType());
        $complement->setPrix($data['prix'] ?? $complement->getPrix());
        
        if (isset($data['disponible'])) {
            $complement->setDisponible($data['disponible']);
        }

        $this->complementRepository->save($complement, true);

        return $complement;
    }

    /**
     * Supprimer un complément
     */
    public function deleteComplement(Complement $complement): bool
    {
        $this->complementRepository->remove($complement, true);
        return true;
    }

    /**
     * Changer la disponibilité d'un complément
     */
    public function toggleDisponibilite(Complement $complement): Complement
    {
        $complement->setDisponible(!$complement->isDisponible());
        $this->complementRepository->save($complement, true);

        return $complement;
    }

    /**
     * Obtenir les compléments par type
     */
    public function getComplementsByType(string $type): array
    {
        return $this->complementRepository->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->andWhere('c.disponible = true')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir toutes les boissons disponibles
     */
    public function getAvailableBoissons(): array
    {
        return $this->getComplementsByType(Complement::TYPE_BOISSON);
    }

    /**
     * Obtenir toutes les frites disponibles
     */
    public function getAvailableFrites(): array
    {
        return $this->getComplementsByType(Complement::TYPE_FRITE);
    }

    /**
     * Rechercher des compléments
     */
    public function searchComplements(string $searchTerm, ?string $type = null): array
    {
        $qb = $this->complementRepository->createQueryBuilder('c');

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
     * Vérifier si un complément peut être supprimé
     */
    public function canDeleteComplement(Complement $complement): array
    {
        $issues = [];
        
        // Vérifier si le complément est utilisé dans des menus
        $menusAsBoisson = $complement->getMenusBoisson();
        $menusAsFrite = $complement->getMenusFrite();
        
        if (!$menusAsBoisson->isEmpty() || !$menusAsFrite->isEmpty()) {
            $issues[] = 'Ce complément est utilisé dans ' . 
                       (count($menusAsBoisson) + count($menusAsFrite)) . 
                       ' menu(s)';
        }

        return [
            'can_delete' => empty($issues),
            'issues' => $issues
        ];
    }

    /**
     * Obtenir les statistiques des compléments
     */
    public function getComplementStats(): array
    {
        $qb = $this->complementRepository->createQueryBuilder('c');
        
        $stats = $qb->select('c.type, COUNT(c.id) as count, SUM(CASE WHEN c.disponible = true THEN 1 ELSE 0 END) as available')
            ->groupBy('c.type')
            ->getQuery()
            ->getResult();

        $formattedStats = [];
        foreach ($stats as $stat) {
            $formattedStats[$stat['type']] = [
                'total' => $stat['count'],
                'available' => $stat['available'],
                'unavailable' => $stat['count'] - $stat['available']
            ];
        }

        return $formattedStats;
    }
}