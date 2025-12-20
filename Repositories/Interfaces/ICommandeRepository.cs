using BrasilBurger.Models.Entities;

namespace BrasilBurger.Repositories.Interfaces;

public interface ICommandeRepository
{
    Task<Commande?> GetByIdAsync(Guid id);
    Task<Commande?> GetByNumeroAsync(string numeroCommande);
    Task<IEnumerable<Commande>> GetByClientIdAsync(Guid clientId);
    Task<IEnumerable<Commande>> GetAllAsync();
    Task<Commande> AddAsync(Commande commande);
    Task UpdateAsync(Commande commande);
    Task DeleteAsync(Guid id);
}
