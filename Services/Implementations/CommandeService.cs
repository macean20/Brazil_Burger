using BrasilBurger.Models.DTOs;
using BrasilBurger.Models.Entities;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Repositories.Interfaces;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Services.Implementations;

public class CommandeService : ICommandeService
{
    private readonly ICommandeRepository _commandeRepository;
    private readonly IZoneRepository _zoneRepository;

    public CommandeService(ICommandeRepository commandeRepository, IZoneRepository zoneRepository)
    {
        _commandeRepository = commandeRepository;
        _zoneRepository = zoneRepository;
    }

    public async Task<(bool Success, string Message, Commande? Commande)> CreateCommandeAsync(
        Guid clientId,
        CommandeViewModel model,
        List<PanierItemViewModel> items)
    {
        if (!items.Any())
        {
            return (false, "Le panier est vide", null);
        }

        var commande = new Commande
        {
            ClientId = clientId,
            NumeroCommande = GenerateNumeroCommande(),
            MontantTotal = model.Total,
            Etat = "EN_ATTENTE",
            AdresseLivraison = model.AdresseLivraison,
            ZoneId = model.ZoneId
        };

        foreach (var item in items)
        {
            var ligne = new LigneCommande
            {
                TypeItem = item.Type,
                Quantite = item.Quantite,
                PrixUnitaire = item.Prix,
                SousTotal = item.SousTotal
            };

            if (item.Type == "BURGER")
            {
                ligne.BurgerId = item.Id;
            }
            else if (item.Type == "MENU")
            {
                ligne.MenuId = item.Id;
            }

            commande.LignesCommande.Add(ligne);
        }

        var result = await _commandeRepository.AddAsync(commande);
        return (true, "Commande créée avec succès", result);
    }

    public async Task<IEnumerable<CommandeDto>> GetCommandesByClientAsync(Guid clientId)
    {
        var commandes = await _commandeRepository.GetByClientIdAsync(clientId);

        return commandes.Select(c => new CommandeDto
        {
            Id = c.Id,
            NumeroCommande = c.NumeroCommande,
            DateCommande = c.DateCommande,
            MontantTotal = c.MontantTotal,
            Etat = c.Etat,
            AdresseLivraison = c.AdresseLivraison,
            Lignes = c.LignesCommande.Select(lc => new LigneCommandeDto
            {
                TypeItem = lc.TypeItem,
                NomItem = lc.TypeItem == "BURGER" ? lc.Burger?.Nom ?? "" : lc.Menu?.Nom ?? "",
                ImageUrl = lc.TypeItem == "BURGER" ? lc.Burger?.ImageUrl : lc.Menu?.Burger?.ImageUrl,
                Quantite = lc.Quantite,
                PrixUnitaire = lc.PrixUnitaire,
                SousTotal = lc.SousTotal
            }).ToList(),
            Paiement = c.Paiement != null ? new PaiementDto
            {
                ModePaiement = c.Paiement.ModePaiement,
                DatePaiement = c.Paiement.DatePaiement,
                Montant = c.Paiement.Montant,
                Statut = c.Paiement.Statut
            } : null
        });
    }

    public async Task<CommandeDto?> GetCommandeByIdAsync(Guid id)
    {
        var c = await _commandeRepository.GetByIdAsync(id);
        if (c == null) return null;

        return new CommandeDto
        {
            Id = c.Id,
            NumeroCommande = c.NumeroCommande,
            DateCommande = c.DateCommande,
            MontantTotal = c.MontantTotal,
            Etat = c.Etat,
            AdresseLivraison = c.AdresseLivraison,
            Lignes = c.LignesCommande.Select(lc => new LigneCommandeDto
            {
                TypeItem = lc.TypeItem,
                NomItem = lc.TypeItem == "BURGER" ? lc.Burger?.Nom ?? "" : lc.Menu?.Nom ?? "",
                ImageUrl = lc.TypeItem == "BURGER" ? lc.Burger?.ImageUrl : lc.Menu?.Burger?.ImageUrl,
                Quantite = lc.Quantite,
                PrixUnitaire = lc.PrixUnitaire,
                SousTotal = lc.SousTotal
            }).ToList(),
            Paiement = c.Paiement != null ? new PaiementDto
            {
                ModePaiement = c.Paiement.ModePaiement,
                DatePaiement = c.Paiement.DatePaiement,
                Montant = c.Paiement.Montant,
                Statut = c.Paiement.Statut
            } : null
        };
    }

    public async Task<IEnumerable<ZoneDto>> GetAllZonesAsync()
    {
        var zones = await _zoneRepository.GetAllAsync();
        return zones.Select(z => new ZoneDto
        {
            Id = z.Id,
            Nom = z.Nom,
            PrixLivraison = z.PrixLivraison
        });
    }

    private string GenerateNumeroCommande()
    {
        return $"#BR{DateTime.Now:yyyyMMdd}{new Random().Next(1000, 9999)}";
    }
}
