using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Services.Interfaces;
using BrasilBurger.Models.DTOs; // Ajoutez cette ligne

namespace BrasilBurger.Controllers;

public class CatalogueController : Controller
{
    private readonly ICatalogueService _catalogueService;

    public CatalogueController(ICatalogueService catalogueService)
    {
        _catalogueService = catalogueService;
    }

    public async Task<IActionResult> Index(string? filter)
    {
        var burgers = await _catalogueService.GetAvailableBurgersAsync();
        var menus = await _catalogueService.GetAvailableMenusAsync();

        var model = new CatalogueViewModel
        {
            Burgers = burgers.ToList(),
            Menus = menus.ToList(),
            FilterType = filter,
            TotalBurgers = burgers.Count(),
            TotalMenus = menus.Count()
        };

        return View(model);
    }

    public async Task<IActionResult> Burgers()
    {
        var burgers = await _catalogueService.GetAvailableBurgersAsync();

        var model = new CatalogueViewModel
        {
            Burgers = burgers.ToList(),
            Menus = new List<MenuDto>(), // REMPLACEZ DTOs.MenuDto par MenuDto
            FilterType = "burgers",
            TotalBurgers = burgers.Count(),
            TotalMenus = 0
        };

        return View("Index", model);
    }

    public async Task<IActionResult> Menus()
    {
        var menus = await _catalogueService.GetAvailableMenusAsync();

        var model = new CatalogueViewModel
        {
            Burgers = new List<BurgerDto>(), // REMPLACEZ DTOs.BurgerDto par BurgerDto
            Menus = menus.ToList(),
            FilterType = "menus",
            TotalBurgers = 0,
            TotalMenus = menus.Count()
        };

        return View("Index", model);
    }

    public async Task<IActionResult> DetailsBurger(Guid id)
    {
        var burger = await _catalogueService.GetBurgerByIdAsync(id);

        if (burger == null)
        {
            return NotFound();
        }

        return View(burger);
    }

    public async Task<IActionResult> DetailsMenu(Guid id)
    {
        var menu = await _catalogueService.GetMenuByIdAsync(id);

        if (menu == null)
        {
            return NotFound();
        }

        return View(menu);
    }
}