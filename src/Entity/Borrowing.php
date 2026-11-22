<?php

namespace App\Entity;

use App\Repository\BorrowingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Borrowing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Book $book = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Revue $revue = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateBorrowed = null;

    #[ORM\Column]
    private ?bool $bookReturned = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateDue = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateReturned = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;
        return $this;
    }

    public function getRevue(): ?Revue
    {
        return $this->revue;
    }

    public function setRevue(?Revue $revue): static
    {
        $this->revue = $revue;
        return $this;
    }

    public function getDateBorrowed(): ?\DateTimeInterface
    {
        return $this->dateBorrowed;
    }

    public function setDateBorrowed(?\DateTimeInterface $dateBorrowed): static
    {
        $this->dateBorrowed = $dateBorrowed;
        return $this;
    }

    public function isBookReturned(): ?bool
    {
        return $this->bookReturned;
    }

    public function setBookReturned(bool $bookReturned): static
    {
        $this->bookReturned = $bookReturned;
        return $this;
    }

    public function getDateDue(): ?\DateTimeInterface
    {
        return $this->dateDue;
    }

    public function setDateDue(?\DateTimeInterface $dateDue): static
    {
        $this->dateDue = $dateDue;
        return $this;
    }

    public function getDateReturned(): ?\DateTimeInterface
    {
        return $this->dateReturned;
    }

    public function setDateReturned(?\DateTimeInterface $dateReturned): static
    {
        $this->dateReturned = $dateReturned;
        return $this;
    }

    /**
     * Returns true if the borrowing is overdue (not returned and past due date).
     */
    public function isOverdue(): bool
    {
        if ($this->bookReturned) {
            return false;
        }
        if (!$this->dateDue) {
            return false;
        }
        return new \DateTime() > $this->dateDue;
    }

    #[ORM\PrePersist]
    public function setInitialDates(): void
    {
        if (!$this->dateBorrowed) {
            $this->dateBorrowed = new \DateTime();
        }
        if (!$this->dateDue) {
            $this->dateDue = (clone $this->dateBorrowed)->modify('+14 days');
        }
    }
}
