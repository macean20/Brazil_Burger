using Microsoft.EntityFrameworkCore;
using BrasilBurger.Models.Entities;

namespace BrasilBurger.Data;

public class BrasilBurgerContext : DbContext
{
    public BrasilBurgerContext(DbContextOptions<BrasilBurgerContext> options) : base(options)
    {
    }

    public DbSet<Client> Clients { get; set; }
    public DbSet<Burger> Burgers { get; set; }
    public DbSet<Complement> Complements { get; set; }
    public DbSet<Menu> Menus { get; set; }
    public DbSet<Zone> Zones { get; set; }
    public DbSet<Quartier> Quartiers { get; set; }
    public DbSet<Commande> Commandes { get; set; }
    public DbSet<LigneCommande> LignesCommande { get; set; }
    public DbSet<Paiement> Paiements { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        modelBuilder.Entity<Client>(entity =>
        {
            entity.ToTable("client");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.Email).IsUnique();
            entity.HasIndex(e => e.Telephone).IsUnique();
        });

        modelBuilder.Entity<Burger>(entity =>
        {
            entity.ToTable("burger");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.Disponible);
            entity.Property(e => e.Prix).HasPrecision(10, 2);
        });

        modelBuilder.Entity<Complement>(entity =>
        {
            entity.ToTable("complement");
            entity.HasKey(e => e.Id);
            entity.Property(e => e.Prix).HasPrecision(10, 2);
        });

        modelBuilder.Entity<Menu>(entity =>
        {
            entity.ToTable("menu");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.Disponible);
            entity.Property(e => e.PrixTotal).HasPrecision(10, 2);

            entity.HasOne(m => m.Burger)
                .WithMany(b => b.Menus)
                .HasForeignKey(m => m.BurgerId)
                .OnDelete(DeleteBehavior.Cascade);

            entity.HasOne(m => m.Boisson)
                .WithMany()
                .HasForeignKey(m => m.BoissonId)
                .OnDelete(DeleteBehavior.Restrict);

            entity.HasOne(m => m.Frite)
                .WithMany()
                .HasForeignKey(m => m.FriteId)
                .OnDelete(DeleteBehavior.Restrict);
        });

        modelBuilder.Entity<Zone>(entity =>
        {
            entity.ToTable("zone");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.Nom).IsUnique();
            entity.Property(e => e.PrixLivraison).HasPrecision(10, 2);
        });

        modelBuilder.Entity<Quartier>(entity =>
        {
            entity.ToTable("quartier");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.ZoneId);

            entity.HasOne(q => q.Zone)
                .WithMany(z => z.Quartiers)
                .HasForeignKey(q => q.ZoneId)
                .OnDelete(DeleteBehavior.Cascade);
        });

        modelBuilder.Entity<Commande>(entity =>
        {
            entity.ToTable("commande");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.ClientId);
            entity.HasIndex(e => e.Etat);
            entity.HasIndex(e => e.DateCommande);
            entity.HasIndex(e => e.ZoneId);
            entity.HasIndex(e => e.NumeroCommande).IsUnique();
            entity.Property(e => e.MontantTotal).HasPrecision(10, 2);

            entity.HasOne(c => c.Client)
                .WithMany(cl => cl.Commandes)
                .HasForeignKey(c => c.ClientId)
                .OnDelete(DeleteBehavior.Cascade);

            entity.HasOne(c => c.Zone)
                .WithMany(z => z.Commandes)
                .HasForeignKey(c => c.ZoneId)
                .OnDelete(DeleteBehavior.SetNull);
        });

        modelBuilder.Entity<LigneCommande>(entity =>
        {
            entity.ToTable("ligne_commande");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.CommandeId);
            entity.Property(e => e.PrixUnitaire).HasPrecision(10, 2);
            entity.Property(e => e.SousTotal).HasPrecision(10, 2);

            entity.HasOne(lc => lc.Commande)
                .WithMany(c => c.LignesCommande)
                .HasForeignKey(lc => lc.CommandeId)
                .OnDelete(DeleteBehavior.Cascade);

            entity.HasOne(lc => lc.Burger)
                .WithMany(b => b.LignesCommande)
                .HasForeignKey(lc => lc.BurgerId)
                .OnDelete(DeleteBehavior.Restrict);

            entity.HasOne(lc => lc.Menu)
                .WithMany(m => m.LignesCommande)
                .HasForeignKey(lc => lc.MenuId)
                .OnDelete(DeleteBehavior.Restrict);
        });

        modelBuilder.Entity<Paiement>(entity =>
        {
            entity.ToTable("paiement");
            entity.HasKey(e => e.Id);
            entity.HasIndex(e => e.CommandeId).IsUnique();
            entity.HasIndex(e => e.ReferenceTransaction).IsUnique();
            entity.Property(e => e.Montant).HasPrecision(10, 2);

            entity.HasOne(p => p.Commande)
                .WithOne(c => c.Paiement)
                .HasForeignKey<Paiement>(p => p.CommandeId)
                .OnDelete(DeleteBehavior.Cascade);
        });
    }
}
