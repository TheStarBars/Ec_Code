<?php

namespace App\Repository;

use App\Entity\BookRead;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookRead>
 */
class BookReadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, CategoryRepository $categoryRepository)
    {
        parent::__construct($registry, BookRead::class);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Method to find all ReadBook entities by user_id
     * @param int $userId
     * @param bool $readState
     * @return array
     */
    public function findByUserId(int $userId, bool $readState): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user_id = :userId')
            ->andWhere('r.is_read = :isRead')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', $readState)
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }



    /**
     * Trouve les évaluations moyennes des livres par catégorie.
     *
     * @return array Liste des catégories et leurs évaluations moyennes.
     */
    public function findAverageRatingsByCategory(int $userId): array
    {
        $categories = $this->categoryRepository->findAll();

        // Initialiser un tableau avec des valeurs par défaut (0) pour chaque catégorie
        $ratingsByCategory = [];
        foreach ($categories as $category) {
            $ratingsByCategory[$category->getId()] = [
                'category' => $category->getName(),
                'rating' => 0, // Valeur initiale à 0
            ];
        }

        $qb = $this->createQueryBuilder('br')
            ->select('b.category_id', 'AVG(br.rating) as avg_rating')
            ->join('App\Entity\Book', 'b', 'WITH', 'b.id = br.book_id') // jointure entre book_read et book
            ->where('br.is_read = 1')
            ->andWhere('br.user_id = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('b.category_id');


        $results = $qb->getQuery()->getResult();

        // Mettre à jour les catégories existantes avec les notes moyennes
        foreach ($results as $result) {
            $categoryId = (int) $result['category_id'];
            if (isset($ratingsByCategory[$categoryId])) {
                $ratingsByCategory[$categoryId]['rating'] = (float) $result['avg_rating'];
            }
        }

        // Transformer le tableau associatif en un tableau d'arrays [category, rating]
        $finalResult = [];
        foreach ($ratingsByCategory as $categoryData) {
            $finalResult[] = [$categoryData['category'], $categoryData['rating']];
        }

        return $finalResult;
    }
}
