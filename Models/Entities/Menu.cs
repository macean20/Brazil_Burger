using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("menu")]
public class Menu
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Column("description")]
    public string? Description { get; set; }

    [Required]
    [Column("burger_id")]
    public Guid BurgerId { get; set; }

    [Required]
    [Column("boisson_id")]
    public Guid BoissonId { get; set; }

    [Required]
    [Column("frite_id")]
    public Guid FriteId { get; set; }

    [Required]
    [Column("prix_total")]
    public decimal PrixTotal { get; set; }

    [Column("disponible")]
    public bool Disponible { get; set; } = true;

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    [ForeignKey("BurgerId")]
    public virtual Burger? Burger { get; set; }

    [ForeignKey("BoissonId")]
    public virtual Complement? Boisson { get; set; }

    [ForeignKey("FriteId")]
    public virtual Complement? Frite { get; set; }

    public virtual ICollection<LigneCommande> LignesCommande { get; set; } = new List<LigneCommande>();
}
