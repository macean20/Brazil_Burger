using Microsoft.EntityFrameworkCore;
using BrasilBurger.Data;
using BrasilBurger.Models.Entities;
using BrasilBurger.Repositories.Interfaces;

namespace BrasilBurger.Repositories.Implementations;

public class ZoneRepository : IZoneRepository
{
    private readonly BrasilBurgerContext _context;

    public ZoneRepository(BrasilBurgerContext context)
    {
        _context = context;
    }

    public async Task<Zone?> GetByIdAsync(Guid id)
    {
        return await _context.Zones
            .Include(z => z.Quartiers)
            .FirstOrDefaultAsync(z => z.Id == id);
    }

    public async Task<IEnumerable<Zone>> GetAllAsync()
    {
        return await _context.Zones
            .Include(z => z.Quartiers)
            .OrderBy(z => z.Nom)
            .ToListAsync();
    }

    public async Task<Zone> AddAsync(Zone zone)
    {
        _context.Zones.Add(zone);
        await _context.SaveChangesAsync();
        return zone;
    }

    public async Task UpdateAsync(Zone zone)
    {
        _context.Entry(zone).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    public async Task DeleteAsync(Guid id)
    {
        var zone = await _context.Zones.FindAsync(id);
        if (zone != null)
        {
            _context.Zones.Remove(zone);
            await _context.SaveChangesAsync();
        }
    }
}
