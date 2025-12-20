using Microsoft.EntityFrameworkCore;
using BrasilBurger.Data;
using BrasilBurger.Models.Entities;
using BrasilBurger.Repositories.Interfaces;

namespace BrasilBurger.Repositories.Implementations;

public class BurgerRepository : IBurgerRepository
{
    private readonly BrasilBurgerContext _context;

    public BurgerRepository(BrasilBurgerContext context)
    {
        _context = context;
    }

    public async Task<Burger?> GetByIdAsync(Guid id)
    {
        return await _context.Burgers.FindAsync(id);
    }

    public async Task<IEnumerable<Burger>> GetAllAsync()
    {
        return await _context.Burgers.ToListAsync();
    }

    public async Task<IEnumerable<Burger>> GetDisponiblesAsync()
    {
        return await _context.Burgers
            .Where(b => b.Disponible)
            .OrderBy(b => b.Nom)
            .ToListAsync();
    }

    public async Task<Burger> AddAsync(Burger burger)
    {
        _context.Burgers.Add(burger);
        await _context.SaveChangesAsync();
        return burger;
    }

    public async Task UpdateAsync(Burger burger)
    {
        _context.Entry(burger).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    public async Task DeleteAsync(Guid id)
    {
        var burger = await _context.Burgers.FindAsync(id);
        if (burger != null)
        {
            _context.Burgers.Remove(burger);
            await _context.SaveChangesAsync();
        }
    }
}
