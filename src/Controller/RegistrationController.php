<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private Security $security;

    // Injection de dépendances via le constructeur
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Cette méthode permet de gérer l'inscription d'un nouvel utilisateur.
     * Elle gère la création du formulaire d'inscription, la validation des données
     * et l'enregistrement de l'utilisateur dans la base de données.
     *
     * - Le mot de passe est hashé avant d'être enregistré.
     * - Un contrôle est effectué pour vérifier si l'email existe déjà.
     * - L'utilisateur doit accepter les conditions d'utilisation.
     * - Après validation, l'utilisateur est connecté automatiquement.
     *
     * @Route("/register", name="auth.register")
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/register', name: 'auth.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $plainPassword = $form->get('password')->getData();
                $email = $form->get('email')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $this->addFlash('error', "Cet email est déjà utilisé.");
                    return $this->redirectToRoute('auth.register');
                }

                $agreeTerms = $form->get('agreeTerms')->getData();
                if (!$agreeTerms) {
                    $this->addFlash('error', "Vous devez accepter les GCU.");
                    return $this->redirectToRoute('auth.register');
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre inscription a été réussie.');
                $this->security->login($user);

                return $this->redirectToRoute('app.home');
            }
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
