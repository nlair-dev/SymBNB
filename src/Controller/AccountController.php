<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils) : Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
        ]);
    }

    /**
     * Permet de se deconnecter 
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout() : void
    {
        
    }


    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register")
     * @param Request $request
     * @param ObjectManager $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder) : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée! Vous pouvez maintenant vous connecter'
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * @Route("/account/profile", name="account_profile")
     * @param Request $request
     * @param ObjectManager $em
     * @return Response
     */
    public function profil(Request $request, ObjectManager $em) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Votre compte a bien été modifié');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/account/password-update", name="account_password")
     * @param Request $request
     * @param ObjectManager $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder) : Response
    {
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $encoder->encodePassword($user, $passwordUpdate->getNewPassword());
                $user->setHash($newPassword);
                $em->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                
                return $this->redirectToRoute('homepage');
            }       
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
