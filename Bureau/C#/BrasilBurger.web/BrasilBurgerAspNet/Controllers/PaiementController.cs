using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Controllers;

public class PaiementController : Controller
{
    private readonly IPaiementService _paiementService;
    private readonly ICommandeService _commandeService;

    public PaiementController(IPaiementService paiementService, ICommandeService commandeService)
    {
        _paiementService = paiementService;
        _commandeService = commandeService;
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Process(PaiementViewModel model)
    {
        var clientId = HttpContext.Session.GetString("ClientId");
        if (string.IsNullOrEmpty(clientId))
        {
            return RedirectToAction("Login", "Account");
        }

        if (!ModelState.IsValid)
        {
            return View("../Commande/Paiement", model);
        }

        var result = await _paiementService.ProcessPaiementAsync(
            model.CommandeId,
            model.ModePaiement,
            model.NumeroTelephone);

        if (!result.Success)
        {
            ModelState.AddModelError(string.Empty, result.Message);
            return View("../Commande/Paiement", model);
        }

        TempData["SuccessMessage"] = "Paiement effectué avec succès!";
        return RedirectToAction("Confirmation", new { id = model.CommandeId });
    }

    [HttpGet]
    public async Task<IActionResult> Confirmation(Guid id)
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
