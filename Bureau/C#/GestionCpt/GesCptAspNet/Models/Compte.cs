namespace GesCptAspNet.Models
{
    public class Compte
    {
        public int IdCompte { get; set; }
        public string NumeroCompte { get; set; }
        public string Titulaire { get; set; }
        public string TypeCompte { get; set; } // Épargne, Courant
        public decimal SoldeActuel { get; set; }
        public DateTime DateCreation { get; set; }
        public string Statut { get; set; }      // Actif, Bloqué
        public DateTime? DateDeblocage { get; set; }

        public ICollection<Transaction> Transactions { get; set; }
    }
}