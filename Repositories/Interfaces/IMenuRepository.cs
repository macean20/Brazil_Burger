using BrasilBurger.Models.Entities;

namespace BrasilBurger.Repositories.Interfaces;

public interface IMenuRepository
{
    Task<Menu?> GetByIdAsync(Guid id);
    Task<IEnumerable<Menu>> GetAllAsync();
    Task<IEnumerable<Menu>> GetDisponiblesAsync();
    Task<Menu> AddAsync(Menu menu);
    Task UpdateAsync(Menu menu);
    Task DeleteAsync(Guid id);
}
