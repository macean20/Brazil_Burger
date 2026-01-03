<?php
// src/Controller/Gestionnaire/DashboardController.php
namespace App\Controller\Gestionnaire;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\BurgerRepository;
use App\Repository\MenuRepository;
use App\Repository\PaiementRepository;
use App\Service\StatistiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestionnaire')]
#[IsGranted('ROLE_GESTIONNAIRE')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'gestionnaire_dashboard')]
    public function index(
        StatistiqueService $statistiqueService,
        BurgerRepository $burgerRepository,
        CommandeRepository $commandeRepository
    ): Response {
        // Statistiques du jour
        $stats = $statistiqueService->getStatsDuJour();
        
        // Top 5 burgers les plus vendus aujourd'hui
        $topBurgers = $burgerRepository->findTopVendusAujourdhui(5);
        
        // Commandes par heure aujourd'hui
        $commandesParHeure = $commandeRepository->getCommandesParHeureAujourdhui();
        
        return $this->render('gestionnaire/dashboard/index.html.twig', [
            'stats' => $stats,
            'top_burgers' => $topBurgers,
            'commandes_par_heure' => $commandesParHeure,
        ]);
    }

    #[Route('/stats', name: 'gestionnaire_dashboard_stats')]
    public function getStats(StatistiqueService $statistiqueService): Response
    {
        $stats = $statistiqueService->getStatsDuJour();
        
        return $this->json([
            'en_cours' => $stats['en_cours'],
            'validees' => $stats['validees'],
            'recettes' => $stats['recettes'],
            'annulees' => $stats['annulees'],
        ]);
    }

    #[Route('/commandes-par-heure', name: 'gestionnaire_commandes_par_heure')]
    public function getCommandesParHeure(CommandeRepository $commandeRepository): Response
    {
        $data = $commandeRepository->getCommandesParHeureAujourdhui();
        
        return $this->json($data);
    }
}