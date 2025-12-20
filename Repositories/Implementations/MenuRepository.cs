using Microsoft.EntityFrameworkCore;
using BrasilBurger.Data;
using BrasilBurger.Models.Entities;
using BrasilBurger.Repositories.Interfaces;

namespace BrasilBurger.Repositories.Implementations;

public class MenuRepository : IMenuRepository
{
    private readonly BrasilBurgerContext _context;

    public MenuRepository(BrasilBurgerContext context)
    {
        _context = context;
    }

    public async Task<Menu?> GetByIdAsync(Guid id)
    {
        return await _context.Menus
            .Include(m => m.Burger)
            .Include(m => m.Boisson)
            .Include(m => m.Frite)
            .FirstOrDefaultAsync(m => m.Id == id);
    }

    public async Task<IEnumerable<Menu>> GetAllAsync()
    {
        return await _context.Menus
            .Include(m => m.Burger)
            .Include(m => m.Boisson)
            .Include(m => m.Frite)
            .ToListAsync();
    }

    public async Task<IEnumerable<Menu>> GetDisponiblesAsync()
    {
        return await _context.Menus
            .Include(m => m.Burger)
            .Include(m => m.Boisson)
            .Include(m => m.Frite)
            .Where(m => m.Disponible && m.Burger!.Disponible)
            .OrderBy(m => m.Nom)
            .ToListAsync();
    }

    public async Task<Menu> AddAsync(Menu menu)
    {
        _context.Menus.Add(menu);
        await _context.SaveChangesAsync();
        return menu;
    }

    public async Task UpdateAsync(Menu menu)
    {
        _context.Entry(menu).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    public async Task DeleteAsync(Guid id)
    {
        var menu = await _context.Menus.FindAsync(id);
        if (menu != null)
        {
            _context.Menus.Remove(menu);
            await _context.SaveChangesAsync();
        }
    }
}
