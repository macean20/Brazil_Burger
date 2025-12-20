using System.ComponentModel.DataAnnotations;

namespace BrasilBurger.Models.ViewModels;

public class CommandeViewModel
{
    [Required(ErrorMessage = "Veuillez choisir un mode de livraison")]
    public string TypeCommande { get; set; } = string.Empty;

    public Guid? ZoneId { get; set; }

    [Required(ErrorMessage = "L'adresse est requise")]
    public string AdresseLivraison { get; set; } = string.Empty;

    [Required(ErrorMessage = "Le téléphone est requis")]
    [Phone(ErrorMessage = "Numéro de téléphone invalide")]
    public string Telephone { get; set; } = string.Empty;

    public string? Notes { get; set; }

    public decimal SousTotal { get; set; }
    public decimal FraisLivraison { get; set; }
    public decimal Total { get; set; }

    public List<ZoneDto> Zones { get; set; } = new();
}

public class ZoneDto
{
    public Guid Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public decimal PrixLivraison { get; set; }
}
