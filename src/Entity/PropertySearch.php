<?php

namespace App\Entity;

use App\Entity\Category;

class PropertySearch
{
    private ?string $nom = null;
    private ?Category $category = null; // Ajoutez cette propriété

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    // Ajoutez ces getter et setter
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}