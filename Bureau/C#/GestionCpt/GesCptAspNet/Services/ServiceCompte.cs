using GesCptAspNet.Data;
using GesCptAspNet.Models;
using Microsoft.EntityFrameworkCore;

namespace GesCptAspNet.Services
{
    public class ServiceCompte : IServiceCompte
    {
        private readonly DbContext _context;

        public ServiceCompte(DbContext context)
        {
            _context = context;
        }

        public Compte ObtenirCompteParNumero(string numeroCompte)
        {
            return _context.Comptes
                .Include(c => c.Transactions)
                .FirstOrDefault(c => c.NumeroCompte == numeroCompte);
        }

        public (List<Transaction> Transactions, int TotalCount) ObtenirTransactionsGlobales(
            int page,
            int taillePage,
            string filtreType
        )
        {
            var query = _context.Transactions
                .Include(t => t.Compte)
                .AsQueryable();

            if (!string.IsNullOrEmpty(filtreType))
            {
                query = query.Where(t => t.TypeTransaction == filtreType);
            }

            query = query.OrderByDescending(t => t.DateTransaction);

            int total = query.Count();

            var data = query
                .Skip((page - 1) * taillePage)
                .Take(taillePage)
                .ToList();

            return (data, total);
        }

        public StatistiquesCompteDto ObtenirStatistiques(string numeroCompte)
        {
            var compte = ObtenirCompteParNumero(numeroCompte);
            if (compte == null) return null;

            var trans = compte.Transactions;

            return new StatistiquesCompteDto
            {
                TotalDepots = trans.Where(t => t.TypeTransaction == "Dépôt").Sum(t => t.Montant),
                TotalRetraits = trans.Where(t => t.TypeTransaction == "Retrait").Sum(t => t.Montant),
                NombreTransactions = trans.Count,
                DerniereTransaction = trans
                    .OrderByDescending(t => t.DateTransaction)
                    .FirstOrDefault()?.DateTransaction
            };
        }

        public void GenererDonneesFictives()
        {
            if (_context.Comptes.Any())
                return; 

            var random = new Random();
            string[] noms = {
                "Amadou Diallo", "Fatou Ndiaye", "Moussa Ba",
                "Awa Sy", "Cheikh Diop", "Khadim Ndiaye",
                "Sokhna Fall", "Ibrahima Sow", "Marie Faye",
                "Alioune Sarr", "Ndeye Diop", "Ousmane Kane",
                "Astou Ba", "Pape Ndiaye", "Khalifa Sy"
            };

            for (int i = 0; i < 15; i++)
            {
                var compte = new Compte
                {
                    NumeroCompte = $"C{i + 1:000000}",
                    Titulaire = noms[i],
                    TypeCompte = (i % 2 == 0) ? "Épargne" : "Courant",
                    SoldeActuel = 500000,
                    DateCreation = DateTime.Now.AddMonths(-random.Next(1, 12)),
                    Statut = "Actif",
                    DateDeblocage = null
                };

                _context.Comptes.Add(compte);
                _context.SaveChanges();

                for (int j = 0; j < 4; j++)

                {
                    string type = (j % 2 == 0) ? "Dépôt" : "Retrait";
                    decimal montant = random.Next(50000, 200000);

                    decimal nouveauSolde = type == "Dépôt"
                        ? compte.SoldeActuel + montant
                        : compte.SoldeActuel - montant;

                    var trans = new Transaction
                    {
                        IdCompte = compte.IdCompte,
                        DateTransaction = DateTime.Now.AddDays(-(i + j)),
                        TypeTransaction = type,
                        Montant = montant,
                        SoldeApres = nouveauSolde,
                        Description = type == "Dépôt" ? "Versement" : "Retrait guichet"
                    };

                    compte.SoldeActuel = nouveauSolde;
                    _context.Transactions.Add(trans);
                }

                _context.SaveChanges();
            }
        }
    }
}