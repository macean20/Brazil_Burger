<?php
// src/Entity/Livreur.php

namespace App\Entity;

use App\Repository\LivreurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivreurRepository::class)]
#[ORM\Table(name: 'livreur')]
class Livreur
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\Column(length: 100)]
    private string $prenom;

    #[ORM\Column(length: 20)]
    private string $telephone;

    #[ORM\Column(length: 150, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(options: ['default' => true])]
    private bool $disponible = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'livreur', targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $telephone): self { $this->telephone = $telephone; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function isDisponible(): bool { return $this->disponible; }
    public function setDisponible(bool $disponible): self { $this->disponible = $disponible; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, Commande> */
    public function getCommandes(): Collection { return $this->commandes; }
    public function addCommande(Commande $commande): self {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setLivreur($this);
        }
        return $this;
    }
    public function removeCommande(Commande $commande): self {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getLivreur() === $this) $commande->setLivreur(null);
        }
        return $this;
    }

    public function __toString(): string { return $this->prenom . ' ' . $this->nom; }
}
