<?php
// src/Service/Burger/BurgerImageService.php
namespace App\Service\Burger;

use App\Entity\Burger;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\Upload\ImageUploadService;

class BurgerImageService
{
    private ImageUploadService $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * Upload et associer une image à un burger
     */
    public function uploadAndSetImage(Burger $burger, UploadedFile $imageFile): Burger
    {
        // Supprimer l'ancienne image si elle existe
        if ($burger->getImageUrl()) {
            $this->deleteImage($burger);
        }

        $imageUrl = $this->imageUploadService->upload($imageFile, 'burgers');
        $burger->setImageUrl($imageUrl);

        return $burger;
    }

    /**
     * Supprimer l'image d'un burger
     */
    public function deleteImage(Burger $burger): bool
    {
        if (!$burger->getImageUrl()) {
            return false;
        }

        $success = $this->imageUploadService->delete($burger->getImageUrl());
        
        if ($success) {
            $burger->setImageUrl(null);
        }

        return $success;
    }

    /**
     * Valider une image de burger
     */
    public function validateImage(UploadedFile $imageFile): array
    {
        $errors = [];

        // Vérifier la taille (max 2MB)
        if ($imageFile->getSize() > 2 * 1024 * 1024) {
            $errors[] = 'L\'image ne doit pas dépasser 2MB';
        }

        // Vérifier le type MIME
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($imageFile->getMimeType(), $allowedMimeTypes)) {
            $errors[] = 'Format d\'image non supporté. Utilisez JPEG, PNG ou WebP';
        }

        // Vérifier les dimensions (optionnel)
        $imageInfo = getimagesize($imageFile->getPathname());
        if ($imageInfo) {
            [$width, $height] = $imageInfo;
            
            if ($width < 400 || $height < 300) {
                $errors[] = 'L\'image doit faire au moins 400x300 pixels';
            }
            
            if ($width > 2000 || $height > 2000) {
                $errors[] = 'L\'image est trop grande (max 2000x2000 pixels)';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Générer une URL optimisée pour l'affichage
     */
    public function getOptimizedImageUrl(?string $imageUrl, array $transformations = []): string
    {
        if (!$imageUrl) {
            // Image par défaut
            return '/images/default-burger.png';
        }

        // Si Cloudinary est configuré, générer une URL optimisée
        if (str_contains($imageUrl, 'cloudinary.com')) {
            return $this->imageUploadService->getOptimizedUrl($imageUrl, $transformations);
        }

        return $imageUrl;
    }

    /**
     * Redimensionner une image
     */
    public function resizeImage(string $imagePath, int $width = 800, int $height = 600): bool
    {
        return $this->imageUploadService->resizeImage($imagePath, $width, $height);
    }
}