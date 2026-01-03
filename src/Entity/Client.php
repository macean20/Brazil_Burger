<?php
// src/Entity/Client.php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'client')]
class Client
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\Column(length: 100)]
    private string $prenom;

    #[ORM\Column(length: 20, unique: true)]
    private string $telephone;

    #[ORM\Column(length: 150, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }

    public function getNom(): string { return $this->nom; }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string { return $this->prenom; }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): string { return $this->telephone; }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): string { return $this->email; }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string { return $this->password; }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
