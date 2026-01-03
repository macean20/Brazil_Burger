<?php
// src/Service/Livraison/LivreurService.php
namespace App\Service\Livraison;

use App\Entity\Livreur;
use App\Repository\LivreurRepository;
use App\Entity\Commande;

class LivreurService
{
    private LivreurRepository $livreurRepository;

    public function __construct(LivreurRepository $livreurRepository)
    {
        $this->livreurRepository = $livreurRepository;
    }

    /**
     * Créer un nouveau livreur
     */
    public function createLivreur(array $data): Livreur
    {
        $livreur = new Livreur();
        $livreur->setNom($data['nom']);
        $livreur->setPrenom($data['prenom']);
        $livreur->setTelephone($data['telephone']);
        $livreur->setEmail($data['email']);
        
        // Hasher le mot de passe
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $livreur->setPassword($hashedPassword);
        }
        
        $livreur->setDisponible($data['disponible'] ?? true);

        $this->livreurRepository->save($livreur, true);

        return $livreur;
    }

    /**
     * Mettre à jour un livreur
     */
    public function updateLivreur(Livreur $livreur, array $data): Livreur
    {
        $livreur->setNom($data['nom'] ?? $livreur->getNom());
        $livreur->setPrenom($data['prenom'] ?? $livreur->getPrenom());
        $livreur->setTelephone($data['telephone'] ?? $livreur->getTelephone());
        $livreur->setEmail($data['email'] ?? $livreur->getEmail());
        
        // Mettre à jour le mot de passe si fourni
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $livreur->setPassword($hashedPassword);
        }
        
        if (isset($data['disponible'])) {
            $livreur->setDisponible($data['disponible']);
        }

        $this->livreurRepository->save($livreur, true);

        return $livreur;
    }

    /**
     * Supprimer un livreur
     */
    public function deleteLivreur(Livreur $livreur): bool
    {
        // Vérifier si le livreur a des commandes associées
        if (!$livreur->getCommandes()->isEmpty()) {
            throw new \RuntimeException('Impossible de supprimer un livreur avec des commandes associées');
        }

        $this->livreurRepository->remove($livreur, true);
        return true;
    }

    /**
     * Changer la disponibilité d'un livreur
     */
    public function toggleDisponibilite(Livreur $livreur): Livreur
    {
        $livreur->setDisponible(!$livreur->isDisponible());
        $this->livreurRepository->save($livreur, true);

        return $livreur;
    }

    /**
     * Obtenir les livreurs disponibles
     */
    public function getAvailableLivreurs(): array
    {
        return $this->livreurRepository->findBy([
            'disponible' => true
        ], ['nom' => 'ASC', 'prenom' => 'ASC']);
    }

    /**
     * Trouver un livreur disponible pour une commande
     */
    public function findAvailableLivreurForCommande(Commande $commande): ?Livreur
    {
        // Logique pour trouver le livreur le plus approprié
        $livreurs = $this->getAvailableLivreurs();
        
        if (empty($livreurs)) {
            return null;
        }

        // Pour l'instant, retourner le premier livreur disponible
        // Plus tard: implémenter une logique plus sophistiquée
        // (proximité, charge de travail, etc.)
        return $livreurs[0];
    }

    /**
     * Affecter un livreur à une commande
     */
    public function assignLivreurToCommande(Livreur $livreur, Commande $commande): bool
    {
        if (!$livreur->isDisponible()) {
            return false;
        }

        $commande->setLivreur($livreur);
        return true;
    }

    /**
     * Libérer un livreur (retirer l'affectation)
     */
    public function releaseLivreurFromCommande(Livreur $livreur, Commande $commande): bool
    {
        if ($commande->getLivreur() === $livreur) {
            $commande->setLivreur(null);
            return true;
        }

        return false;
    }

    /**
     * Obtenir les statistiques d'un livreur
     */
    public function getLivreurStats(Livreur $livreur): array
    {
        $commandes = $livreur->getCommandes();
        $totalCommandes = $commandes->count();
        
        $commandesLivrees = $commandes->filter(function(Commande $commande) {
            return $commande->getEtat() === Commande::ETAT_LIVREE;
        })->count();

        $revenueTotal = 0;
        foreach ($commandes as $commande) {
            if ($commande->getZone()) {
                $revenueTotal += (float) $commande->getZone()->getPrixLivraison();
            }
        }

        return [
            'total_commandes' => $totalCommandes,
            'commandes_livrees' => $commandesLivrees,
            'taux_livraison' => $totalCommandes > 0 ? 
                round(($commandesLivrees / $totalCommandes) * 100, 2) : 0,
            'revenue_generes' => $revenueTotal,
            'moyenne_commandes_jour' => $this->calculateAverageCommandsPerDay($livreur)
        ];
    }

    /**
     * Calculer la moyenne de commandes par jour
     */
    private function calculateAverageCommandsPerDay(Livreur $livreur): float
    {
        $commandes = $livreur->getCommandes();
        
        if ($commandes->isEmpty()) {
            return 0.0;
        }

        // Trouver la première et dernière commande
        $dates = [];
        foreach ($commandes as $commande) {
            $dates[] = $commande->getDateCommande();
        }

        sort($dates);
        $firstDate = reset($dates);
        $lastDate = end($dates);

        $interval = $firstDate->diff($lastDate);
        $days = max($interval->days, 1); // Au moins 1 jour

        return round($commandes->count() / $days, 2);
    }

    /**
     * Valider les données d'un livreur
     */
    public function validateLivreurData(array $data): array
    {
        $errors = [];

        // Validation des champs obligatoires
        $requiredFields = ['nom', 'prenom', 'telephone', 'email'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ $field est obligatoire";
            }
        }

        // Validation email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format d\'email invalide';
        }

        // Validation téléphone
        if (!empty($data['telephone']) && !preg_match('/^[0-9+\s\-\(\)]{8,20}$/', $data['telephone'])) {
            $errors[] = 'Format de téléphone invalide';
        }

        // Vérifier l'unicité de l'email
        $existingLivreur = $this->livreurRepository->findOneBy(['email' => $data['email'] ?? '']);
        if ($existingLivreur && (!isset($data['id']) || $data['id'] != $existingLivreur->getId())) {
            $errors[] = 'Un livreur avec cet email existe déjà';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Rechercher des livreurs
     */
    public function searchLivreurs(string $searchTerm, ?bool $disponible = null): array
    {
        $qb = $this->livreurRepository->createQueryBuilder('l');

        if ($searchTerm) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('l.nom', ':search'),
                $qb->expr()->like('l.prenom', ':search'),
                $qb->expr()->like('l.telephone', ':search'),
                $qb->expr()->like('l.email', ':search')
            ))
            ->setParameter('search', '%' . $searchTerm . '%');
        }

        if ($disponible !== null) {
            $qb->andWhere('l.disponible = :disponible')
               ->setParameter('disponible', $disponible);
        }

        return $qb->orderBy('l.nom', 'ASC')
                  ->addOrderBy('l.prenom', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}