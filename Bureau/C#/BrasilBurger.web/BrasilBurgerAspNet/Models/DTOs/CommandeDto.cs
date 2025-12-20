namespace BrasilBurger.Models.DTOs;

public class CommandeDto
{
    public Guid Id { get; set; }
    public string NumeroCommande { get; set; } = string.Empty;
    public DateTime DateCommande { get; set; }
    public decimal MontantTotal { get; set; }
    public string Etat { get; set; } = string.Empty;
    public string AdresseLivraison { get; set; } = string.Empty;
    public List<LigneCommandeDto> Lignes { get; set; } = new();
    public PaiementDto? Paiement { get; set; }
}

public class LigneCommandeDto
{
    public string TypeItem { get; set; } = string.Empty;
    public string NomItem { get; set; } = string.Empty;
    public string? ImageUrl { get; set; }
    public int Quantite { get; set; }
    public decimal PrixUnitaire { get; set; }
    public decimal SousTotal { get; set; }
}

public class PaiementDto
{
    public string ModePaiement { get; set; } = string.Empty;
    public DateTime DatePaiement { get; set; }
    public decimal Montant { get; set; }
    public string Statut { get; set; } = string.Empty;
}
