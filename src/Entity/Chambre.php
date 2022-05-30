<?php

namespace App\Entity;

use App\Repository\ChambreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChambreRepository::class)]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Category', inversedBy: 'chambres')]
    #[ORM\JoinColumn(nullable: true)]
    private $category;
  
    public function getCategoryName(): string
    {
        //Cette méthode nous rend une chaine de caractère avec le nom de la Catégorie liée à notre objet chambre, ou "Aucune" si aucune catégorie n'est liée
        if($this->category){ //Si la chambre est bien lié à une Catégorie
            return $this->category->getName();
        } else return 'Aucun';
    }

    
    public function getThumbnail(): string
    {
        //Cette méthode rend l'adresse de la vignette associée à la Category de l'objet
        if($this->category && ($this->category->getName() != 'Autre')){ //Si notre Product est lié à une Category
            return 'assets/img/image_' . strtolower($this->category->getName()) . '.jpg';
        } else return 'assets/img/image_villa.jpg'; //On retourne "aucun" si la Catégorie est inexistante ou "Autre"
    }

    public function clearFields(): void
    {
        // 
        $this->name = null;
        $this->description = null;
        $this->price = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

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
