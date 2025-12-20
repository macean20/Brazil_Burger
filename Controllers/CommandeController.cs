using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Helpers;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Controllers;

public class CommandeController : Controller
{
    private readonly ICommandeService _commandeService;

    public CommandeController(ICommandeService commandeService)
    {
        _commandeService = commandeService;
    }

    [HttpGet]
    public async Task<IActionResult> Checkout()
    {
        var clientId = HttpContext.Session.GetString("ClientId");
        if (string.IsNullOrEmpty(clientId))
        {
            return RedirectToAction("Login", "Account", new { returnUrl = "/Commande/Checkout" });
        }

        var panier = HttpContext.Session.GetObjectFromJson<PanierViewModel>("Panier");
        if (panier == null || !panier.Items.Any())
        {
            return RedirectToAction("Index", "Panier");
        }

        var zones = await _commandeService.GetAllZonesAsync();

        var model = new CommandeViewModel
        {
            SousTotal = panier.SousTotal,
            FraisLivraison = 0,
            Total = panier.SousTotal,
            Zones = zones.ToList()
        };

        return View(model);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Checkout(CommandeViewModel model)
    {
        var clientId = HttpContext.Session.GetString("ClientId");
        if (string.IsNullOrEmpty(clientId))
        {
            return RedirectToAction("Login", "Account");
        }

        var panier = HttpContext.Session.GetObjectFromJson<PanierViewModel>("Panier");
        if (panier == null || !panier.Items.Any())
        {
            return RedirectToAction("Index", "Panier");
        }

        if (model.TypeCommande == "Livraison" && model.ZoneId.HasValue)
        {
            var zones = await _commandeService.GetAllZonesAsync();
            var zone = zones.FirstOrDefault(z => z.Id == model.ZoneId.Value);
            if (zone != null)
            {
                model.FraisLivraison = zone.PrixLivraison;
            }
        }

        model.SousTotal = panier.SousTotal;
        model.Total = model.SousTotal + model.FraisLivraison;

        if (!ModelState.IsValid)
        {
            var zones = await _commandeService.GetAllZonesAsync();
            model.Zones = zones.ToList();
            return View(model);
        }

        var result = await _commandeService.CreateCommandeAsync(
            Guid.Parse(clientId),
            model,
            panier.Items);

        if (!result.Success)
        {
            ModelState.AddModelError(string.Empty, result.Message);
            var zones = await _commandeService.GetAllZonesAsync();
            model.Zones = zones.ToList();
            return View(model);
        }

        HttpContext.Session.SetString("CommandeId", result.Commande!.Id.ToString());
        HttpContext.Session.Remove("Panier");

        return RedirectToAction("Paiement", new { id = result.Commande.Id });
    }

    [HttpGet]
    public async Task<IActionResult> Paiement(Guid id)
    {
        var clientId = HttpContext.Session.GetString("ClientId");
        if (string.IsNullOrEmpty(clientId))
        {
            return RedirectToAction("Login", "Account");
        }

        var commande = await _commandeService.GetCommandeByIdAsync(id);
        if (commande == null)
        {
            return NotFound();
        }

        var zones = await _commandeService.GetAllZonesAsync();
        var fraisLivraison = commande.MontantTotal > 0 ? zones.FirstOrDefault()?.PrixLivraison ?? 0 : 0;

        var model = new PaiementViewModel
        {
            CommandeId = commande.Id,
            NumeroCommande = commande.NumeroCommande,
            MontantTotal = commande.MontantTotal,
            SousTotal = commande.MontantTotal - fraisLivraison,
            FraisLivraison = fraisLivraison
        };

        return View(model);
    }

    [HttpGet]
    public async Task<IActionResult> MesCommandes()
    {
        var clientId = HttpContext.Session.GetString("ClientId");
        if (string.IsNullOrEmpty(clientId))
        {
            return RedirectToAction("Login", "Account");
        }

        var commandes = await _commandeService.GetCommandesByClientAsync(Guid.Parse(clientId));

        var model = new MesCommandesViewModel
        {
            Commandes = commandes.ToList()
        };

        return View(model);
    }

    [HttpGet]
    public async Task<IActionResult> Details(Guid id)
    {
        var clientId = HttpContext.Session.GetString("ClientId");
        if (string.IsNullOrEmpty(clientId))
        {
            return RedirectToAction("Login", "Account");
        }

        var commande = await _commandeService.GetCommandeByIdAsync(id);
        if (commande == null)
        {
            return NotFound();
        }

        return View(commande);
    }
}
