using BrasilBurger.Models.Entities;

namespace BrasilBurger.Services.Interfaces;

public interface IPaiementService
{
    Task<(bool Success, string Message, Paiement? Paiement)> ProcessPaiementAsync(
        Guid commandeId,
        string modePaiement,
        string numeroTelephone);
    Task<Paiement?> GetPaiementByCommandeIdAsync(Guid commandeId);
    string GenerateReferenceTransaction(string modePaiement);
}
