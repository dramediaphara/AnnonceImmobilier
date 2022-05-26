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


    public function __construct($name = '', $description = '', $price = '')
    {
        if(!$name){
            $this->name = "Name" .uniqid();
        }else{
            
            $this->name = $name;
        }
        // contenu
        if(!$description){
            $this->content = "Description";
        }else{
            $this->description = $description;
        }
        // Price
        if(!$price){
            $this->price = "price";
        }else{
            $this->price = $price;
        }
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
}
