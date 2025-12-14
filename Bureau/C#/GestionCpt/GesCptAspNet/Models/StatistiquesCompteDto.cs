namespace GesCptAspNet.Models
{
    public class StatistiquesCompteDto
    {
        public decimal TotalDepots { get; set; }
        public decimal TotalRetraits { get; set; }
        public int NombreTransactions { get; set; }
        public DateTime? DerniereTransaction { get; set; }
    }
}