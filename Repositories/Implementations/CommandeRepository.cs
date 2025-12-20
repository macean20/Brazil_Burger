using Microsoft.EntityFrameworkCore;
using BrasilBurger.Data;
using BrasilBurger.Models.Entities;
using BrasilBurger.Repositories.Interfaces;

namespace BrasilBurger.Repositories.Implementations;

public class CommandeRepository : ICommandeRepository
{
    private readonly BrasilBurgerContext _context;

    public CommandeRepository(BrasilBurgerContext context)
    {
        _context = context;
    }

    public async Task<Commande?> GetByIdAsync(Guid id)
    {
        return await _context.Commandes
            .Include(c => c.Client)
            .Include(c => c.Zone)
            .Include(c => c.LignesCommande)
                .ThenInclude(lc => lc.Burger)
            .Include(c => c.LignesCommande)
                .ThenInclude(lc => lc.Menu)
                    .ThenInclude(m => m!.Burger)
            .Include(c => c.Paiement)
            .FirstOrDefaultAsync(c => c.Id == id);
    }

    public async Task<Commande?> GetByNumeroAsync(string numeroCommande)
    {
        return await _context.Commandes
            .Include(c => c.Client)
            .Include(c => c.Zone)
            .Include(c => c.LignesCommande)
                .ThenInclude(lc => lc.Burger)
            .Include(c => c.LignesCommande)
                .ThenInclude(lc => lc.Menu)
            .Include(c => c.Paiement)
            .FirstOrDefaultAsync(c => c.NumeroCommande == numeroCommande);
    }

    public async Task<IEnumerable<Commande>> GetByClientIdAsync(Guid clientId)
    {
        return await _context.Commandes
            .Include(c => c.Zone)
            .Include(c => c.LignesCommande)
                .ThenInclude(lc => lc.Burger)
            .Include(c => c.LignesCommande)
                .ThenInclude(lc => lc.Menu)
                    .ThenInclude(m => m!.Burger)
            .Include(c => c.Paiement)
            .Where(c => c.ClientId == clientId)
            .OrderByDescending(c => c.DateCommande)
            .ToListAsync();
    }

    public async Task<IEnumerable<Commande>> GetAllAsync()
    {
        return await _context.Commandes
            .Include(c => c.Client)
            .Include(c => c.Zone)
            .Include(c => c.LignesCommande)
            .Include(c => c.Paiement)
            .OrderByDescending(c => c.DateCommande)
            .ToListAsync();
    }

    public async Task<Commande> AddAsync(Commande commande)
    {
        _context.Commandes.Add(commande);
        await _context.SaveChangesAsync();
        return commande;
    }

    public async Task UpdateAsync(Commande commande)
    {
        commande.UpdatedAt = DateTime.UtcNow;
        _context.Entry(commande).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    public async Task DeleteAsync(Guid id)
    {
        var commande = await _context.Commandes.FindAsync(id);
        if (commande != null)
        {
            _context.Commandes.Remove(commande);
            await _context.SaveChangesAsync();
        }
    }
}
