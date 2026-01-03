<?php
// src/Service/Burger/BurgerService.php
namespace App\Service\Burger;

use App\Entity\Burger;
use App\Repository\BurgerRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\Upload\ImageUploadService;

class BurgerService
{
    private BurgerRepository $burgerRepository;
    private ImageUploadService $imageUploadService;

    public function __construct(
        BurgerRepository $burgerRepository,
        ImageUploadService $imageUploadService
    ) {
        $this->burgerRepository = $burgerRepository;
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Créer un nouveau burger
     */
    public function createBurger(array $data, ?UploadedFile $imageFile = null): Burger
    {
        $burger = new Burger();
        $burger->setNom($data['nom'] ?? '');
        $burger->setDescription($data['description'] ?? null);
        $burger->setPrix($data['prix'] ?? 0);
        $burger->setDisponible($data['disponible'] ?? true);

        if ($imageFile) {
            $imageUrl = $this->imageUploadService->upload($imageFile, 'burgers');
            $burger->setImageUrl($imageUrl);
        }

        $this->burgerRepository->save($burger, true);

        return $burger;
    }

    /**
     * Mettre à jour un burger
     */
    public function updateBurger(Burger $burger, array $data, ?UploadedFile $imageFile = null): Burger
    {
        $burger->setNom($data['nom'] ?? $burger->getNom());
        $burger->setDescription($data['description'] ?? $burger->getDescription());
        $burger->setPrix($data['prix'] ?? $burger->getPrix());
        
        if (isset($data['disponible'])) {
            $burger->setDisponible($data['disponible']);
        }

        if ($imageFile) {
            // Supprimer l'ancienne image si elle existe
            if ($burger->getImageUrl()) {
                $this->imageUploadService->delete($burger->getImageUrl());
            }
            
            $imageUrl = $this->imageUploadService->upload($imageFile, 'burgers');
            $burger->setImageUrl($imageUrl);
        }

        $this->burgerRepository->save($burger, true);

        return $burger;
    }

    /**
     * Supprimer un burger
     */
    public function deleteBurger(Burger $burger): bool
    {
        // Supprimer l'image associée
        if ($burger->getImageUrl()) {
            $this->imageUploadService->delete($burger->getImageUrl());
        }

        $this->burgerRepository->remove($burger, true);

        return true;
    }

    /**
     * Changer la disponibilité d'un burger
     */
    public function toggleDisponibilite(Burger $burger): Burger
    {
        $burger->setDisponible(!$burger->isDisponible());
        $this->burgerRepository->save($burger, true);

        return $burger;
    }

    /**
     * Rechercher des burgers
     */
    public function searchBurgers(string $searchTerm): array
    {
        return $this->burgerRepository->searchByName($searchTerm);
    }

    /**
     * Obtenir les burgers disponibles
     */
    public function getAvailableBurgers(): array
    {
        return $this->burgerRepository->findAvailable();
    }

    /**
     * Obtenir les top burgers vendus
     */
    public function getTopBurgers(int $limit = 5): array
    {
        return $this->burgerRepository->findTopVendusAujourdhui($limit);
    }
}