using BrasilBurger.Data;
using BrasilBurger.Models.Entities;
using BrasilBurger.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace BrasilBurger.Services.Implementations;

public class PaiementService : IPaiementService
{
    private readonly BrasilBurgerContext _context;

    public PaiementService(BrasilBurgerContext context)
    {
        _context = context;
    }

    public async Task<(bool Success, string Message, Paiement? Paiement)> ProcessPaiementAsync(
        Guid commandeId,
        string modePaiement,
        string numeroTelephone)
    {
        var commande = await _context.Commandes.FindAsync(commandeId);
        if (commande == null)
        {
            return (false, "Commande introuvable", null);
        }

        var existingPaiement = await _context.Paiements
            .FirstOrDefaultAsync(p => p.CommandeId == commandeId);

        if (existingPaiement != null)
        {
            return (false, "Cette commande a déjà été payée", null);
        }

        var paiement = new Paiement
        {
            CommandeId = commandeId,
            Montant = commande.MontantTotal,
            ModePaiement = modePaiement.ToUpper(),
            ReferenceTransaction = GenerateReferenceTransaction(modePaiement),
            Statut = "REUSSI"
        };

        _context.Paiements.Add(paiement);

        commande.Etat = "VALIDEE";
        _context.Entry(commande).State = EntityState.Modified;

        await _context.SaveChangesAsync();

        return (true, "Paiement effectué avec succès", paiement);
    }

    public async Task<Paiement?> GetPaiementByCommandeIdAsync(Guid commandeId)
    {
        return await _context.Paiements
            .Include(p => p.Commande)
            .FirstOrDefaultAsync(p => p.CommandeId == commandeId);
    }

    public string GenerateReferenceTransaction(string modePaiement)
    {
        var prefix = modePaiement.ToUpper() == "WAVE" ? "WV" : "OM";
        var timestamp = DateTime.Now.ToString("yyyyMMddHHmmss");
        var random = new Random().Next(1000, 9999);
        return $"{prefix}-{timestamp}-{random}";
    }
}
