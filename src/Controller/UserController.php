<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Tweet;
use App\Form\TweetType;
use App\Repository\TweetRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/user", name="user_like")
     */
    public function user_like(Request $request, TweetRepository $repository,UserInterface $user, UsersRepository $repositoryuser)
    {



        $idtweet = $request->request->get('idtweet');
        $like = $request->request->get('like');
        $tweet = $repository->findOneBy(['id' =>$idtweet]);



        $relationlike = new Like();

        $userid = $user->getId();

        $user = $repositoryuser->findOneBy(['id' =>$userid]);

        if($request->isXmlHttpRequest()){

            $em = $this->getDoctrine()->getManager();

            $test = $tweet->getNombreLike();


            $test += $like;
            $tweet->setNombreLike($test);


            $relationlike->setTweet($tweet);
            $relationlike->setUser($user);
            $em->persist($tweet);
            $em->persist($relationlike);
            $em->flush();
            return new JsonResponse($tweet);
        }

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

        $html = "";


        foreach ($allTweet as $item) {
            $html .= "

            <div class='tweet'>
                <div class='icon''>
                    <img src='/img/icon/twitter_white.png' class='img-fluid'>
                </div>
                <div class='content'>
                    <div class='author'>
                        <h3 class='pseudo'>".$item->getText()." <strong>@Nom_du_compte</strong></h3>
                        <p class='mx-1'>·</p>
                        <p>14/03/2021</p>
                    </div>
                    <div class='text w-100'>
                        <p class='text-justify'>".$item->getText()."</p>
                    </div>";

            if($item->getImage() != null) {

                $html .="<div class='img'>
                        <img src='/image_tweet/".$item->getImage()."'>
                    </div>";

            }

            $html .= "
                    <div class='interacting'>
                        <div class='item'>
                            <p><i class='fa fa-comment'></i>14</p>
                        </div>
                        <div class='item'>
                            <p><a href='dashboard.html.twig'><i class='fa fa-heart'></i></a>".$item->getNombreLike()."</p>
                        </div>
                    </div>
                </div>
            </div>

            ";
        }


        dump($allTweet);


        //2-Créer le formulaire associé à cette entité
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);


        //3-Prévoir ce qui doit se passer après la validation du formulaire (ajout dans la bdd)
        if($request->isXmlHttpRequest()){
            //Récupérer l'entityManager (doctrine)
            $em = $this->getDoctrine()->getManager();


            $iduser = $repository->findOneBy(['id' =>$userid]);
            $tweet->setUsers($iduser);
            $tweet->setNombreLike(0);

            $uploadedFile = $form->get('image')->getData();
            dump($uploadedFile);

            if($uploadedFile) {
                $newFileName = $uploaderHelper->uploadFile($uploadedFile);
                $tweet->setImage($newFileName);
            }

            $em->persist($tweet);
            $em->flush();

            $html .=  "

            <div class='tweet'>
                <div class='icon''>
                    <img src='/img/icon/twitter_white.png' class='img-fluid'>
                </div>
                <div class='content'>
                    <div class='author'>
                        <h3 class='pseudo'>".$tweet->getText()." <strong>@Nom_du_compte</strong></h3>
                        <p class='mx-1'>·</p>
                        <p>14/03/2021</p>
                    </div>
                    <div class='text w-100'>
                        <p class='text-justify'>".$tweet->getText()."</p>
                    </div>";

            if($tweet->getImage() != null) {
                $html .= "<div class='img'>
                        <img src='/image_tweet/".$tweet->getImage()."'>
                    </div>";

            }

            $html .= "
                    <div class='interacting'>
                        <div class='item'>
                            <p><i class='fa fa-comment'></i>14</p>
                        </div>
                        <div class='item'>
                            <p><a href='dashboard.html.twig'><i class='fa fa-heart'></i></a>".$tweet->getNombreLike()."</p>
                        </div>
                    </div>
                </div>
            </div>

            ";


            $this->addFlash('success', 'succès');
            return new JsonResponse($html);


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
     * @Route("/inscription", name="test")
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
