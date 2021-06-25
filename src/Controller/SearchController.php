<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\Panier\PanierService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{


    /**
     * @Route("/searchrender", name="searchrender")
     */
    public function search(ArticleRepository $repository, Request $request, PaginatorInterface $paginator, PanierService $panierService)
    {

        $article = $repository->search($request->query->get('search'));
        $panier = $panierService->getFullPanier();

        // si la requête retourne un article, l'article  est renvoyé vers la page detail
        if (count($article) === 1){

            $articles = $paginator->paginate(
                $article, // on passe les données
                $request->query->getInt('page', 1),
            6//numero 1 page par defaut

            );

            return $this->render('find.html.twig',
                [
                    'articles' => $articles,
                    'panier'=>$panier
                ]
            );
        }
        // si la requête ne retourne pas de patient, l'utilisateur est renvoyé vers une page
        // lui indiquant que sa requête n'a pas retourné de résultats
//        elseif (count($article) === 0){
//            return $this->render('message.html.twig',
//                [
//
//                    'article' => $article,
//
//                ]
//            );
//        }
        // si plusieurs patients correspondent à la requête, l'utilisateur est renvoyé vers le tableau
        // présentant ceux-ci
        else {

            $articles = $paginator->paginate(
                $article, // on passe les données
                $request->query->getInt('page', 1),
                6//numero 1 page par defaut

            );

            return $this->render('find.html.twig',
                [
                    'articles' => $articles,
                    'panier' => $panier

                ]
            );

        }

    }

    /**
     * @Route("/handle_search")
     */
    public function autocomplete(Request $request, ArticleRepository $repository)
    {

        $term = $request->query->get('query');
        
        $array = $repository->autocomplete($term);
            // la méthode présente dans le repository patient est utilisée ici en paramètre

        
        // le résultat est ensuite encodé au format json pour l'appel en ajax
        return new JsonResponse($array);
    }






}

