<?php
namespace App\Entity;

use App\Repository\ZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
#[ORM\Table(name: 'zone')]
class Zone
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $nom;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $prixLivraison;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Quartier::class, cascade: ['persist', 'remove'])]
    private Collection $quartiers;

    #[ORM\OneToMany(mappedBy: 'zone', targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->quartiers = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getPrixLivraison(): string { return $this->prixLivraison; }
    public function setPrixLivraison(string $prixLivraison): self { $this->prixLivraison = $prixLivraison; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, Quartier> */
    public function getQuartiers(): Collection { return $this->quartiers; }
    public function addQuartier(Quartier $quartier): self {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers->add($quartier);
            $quartier->setZone($this);
        }
        return $this;
    }
    public function removeQuartier(Quartier $quartier): self {
        if ($this->quartiers->removeElement($quartier)) {
            if ($quartier->getZone() === $this) $quartier->setZone(null);
        }
        return $this;
    }

    /** @return Collection<int, Commande> */
    public function getCommandes(): Collection { return $this->commandes; }
    public function addCommande(Commande $commande): self {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setZone($this);
        }
        return $this;
    }
    public function removeCommande(Commande $commande): self {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getZone() === $this) $commande->setZone(null);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom . ' (' . $this->prixLivraison . ' FCFA)';
    }
}
