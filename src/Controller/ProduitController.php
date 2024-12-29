<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

#[Route('/produit')]
final class ProduitController extends AbstractController
{   
    //Accès à la page de tous les produits
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository, TranslatorInterface $translator): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    //Accès à la page d'ajout d'un produit
    #[Route('/admin/produit/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('photo')->getData();
 
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
 
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ...
                }
 
                $produit->setPhoto($newFilename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash($translator->trans('Succès'), $translator->trans('Produit ajouté'));
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    //Possibilité d'ajouter un produit dans le panier si l'utilisateur est connecté
    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createFormBuilder()
        ->add('quantite', IntegerType::class, [
            'constraints' => [
                new NotBlank(),
                new GreaterThan(['value' => 0])
            ],
            'data' => 1,
            'attr' => [
                'min' => 1
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user) {
                $this->addFlash($translator->trans('Erreur'), $translator->trans('Veuillez vous connecter pour ajouter des produits au panier'));
                return $this->redirectToRoute('app_login');
            }
            
            $panier = $entityManager->getRepository(Panier::class)->findOneBy([
                'user' => $user,
                'etat' => 'active'
            ]);
    
            if (!$panier) {
                $panier = new Panier();
                $panier->setUser($user);
                $panier->setEtat('active');
                $entityManager->persist($panier);
            }    

            $panierItem = $entityManager->getRepository(Panier::class)->findOneBy([
                'panier' => $panier,
                'produit' => $produit
            ]);
            
            if ($produit) {
                $produit->setQuantity($produit->getQuantite() + $form->get('quantite')->getData());
            }
            else {
            $produit = new Panier();
            $produit->setProduit($produit);
            $produit->setQuantite($form->get('quantite')->getData());
            $produit->setPanier($panier);
            $entityManager->persist($produit);
            }

            $entityManager->flush();

            $this->addFlash($translator->trans('Succès'), $translator->trans('Produit ajouté au panier'));
            return $this->redirectToRoute('app_produit_show');
        }
        
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    //Possibilité de modifier le produit
    #[Route('/admin/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash($translator->trans('Succès'), $translator->trans('Produit modifié'));
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    //Possibilité de supprimer le produit
    #[Route('/admin/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();

            $this->addFlash($translator->trans('Erreur'), $translator->trans('Produit supprimé'));
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
