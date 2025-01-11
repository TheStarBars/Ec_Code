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

    public function __construct(BookReadRepository $bookReadRepository, private readonly Security $security, BookRepository $bookRepository, CategoryRepository $categoryRepository, entityManagerInterface $em)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
    }

    /**
     * Affiche la page d'accueil avec les livres lus et non lus par l'utilisateur, ainsi que les évaluations moyennes par catégorie.
     *
     * Cette méthode vérifie si un utilisateur est connecté, récupère les livres lus et non lus,
     * et les catégories associées aux livres. Elle passe ces informations à la vue pour l'affichage.
     *
     * @throws Exception Si une erreur liée à la base de données se produit.
     *
     * @return Response La réponse contenant la vue rendue de la page d'accueil.
     */
    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->redirectToRoute('auth.login');
        }

        $booksNotRead  = $this->bookReadRepository->findByUserId($user->getId(), false);
        $booksRead  = $this->bookReadRepository->findByUserId($user->getId(), true);

        $book = $this->bookRepository->findAll();

        $data = $this->bookReadRepository->findAverageRatingsByCategory($user->getId());

        return $this->render('pages/home.html.twig', [
            'booksNotRead' => $booksNotRead,
            'name' => 'Accueil',
            'user' => $user,
            'books' => $book,
            'booksRead' => $booksRead,
            'data' => json_encode($data),
        ]);
    }
}
