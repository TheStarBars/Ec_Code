<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\String\s;

class HomeController extends AbstractController
{
    private BookReadRepository $readBookRepository;

    // Inject the repository via the constructor
    public function __construct(BookReadRepository $bookReadRepository, private readonly Security $security)
    {
        $this->bookReadRepository = $bookReadRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        $userId     = 1;
        $booksRead  = $this->bookReadRepository->findByUserId($userId, false);
        $email = $this->security->getUser()->getUserIdentifier();


        // Render the 'hello.html.twig' template
        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'name'      => 'Accueil', // Pass data to the view
            'email'     => $email,
        ]);
    }
}
