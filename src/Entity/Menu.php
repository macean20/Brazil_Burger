<?php
// src/Entity/Menu.php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: 'menu')]
class Menu
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Burger::class, inversedBy: 'menus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Burger $burger = null;

    #[ORM\ManyToOne(targetEntity: Complement::class, inversedBy: 'menusBoisson')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complement $boisson = null;

    #[ORM\ManyToOne(targetEntity: Complement::class, inversedBy: 'menusFrite')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complement $frite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $prixTotal;

    #[ORM\Column(options: ['default' => true])]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'menu', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getBurger(): ?Burger { return $this->burger; }
    public function setBurger(?Burger $burger): self { $this->burger = $burger; return $this; }

    public function getBoisson(): ?Complement { return $this->boisson; }
    public function setBoisson(?Complement $boisson): self { $this->boisson = $boisson; return $this; }

    public function getFrite(): ?Complement { return $this->frite; }
    public function setFrite(?Complement $frite): self { $this->frite = $frite; return $this; }

    public function getPrixTotal(): string { return $this->prixTotal; }
    public function setPrixTotal(string $prixTotal): self { $this->prixTotal = $prixTotal; return $this; }

    public function isDisponible(): bool { return $this->disponible; }
    public function setDisponible(bool $disponible): self { $this->disponible = $disponible; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, LigneCommande> */
    public function getLigneCommandes(): Collection { return $this->ligneCommandes; }
    public function addLigneCommande(LigneCommande $ligneCommande): self {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setMenu($this);
        }
        return $this;
    }
    public function removeLigneCommande(LigneCommande $ligneCommande): self {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getMenu() === $this) $ligneCommande->setMenu(null);
        }
        return $this;
    }

    public function __toString(): string { return $this->nom . ' (' . $this->prixTotal . ' FCFA)'; }

    public function getComposition(): string
    {
        return $this->burger->getNom() . ' + ' . $this->boisson->getNom() . ' + ' . $this->frite->getNom();
    }
}
