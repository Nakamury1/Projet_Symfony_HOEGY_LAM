<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContenuCommandeController extends AbstractController
{
    #[Route('/contenu/commande', name: 'app_contenu_commande')]
    public function index(): Response
    {
        return $this->render('contenu_commande/index.html.twig', [
            'controller_name' => 'ContenuCommandeController',
        ]);
    }
}
