<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i=1;$i<11;$i++){
            $article=new Article(); //ici on instancie un nouvel objet hérité de la classe App\entity\Article à chaque tour de boucle, pour autant d'articles instanciés, il y aura un insert supplémentaire en BDD
            $article->setNom("Article n°".$i);
            $article->setPrix(rand(100,400));
            $article->setDateCrea(new \DateTime());
            $article->setRef("ref".$i);
            $article->setPhoto("https://picsum.photos/600/".$i);
            
            $manager->persist($article); //persist est une méthode issue de la classe ObjectManager qui permet de garder en mémoire les objets articles créés précédemment et de préparer et binder les requêtes (les valeurs à insérer) avant insertion
        }

        $manager->flush(); //flush est une méthode de ObectManager qui permet d'exécuter les requêtes préparées lors du persist et permet ainsi les inserts en BDD

        //une fois réalisé, il faut charger les fixtures en BDD grâce à DOCTRINE avec la commande suivante : php bin/console doctrine:fixtures:load
    }
}
