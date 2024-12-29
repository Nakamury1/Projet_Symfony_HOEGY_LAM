<?php

namespace App\Controller;

use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Panier;
use App\Repository\ProduitRepository;

class PanierController extends AbstractController
{
    public function __construct(private readonly ProduitRepository $produitRepository){}


    // Accès à la page Panier
    #[Route('/panier', name: 'app_panier', methods:['GET'])]
    public function index(SessionInterface $session, Cart $cart): Response
    {   
        $data = $cart->getCart($session);

        return $this->render('panier/index.html.twig', [
            'items'=> $data['panpan'],
            'total'=>$data['total'],
        ]);
    }

    // Possibilité de voir le produit qui a été ajouté dans le panier
    #[Route('/panier/add/{id}/', name: 'app_panier_add', methods:['GET'])]
    public function addToPanier(int $id, SessionInterface $session):Response
    {
        $panier = $session->get('panier', []);
        
        if (!empty($panier[$id])){
            $panier[$id]++;
        }
        else{
            $panier[$id]=1;
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

    // Possibilité de supprimer le produit du panier
    #[Route('/panier/remove/{id}/', name: 'app_panier_produit_remove', methods:['GET'])]
    public function removeToPanier(int $id, SessionInterface $session):Response
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

    // Possibilité de supprimer le panier
    #[Route('/panier/remove', name: 'app_panier_remove', methods:['GET'])]
    public function remove(SessionInterface $session):Response
    {
        $session->set('panier', []);

        return $this->redirectToRoute('app_panier');
    }
}
