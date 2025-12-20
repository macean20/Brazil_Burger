namespace BrasilBurger.Models.DTOs;


public class MenuDto
{
    public Guid Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string? Description { get; set; }
    public decimal PrixTotal { get; set; }
    public bool Disponible { get; set; }
    public BurgerDto? Burger { get; set; }
    public ComplementDto? Boisson { get; set; }
    public ComplementDto? Frite { get; set; }
}
