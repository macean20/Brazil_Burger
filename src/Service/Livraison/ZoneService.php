<?php
// src/Service/Livraison/ZoneService.php
namespace App\Service\Livraison;

use App\Entity\Zone;
use App\Entity\Quartier;
use App\Repository\ZoneRepository;
use App\Repository\QuartierRepository;

class ZoneService
{
    private ZoneRepository $zoneRepository;
    private QuartierRepository $quartierRepository;

    public function __construct(
        ZoneRepository $zoneRepository,
        QuartierRepository $quartierRepository
    ) {
        $this->zoneRepository = $zoneRepository;
        $this->quartierRepository = $quartierRepository;
    }

    /**
     * Créer une nouvelle zone
     */
    public function createZone(array $data): Zone
    {
        $zone = new Zone();
        $zone->setNom($data['nom']);
        $zone->setPrixLivraison($data['prix_livraison']);
        $zone->setDescription($data['description'] ?? null);

        $this->zoneRepository->save($zone, true);

        // Ajouter les quartiers si fournis
        if (!empty($data['quartiers'])) {
            $this->addQuartiersToZone($zone, $data['quartiers']);
        }

        return $zone;
    }

    /**
     * Mettre à jour une zone
     */
    public function updateZone(Zone $zone, array $data): Zone
    {
        $zone->setNom($data['nom'] ?? $zone->getNom());
        $zone->setPrixLivraison($data['prix_livraison'] ?? $zone->getPrixLivraison());
        $zone->setDescription($data['description'] ?? $zone->getDescription());

        $this->zoneRepository->save($zone, true);

        return $zone;
    }

    /**
     * Supprimer une zone
     */
    public function deleteZone(Zone $zone): bool
    {
        // Vérifier si la zone a des commandes associées
        if (!$zone->getCommandes()->isEmpty()) {
            throw new \RuntimeException('Impossible de supprimer une zone avec des commandes associées');
        }

        $this->zoneRepository->remove($zone, true);
        return true;
    }

    /**
     * Ajouter des quartiers à une zone
     */
    public function addQuartiersToZone(Zone $zone, array $quartierNames): array
    {
        $addedQuartiers = [];
        
        foreach ($quartierNames as $quartierName) {
            $quartierName = trim($quartierName);
            
            if (empty($quartierName)) {
                continue;
            }

            // Vérifier si le quartier existe déjà dans cette zone
            $existingQuartier = $this->quartierRepository->findOneBy([
                'nom' => $quartierName,
                'zone' => $zone
            ]);

            if (!$existingQuartier) {
                $quartier = new Quartier();
                $quartier->setNom($quartierName);
                $quartier->setZone($zone);
                
                $this->quartierRepository->save($quartier, true);
                $addedQuartiers[] = $quartier;
            }
        }

        return $addedQuartiers;
    }

    /**
     * Supprimer un quartier
     */
    public function removeQuartier(Quartier $quartier): bool
    {
        $this->quartierRepository->remove($quartier, true);
        return true;
    }

    /**
     * Rechercher une zone par nom
     */
    public function findZoneByName(string $name): ?Zone
    {
        return $this->zoneRepository->findOneBy(['nom' => $name]);
    }

    /**
     * Obtenir toutes les zones avec leurs quartiers
     */
    public function getAllZonesWithQuartiers(): array
    {
        return $this->zoneRepository->createQueryBuilder('z')
            ->leftJoin('z.quartiers', 'q')
            ->addSelect('q')
            ->orderBy('z.nom', 'ASC')
            ->addOrderBy('q.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtenir les zones disponibles pour la livraison
     */
    public function getAvailableZones(): array
    {
        return $this->zoneRepository->findBy([], ['nom' => 'ASC']);
    }

    /**
     * Calculer le prix de livraison pour une adresse
     */
    public function calculateDeliveryPrice(?Zone $zone, string $address): ?float
    {
        if (!$zone) {
            return null;
        }

        return (float) $zone->getPrixLivraison();
    }

    /**
     * Trouver la zone d'une adresse
     */
    public function findZoneForAddress(string $address): ?Zone
    {
        // Normaliser l'adresse
        $address = strtolower(trim($address));
        
        // Chercher dans tous les quartiers
        $quartiers = $this->quartierRepository->createQueryBuilder('q')
            ->innerJoin('q.zone', 'z')
            ->addSelect('z')
            ->getQuery()
            ->getResult();

        foreach ($quartiers as $quartier) {
            if (stripos($address, strtolower($quartier->getNom())) !== false) {
                return $quartier->getZone();
            }
        }

        // Si aucun quartier trouvé, chercher par nom de zone
        $zones = $this->zoneRepository->findAll();
        foreach ($zones as $zone) {
            if (stripos($address, strtolower($zone->getNom())) !== false) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Obtenir les statistiques des zones
     */
    public function getZoneStats(): array
    {
        $zones = $this->zoneRepository->findAll();
        $stats = [];

        foreach ($zones as $zone) {
            $commandesCount = $zone->getCommandes()->count();
            $quartiersCount = $zone->getQuartiers()->count();
            
            $stats[] = [
                'zone' => $zone,
                'commandes_count' => $commandesCount,
                'quartiers_count' => $quartiersCount,
                'revenue_livraison' => $commandesCount * (float)$zone->getPrixLivraison()
            ];
        }

        // Trier par revenue décroissant
        usort($stats, function($a, $b) {
            return $b['revenue_livraison'] <=> $a['revenue_livraison'];
        });

        return $stats;
    }

    /**
     * Valider une zone avant création/modification
     */
    public function validateZoneData(array $data): array
    {
        $errors = [];

        if (empty($data['nom'])) {
            $errors[] = 'Le nom de la zone est obligatoire';
        }

        if (empty($data['prix_livraison'])) {
            $errors[] = 'Le prix de livraison est obligatoire';
        } elseif (!is_numeric($data['prix_livraison']) || $data['prix_livraison'] < 0) {
            $errors[] = 'Le prix de livraison doit être un nombre positif';
        }

        // Vérifier l'unicité du nom
        $existingZone = $this->findZoneByName($data['nom']);
        if ($existingZone && (!isset($data['id']) || $data['id'] != $existingZone->getId())) {
            $errors[] = 'Une zone avec ce nom existe déjà';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}