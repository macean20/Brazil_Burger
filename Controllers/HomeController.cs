using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Services.Interfaces;
using BrasilBurger.Models.ViewModels;

namespace BrasilBurger.Controllers;

public class HomeController : Controller
{
    private readonly ICatalogueService _catalogueService;

    public HomeController(ICatalogueService catalogueService)
    {
        _catalogueService = catalogueService;
    }

    public async Task<IActionResult> Index()
    {
        var burgers = await _catalogueService.GetAvailableBurgersAsync();
        var menus = await _catalogueService.GetAvailableMenusAsync();

        var model = new CatalogueViewModel
        {
            Burgers = burgers.Take(3).ToList(),
            Menus = menus.Take(3).ToList(),
            TotalBurgers = burgers.Count(),
            TotalMenus = menus.Count()
        };

        return View(model);
    }
}
