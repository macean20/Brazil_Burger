<?php
// src/Controller/Gestionnaire/LivraisonController.php
namespace App\Controller\Gestionnaire;

use App\Entity\Zone;
use App\Entity\Quartier;
use App\Entity\Livreur;
use App\Form\ZoneType;
use App\Form\QuartierType;
use App\Form\LivreurType;
use App\Repository\ZoneRepository;
use App\Repository\QuartierRepository;
use App\Repository\LivreurRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestionnaire/livraison')]
#[IsGranted('ROLE_GESTIONNAIRE')]
class LivraisonController extends AbstractController
{
    #[Route('/zones', name: 'gestionnaire_zone_index', methods: ['GET'])]
    public function zonesIndex(ZoneRepository $zoneRepository): Response
    {
        $zones = $zoneRepository->findBy([], ['nom' => 'ASC']);
        
        return $this->render('gestionnaire/livraison/zone/index.html.twig', [
            'zones' => $zones
        ]);
    }

    #[Route('/zones/nouveau', name: 'gestionnaire_zone_new', methods: ['GET', 'POST'])]
    public function zoneNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $zone = new Zone();
        $form = $this->createForm(ZoneType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zone);
            $entityManager->flush();
            
            $this->addFlash('success', 'Zone créée avec succès!');
            
            return $this->redirectToRoute('gestionnaire_zone_index');
        }

        return $this->render('gestionnaire/livraison/zone/new.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/zones/{id}/edit', name: 'gestionnaire_zone_edit', methods: ['GET', 'POST'])]
    public function zoneEdit(Request $request, Zone $zone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ZoneType::class, $zone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Zone modifiée avec succès!');
            
            return $this->redirectToRoute('gestionnaire_zone_index');
        }

        return $this->render('gestionnaire/livraison/zone/edit.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/zones/{id}', name: 'gestionnaire_zone_delete', methods: ['POST'])]
    public function zoneDelete(Request $request, Zone $zone, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zone->getId(), $request->request->get('_token'))) {
            $entityManager->remove($zone);
            $entityManager->flush();
            
            $this->addFlash('success', 'Zone supprimée avec succès!');
        }

        return $this->redirectToRoute('gestionnaire_zone_index');
    }

    #[Route('/zones/{id}/quartiers', name: 'gestionnaire_zone_quartiers', methods: ['GET'])]
    public function zoneQuartiers(Zone $zone): Response
    {
        return $this->render('gestionnaire/livraison/zone/_quartiers.html.twig', [
            'zone' => $zone
        ]);
    }

    #[Route('/zones/{id}/ajouter-quartier', name: 'gestionnaire_quartier_new', methods: ['POST'])]
    public function quartierNew(Request $request, Zone $zone, EntityManagerInterface $entityManager): JsonResponse
    {
        $nom = $request->request->get('nom');
        
        if (!$nom) {
            return $this->json([
                'success' => false,
                'message' => 'Le nom du quartier est obligatoire'
            ], 400);
        }
        
        $quartier = new Quartier();
        $quartier->setNom($nom);
        $quartier->setZone($zone);
        
        try {
            $entityManager->persist($quartier);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Quartier ajouté avec succès',
                'quartier' => [
                    'id' => $quartier->getId(),
                    'nom' => $quartier->getNom()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Ce quartier existe déjà dans cette zone'
            ], 400);
        }
    }

    #[Route('/quartiers/{id}', name: 'gestionnaire_quartier_delete', methods: ['DELETE'])]
    public function quartierDelete(Quartier $quartier, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($quartier);
        $entityManager->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Quartier supprimé avec succès'
        ]);
    }

    #[Route('/livreurs', name: 'gestionnaire_livreur_index', methods: ['GET'])]
    public function livreursIndex(LivreurRepository $livreurRepository): Response
    {
        $livreurs = $livreurRepository->findBy([], ['nom' => 'ASC']);
        
        return $this->render('gestionnaire/livraison/livreur/index.html.twig', [
            'livreurs' => $livreurs
        ]);
    }

    #[Route('/livreurs/nouveau', name: 'gestionnaire_livreur_new', methods: ['GET', 'POST'])]
    public function livreurNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livreur = new Livreur();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
                $livreur->setPassword($hashedPassword);
            }
            
            $entityManager->persist($livreur);
            $entityManager->flush();
            
            $this->addFlash('success', 'Livreur créé avec succès!');
            
            return $this->redirectToRoute('gestionnaire_livreur_index');
        }

        return $this->render('gestionnaire/livraison/livreur/new.html.twig', [
            'livreur' => $livreur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/livreurs/{id}/edit', name: 'gestionnaire_livreur_edit', methods: ['GET', 'POST'])]
    public function livreurEdit(Request $request, Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe si fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
                $livreur->setPassword($hashedPassword);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Livreur modifié avec succès!');
            
            return $this->redirectToRoute('gestionnaire_livreur_index');
        }

        return $this->render('gestionnaire/livraison/livreur/edit.html.twig', [
            'livreur' => $livreur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/livreurs/{id}', name: 'gestionnaire_livreur_delete', methods: ['POST'])]
    public function livreurDelete(Request $request, Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livreur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livreur);
            $entityManager->flush();
            
            $this->addFlash('success', 'Livreur supprimé avec succès!');
        }

        return $this->redirectToRoute('gestionnaire_livreur_index');
    }

    #[Route('/livreurs/{id}/toggle-disponibilite', name: 'gestionnaire_livreur_toggle_disponibilite', methods: ['POST'])]
    public function livreurToggleDisponibilite(Livreur $livreur, EntityManagerInterface $entityManager): JsonResponse
    {
        $livreur->setDisponible(!$livreur->isDisponible());
        $entityManager->flush();
        
        $message = $livreur->isDisponible() ? 'Livreur rendu disponible' : 'Livreur rendu indisponible';
        
        return $this->json([
            'success' => true,
            'message' => $message,
            'disponible' => $livreur->isDisponible()
        ]);
    }

    #[Route('/commandes-par-zone', name: 'gestionnaire_commandes_par_zone', methods: ['GET'])]
    public function commandesParZone(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->createQueryBuilder('c')
            ->andWhere('c.etat IN (:etats)')
            ->setParameter('etats', [Commande::ETAT_TERMINEE, Commande::ETAT_EN_LIVRAISON])
            ->andWhere('c.zone IS NOT NULL')
            ->orderBy('c.zone', 'ASC')
            ->addOrderBy('c.dateCommande', 'ASC')
            ->getQuery()
            ->getResult();
        
        // Grouper par zone
        $commandesParZone = [];
        foreach ($commandes as $commande) {
            $zoneId = $commande->getZone()->getId();
            if (!isset($commandesParZone[$zoneId])) {
                $commandesParZone[$zoneId] = [
                    'zone' => $commande->getZone(),
                    'commandes' => []
                ];
            }
            $commandesParZone[$zoneId]['commandes'][] = $commande;
        }
        
        return $this->render('gestionnaire/livraison/_commandes_par_zone.html.twig', [
            'commandesParZone' => $commandesParZone
        ]);
    }
}