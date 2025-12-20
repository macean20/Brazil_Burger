using BrasilBurger.Models.Entities;

namespace BrasilBurger.Repositories.Interfaces;

public interface IZoneRepository
{
    Task<Zone?> GetByIdAsync(Guid id);
    Task<IEnumerable<Zone>> GetAllAsync();
    Task<Zone> AddAsync(Zone zone);
    Task UpdateAsync(Zone zone);
    Task DeleteAsync(Guid id);
}
