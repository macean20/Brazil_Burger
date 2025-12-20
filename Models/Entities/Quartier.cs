using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Models.Entities;

[Table("quartier")]
public class Quartier
{
    [Key]
    [Column("id")]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Required]
    [Column("zone_id")]
    public Guid ZoneId { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    [ForeignKey("ZoneId")]
    public virtual Zone? Zone { get; set; }
}
