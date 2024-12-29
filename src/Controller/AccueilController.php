<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccueilController extends AbstractController
{   
    //Accès à la page d'accueil
    #[Route('/', name: 'app_accueil', methods:['GET'])]
    public function index(Request $request, ProduitRepository $produitRepository, TranslatorInterface $translator): Response
    {
        return $this->render('accueil/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    //Accès à la fiche produit à partir de la page d'accueil
    #[Route('/{id}', name: 'app_produit_show', methods:['GET'])]
    public function show(Produit $produit, TranslatorInterface $translator): Response
    {
        return $this->render('accueil/index.html.twig', [
            'produit' => $produit,
        ]);
    }
}
