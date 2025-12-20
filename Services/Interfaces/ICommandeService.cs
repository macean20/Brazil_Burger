using BrasilBurger.Models.DTOs;
using BrasilBurger.Models.Entities;
using BrasilBurger.Models.ViewModels;

namespace BrasilBurger.Services.Interfaces;

public interface ICommandeService
{
    Task<(bool Success, string Message, Commande? Commande)> CreateCommandeAsync(
        Guid clientId,
        CommandeViewModel model,
        List<PanierItemViewModel> items);
    Task<IEnumerable<CommandeDto>> GetCommandesByClientAsync(Guid clientId);
    Task<CommandeDto?> GetCommandeByIdAsync(Guid id);
    Task<IEnumerable<ZoneDto>> GetAllZonesAsync();
}
