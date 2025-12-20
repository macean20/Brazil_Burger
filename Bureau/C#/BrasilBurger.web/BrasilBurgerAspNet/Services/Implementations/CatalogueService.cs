using BrasilBurger.Models.DTOs;
using BrasilBurger.Repositories.Interfaces;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Services.Implementations;

public class CatalogueService : ICatalogueService
{
    private readonly IBurgerRepository _burgerRepository;
    private readonly IMenuRepository _menuRepository;

    public CatalogueService(IBurgerRepository burgerRepository, IMenuRepository menuRepository)
    {
        _burgerRepository = burgerRepository;
        _menuRepository = menuRepository;
    }

    public async Task<IEnumerable<BurgerDto>> GetAllBurgersAsync()
    {
        var burgers = await _burgerRepository.GetAllAsync();
        return burgers.Select(b => new BurgerDto
        {
            Id = b.Id,
            Nom = b.Nom,
            Description = b.Description,
            Prix = b.Prix,
            ImageUrl = b.ImageUrl,
            Disponible = b.Disponible
        });
    }

    public async Task<IEnumerable<BurgerDto>> GetAvailableBurgersAsync()
    {
        var burgers = await _burgerRepository.GetDisponiblesAsync();
        return burgers.Select(b => new BurgerDto
        {
            Id = b.Id,
            Nom = b.Nom,
            Description = b.Description,
            Prix = b.Prix,
            ImageUrl = b.ImageUrl,
            Disponible = b.Disponible
        });
    }

    public async Task<BurgerDto?> GetBurgerByIdAsync(Guid id)
    {
        var burger = await _burgerRepository.GetByIdAsync(id);
        if (burger == null) return null;

        return new BurgerDto
        {
            Id = burger.Id,
            Nom = burger.Nom,
            Description = burger.Description,
            Prix = burger.Prix,
            ImageUrl = burger.ImageUrl,
            Disponible = burger.Disponible
        };
    }

    public async Task<IEnumerable<MenuDto>> GetAllMenusAsync()
    {
        var menus = await _menuRepository.GetAllAsync();
        return menus.Select(m => new MenuDto
        {
            Id = m.Id,
            Nom = m.Nom,
            Description = m.Description,
            PrixTotal = m.PrixTotal,
            Disponible = m.Disponible,
            Burger = m.Burger != null ? new BurgerDto
            {
                Id = m.Burger.Id,
                Nom = m.Burger.Nom,
                Description = m.Burger.Description,
                Prix = m.Burger.Prix,
                ImageUrl = m.Burger.ImageUrl,
                Disponible = m.Burger.Disponible
            } : null,
            Boisson = m.Boisson != null ? new ComplementDto
            {
                Id = m.Boisson.Id,
                Nom = m.Boisson.Nom,
                Type = m.Boisson.Type,
                Prix = m.Boisson.Prix,
                Disponible = m.Boisson.Disponible
            } : null,
            Frite = m.Frite != null ? new ComplementDto
            {
                Id = m.Frite.Id,
                Nom = m.Frite.Nom,
                Type = m.Frite.Type,
                Prix = m.Frite.Prix,
                Disponible = m.Frite.Disponible
            } : null
        });
    }

    public async Task<IEnumerable<MenuDto>> GetAvailableMenusAsync()
    {
        var menus = await _menuRepository.GetDisponiblesAsync();
        return menus.Select(m => new MenuDto
        {
            Id = m.Id,
            Nom = m.Nom,
            Description = m.Description,
            PrixTotal = m.PrixTotal,
            Disponible = m.Disponible,
            Burger = m.Burger != null ? new BurgerDto
            {
                Id = m.Burger.Id,
                Nom = m.Burger.Nom,
                Description = m.Burger.Description,
                Prix = m.Burger.Prix,
                ImageUrl = m.Burger.ImageUrl,
                Disponible = m.Burger.Disponible
            } : null,
            Boisson = m.Boisson != null ? new ComplementDto
            {
                Id = m.Boisson.Id,
                Nom = m.Boisson.Nom,
                Type = m.Boisson.Type,
                Prix = m.Boisson.Prix,
                Disponible = m.Boisson.Disponible
            } : null,
            Frite = m.Frite != null ? new ComplementDto
            {
                Id = m.Frite.Id,
                Nom = m.Frite.Nom,
                Type = m.Frite.Type,
                Prix = m.Frite.Prix,
                Disponible = m.Frite.Disponible
            } : null
        });
    }

    public async Task<MenuDto?> GetMenuByIdAsync(Guid id)
    {
        var menu = await _menuRepository.GetByIdAsync(id);
        if (menu == null) return null;

        return new MenuDto
        {
            Id = menu.Id,
            Nom = menu.Nom,
            Description = menu.Description,
            PrixTotal = menu.PrixTotal,
            Disponible = menu.Disponible,
            Burger = menu.Burger != null ? new BurgerDto
            {
                Id = menu.Burger.Id,
                Nom = menu.Burger.Nom,
                Description = menu.Burger.Description,
                Prix = menu.Burger.Prix,
                ImageUrl = menu.Burger.ImageUrl,
                Disponible = menu.Burger.Disponible
            } : null,
            Boisson = menu.Boisson != null ? new ComplementDto
            {
                Id = menu.Boisson.Id,
                Nom = menu.Boisson.Nom,
                Type = menu.Boisson.Type,
                Prix = menu.Boisson.Prix,
                Disponible = menu.Boisson.Disponible
            } : null,
            Frite = menu.Frite != null ? new ComplementDto
            {
                Id = menu.Frite.Id,
                Nom = menu.Frite.Nom,
                Type = menu.Frite.Type,
                Prix = menu.Frite.Prix,
                Disponible = menu.Frite.Disponible
            } : null
        };
    }
}
