<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'auth.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if($form->isValid()) {
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
                return $this->redirectToRoute('app.home');
            }
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
