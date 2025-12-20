using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("paiement")]
public class Paiement
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [Column("commande_id")]
    public Guid CommandeId { get; set; }

    [Column("date_paiement")]
    public DateTime DatePaiement { get; set; } = DateTime.UtcNow;

    [Required]
    [Column("montant")]
    public decimal Montant { get; set; }

    [Required]
    [MaxLength(10)]
    [Column("mode_paiement")]
    public string ModePaiement { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    [Column("reference_transaction")]
    public string ReferenceTransaction { get; set; } = string.Empty;

    [MaxLength(20)]
    [Column("statut")]
    public string Statut { get; set; } = "EN_ATTENTE";

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    [ForeignKey("CommandeId")]
    public virtual Commande? Commande { get; set; }
}
