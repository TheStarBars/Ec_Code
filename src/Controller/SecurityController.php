<?php

namespace App\Controller;

use AllowDynamicProperties;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AllowDynamicProperties] class SecurityController extends AbstractController
{
    public function __construct(private Security $security)
    {}


    /**
     * Cette méthode gère l'affichage de la page de connexion.
     * Elle récupère les erreurs d'authentification (si présentes) et le dernier nom d'utilisateur utilisé.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'auth.login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    /**
     * Cette méthode gère la déconnexion de l'utilisateur et le redirige vers la page de connexion.
     *
     * @return RedirectResponse
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): RedirectResponse
    {
        $this->security->logout();

        return $this->redirectToRoute('auth.login');
    }
}
