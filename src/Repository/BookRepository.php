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

    /**
     * Search books by keyword (title or keywords field).
     */
    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.title LIKE :kw OR b.keywords LIKE :kw OR b.code LIKE :kw')
            ->setParameter('kw', '%' . $keyword . '%')
            ->orderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Advanced search with filters.
     */
    public function searchWithFilters(
        string $keyword = '',
        ?int   $authorId = null,
        ?int   $publisherId = null,
        ?bool  $availableOnly = false
    ): array {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.publisher', 'p');

        if ($keyword !== '') {
            $qb->andWhere('b.title LIKE :kw OR b.keywords LIKE :kw OR b.code LIKE :kw')
               ->setParameter('kw', '%' . $keyword . '%');
        }

        if ($authorId) {
            $qb->andWhere(':author MEMBER OF b.authors')
               ->setParameter('author', $authorId);
        }

        if ($publisherId) {
            $qb->andWhere('p.id = :pub')
               ->setParameter('pub', $publisherId);
        }

        if ($availableOnly) {
            $qb->andWhere('b.nbCopiesAcquired > b.nbCopiesBorrowed');
        }

        return $qb->orderBy('b.title', 'ASC')->getQuery()->getResult();
    }
}
