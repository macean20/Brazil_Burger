<?php
// src/Controller/Gestionnaire/MenuController.php
namespace App\Controller\Gestionnaire;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Repository\BurgerRepository;
use App\Repository\ComplementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestionnaire/menu')]
#[IsGranted('ROLE_GESTIONNAIRE')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'gestionnaire_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        
        if ($search) {
            $menus = $menuRepository->createQueryBuilder('m')
                ->andWhere('m.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('m.nom', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            $menus = $menuRepository->findBy([], ['nom' => 'ASC']);
        }
        
        return $this->render('gestionnaire/menu/index.html.twig', [
            'menus' => $menus,
            'search' => $search
        ]);
    }

    #[Route('/nouveau', name: 'gestionnaire_menu_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        BurgerRepository $burgerRepository,
        ComplementRepository $complementRepository
    ): Response {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculer le prix total avec réduction de 10%
            $burgerPrix = (float) $menu->getBurger()->getPrix();
            $boissonPrix = (float) $menu->getBoisson()->getPrix();
            $fritePrix = (float) $menu->getFrite()->getPrix();
            
            $prixTotal = ($burgerPrix + $boissonPrix + $fritePrix) * 0.9;
            $menu->setPrixTotal(number_format($prixTotal, 2, '.', ''));
            
            $entityManager->persist($menu);
            $entityManager->flush();
            
            $this->addFlash('success', 'Menu créé avec succès!');
            
            return $this->redirectToRoute('gestionnaire_menu_index');
        }

        return $this->render('gestionnaire/menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'gestionnaire_menu_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Menu $menu, 
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Recalculer le prix total avec réduction de 10%
            $burgerPrix = (float) $menu->getBurger()->getPrix();
            $boissonPrix = (float) $menu->getBoisson()->getPrix();
            $fritePrix = (float) $menu->getFrite()->getPrix();
            
            $prixTotal = ($burgerPrix + $boissonPrix + $fritePrix) * 0.9;
            $menu->setPrixTotal(number_format($prixTotal, 2, '.', ''));
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Menu modifié avec succès!');
            
            return $this->redirectToRoute('gestionnaire_menu_index');
        }

        return $this->render('gestionnaire/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'gestionnaire_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menu);
            $entityManager->flush();
            
            $this->addFlash('success', 'Menu supprimé avec succès!');
        }

        return $this->redirectToRoute('gestionnaire_menu_index');
    }

    #[Route('/{id}/toggle-disponibilite', name: 'gestionnaire_menu_toggle_disponibilite', methods: ['POST'])]
    public function toggleDisponibilite(Menu $menu, EntityManagerInterface $entityManager): Response
    {
        $menu->setDisponible(!$menu->isDisponible());
        $entityManager->flush();
        
        $message = $menu->isDisponible() ? 'Menu rendu disponible' : 'Menu rendu indisponible';
        
        return $this->json([
            'success' => true,
            'message' => $message,
            'disponible' => $menu->isDisponible()
        ]);
    }

    #[Route('/calculer-prix', name: 'gestionnaire_menu_calculer_prix', methods: ['POST'])]
    public function calculerPrix(Request $request, BurgerRepository $burgerRepository, ComplementRepository $complementRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $burgerId = $data['burgerId'] ?? null;
        $boissonId = $data['boissonId'] ?? null;
        $friteId = $data['friteId'] ?? null;
        
        $prixTotal = 0;
        
        if ($burgerId) {
            $burger = $burgerRepository->find($burgerId);
            if ($burger) {
                $prixTotal += (float) $burger->getPrix();
            }
        }
        
        if ($boissonId) {
            $boisson = $complementRepository->find($boissonId);
            if ($boisson) {
                $prixTotal += (float) $boisson->getPrix();
            }
        }
        
        if ($friteId) {
            $frite = $complementRepository->find($friteId);
            if ($frite) {
                $prixTotal += (float) $frite->getPrix();
            }
        }
        
        // Appliquer réduction de 10%
        $prixTotal = $prixTotal * 0.9;
        
        return $this->json([
            'success' => true,
            'prixTotal' => number_format($prixTotal, 2, '.', ''),
            'prixTotalFormatted' => number_format($prixTotal, 0, ',', ' ') . ' FCFA'
        ]);
    }
}