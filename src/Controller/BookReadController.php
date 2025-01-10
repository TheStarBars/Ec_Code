<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRead;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function Symfony\Component\Clock\now;

class BookReadController extends AbstractController
{
    #[Route('/bookread/create', name: 'bookread_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $check = $request->request->get('check');
        $userId = $request->request->get('user_id'); // Récupérer l'ID de l'utilisateur

        // Validation des données
        if (empty($bookId) || empty($description) || empty($rating)) {
            return new JsonResponse(['success' => false, 'message' => 'Données manquantes.'], 400);
        }

        $book = $entityManager->getRepository(Book::class)->find($bookId);
        if (!$book) {
            return new JsonResponse(['success' => false, 'message' => 'Livre introuvable.'], 404);
        }

        $id_user = $entityManager->getRepository(User::class)->find($userId);
        if (!$id_user) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non trouvé.'], 404);
        }

        // Créer l'instance de BookRead
        $bookRead = new BookRead();
        $bookRead->setBook($book);
        $bookRead->setDescription($description);
        $bookRead->setRating((float)$rating);
        $bookRead->setRead(!empty($check) && $check === '1');
        $bookRead->setUser($id_user); // Enregistrer l'utilisateur
        $bookRead->setCreatedAt(new \DateTime('now'));
        $bookRead->setUpdatedAt(new \DateTime('now'));


        // Sauvegarder
        $entityManager->persist($bookRead);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Lecture enregistrée avec succès.',
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
