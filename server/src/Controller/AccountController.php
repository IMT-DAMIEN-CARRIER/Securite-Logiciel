<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route(
     *     "/login",
     *     name="account_login"
     * )
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $username = $authenticationUtils->getLastUsername();

        return self::render(
            'account/login.html.twig',
            [
                'hasError' => null !== $error,
                'username' => $username,
            ]
        );
    }

    /**
     * Permet de se déconnecter.
     *
     * @Route(
     *     "/logout",
     *     name="account_logout"
     * )
     */
    public function logout()
    {
    }

    /**
     * Permet d'afficher le formulaire d'inscription.
     *
     * @Route(
     *     "/register",
     *     name="account_register"
     * )
     *
     * @param Request                      $request
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setHash($encoder->encodePassword($user, $user->getHash()));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.'
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render(
            'account/registration.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
