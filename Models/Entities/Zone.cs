using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("zone")]
public class Zone
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Required]
    [Column("prix_livraison")]
    public decimal PrixLivraison { get; set; }

    [Column("description")]
    public string? Description { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public virtual ICollection<Quartier> Quartiers { get; set; } = new List<Quartier>();
    public virtual ICollection<Commande> Commandes { get; set; } = new List<Commande>();
}
