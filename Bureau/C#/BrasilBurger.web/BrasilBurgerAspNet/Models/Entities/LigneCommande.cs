using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("ligne_commande")]
public class LigneCommande
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [Column("commande_id")]
    public Guid CommandeId { get; set; }

    [Required]
    [MaxLength(20)]
    [Column("type_item")]
    public string TypeItem { get; set; } = string.Empty;

    [Column("burger_id")]
    public Guid? BurgerId { get; set; }

    [Column("menu_id")]
    public Guid? MenuId { get; set; }

    [Required]
    [Column("quantite")]
    public int Quantite { get; set; }

    [Required]
    [Column("prix_unitaire")]
    public decimal PrixUnitaire { get; set; }

    [Required]
    [Column("sous_total")]
    public decimal SousTotal { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    [ForeignKey("CommandeId")]
    public virtual Commande? Commande { get; set; }

    [ForeignKey("BurgerId")]
    public virtual Burger? Burger { get; set; }

    [ForeignKey("MenuId")]
    public virtual Menu? Menu { get; set; }
}
