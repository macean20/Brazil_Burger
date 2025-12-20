using BrasilBurger.Models.DTOs;

namespace BrasilBurger.Services.Interfaces;

public interface ICatalogueService
{
    Task<IEnumerable<BurgerDto>> GetAllBurgersAsync();
    Task<IEnumerable<BurgerDto>> GetAvailableBurgersAsync();
    Task<BurgerDto?> GetBurgerByIdAsync(Guid id);
    Task<IEnumerable<MenuDto>> GetAllMenusAsync();
    Task<IEnumerable<MenuDto>> GetAvailableMenusAsync();
    Task<MenuDto?> GetMenuByIdAsync(Guid id);
}
