using Microsoft.EntityFrameworkCore;
using BrasilBurger.Data;
using BrasilBurger.Models.Entities;
using BrasilBurger.Repositories.Interfaces;

namespace BrasilBurger.Repositories.Implementations;

public class ClientRepository : IClientRepository
{
    private readonly BrasilBurgerContext _context;

    public ClientRepository(BrasilBurgerContext context)
    {
        _context = context;
    }

    public async Task<Client?> GetByIdAsync(Guid id)
    {
        return await _context.Clients
            .Include(c => c.Commandes)
            .FirstOrDefaultAsync(c => c.Id == id);
    }

    public async Task<Client?> GetByEmailAsync(string email)
    {
        return await _context.Clients
            .FirstOrDefaultAsync(c => c.Email.ToLower() == email.ToLower());
    }

    public async Task<Client?> GetByTelephoneAsync(string telephone)
    {
        return await _context.Clients
            .FirstOrDefaultAsync(c => c.Telephone == telephone);
    }

    public async Task<IEnumerable<Client>> GetAllAsync()
    {
        return await _context.Clients.ToListAsync();
    }

    public async Task<Client> AddAsync(Client client)
    {
        _context.Clients.Add(client);
        await _context.SaveChangesAsync();
        return client;
    }

    public async Task UpdateAsync(Client client)
    {
        _context.Entry(client).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    public async Task DeleteAsync(Guid id)
    {
        var client = await _context.Clients.FindAsync(id);
        if (client != null)
        {
            _context.Clients.Remove(client);
            await _context.SaveChangesAsync();
        }
    }

    public async Task<bool> EmailExistsAsync(string email)
    {
        return await _context.Clients.AnyAsync(c => c.Email.ToLower() == email.ToLower());
    }

    public async Task<bool> TelephoneExistsAsync(string telephone)
    {
        return await _context.Clients.AnyAsync(c => c.Telephone == telephone);
    }
}
