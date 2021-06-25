<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Service\Panier\PanierService;
use App\Repository\CommandeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
    #[Route('/', name: 'home')]
    
    public function home(ArticleRepository $articleRepository, PanierService $panierService, CategorieRepository $categorieRepository, PaginatorInterface $paginator, Request $request): Response //ici on injecte la dépendance de ArticleRepository afin de pouvoir utiliser les méthodes de la classe ArticleRepository
    {
        $prenom="Maha";
        $panier=$panierService->getFullPanier();
        $categories=$categorieRepository->findAll();

        $articles=$articleRepository->findAll(); //On utilise la méthode findAll de ArticleRepository afin de faire une requête de select * de nos articles que nous allons transmettre à notre vue twig

        $articles=$paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('front/home.html.twig', [
            'prenom'=>$prenom,
            'articles'=>$articles,
            'panier'=>$panier,
            'categories'=>$categories
        ]);
    }

    /**
     * @Route ("/homefilter", name="homefilter")
     */
    public function homefilter(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, PanierService $panierService, Request $request, PaginatorInterface $paginator){

        $categories = $categorieRepository->findAll();
        $panier = $panierService->getFullPanier();
        $prixmax=$request->request->get('prixmax');
        $cat = $request->request->get('cate');

        if($cat=='all' && $prixmax==50):
            $articles = $articleRepository->findAll();
        elseif($cat!=='all' && $prixmax==50):
            $articles=$articleRepository->findBy(['categorie'=>$cat], ['prix'=>'ASC']);
        elseif($cat=='all' && $prixmax!==50):
            $articles=$articleRepository->findByPrice($prixmax);
        elseif($cat!='all' && $prixmax!==50):
            $articles=$articleRepository->findByCategoryAndPrice($cat, $prixmax);
        endif;

        $articles=$paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render("front/home.html.twig", [
            'articles'=>$articles,
            'panier'=>$panier,
            'categories'=>$categories
        ]);
    }

    /**
     * @Route ("/commandes_user", name="commandes_user")
     */
    public function commandes_user(CommandeRepository $repository, PanierService $panierService){

        $panier=$panierService->getFullPanier();
        $commandes=$repository->findBy(["user"=>$this->getUser()], ['id'=>'DESC']);
        return $this->render("front/commandes_user.html.twig", [
            'commandes'=>$commandes,
            'panier'=>$panier
        ]);
    }

    /**
     * @Route ("/mail_form", name="mail_form")
     */
    public function mail_form(PanierService $panierService){
        $panier=$panierService->getFullPanier();
        return $this->render("front/mail_form.html.twig", [
            'panier'=>$panier
        ]);
    }





}

