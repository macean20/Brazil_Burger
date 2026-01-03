<?php
// src/Entity/Burger.php

namespace App\Entity;

use App\Repository\BurgerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BurgerRepository::class)]
#[ORM\Table(name: 'burger')]
class Burger
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $prix;

    #[ORM\Column(name: 'image_url', length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'burger', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    #[ORM\OneToMany(mappedBy: 'burger', targetEntity: Menu::class)]
    private Collection $menus;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }

    public function getNom(): string { return $this->nom; }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): string { return $this->prix; }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImageUrl(): ?string { return $this->imageUrl; }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function isDisponible(): bool { return $this->disponible; }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return $this->nom . ' (' . $this->prix . ' FCFA)';
    }
}
