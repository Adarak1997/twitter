<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Form\TweetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_index")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/dashboard", name="user_dashboard")
     */
    public function user_dashboard(Request $request, UserInterface $user)
    {
        $tweet = new Tweet();

        $userId = $user->getId();

        dump($userId);


        //2-Créer le formulaire associé à cette entité
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);

        //3-Prévoir ce qui doit se passer après la validation du formulaire (ajout dans la bdd)
        if($form->isSubmitted() and $form->isValid()){
            //Récupérer l'entityManager (doctrine)
            $em = $this->getDoctrine()->getManager();

            $tweet->setUsers($userId);


            $em->persist($tweet);
            $em->flush();

            return $this->redirectToRoute("user_dashboard");
        }

        //4-Afficher le formulaire via le twig (view)
        return $this->render('user/dashboard.html.twig', [
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/user/profil", name="user_profil")
     */
    public function user_profil()
    {

        return $this->render('user/profil.html.twig', [

        ]);
    }

    /**
     * @Route("/user/{id}/update", name="user_update")
     */
    public function update(Request $request, Users $compte = null)
    {
        if($compte !== null){
            //2- Créer un formulaire associé à cette entité
            $form = $this->createForm(LoginType::class, $compte);
            $form->remove('password');
            $form->remove('submit');
            $form->add('modifier', SubmitType::class);
            $form->handleRequest($request);

            //3- Prévoir ce qui doit se passer après la validation du formulaire (ajout dans la BDD)
            if($form->isSubmitted() and $form->isValid()){
                //Récupérer l'entityManager (doctrine)
                $em = $this->getDoctrine()->getManager();

                $em->persist($compte);
                $em->flush();
                return $this->redirectToRoute("user_dashboard");
            }
            //4- Afficher le formulaire via le twig (vue)
            return $this->render('user/editer.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute("user_dashboard");
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function signin(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new Users();

        //2-Créer le formulaire associé à cette entité
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);

        //3-Prévoir ce qui doit se passer après la validation du formulaire (ajout dans la bdd)
        if($form->isSubmitted() and $form->isValid()){
            //Récupérer l'entityManager (doctrine)
            $em = $this->getDoctrine()->getManager();

                // encode the plain password
                $user->setPassword(
                  $passwordEncoder->encodePassword(
                     $user,
                     $form->get('password')->getData()
                 )
                );

            

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("app_login");
        }

        //4-Afficher le formulaire via le twig (view)
        return $this->render('user/signin.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
