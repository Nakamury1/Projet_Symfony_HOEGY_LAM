<?php

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart{

    public function __construct(private readonly ProduitRepository $produitRepository){}

    public function getCart(SessionInterface $session): array
    {
        $panpan = $session ->get('panier', []);
        $panpandonnees = [];

        foreach($panpan as $id=>$quantite){
            $panpandonnees[] = [
                'produit'=>$this->produitRepository->find($id),
                'quantite'=>$quantite,
            ];
        }

        $total = array_sum(array_map(function($item){
            return $item['produit']->getPrix() * $item['quantite'];
        }, $panpandonnees));

        return [
            'panpan' => $panpandonnees,
            'total' => $total,
        ];
    }
}

