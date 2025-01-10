<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRead;
use App\Repository\BookReadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookReadUpdateController extends AbstractController
{
    #[Route('/bookread/{id}/edit', name: 'book_read_edit', methods: ['GET'])]
    public function edit(int $id, BookReadRepository $repository): JsonResponse
    {
        $bookRead = $repository->find($id);

        if (!$bookRead) {
            return new JsonResponse(['success' => false, 'message' => 'Livre introuvable.'], 404);
        }

        return new JsonResponse([
            'success' => true,
            'book' => [
                'name' => $bookRead->getBook()->getName(),
                'description' => $bookRead->getBook()->getDescription(),
            ],
        ]);
    }

    #[Route('/bookread/update', name: 'bookread_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $check = $request->request->get('check');
        $bookId = $request->request->get('book');

        // Récupérer l'entité BookRead
        $bookRead = $entityManager->getRepository(BookRead::class)->findOneBy(['book_id' => $bookId]);
        if (!$bookRead) {
            return new JsonResponse(['success' => false, 'message' => 'Lecture introuvable.'], 404);
        }

        // Récupérer l'entité Book
        $book = $entityManager->getRepository(Book::class)->find($bookId);
        if (!$book) {
            return new JsonResponse(['success' => false, 'message' => 'Livre introuvable.'], 404);
        }

        // Mettre à jour les données
        $bookRead->setBook($book);
        $bookRead->setDescription($description);
        $bookRead->setRating((float)$rating);
        $bookRead->setRead($check === '1'); // Compare directement à '1' pour un booléen
        $bookRead->setUpdatedAt(new \DateTime('now'));

        // Sauvegarder
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Lecture mise à jour avec succès.',
            'data' => [
                'id' => $bookRead->getId(),
                'book' => $book->getName(),
                'rating' => $bookRead->getRating(),
                'description' => $bookRead->getDescription(),
                'isFinished' => $bookRead->isRead(),
            ],
        ]);
    }
}
