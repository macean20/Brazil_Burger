<?php
// src/Service/Upload/ImageUploadService.php
namespace App\Service\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploadService
{
    private string $uploadsDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $uploadsDirectory, SluggerInterface $slugger)
    {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Upload une image
     */
    public function upload(UploadedFile $file, string $subdirectory = ''): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $uploadDir = $this->uploadsDirectory;
        if ($subdirectory) {
            $uploadDir .= '/' . trim($subdirectory, '/');
        }

        // Créer le répertoire s'il n'existe pas
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file->move($uploadDir, $newFilename);

        return '/uploads/' . ($subdirectory ? $subdirectory . '/' : '') . $newFilename;
    }

    /**
     * Supprimer une image
     */
    public function delete(string $imageUrl): bool
    {
        if (!$imageUrl) {
            return false;
        }

        $filePath = $this->uploadsDirectory . '/../public' . $imageUrl;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Vérifier si une image est valide
     */
    public function isValidImage(UploadedFile $file): bool
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        
        return in_array($file->getMimeType(), $allowedMimeTypes);
    }

    /**
     * Redimensionner une image
     */
    public function resizeImage(string $imagePath, int $maxWidth = 800, int $maxHeight = 600): bool
    {
        if (!file_exists($imagePath)) {
            return false;
        }

        $mimeType = mime_content_type($imagePath);
        
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($imagePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($imagePath);
                break;
            default:
                return false;
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculer les nouvelles dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int) ($originalWidth * $ratio);
        $newHeight = (int) ($originalHeight * $ratio);

        // Créer une nouvelle image redimensionnée
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Conserver la transparence pour les PNG
        if ($mimeType === 'image/png') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Sauvegarder l'image redimensionnée
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($resizedImage, $imagePath, 90);
                break;
            case 'image/png':
                imagepng($resizedImage, $imagePath, 9);
                break;
            case 'image/webp':
                imagewebp($resizedImage, $imagePath, 90);
                break;
        }

        imagedestroy($image);
        imagedestroy($resizedImage);

        return true;
    }
}