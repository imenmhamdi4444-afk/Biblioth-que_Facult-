<?php

namespace App\Entity;

use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
class Publisher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $directorName = null;

    #[ORM\Column(length: 255)]
    private ?string $commercialContact = null;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: Book::class)]
    private Collection $books;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: Revue::class)]
    private Collection $revues;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->revues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getDirectorName(): ?string
    {
        return $this->directorName;
    }

    public function setDirectorName(string $directorName): static
    {
        $this->directorName = $directorName;
        return $this;
    }

    public function getCommercialContact(): ?string
    {
        return $this->commercialContact;
    }

    public function setCommercialContact(string $commercialContact): static
    {
        $this->commercialContact = $commercialContact;
        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @return Collection<int, Revue>
     */
    public function getRevues(): Collection
    {
        return $this->revues;
    }

    public function __toString(): string
    {
        return $this->raisonSociale ?? '';
    }
}
