<?php

namespace App\Controller;


use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private BookReadRepository $readBookRepository;

    // Inject the repository via the constructor
    public function __construct(BookReadRepository $bookReadRepository, private readonly Security $security, BookRepository $bookRepository, CategoryRepository $categoryRepository, entityManagerInterface $em)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('auth.login');
        }
        ;
        $booksNotRead  = $this->bookReadRepository->findByUserId($this->security->getUser()->getId(), false);
        $booksRead  = $this->bookReadRepository->findByUserId($this->security->getUser()->getId(), true);
        $book = $this->bookRepository->findAll();


        $data = $this->bookReadRepository->findAverageRatingsByCategory();
        // Render the 'hello.html.twig' template
        return $this->render('pages/home.html.twig', [
            'booksNotRead' => $booksNotRead,
            'name'      => 'Accueil', // Pass data to the view
            'user'     => $user,
            'books'     => $book,
            'booksRead' => $booksRead,
            'data' => json_encode($data),
        ]);
    }
}
