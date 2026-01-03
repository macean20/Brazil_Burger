<?php
// src/Service/StatistiqueService.php
namespace App\Service;

use App\Repository\CommandeRepository;
use App\Repository\PaiementRepository;

class StatistiqueService
{
    private CommandeRepository $commandeRepository;
    private PaiementRepository $paiementRepository;

    public function __construct(
        CommandeRepository $commandeRepository,
        PaiementRepository $paiementRepository
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->paiementRepository = $paiementRepository;
    }

    /**
     * Obtenir les statistiques du jour
     */
    public function getStatsDuJour(): array
    {
        return [
            'en_cours' => $this->commandeRepository->countEnCoursAujourdhui(),
            'validees' => $this->commandeRepository->countValideesAujourdhui(),
            'recettes' => $this->paiementRepository->getRecettesAujourdhui(),
            'annulees' => $this->commandeRepository->countAnnuleesAujourdhui(),
        ];
    }

    /**
     * Obtenir les recettes du jour
     */
    public function getRecettesJournalieres(): float
    {
        return $this->paiementRepository->getRecettesAujourdhui();
    }
}