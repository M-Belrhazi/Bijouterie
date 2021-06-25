<?php

namespace App\Service\Panier;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService{
    public $session;
    public $articleRepository;

    public function __construct(SessionInterface $session, ArticleRepository $articleRepository){
        $this->session = $session;
        $this->articleRepository = $articleRepository;
    }


    public function add(int $id){
        //déclaration en session d'un panier qui charge les articles par id et par quantité
        $panier=$this->session->get('panier', []);
        if (!empty($panier[$id])):
            $panier[$id]++;
        else:
            $panier[$id]=1;
        endif;

        $this->session->set('panier', $panier);
    }

    public function remove(int $id){
        
        $panier=$this->session->get('panier', []);
        if (!empty($panier[$id])):
            $panier[$id]--;
        else:
            unset($panier[$id]);
        endif;

        $this->session->set('panier', $panier);
    }

    public function delete(int $id){
        
        $panier=$this->session->get('panier', []);
        if (!empty($panier[$id])):
            unset($panier[$id]);
        endif;

        $this->session->set('panier', $panier);
    }

    public function getFullPanier() :array
    {
        //On fait une boucle permettant de synthétiser les ajouts effectués par article avec la quantité correspondante

        $panier=$this->session->get('panier', []);

        $panierDetail=[];

        foreach($panier as $id=>$quantite){
            if($quantite<1):$quantite=0; endif;
            $panierDetail[]=[
                'article'=>$this->articleRepository->find($id),
                'quantite'=>$quantite
            ];
        }

        return $panierDetail;
    }

    public function getTotal(): int
    {
        $total=0;
        foreach($this->getFullPanier() as $item){
            $total += $item['article']->getPrix() * $item['quantite'];
        }

        return $total;
    }

    public function getTotalRemise($remise): int
    {
        $total=0;
        foreach($this->getFullPanier() as $item){
            $total += $item['article']->getPrix() * $item['quantite'];
        }

        return $total-$remise;
    }

    
}