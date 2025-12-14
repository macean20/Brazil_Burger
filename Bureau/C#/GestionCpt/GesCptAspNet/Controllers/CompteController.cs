using GesCptAspNet.Models;
using GesCptAspNet.Services;
using Microsoft.AspNetCore.Mvc;

namespace GesCptAspNet.Controllers
{
    public class CompteController : Controller
    {
        private readonly IServiceCompte _service;
        private const int TaillePage = 5; 

        public ComptesController(IServiceCompte service)
        {
            _service = service;
        }

        public IActionResult Details(string numeroCompte, string filtreType, int page = 1)

        {
            var (transactions, total) = _service.ObtenirTransactionsGlobales(page, TaillePage, filtreType);

            var vm = new CompteDetailsViewModel
            {
                NumeroRecherche = numeroCompte,
                FiltreType = filtreType,
                PageCourante = page,
                TotalPages = (int)Math.Ceiling(total / (double)TaillePage),
                Transactions = transactions
            };

            if (!string.IsNullOrWhiteSpace(numeroCompte))
            {
                var compte = _service.ObtenirCompteParNumero(numeroCompte);
                if (compte == null)
                {
                    vm.MessageErreur = "Numéro de compte invalide. Vérifiez et réessayez.";
                }
                else
                {
                    vm.Compte = compte;
                    vm.Statistiques = _service.ObtenirStatistiques(numeroCompte);
                }
            }

            return View(vm);
        }
    }
}