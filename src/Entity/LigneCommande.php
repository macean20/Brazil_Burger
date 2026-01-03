<?php
// src/Entity/LigneCommande.php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
#[ORM\Table(name: 'ligne_commande')]
class LigneCommande
{
    const TYPE_BURGER = 'BURGER';
    const TYPE_MENU = 'MENU';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'ligneCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column(length: 20)]
    private string $typeItem;

    #[ORM\ManyToOne(targetEntity: Burger::class, inversedBy: 'ligneCommandes')]
    private ?Burger $burger = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'ligneCommandes')]
    private ?Menu $menu = null;

    #[ORM\Column]
    private int $quantite;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $prixUnitaire;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $sousTotal;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string { return $this->id; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): self { $this->commande = $commande; return $this; }
    public function getTypeItem(): string { return $this->typeItem; }
    public function setTypeItem(string $typeItem): self { $this->typeItem = $typeItem; return $this; }

    public function getBurger(): ?Burger { return $this->burger; }
    public function setBurger(?Burger $burger): self { $this->burger = $burger; return $this; }

    public function getMenu(): ?Menu { return $this->menu; }
    public function setMenu(?Menu $menu): self { $this->menu = $menu; return $this; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): self { $this->quantite = $quantite; return $this; }

    public function getPrixUnitaire(): string { return $this->prixUnitaire; }
    public function setPrixUnitaire(string $prixUnitaire): self { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getSousTotal(): string { return $this->sousTotal; }
    public function setSousTotal(string $sousTotal): self { $this->sousTotal = $sousTotal; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getProduit(): Burger|Menu|null
    {
        return $this->typeItem === self::TYPE_BURGER ? $this->burger : $this->menu;
    }

    public function getNomProduit(): string
    {
        $produit = $this->getProduit();
        return $produit ? $produit->getNom() : 'Produit inconnu';
    }

    public function __toString(): string
    {
        return $this->getNomProduit() . ' x' . $this->quantite . ' = ' . $this->sousTotal . ' FCFA';
    }
}
