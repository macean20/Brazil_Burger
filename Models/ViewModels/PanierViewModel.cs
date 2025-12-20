namespace BrasilBurger.Models.ViewModels;

public class PanierViewModel
{
    public List<PanierItemViewModel> Items { get; set; } = new();
    public decimal SousTotal { get; set; }
    public decimal FraisLivraison { get; set; }
    public decimal Total { get; set; }
    public int TotalItems { get; set; }
}

public class PanierItemViewModel
{
    public Guid Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string? ImageUrl { get; set; }
    public decimal Prix { get; set; }
    public int Quantite { get; set; }
    public decimal SousTotal { get; set; }
    public string Type { get; set; } = string.Empty;
}
