using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("complement")]
public class Complement
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Required]
    [MaxLength(20)]
    [Column("type")]
    public string Type { get; set; } = string.Empty;

    [Required]
    [Column("prix")]
    public decimal Prix { get; set; }

    [Column("disponible")]
    public bool Disponible { get; set; } = true;

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
}
