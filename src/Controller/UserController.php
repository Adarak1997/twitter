<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Form\TweetType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Service\UploaderHelper;

use Symfony\Component\HttpFoundation\File\UploadedFile;


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
    public function user_dashboard(Request $request, UsersRepository $repository, UserInterface $user, UploaderHelper $uploaderHelper)
    {


            $tweet = new Tweet();



            $userid = $user->getId();
            dump($userid);

        $allTweet = $this->getDoctrine()->getRepository(Tweet::class)->findBy(['users' => $userid ]);
        dump($allTweet);


        //2-Créer le formulaire associé à cette entité
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);

        //3-Prévoir ce qui doit se passer après la validation du formulaire (ajout dans la bdd)
        if($form->isSubmitted() and $form->isValid()){
            //Récupérer l'entityManager (doctrine)
            $em = $this->getDoctrine()->getManager();


            $iduser = $repository->findOneBy(['id' =>$userid]);
            $tweet->setUsers($iduser);

            $uploadedFile = $form->get('image')->getData();
            dump($uploadedFile);

            if($uploadedFile) {
                $newFileName = $uploaderHelper->uploadFile($uploadedFile);
                $tweet->setImage($newFileName);
            }

            $em->persist($tweet);
            $em->flush();


            return $this->redirectToRoute("user_dashboard");
        }



        //4-Afficher le formulaire via le twig (view)
        return $this->render('user/dashboard.html.twig', [
            'form' => $form->createView(),
            'tweets' => $allTweet
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
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function delete(Users $compte = null)
    {
        if($compte !== null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($compte);
            $em->flush();
        }
        return $this->redirectToRoute("inscription");
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
            $form->remove('username');
            $form->remove('email');
            $form->add('username', TextType::class, array(
                'disabled' => true
            ));
            $form->add('email', TextType::class, array(
                'disabled' => true
            ));
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

    /**
     * @Route("/user/search", name="user_search")
     */
    public function search()
    {
        return $this->render('user/search.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
