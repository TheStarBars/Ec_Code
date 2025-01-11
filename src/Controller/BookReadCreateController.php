<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookRead;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookReadCreateController extends AbstractController
{
    /**
     * Crée une nouvelle entrée de lecture dans la base de données.
     *
     * Cette méthode gère la création d'un enregistrement de lecture pour un utilisateur et un livre spécifiques.
     * Elle valide les données reçues, vérifie l'existence du livre et de l'utilisateur,
     * puis enregistre l'enregistrement dans la base de données.
     *
     * @param Request $request Objet HTTP contenant les données du formulaire.
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités pour les opérations en base de données.
     *
     * @return JsonResponse Une réponse JSON indiquant le succès ou l'échec de l'opération
     */
    #[Route('/bookread/create', name: 'bookread_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $check = $request->request->get('check');
        $userId = $request->request->get('user_id');

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

        $bookRead = new BookRead();
        $bookRead->setBook($book);
        $bookRead->setDescription($description);
        $bookRead->setRating((float)$rating);
        $bookRead->setRead(!empty($check) && $check === '1');
        $bookRead->setUser($id_user);
        $bookRead->setCreatedAt(new \DateTime('now'));
        $bookRead->setUpdatedAt(new \DateTime('now'));

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
