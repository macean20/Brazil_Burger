using BrasilBurger.Models.Entities;
using BrasilBurger.Models.ViewModels;

namespace BrasilBurger.Services.Interfaces;

public interface IAuthService
{
    Task<(bool Success, string Message, Client? Client)> RegisterAsync(RegisterViewModel model);
    Task<(bool Success, string Message, Client? Client)> LoginAsync(LoginViewModel model);
    Task<Client?> GetClientByIdAsync(Guid id);
    string HashPassword(string password);
    bool VerifyPassword(string password, string passwordHash);
}
