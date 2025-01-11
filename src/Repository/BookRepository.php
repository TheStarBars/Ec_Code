<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    /**
     * Recherche des livres par titre.
     *
     * Cette méthode permet de trouver des livres dont le titre correspond au critère de recherche.
     *
     * @param string $name Le titre (ou partie du titre) du livre à rechercher.
     * @return Book[] Un tableau d'objets Book correspondant au critère de recherche.
     */
    public function findByName(string $name): array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.title LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();

        return $qb->getResult();
    }
}
