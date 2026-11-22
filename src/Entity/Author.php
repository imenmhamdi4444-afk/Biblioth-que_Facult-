<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_author_name', columns: ['name', 'surname'])]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $workAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $interest1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $interest2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $interest3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $origin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;
        return $this;
    }

    public function getWorkAddress(): ?string
    {
        return $this->workAddress;
    }

    public function setWorkAddress(?string $workAddress): static
    {
        $this->workAddress = $workAddress;
        return $this;
    }

    public function getInterest1(): ?string
    {
        return $this->interest1;
    }

    public function setInterest1(?string $interest1): static
    {
        $this->interest1 = $interest1;
        return $this;
    }

    public function getInterest2(): ?string
    {
        return $this->interest2;
    }

    public function setInterest2(?string $interest2): static
    {
        $this->interest2 = $interest2;
        return $this;
    }

    public function getInterest3(): ?string
    {
        return $this->interest3;
    }

    public function setInterest3(?string $interest3): static
    {
        $this->interest3 = $interest3;
        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->surname;
    }
}
