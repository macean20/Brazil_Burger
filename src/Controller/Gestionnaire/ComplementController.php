<?php
// src/Controller/Gestionnaire/ComplementController.php
namespace App\Controller\Gestionnaire;

use App\Entity\Complement;
use App\Form\ComplementType;
use App\Repository\ComplementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/gestionnaire/complement')]
#[IsGranted('ROLE_GESTIONNAIRE')]
class ComplementController extends AbstractController
{
    #[Route('/', name: 'gestionnaire_complement_index', methods: ['GET'])]
    public function index(ComplementRepository $complementRepository, Request $request): Response
    {
        $type = $request->query->get('type');
        $search = $request->query->get('search');
        
        $qb = $complementRepository->createQueryBuilder('c');
        
        if ($type) {
            $qb->andWhere('c.type = :type')
               ->setParameter('type', $type);
        }
        
        if ($search) {
            $qb->andWhere('c.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        $qb->orderBy('c.type', 'ASC')
           ->addOrderBy('c.nom', 'ASC');
           
        $complements = $qb->getQuery()->getResult();
        
        return $this->render('gestionnaire/complement/index.html.twig', [
            'complements' => $complements,
            'type' => $type,
            'search' => $search
        ]);
    }

    #[Route('/nouveau', name: 'gestionnaire_complement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $complement = new Complement();
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($complement);
            $entityManager->flush();
            
            $this->addFlash('success', 'Complément créé avec succès!');
            
            return $this->redirectToRoute('gestionnaire_complement_index');
        }

        return $this->render('gestionnaire/complement/new.html.twig', [
            'complement' => $complement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'gestionnaire_complement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Complement $complement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Complément modifié avec succès!');
            
            return $this->redirectToRoute('gestionnaire_complement_index');
        }

        return $this->render('gestionnaire/complement/edit.html.twig', [
            'complement' => $complement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'gestionnaire_complement_delete', methods: ['POST'])]
    public function delete(Request $request, Complement $complement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$complement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($complement);
            $entityManager->flush();
            
            $this->addFlash('success', 'Complément supprimé avec succès!');
        }

        return $this->redirectToRoute('gestionnaire_complement_index');
    }

    #[Route('/{id}/toggle-disponibilite', name: 'gestionnaire_complement_toggle_disponibilite', methods: ['POST'])]
    public function toggleDisponibilite(Complement $complement, EntityManagerInterface $entityManager): Response
    {
        $complement->setDisponible(!$complement->isDisponible());
        $entityManager->flush();
        
        $message = $complement->isDisponible() ? 'Complément rendu disponible' : 'Complément rendu indisponible';
        
        return $this->json([
            'success' => true,
            'message' => $message,
            'disponible' => $complement->isDisponible()
        ]);
    }

    #[Route('/types', name: 'gestionnaire_complement_types', methods: ['GET'])]
    public function getTypes(): JsonResponse
    {
        return $this->json([
            'types' => [
                ['value' => Complement::TYPE_BOISSON, 'label' => 'Boisson'],
                ['value' => Complement::TYPE_FRITE, 'label' => 'Frite']
            ]
        ]);
    }
}