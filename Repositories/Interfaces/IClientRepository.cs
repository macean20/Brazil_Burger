using BrasilBurger.Models.Entities;

namespace BrasilBurger.Repositories.Interfaces;

public interface IClientRepository
{
    Task<Client?> GetByIdAsync(Guid id);
    Task<Client?> GetByEmailAsync(string email);
    Task<Client?> GetByTelephoneAsync(string telephone);
    Task<IEnumerable<Client>> GetAllAsync();
    Task<Client> AddAsync(Client client);
    Task UpdateAsync(Client client);
    Task DeleteAsync(Guid id);
    Task<bool> EmailExistsAsync(string email);
    Task<bool> TelephoneExistsAsync(string telephone);
}
