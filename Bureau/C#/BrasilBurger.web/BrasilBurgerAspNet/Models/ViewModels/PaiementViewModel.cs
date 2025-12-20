using System.ComponentModel.DataAnnotations;

namespace BrasilBurger.Models.ViewModels;

public class PaiementViewModel
{
    public Guid CommandeId { get; set; }
    public string NumeroCommande { get; set; } = string.Empty;

    [Required(ErrorMessage = "Veuillez choisir un mode de paiement")]
    public string ModePaiement { get; set; } = string.Empty;

    [Required(ErrorMessage = "Le numéro de téléphone est requis")]
    public string NumeroTelephone { get; set; } = string.Empty;

    public decimal MontantTotal { get; set; }
    public decimal SousTotal { get; set; }
    public decimal FraisLivraison { get; set; }
}
