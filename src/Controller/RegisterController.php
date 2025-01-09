<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'auth.register')]
    public function index(): Response
    {
        return $this->render('auth/register.html.twig', [
            'name' => 'Thibaud', // Pass data to the view
        ]);
    }
}
