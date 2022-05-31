<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\Column(type: 'datetime')]
    private $creationDate;

    
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Chambre', inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: true)]
    private $chambre;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Order', inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: true)]
    private $order;

    public function __construct()
    {
        $this->creationDate = new \DateTime("now");
    }

    public function getTotalPrice(): float
    {
        //Cette méthode renvoie le prix total de la Reservation selon le prix actuel de la référence Product
        if($this->chambre){ //Si un Product est lié à notre Reservation
            return $this->quantity * $this->chambre->getPrice();
        } else return 0;
    }

    public function getProductName(): string
    {
        //Cette méthode nous permet de récupérer le nom de notre Product lié sans risque d'erreur
        if($this->chambre){
            return $this->chambre->getName();
        } else return 'Chambre inconnu';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): self
    {
        $this->chambre = $chambre;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
