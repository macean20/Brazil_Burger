<?php
// src/Entity/Commande.php
namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
#[ORM\HasLifecycleCallbacks]
class Commande
{
    const ETAT_EN_ATTENTE = 'EN_ATTENTE';
    const ETAT_VALIDEE = 'VALIDEE';
    const ETAT_EN_PREPARATION = 'EN_PREPARATION';
    const ETAT_TERMINEE = 'TERMINEE';
    const ETAT_EN_LIVRAISON = 'EN_LIVRAISON';
    const ETAT_LIVREE = 'LIVREE';
    const ETAT_ANNULEE = 'ANNULEE';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numeroCommande = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateCommande = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montantTotal = null;

    #[ORM\Column(length: 20, options: ['default' => 'EN_ATTENTE'])]
    private ?string $etat = self::ETAT_EN_ATTENTE;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'commandes')]
    private ?Zone $zone = null;

    #[ORM\ManyToOne(targetEntity: Livreur::class, inversedBy: 'commandes')]
    private ?Livreur $livreur = null;

    #[ORM\Column(type: 'text')]
    private ?string $adresseLivraison = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist', 'remove'])]
    private Collection $ligneCommandes;

    #[ORM\OneToOne(mappedBy: 'commande', targetEntity: Paiement::class, cascade: ['persist', 'remove'])]
    private ?Paiement $paiement = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->dateCommande = new \DateTimeImmutable();
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?string
    {
        return $this->numeroCommande;
    }

    public function setNumeroCommande(string $numeroCommande): static
    {
        $this->numeroCommande = $numeroCommande;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getDateCommande(): ?\DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeImmutable $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;
        return $this;
    }

    public function getLivreur(): ?Livreur
    {
        return $this->livreur;
    }

    public function setLivreur(?Livreur $livreur): static
    {
        $this->livreur = $livreur;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }
        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): static
    {
        // set the owning side of the relation if necessary
        if ($paiement !== null && $paiement->getCommande() !== $this) {
            $paiement->setCommande($this);
        }

        $this->paiement = $paiement;
        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->numeroCommande . ' - ' . $this->client . ' (' . $this->montantTotal . ' FCFA)';
    }

    public function getEtatLabel(): string
    {
        $etats = [
            self::ETAT_EN_ATTENTE => 'En attente',
            self::ETAT_VALIDEE => 'Validée',
            self::ETAT_EN_PREPARATION => 'En préparation',
            self::ETAT_TERMINEE => 'Terminée',
            self::ETAT_EN_LIVRAISON => 'En livraison',
            self::ETAT_LIVREE => 'Livrée',
            self::ETAT_ANNULEE => 'Annulée'
        ];
        
        return $etats[$this->etat] ?? $this->etat;
    }

    public function canBeCanceled(): bool
    {
        return in_array($this->etat, [self::ETAT_EN_ATTENTE, self::ETAT_VALIDEE]);
    }

    public function canBePrepared(): bool
    {
        return in_array($this->etat, [self::ETAT_VALIDEE]);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->etat, [self::ETAT_EN_PREPARATION]);
    }

    public function canBeDelivered(): bool
    {
        return in_array($this->etat, [self::ETAT_TERMINEE]);
    }
}