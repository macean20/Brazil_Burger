using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Controllers;

public class AccountController : Controller
{
    private readonly IAuthService _authService;

    public AccountController(IAuthService authService)
    {
        _authService = authService;
    }

    [HttpGet]
    public IActionResult Login(string? returnUrl = null)
    {
        if (HttpContext.Session.GetString("ClientId") != null)
        {
            return RedirectToAction("Index", "Catalogue");
        }

        ViewData["ReturnUrl"] = returnUrl;
        return View();
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Login(LoginViewModel model, string? returnUrl = null)
    {
        if (!ModelState.IsValid)
        {
            return View(model);
        }

        var result = await _authService.LoginAsync(model);

        if (!result.Success)
        {
            ModelState.AddModelError(string.Empty, result.Message);
            return View(model);
        }

        HttpContext.Session.SetString("ClientId", result.Client!.Id.ToString());
        HttpContext.Session.SetString("ClientNom", result.Client.Prenom + " " + result.Client.Nom);
        HttpContext.Session.SetString("ClientEmail", result.Client.Email);

        if (!string.IsNullOrEmpty(returnUrl) && Url.IsLocalUrl(returnUrl))
        {
            return Redirect(returnUrl);
        }

        return RedirectToAction("Index", "Catalogue");
    }

    [HttpGet]
    public IActionResult Register()
    {
        if (HttpContext.Session.GetString("ClientId") != null)
        {
            return RedirectToAction("Index", "Catalogue");
        }

        return View();
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Register(RegisterViewModel model)
    {
        if (!ModelState.IsValid)
        {
            return View(model);
        }

        var result = await _authService.RegisterAsync(model);

        if (!result.Success)
        {
            ModelState.AddModelError(string.Empty, result.Message);
            return View(model);
        }

        HttpContext.Session.SetString("ClientId", result.Client!.Id.ToString());
        HttpContext.Session.SetString("ClientNom", result.Client.Prenom + " " + result.Client.Nom);
        HttpContext.Session.SetString("ClientEmail", result.Client.Email);

        return RedirectToAction("Index", "Catalogue");
    }

    [HttpPost]
    public IActionResult Logout()
    {
        HttpContext.Session.Clear();
        return RedirectToAction("Index", "Home");
    }
}
