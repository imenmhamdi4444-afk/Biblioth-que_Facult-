<?php

namespace App\Repository;

use App\Entity\Borrowing;
use App\Entity\Book;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Borrowing>
 *
 * @method Borrowing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Borrowing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Borrowing[]    findAll()
 * @method Borrowing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BorrowingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Borrowing::class);
    }

    /**
     * Returns the most borrowed books with their borrow count.
     */
    public function findMostPopularBooks(): array
    {
        $sql = "SELECT b2.title, COUNT(*) AS howMany 
                FROM borrowing b1 
                INNER JOIN book b2 ON b2.id = b1.book_id
                GROUP BY b2.title
                ORDER BY howMany DESC";

        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        return $statement->executeQuery()->fetchAllAssociative();
    }

    /**
     * Returns all active (not yet returned) borrowings.
     *
     * @return Borrowing[]
     */
    public function findActiveBorrowings(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.bookReturned = false')
            ->orderBy('b.dateBorrowed', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all overdue borrowings (past due date, not returned).
     *
     * @return Borrowing[]
     */
    public function findOverdueBorrowings(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.bookReturned = false')
            ->andWhere('b.dateDue < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('b.dateDue', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all borrowings for a given student.
     *
     * @return Borrowing[]
     */
    public function findByStudent(Student $student): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.student = :student')
            ->setParameter('student', $student)
            ->orderBy('b.dateBorrowed', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all borrowings for a given book.
     *
     * @return Borrowing[]
     */
    public function findByBook(Book $book): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.book = :book')
            ->setParameter('book', $book)
            ->orderBy('b.dateBorrowed', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
