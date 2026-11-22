<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Publisher $publisher = null;

    #[ORM\ManyToMany(targetEntity: Author::class)]
    private Collection $authors;

    #[ORM\Column]
    private ?int $nbCopiesAcquired = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $nbCopiesBorrowed = 0;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $keywords = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): static
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);
        return $this;
    }

    public function getNbCopiesAcquired(): ?int
    {
        return $this->nbCopiesAcquired;
    }

    public function setNbCopiesAcquired(int $nbCopiesAcquired): static
    {
        $this->nbCopiesAcquired = $nbCopiesAcquired;
        return $this;
    }

    public function getNbCopiesBorrowed(): int
    {
        return $this->nbCopiesBorrowed;
    }

    public function setNbCopiesBorrowed(int $nbCopiesBorrowed): static
    {
        $this->nbCopiesBorrowed = $nbCopiesBorrowed;
        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    #[ORM\PrePersist]
    public function generateCode(): void
    {
        if (empty($this->code)) {
            $this->code = 'LIV-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
        }
    }

    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
