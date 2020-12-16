<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
     * @Route("/user/profil", name="user_profil")
     */
    public function user_profil()
    {

        $user = $this->getDoctrine()->getRepository(Users::class)->findAll();

        return $this->render('user/profil.html.twig', [
            'users' => $user,
        ]);
    }

    /**
     * @Route("/", name="inscription")
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
            //$user->setPassword(
             //   $passwordEncoder->encodePassword(
              //      $user,
              //      $form->get('password')->getData()
              //  )
            //);

            

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("user_profil");
        }

        //4-Afficher le formulaire via le twig (view)
        return $this->render('user/signin.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
