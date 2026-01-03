<?php
// src/Controller/Gestionnaire/CommandeController.php
namespace App\Controller\Gestionnaire;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Form\CommandeFilterType;
use App\Repository\CommandeRepository;
use App\Repository\LivreurRepository;
use App\Service\CommandeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestionnaire/commande')]
#[IsGranted('ROLE_GESTIONNAIRE')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'gestionnaire_commande_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        CommandeRepository $commandeRepository,
        CommandeService $commandeService
    ): Response {
        $form = $this->createForm(CommandeFilterType::class);
        $form->handleRequest($request);

        $filters = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        } else {
            // Par défaut, afficher les commandes du jour
            $filters['date'] = new \DateTime();
        }
        
        $commandes = $commandeService->filtrerCommandes($filters);
        
        return $this->render('gestionnaire/commande/index.html.twig', [
            'commandes' => $commandes,
            'form' => $form->createView(),
            'filters' => $filters
        ]);
    }

    #[Route('/{id}', name: 'gestionnaire_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('gestionnaire/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/changer-statut/{nouvelEtat}', name: 'gestionnaire_commande_changer_statut', methods: ['POST'])]
    public function changerStatut(
        Commande $commande, 
        string $nouvelEtat,
        CommandeService $commandeService
    ): JsonResponse {
        if ($commandeService->changerStatut($commande, $nouvelEtat)) {
            return $this->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'etat' => $commande->getEtat(),
                'etatLabel' => $commande->getEtatLabel()
            ]);
        }
        
        return $this->json([
            'success' => false,
            'message' => 'Transition de statut non autorisée'
        ], 400);
    }

    #[Route('/{id}/annuler', name: 'gestionnaire_commande_annuler', methods: ['POST'])]
    public function annuler(
        Commande $commande,
        CommandeService $commandeService
    ): JsonResponse {
        if ($commandeService->annulerCommande($commande)) {
            return $this->json([
                'success' => true,
                'message' => 'Commande annulée avec succès',
                'etat' => $commande->getEtat(),
                'etatLabel' => $commande->getEtatLabel()
            ]);
        }
        
        return $this->json([
            'success' => false,
            'message' => 'Cette commande ne peut pas être annulée'
        ], 400);
    }

    #[Route('/{id}/affecter-livreur', name: 'gestionnaire_commande_affecter_livreur', methods: ['POST'])]
    public function affecterLivreur(
        Request $request,
        Commande $commande,
        LivreurRepository $livreurRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $livreurId = $data['livreur_id'] ?? null;
        
        if (!$livreurId) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez sélectionner un livreur'
            ], 400);
        }
        
        $livreur = $livreurRepository->find($livreurId);
        
        if (!$livreur) {
            return $this->json([
                'success' => false,
                'message' => 'Livreur non trouvé'
            ], 404);
        }
        
        if (!$livreur->isDisponible()) {
            return $this->json([
                'success' => false,
                'message' => 'Ce livreur n\'est pas disponible'
            ], 400);
        }
        
        // Vérifier que la commande est prête pour la livraison
        if ($commande->getEtat() !== Commande::ETAT_TERMINEE && 
            $commande->getEtat() !== Commande::ETAT_EN_LIVRAISON) {
            return $this->json([
                'success' => false,
                'message' => 'La commande doit être terminée pour être affectée à un livreur'
            ], 400);
        }
        
        $commande->setLivreur($livreur);
        $commande->setEtat(Commande::ETAT_EN_LIVRAISON);
        
        $entityManager->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Livreur affecté avec succès',
            'livreur' => [
                'id' => $livreur->getId(),
                'nom' => $livreur->getPrenom() . ' ' . $livreur->getNom(),
                'telephone' => $livreur->getTelephone()
            ]
        ]);
    }

    #[Route('/{id}/details', name: 'gestionnaire_commande_details_modal', methods: ['GET'])]
    public function detailsModal(Commande $commande): Response
    {
        return $this->render('gestionnaire/commande/_modal_details.html.twig', [
            'commande' => $commande
        ]);
    }
}