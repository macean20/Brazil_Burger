<?php
namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuartierRepository::class)]
#[ORM\Table(name: 'quartier')]
#[ORM\UniqueConstraint(name: 'quartier_unique_zone', columns: ['nom', 'zone_id'])]
class Quartier
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 100)]
    private string $nom;

    #[ORM\ManyToOne(targetEntity: Zone::class, inversedBy: 'quartiers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Zone $zone = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getZone(): ?Zone { return $this->zone; }
    public function setZone(?Zone $zone): self { $this->zone = $zone; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function __toString(): string
    {
        return $this->nom . ' (' . $this->zone->getNom() . ')';
    }
}
