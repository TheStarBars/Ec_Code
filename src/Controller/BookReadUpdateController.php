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
    #[Route('/bookread/update', name: 'bookread_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $readBookId = $request->request->get('readbook');
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $checked = $request->request->get('check') === 'on';

        // Vérifie l'existence de ReadBook
        $readBook = $entityManager->getRepository(BookRead::class)->find($readBookId);
        if (!$readBook) {
            return new JsonResponse(['success' => false, 'message' => 'ReadBook non trouvé.']);
        }

        // Vérifie l'existence de Book
        $book = $entityManager->getRepository(Book::class)->find($bookId);
        if (!$book) {
            return new JsonResponse(['success' => false, 'message' => 'Book non trouvé.']);
        }

        // Met à jour les champs
        $readBook->setBook($book);
        $readBook->setDescription($description);
        $readBook->setRating((float) $rating);
        $readBook->setRead($checked);

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}

