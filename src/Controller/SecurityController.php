<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\PromoRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription",name="registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, PanierService $panierService, PromoRepository $promoRepository){

        //UserPasswordEncoderInterface pour pouvoir fonctionner attend de l'objet User que celui-ci hérite de la classe UserInterface, cette dernière classe attend des méthodes bien précises afin de s'assurer du bon fonctionnement de l'authentification (cf User entity)
        $panier=$panierService->getFullPanier();

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hash=$encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $user->setPromo(0);

            $user->setDateInscription(new \DateTime());

            $date= strtotime("now + 10 day");
            $datef=date('d-m-Y', $date);
            $promo=$promoRepository->findByRegistrationName('REGISTRATION');
            $promotion=$promo->getNom();
            $remise=$promo->getRemise();
            $montant=$promo->getMontantmin();


            $nom=$request->request->get('registration')['nom'];
            $prenom=$request->request->get('registration')['prenom'];
            $to=$request->request->get('registration')['email'];

            $transporter=(new \Swift_SmtpTransport("smtp.gmail.com", 465, "ssl"))
            ->setUsername('dorancocovid2021@gmail.com')
            ->setPassword('Dorancosalle06');

            $mailer=new \Swift_Mailer($transporter);
                
            $nom=$user->getNom();
            $prenom=$user->getPrenom();
            $mess="Bienvenue $prenom !! Jusqu'au $datef, pour tout achat d'un montant minimum de $montant €, vous bénéficiez d'une remise de $remise € avec le code promo suivant : $promotion . Venez vite en profiter !";

            $motif="Nouvelle promotion";
            $from="dorancocovid2021@gmail.com";

            $message=(new \Swift_Message("$motif"))
            ->setFrom($from)
            ->setTo($to);

            $cid = $message->embed(\Swift_Image::fromPath('photos/20210623134212-60d31e1443474-montre5.jpg'));
            $message->setBody(
                $this->renderView("front/mail_template.html.twig", [
                    'message'=>$mess,
                    'nom'=>$nom,
                    'prenom'=>$prenom,
                    'motif'=>$motif,
                    'email'=>$from,
                    'cid'=>$cid
                ]),
                'text/html'
            );
            $mailer->send($message);


            $manager->persist($user);

            $manager->flush();
            

            $this->addFlash('success', "Félicitations, votre compte a bien été créé. Vous pouvez à présent vous connecter !");

            return $this->redirectToRoute('login', [
                'panier'=>$panier
            ]);

        }


        return $this->render('security/inscription.html.twig', [
            'formu'=>$form->createView(),
            'panier'=>$panier
        ]);

    }

    /**
     * @Route("/login",name="login")
     */
    public function login(PanierService $panierService){

        $panier=$panierService->getFullPanier();
        return $this->render('security/connexion.html.twig', [
            'panier'=>$panier
        ]);
    }

    /**
     * @Route("/logout",name="logout")
     */
    public function logout(){

    }








}
