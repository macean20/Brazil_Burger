<?php
namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ORM\Table(name: 'paiement')]
class Paiement
{
    const MODE_WAVE = 'WAVE';
    const MODE_OM = 'OM';
    
    const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    const STATUT_REUSSI = 'REUSSI';
    const STATUT_ECHOUE = 'ECHOUE';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\OneToOne(targetEntity: Commande::class, inversedBy: 'paiement')]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?Commande $commande = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $datePaiement = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $montant;

    #[ORM\Column(length: 10)]
    private string $modePaiement;

    #[ORM\Column(length: 100, unique: true)]
    private string $referenceTransaction;

    #[ORM\Column(length: 20)]
    private string $statut;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->datePaiement = new \DateTimeImmutable();
        $this->statut = self::STATUT_EN_ATTENTE;
    }

    public function getId(): ?string { return $this->id; }
    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): self { $this->commande = $commande; return $this; }
    public function getDatePaiement(): ?\DateTimeImmutable { return $this->datePaiement; }
    public function setDatePaiement(\DateTimeImmutable $datePaiement): self { $this->datePaiement = $datePaiement; return $this; }
    public function getMontant(): string { return $this->montant; }
    public function setMontant(string $montant): self { $this->montant = $montant; return $this; }
    public function getModePaiement(): string { return $this->modePaiement; }
    public function setModePaiement(string $modePaiement): self { $this->modePaiement = $modePaiement; return $this; }
    public function getReferenceTransaction(): string { return $this->referenceTransaction; }
    public function setReferenceTransaction(string $ref): self { $this->referenceTransaction = $ref; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getModePaiementLabel(): string
    {
        return match($this->modePaiement) {
            self::MODE_WAVE => 'Wave',
            self::MODE_OM => 'Orange Money',
            default => $this->modePaiement
        };
    }

    public function getStatutLabel(): string
    {
        return match($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_REUSSI => 'Réussi',
            self::STATUT_ECHOUE => 'Échoué',
            default => $this->statut
        };
    }

    public function __toString(): string
    {
        return $this->referenceTransaction . ' - ' . $this->montant . ' FCFA';
    }
}
