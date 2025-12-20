namespace BrasilBurger.Models.DTOs;

public class ComplementDto
{
    public Guid Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string Type { get; set; } = string.Empty;
    public decimal Prix { get; set; }
    public bool Disponible { get; set; }
}
