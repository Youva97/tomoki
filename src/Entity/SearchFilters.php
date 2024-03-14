<?php

namespace App\Entity;

use App\Repository\SearchFiltersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SearchFiltersRepository::class)]
class SearchFilters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::OBJECT)]
    private ?object $categories = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategories(): ?object
    {
        return $this->categories;
    }

    public function setCategories(object $categories): static
    {
        $this->categories = $categories;

        return $this;
    }
}
