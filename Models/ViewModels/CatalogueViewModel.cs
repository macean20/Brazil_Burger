using BrasilBurger.Models.DTOs;

namespace BrasilBurger.Models.ViewModels;

public class CatalogueViewModel
{
    public List<BurgerDto> Burgers { get; set; } = new();
    public List<MenuDto> Menus { get; set; } = new();
    public string? SearchTerm { get; set; }
    public string? FilterType { get; set; }
    public int TotalBurgers { get; set; }
    public int TotalMenus { get; set; }
}
