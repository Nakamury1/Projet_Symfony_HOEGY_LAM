<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use App\Repository\ContenuPanierRepository;
use App\Service\Cart;

class MonCompteController extends AbstractController
{   
    // Accès à la page de compte de l'utlisateur avec mis à jour des informations personnelles et de son historique de commande
    #[Route('/mon_compte', name: 'app_mon_compte', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PanierRepository $panierRepository): Response
    {   
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté');
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Compte mis à jour');
            return $this->redirectToRoute('app_mon_compte');
        }

        // Utiliser l'ID de l'utilisateur connecté
        $paniers = $panierRepository->findBy(['user' => $user->getId()]);
        
        return $this->render('mon_compte/index.html.twig', [
            'registrationForm' => $form,
            'paniers' => $paniers,
        ]);
    }

    #[Route('/contenu_commande/{id}', name: 'app_mon_compte_show', methods: ['GET'])]
    public function showCommande(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, Panier $panier, PanierRepository $panierRepository, ContenuPanierRepository $contenuPanierRepository, Cart $cart): Response
    {   
        $data = $cart->getCart($session);
        $user = $this->getUser();
        $paniers = $panierRepository->findBy(['user' => $user->getId()]);
        $contenuPanier = $contenuPanierRepository->findBy(['panier' => $panier]);
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($panier->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    
        $contenuPanier = $contenuPanierRepository->findBy(['panier' => $panier]);
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_mon_compte');
        }

        return $this->render('mon_compte/show.html.twig', [
            'panierForm' => $form->createView(),
            'panier' => $panier,
            'contenuPanier' => $contenuPanier,
            'paniers' => $paniers,
        ]);
    }
}