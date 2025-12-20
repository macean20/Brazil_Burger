using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Helpers;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Controllers;

public class PanierController : Controller
{
    private readonly ICatalogueService _catalogueService;

    public PanierController(ICatalogueService catalogueService)
    {
        _catalogueService = catalogueService;
    }

    public IActionResult Index()
    {
        var panier = GetPanier();
        return View(panier);
    }

    [HttpPost]
    public async Task<IActionResult> AjouterBurger(Guid id, int quantite = 1)
    {
        var burger = await _catalogueService.GetBurgerByIdAsync(id);
        if (burger == null)
        {
            return NotFound();
        }

        var panier = GetPanier();
        var existingItem = panier.Items.FirstOrDefault(i => i.Id == id && i.Type == "BURGER");

        if (existingItem != null)
        {
            existingItem.Quantite += quantite;
            existingItem.SousTotal = existingItem.Quantite * existingItem.Prix;
        }
        else
        {
            panier.Items.Add(new PanierItemViewModel
            {
                Id = burger.Id,
                Nom = burger.Nom,
                ImageUrl = burger.ImageUrl,
                Prix = burger.Prix,
                Quantite = quantite,
                SousTotal = burger.Prix * quantite,
                Type = "BURGER"
            });
        }

        CalculerTotaux(panier);
        SavePanier(panier);

        return RedirectToAction("Index");
    }

    [HttpPost]
    public async Task<IActionResult> AjouterMenu(Guid id, int quantite = 1)
    {
        var menu = await _catalogueService.GetMenuByIdAsync(id);
        if (menu == null)
        {
            return NotFound();
        }

        var panier = GetPanier();
        var existingItem = panier.Items.FirstOrDefault(i => i.Id == id && i.Type == "MENU");

        if (existingItem != null)
        {
            existingItem.Quantite += quantite;
            existingItem.SousTotal = existingItem.Quantite * existingItem.Prix;
        }
        else
        {
            panier.Items.Add(new PanierItemViewModel
            {
                Id = menu.Id,
                Nom = menu.Nom,
                ImageUrl = menu.Burger?.ImageUrl,
                Prix = menu.PrixTotal,
                Quantite = quantite,
                SousTotal = menu.PrixTotal * quantite,
                Type = "MENU"
            });
        }

        CalculerTotaux(panier);
        SavePanier(panier);

        return RedirectToAction("Index");
    }

    [HttpPost]
    public IActionResult UpdateQuantite(Guid id, string type, int quantite)
    {
        var panier = GetPanier();
        var item = panier.Items.FirstOrDefault(i => i.Id == id && i.Type == type);

        if (item != null)
        {
            if (quantite <= 0)
            {
                panier.Items.Remove(item);
            }
            else
            {
                item.Quantite = quantite;
                item.SousTotal = item.Quantite * item.Prix;
            }

            CalculerTotaux(panier);
            SavePanier(panier);
        }

        return RedirectToAction("Index");
    }

    [HttpPost]
    public IActionResult Supprimer(Guid id, string type)
    {
        var panier = GetPanier();
        var item = panier.Items.FirstOrDefault(i => i.Id == id && i.Type == type);

        if (item != null)
        {
            panier.Items.Remove(item);
            CalculerTotaux(panier);
            SavePanier(panier);
        }

        return RedirectToAction("Index");
    }

    [HttpPost]
    public IActionResult Vider()
    {
        HttpContext.Session.Remove("Panier");
        return RedirectToAction("Index");
    }

    private PanierViewModel GetPanier()
    {
        var panier = HttpContext.Session.GetObjectFromJson<PanierViewModel>("Panier");
        if (panier == null)
        {
            panier = new PanierViewModel();
        }
        return panier;
    }

    private void SavePanier(PanierViewModel panier)
    {
        HttpContext.Session.SetObjectAsJson("Panier", panier);
    }

    private void CalculerTotaux(PanierViewModel panier)
    {
        panier.SousTotal = panier.Items.Sum(i => i.SousTotal);
        panier.Total = panier.SousTotal + panier.FraisLivraison;
        panier.TotalItems = panier.Items.Sum(i => i.Quantite);
    }
}
