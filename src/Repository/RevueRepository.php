<?php

namespace App\Repository;

use App\Entity\Revue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Revue>
 */
class RevueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Revue::class);
    }

    /**
     * Search revues by keyword (title or keywords field).
     */
    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.title LIKE :kw OR r.keywords LIKE :kw OR r.code LIKE :kw')
            ->setParameter('kw', '%' . $keyword . '%')
            ->orderBy('r.title', 'ASC')
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
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.authors', 'a')
            ->leftJoin('r.publisher', 'p');

        if ($keyword !== '') {
            $qb->andWhere('r.title LIKE :kw OR r.keywords LIKE :kw OR r.code LIKE :kw')
               ->setParameter('kw', '%' . $keyword . '%');
        }

        if ($authorId) {
            $qb->andWhere(':author MEMBER OF r.authors')
               ->setParameter('author', $authorId);
        }

        if ($publisherId) {
            $qb->andWhere('p.id = :pub')
               ->setParameter('pub', $publisherId);
        }

        if ($availableOnly) {
            $qb->andWhere('r.nbCopiesAcquired > r.nbCopiesBorrowed');
        }

        return $qb->orderBy('r.title', 'ASC')->getQuery()->getResult();
    }
}
