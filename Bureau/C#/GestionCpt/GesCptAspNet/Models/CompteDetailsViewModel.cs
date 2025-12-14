using System.Collections.Generic;

namespace GesCptAspNet.Models
{
    public class CompteDetailsViewModel
    {
        // Pour la recherche
        public string NumeroRecherche { get; set; }
        public string MessageErreur { get; set; }

        // Pour le compte sélectionné
        public Compte Compte { get; set; }
        public StatistiquesCompteDto Statistiques { get; set; }

        // Historique global (toujours affiché)
        public IEnumerable<Transaction> Transactions { get; set; }
        public string FiltreType { get; set; }
        public int PageCourante { get; set; }
        public int TotalPages { get; set; }
    }
}