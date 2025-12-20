using BrasilBurger.Models.Entities;
using BrasilBurger.Models.ViewModels;
using BrasilBurger.Repositories.Interfaces;
using BrasilBurger.Services.Interfaces;

namespace BrasilBurger.Services.Implementations;

public class AuthService : IAuthService
{
    private readonly IClientRepository _clientRepository;

    public AuthService(IClientRepository clientRepository)
    {
        _clientRepository = clientRepository;
    }

    public async Task<(bool Success, string Message, Client? Client)> RegisterAsync(RegisterViewModel model)
    {
        if (await _clientRepository.EmailExistsAsync(model.Email))
        {
            return (false, "Cet email est déjà utilisé", null);
        }

        if (await _clientRepository.TelephoneExistsAsync(model.Telephone))
        {
            return (false, "Ce numéro de téléphone est déjà utilisé", null);
        }

        var names = model.NomComplet.Trim().Split(' ', 2);
        var prenom = names.Length > 0 ? names[0] : model.NomComplet;
        var nom = names.Length > 1 ? names[1] : "";

        var client = new Client
        {
            Nom = nom,
            Prenom = prenom,
            Email = model.Email,
            Telephone = model.Telephone,
            Password = HashPassword(model.Password)
        };

        var result = await _clientRepository.AddAsync(client);
        return (true, "Inscription réussie", result);
    }

    public async Task<(bool Success, string Message, Client? Client)> LoginAsync(LoginViewModel model)
    {
        var client = await _clientRepository.GetByEmailAsync(model.Email);

        if (client == null)
        {
            return (false, "Email ou mot de passe incorrect", null);
        }

        if (!VerifyPassword(model.Password, client.Password))
        {
            return (false, "Email ou mot de passe incorrect", null);
        }

        return (true, "Connexion réussie", client);
    }

    public async Task<Client?> GetClientByIdAsync(Guid id)
    {
        return await _clientRepository.GetByIdAsync(id);
    }

    public string HashPassword(string password)
    {
        return BCrypt.Net.BCrypt.HashPassword(password);
    }

    public bool VerifyPassword(string password, string passwordHash)
    {
        return BCrypt.Net.BCrypt.Verify(password, passwordHash);
    }
}
