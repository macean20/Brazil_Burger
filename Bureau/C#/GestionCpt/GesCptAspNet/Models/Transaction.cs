namespace GesCptAspNet.Models
{
    public class Transaction
    {
        public int IdTransaction { get; set; }

        public int IdCompte { get; set; }
        public Compte Compte { get; set; }

        public DateTime DateTransaction { get; set; }
        public string TypeTransaction { get; set; } // "Dépôt" ou "Retrait"
        public decimal Montant { get; set; }
        public decimal SoldeApres { get; set; }
        public string Description { get; set; }
    }
}
