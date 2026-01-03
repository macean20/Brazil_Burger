<?php
// src/Service/Upload/CloudinaryService.php
namespace App\Service\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(
        string $cloudName,
        string $apiKey,
        string $apiSecret
    ) {
        $config = Configuration::instance();
        $config->cloud->cloudName = $cloudName;
        $config->cloud->apiKey = $apiKey;
        $config->cloud->apiSecret = $apiSecret;
        $config->url->secure = true;

        $this->cloudinary = new Cloudinary($config);
    }

    /**
     * Upload une image sur Cloudinary
     */
    public function uploadImage(UploadedFile $file, string $folder = 'brasil-burger'): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'public_id' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . uniqid(),
                'overwrite' => true,
                'resource_type' => 'auto',
                'transformation' => [
                    'width' => 800,
                    'height' => 600,
                    'crop' => 'fill',
                    'gravity' => 'auto',
                    'quality' => 'auto'
                ]
            ]
        );

        return $result['secure_url'];
    }

    /**
     * Supprimer une image de Cloudinary
     */
    public function deleteImage(string $imageUrl): bool
    {
        try {
            // Extraire le public_id de l'URL
            $publicId = $this->extractPublicIdFromUrl($imageUrl);
            
            if ($publicId) {
                $result = $this->cloudinary->uploadApi()->destroy($publicId);
                return isset($result['result']) && $result['result'] === 'ok';
            }
            
            return false;
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas bloquer
            return false;
        }
    }

    /**
     * Extraire le public_id d'une URL Cloudinary
     */
    private function extractPublicIdFromUrl(string $imageUrl): ?string
    {
        // Pattern pour extraire le public_id d'une URL Cloudinary
        $pattern = '/\/upload\/(?:v\d+\/)?([^\.]+)/';
        
        if (preg_match($pattern, $imageUrl, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Générer une URL optimisée avec transformations
     */
    public function getOptimizedUrl(string $imageUrl, array $transformations = []): string
    {
        $defaultTransformations = [
            'q_auto',
            'f_auto',
            'c_fill',
            'g_auto',
            'w_800',
            'h_600'
        ];

        $transformations = array_merge($defaultTransformations, $transformations);
        $transformationsString = implode(',', $transformations);

        // Insérer les transformations dans l'URL
        $urlParts = explode('/upload/', $imageUrl);
        if (count($urlParts) === 2) {
            return $urlParts[0] . '/upload/' . $transformationsString . '/' . $urlParts[1];
        }

        return $imageUrl;
    }

    /**
     * Upload multiple d'images
     */
    public function uploadMultiple(array $files, string $folder = 'brasil-burger'): array
    {
        $urls = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $urls[] = $this->uploadImage($file, $folder);
            }
        }
        
        return $urls;
    }
}