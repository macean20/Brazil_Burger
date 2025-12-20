namespace BrasilBurger.Models.DTOs;

public class BurgerDto
{
    public Guid Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string? Description { get; set; }
    public decimal Prix { get; set; }
    public string? ImageUrl { get; set; }
    public bool Disponible { get; set; }
}
