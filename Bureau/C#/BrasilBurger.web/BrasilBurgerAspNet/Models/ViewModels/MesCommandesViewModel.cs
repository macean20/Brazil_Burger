using BrasilBurger.Models.DTOs;

namespace BrasilBurger.Models.ViewModels;

public class MesCommandesViewModel
{
    public List<CommandeDto> Commandes { get; set; } = new();
    public string? FilterEtat { get; set; }
}
