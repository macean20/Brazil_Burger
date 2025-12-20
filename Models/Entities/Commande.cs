using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("commande")]
public class Commande
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(50)]
    [Column("numero_commande")]
    public string NumeroCommande { get; set; } = string.Empty;

    [Required]
    [Column("client_id")]
    public Guid ClientId { get; set; }

    [Column("date_commande")]
    public DateTime DateCommande { get; set; } = DateTime.UtcNow;

    [Required]
    [Column("montant_total")]
    public decimal MontantTotal { get; set; }

    [Required]
    [MaxLength(20)]
    [Column("etat")]
    public string Etat { get; set; } = "EN_ATTENTE";

    [Column("zone_id")]
    public Guid? ZoneId { get; set; }

    [Column("livreur_id")]
    public Guid? LivreurId { get; set; }

    [Required]
    [Column("adresse_livraison")]
    public string AdresseLivraison { get; set; } = string.Empty;

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    [Column("updated_at")]
    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    [ForeignKey("ClientId")]
    public virtual Client? Client { get; set; }

    [ForeignKey("ZoneId")]
    public virtual Zone? Zone { get; set; }

    public virtual ICollection<LigneCommande> LignesCommande { get; set; } = new List<LigneCommande>();
    public virtual Paiement? Paiement { get; set; }
}
