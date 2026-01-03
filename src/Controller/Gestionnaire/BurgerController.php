<?php
// src/Controller/Gestionnaire/BurgerController.php
namespace App\Controller\Gestionnaire;

use App\Entity\Burger;
use App\Form\BurgerType;
use App\Repository\BurgerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestionnaire/burger')]
#[IsGranted('ROLE_GESTIONNAIRE')]
class BurgerController extends AbstractController
{
    #[Route('/', name: 'gestionnaire_burger_index', methods: ['GET'])]
    public function index(BurgerRepository $burgerRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        
        if ($search) {
            $burgers = $burgerRepository->searchByName($search);
        } else {
            $burgers = $burgerRepository->findBy([], ['nom' => 'ASC']);
        }
        
        return $this->render('gestionnaire/burger/index.html.twig', [
            'burgers' => $burgers,
            'search' => $search
        ]);
    }

    #[Route('/nouveau', name: 'gestionnaire_burger_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $burger = new Burger();
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory').'/burgers',
                    $newFilename
                );
                $burger->setImageUrl('/uploads/burgers/'.$newFilename);
            }
            
            $entityManager->persist($burger);
            $entityManager->flush();
            
            $this->addFlash('success', 'Burger créé avec succès!');
            
            return $this->redirectToRoute('gestionnaire_burger_index');
        }

        return $this->render('gestionnaire/burger/new.html.twig', [
            'burger' => $burger,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'gestionnaire_burger_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Burger $burger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                // Supprimer l'ancienne image si elle existe
                $oldImage = $burger->getImageUrl();
                if ($oldImage && file_exists($this->getParameter('kernel.project_dir').'/public'.$oldImage)) {
                    unlink($this->getParameter('kernel.project_dir').'/public'.$oldImage);
                }
                
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory').'/burgers',
                    $newFilename
                );
                $burger->setImageUrl('/uploads/burgers/'.$newFilename);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Burger modifié avec succès!');
            
            return $this->redirectToRoute('gestionnaire_burger_index');
        }

        return $this->render('gestionnaire/burger/edit.html.twig', [
            'burger' => $burger,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'gestionnaire_burger_delete', methods: ['POST'])]
    public function delete(Request $request, Burger $burger, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$burger->getId(), $request->request->get('_token'))) {
            // Supprimer l'image si elle existe
            $imageUrl = $burger->getImageUrl();
            if ($imageUrl && file_exists($this->getParameter('kernel.project_dir').'/public'.$imageUrl)) {
                unlink($this->getParameter('kernel.project_dir').'/public'.$imageUrl);
            }
            
            $entityManager->remove($burger);
            $entityManager->flush();
            
            $this->addFlash('success', 'Burger supprimé avec succès!');
        }

        return $this->redirectToRoute('gestionnaire_burger_index');
    }

    #[Route('/{id}/toggle-disponibilite', name: 'gestionnaire_burger_toggle_disponibilite', methods: ['POST'])]
    public function toggleDisponibilite(Burger $burger, EntityManagerInterface $entityManager): Response
    {
        $burger->setDisponible(!$burger->isDisponible());
        $entityManager->flush();
        
        $message = $burger->isDisponible() ? 'Burger rendu disponible' : 'Burger rendu indisponible';
        
        return $this->json([
            'success' => true,
            'message' => $message,
            'disponible' => $burger->isDisponible()
        ]);
    }
}