using Microsoft.EntityFrameworkCore;
using GesCptAspNet.Models;

namespace GesCptAspNet.Data
{
    public class DbContext : DbContext
    {
        public DbContext(DbContextOptions<DbContext> options) : base(options)
        {
        }

        public DbSet<Compte> Comptes { get; set; }
        public DbSet<Transaction> Transactions { get; set; }
    }
}