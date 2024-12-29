<?php

namespace App\Controller;

use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\ProduitRepository;
use App\Entity\ContenuPanier;
use App\Repository\ContenuPanierRepository;

class ContenuCommandeController extends AbstractController
{
    #[Route('/contenu_commande', name: 'app_contenu_commande_show')]
    public function index(Request $request, SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    { 
        $data = $cart->getCart($session);
        
        $panier = new Panier(); 
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($panier->isEtat()){

                $panier->setTotalPrix($data['total']);
                $panier->setDateachat(new \DateTime());
                $entityManager->persist($panier);
                
                foreach ($data['panpan'] as $value){
                    $contpanier = new ContenuPanier();
                    $contpanier->setPanier($panier);
                    $contpanier->setProduit($value['produit']);
                    $contpanier->setQuantite($value['quantite']);
                    $entityManager->persist($contpanier);
                }

                $entityManager->flush();
            }

            $session->set('panier', []);
            return $this->redirectToRoute('app_panier');
        }

        return $this->render('contenu_commande/index.html.twig', [
            'panierForm' => $form->createView(),
            'now'=> new \DateTime,
            'items'=> $data['panpan'],
            'total'=>$data['total'],
        ]);
    }
}
