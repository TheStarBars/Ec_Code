<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRead;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookReadUpdateController extends AbstractController
{
    /**
     * Met à jour une entrée de lecture existante dans la base de donnée.
     *
     * Cette méthode permet de modifier les informations liées à une lecture (livre associé, description,
     * évaluation, statut de lecture) en se basant sur les données fournies dans la requête.
     *
     * @param Request $request Objet HTTP contenant les donnée du formulaire.
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités pour effectuer les opérations en base de données.
     *
     * @return JsonResponse Une réponse JSON indiquant le succès ou l'échec de l'opération
     */
    #[Route('/bookread/update', name: 'bookread_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $readBookId = $request->request->get('readbook');
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $checked = $request->request->get('check') === 'on';

        $readBook = $entityManager->getRepository(BookRead::class)->find($readBookId);
        if (!$readBook) {
            return new JsonResponse(['success' => false, 'message' => 'ReadBook non trouvé.']);
        }

        $book = $entityManager->getRepository(Book::class)->find($bookId);
        if (!$book) {
            return new JsonResponse(['success' => false, 'message' => 'Book non trouvé.']);
        }

        $readBook->setBook($book);
        $readBook->setDescription($description);
        $readBook->setRating((float) $rating);
        $readBook->setRead($checked);
        $readBook->setUpdatedAt(new \DateTime('now'));

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
