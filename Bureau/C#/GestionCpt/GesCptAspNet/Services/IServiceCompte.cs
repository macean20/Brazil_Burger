using GesCptAspNet.Models;
using System.Collections.Generic;

namespace GesCptAspNet.Services
{
    public interface IServiceCompte
    {
        Compte ObtenirCompteParNumero(string numeroCompte);

        (List<Transaction> Transactions, int TotalCount) ObtenirTransactionsGlobales(
            int page,
            int taillePage,
            string filtreType
        );

        StatistiquesCompteDto ObtenirStatistiques(string numeroCompte);

        void GenererDonneesFictives();
    }
}