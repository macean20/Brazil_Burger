<?php
// src/Entity/Complement.php

namespace App\Entity;

use App\Repository\ComplementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplementRepository::class)]
#[ORM\Table(name: 'complement')]
class Complement
{
    const TYPE_BOISSON = 'BOISSON';
    const TYPE_FRITE = 'FRITE';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\Column(length: 20)]
    private string $type;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $prix;

    #[ORM\Column(options: ['default' => true])]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'boisson', targetEntity: Menu::class)]
    private Collection $menusBoisson;

    #[ORM\OneToMany(mappedBy: 'frite', targetEntity: Menu::class)]
    private Collection $menusFrite;

    public function __construct()
    {
        $this->menusBoisson = new ArrayCollection();
        $this->menusFrite = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getPrix(): string { return $this->prix; }
    public function setPrix(string $prix): self { $this->prix = $prix; return $this; }

    public function isDisponible(): bool { return $this->disponible; }
    public function setDisponible(bool $disponible): self { $this->disponible = $disponible; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getMenusBoisson(): Collection { return $this->menusBoisson; }
    public function addMenusBoisson(Menu $menu): self {
        if (!$this->menusBoisson->contains($menu)) {
            $this->menusBoisson->add($menu);
            $menu->setBoisson($this);
        }
        return $this;
    }
    public function removeMenusBoisson(Menu $menu): self {
        if ($this->menusBoisson->removeElement($menu)) {
            if ($menu->getBoisson() === $this) $menu->setBoisson(null);
        }
        return $this;
    }

    public function getMenusFrite(): Collection { return $this->menusFrite; }
    public function addMenusFrite(Menu $menu): self {
        if (!$this->menusFrite->contains($menu)) {
            $this->menusFrite->add($menu);
            $menu->setFrite($this);
        }
        return $this;
    }
    public function removeMenusFrite(Menu $menu): self {
        if ($this->menusFrite->removeElement($menu)) {
            if ($menu->getFrite() === $this) $menu->setFrite(null);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom . ' (' . $this->type . ') - ' . $this->prix . ' FCFA';
    }
}
