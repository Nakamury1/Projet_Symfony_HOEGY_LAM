<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FicheProduitController extends AbstractController
{
    #[Route('/fiche/produit', name: 'app_fiche_produit')]
    public function index(): Response
    {
        return $this->render('fiche_produit/index.html.twig', [
            'controller_name' => 'FicheProduitController',
        ]);
    }
}
