using System.ComponentModel.DataAnnotations;

namespace BrasilBurger.Models.ViewModels;

public class RegisterViewModel
{
    [Required(ErrorMessage = "Le nom complet est requis")]
    [Display(Name = "Nom complet")]
    public string NomComplet { get; set; } = string.Empty;

    [Required(ErrorMessage = "L'email est requis")]
    [EmailAddress(ErrorMessage = "Email invalide")]
    [Display(Name = "Email")]
    public string Email { get; set; } = string.Empty;

    [Required(ErrorMessage = "Le téléphone est requis")]
    [Phone(ErrorMessage = "Numéro de téléphone invalide")]
    [Display(Name = "Téléphone")]
    public string Telephone { get; set; } = string.Empty;

    [Required(ErrorMessage = "Le mot de passe est requis")]
    [StringLength(100, ErrorMessage = "Le mot de passe doit contenir au moins {2} caractères", MinimumLength = 6)]
    [DataType(DataType.Password)]
    [Display(Name = "Mot de passe")]
    public string Password { get; set; } = string.Empty;

    [DataType(DataType.Password)]
    [Display(Name = "Confirmer le mot de passe")]
    [Compare("Password", ErrorMessage = "Les mots de passe ne correspondent pas")]
    public string ConfirmPassword { get; set; } = string.Empty;
}
