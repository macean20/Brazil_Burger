using BrasilBurger.Models.Entities;

namespace BrasilBurger.Repositories.Interfaces;

public interface IBurgerRepository
{
    Task<Burger?> GetByIdAsync(Guid id);
    Task<IEnumerable<Burger>> GetAllAsync();
    Task<IEnumerable<Burger>> GetDisponiblesAsync();
    Task<Burger> AddAsync(Burger burger);
    Task UpdateAsync(Burger burger);
    Task DeleteAsync(Guid id);
}
