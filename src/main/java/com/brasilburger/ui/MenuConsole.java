package com.brasilburger.ui;

import com.brasilburger.models.Burger;
import com.brasilburger.models.Complement;
import com.brasilburger.models.Menu;
import com.brasilburger.models.TypeComplement;
import com.brasilburger.services.interfaces.IBurgerService;
import com.brasilburger.services.interfaces.IComplementService;
import com.brasilburger.services.interfaces.IMenuService;

import java.math.BigDecimal;
import java.util.List;
import java.util.Scanner;
import java.util.UUID;

public class MenuConsole {
    private final Scanner scanner;
    private final InputHelper inputHelper;
    private final IBurgerService burgerService;
    private final IComplementService complementService;
    private final IMenuService menuService;

    public MenuConsole(IBurgerService burgerService, IComplementService complementService, IMenuService menuService) {
        this.scanner = new Scanner(System.in);
        this.inputHelper = new InputHelper(scanner);
        this.burgerService = burgerService;
        this.complementService = complementService;
        this.menuService = menuService;
    }

    public void start() {
        while (true) {
            afficherMenuPrincipal();
            int choix = inputHelper.readInt("Votre choix: ");

            try {
                switch (choix) {
                    case 1:
                        menuGestionBurgers();
                        break;
                    case 2:
                        menuGestionComplements();
                        break;
                    case 3:
                        menuGestionMenus();
                        break;
                    case 0:
                        System.out.println("\nMerci d'avoir utilisé l'application Brasil Burger!");
                        scanner.close();
                        return;
                    default:
                        System.out.println("\nChoix invalide. Veuillez réessayer.");
                }
            } catch (Exception e) {
                System.err.println("\nErreur: " + e.getMessage());
                inputHelper.pause();
            }
        }
    }

    private void afficherMenuPrincipal() {
        System.out.println("\n╔════════════════════════════════════════╗");
        System.out.println("║    BRASIL BURGER - GESTION CONSOLE     ║");
        System.out.println("╚════════════════════════════════════════╝");
        System.out.println("  1. Gestion des Burgers");
        System.out.println("  2. Gestion des Compléments");
        System.out.println("  3. Gestion des Menus");
        System.out.println("  0. Quitter");
        System.out.println("─────────────────────────────────────────");
    }

    private void menuGestionBurgers() {
        while (true) {
            System.out.println("\n╔════════════════════════════════════════╗");
            System.out.println("║       GESTION DES BURGERS              ║");
            System.out.println("╚════════════════════════════════════════╝");
            System.out.println("  1. Créer un burger");
            System.out.println("  2. Afficher tous les burgers");
            System.out.println("  3. Afficher burgers disponibles");
            System.out.println("  4. Modifier un burger");
            System.out.println("  5. Supprimer un burger");
            System.out.println("  6. Changer disponibilité d'un burger");
            System.out.println("  0. Retour au menu principal");
            System.out.println("─────────────────────────────────────────");

            int choix = inputHelper.readInt("Votre choix: ");

            try {
                switch (choix) {
                    case 1:
                        creerBurger();
                        break;
                    case 2:
                        afficherBurgers();
                        break;
                    case 3:
                        afficherBurgersDisponibles();
                        break;
                    case 4:
                        modifierBurger();
                        break;
                    case 5:
                        supprimerBurger();
                        break;
                    case 6:
                        toggleDisponibiliteBurger();
                        break;
                    case 0:
                        return;
                    default:
                        System.out.println("\nChoix invalide.");
                }
            } catch (Exception e) {
                System.err.println("\nErreur: " + e.getMessage());
            }
            inputHelper.pause();
        }
    }

    private void creerBurger() {
        System.out.println("\n=== CRÉATION D'UN BURGER ===");
        String nom = inputHelper.readString("Nom du burger: ");
        String description = inputHelper.readString("Description: ");
        BigDecimal prix = inputHelper.readBigDecimal("Prix (FCFA): ");
        String imageUrl = inputHelper.readString("URL de l'image (optionnel): ");

        Burger burger = burgerService.createBurger(nom, description, prix,
                imageUrl.isEmpty() ? null : imageUrl);
        System.out.println("\n✓ Burger créé avec succès!");
        System.out.println(burger);
    }

    private void afficherBurgers() {
        System.out.println("\n=== LISTE DES BURGERS ===");
        List<Burger> burgers = burgerService.getAllBurgers();

        if (burgers.isEmpty()) {
            System.out.println("Aucun burger trouvé.");
            return;
        }

        System.out.println(String.format("%-36s | %-25s | %10s | %11s", "ID", "Nom", "Prix", "Disponible"));
        System.out.println("─".repeat(90));

        for (Burger burger : burgers) {
            System.out.println(String.format("%-36s | %-25s | %10.2f | %11s",
                    burger.getId(),
                    burger.getNom(),
                    burger.getPrix(),
                    burger.isDisponible() ? "Oui" : "Non"));
        }
    }

    private void afficherBurgersDisponibles() {
        System.out.println("\n=== BURGERS DISPONIBLES ===");
        List<Burger> burgers = burgerService.getAvailableBurgers();

        if (burgers.isEmpty()) {
            System.out.println("Aucun burger disponible.");
            return;
        }

        for (Burger burger : burgers) {
            System.out.println("\n" + burger.getNom() + " - " + burger.getPrix() + " FCFA");
            if (burger.getDescription() != null) {
                System.out.println("  " + burger.getDescription());
            }
        }
    }

    private void modifierBurger() {
        afficherBurgers();
        System.out.println("\n=== MODIFICATION D'UN BURGER ===");
        String idStr = inputHelper.readString("ID du burger à modifier: ");
        UUID id = UUID.fromString(idStr);

        Burger burger = burgerService.getBurgerById(id);
        System.out.println("Burger actuel: " + burger);

        String nom = inputHelper.readString("Nouveau nom [" + burger.getNom() + "]: ");
        String description = inputHelper.readString("Nouvelle description [" + burger.getDescription() + "]: ");
        BigDecimal prix = inputHelper.readBigDecimal("Nouveau prix [" + burger.getPrix() + "]: ");
        String imageUrl = inputHelper.readString("Nouvelle URL image [" + burger.getImageUrl() + "]: ");
        boolean disponible = inputHelper.readBoolean("Disponible [" + (burger.isDisponible() ? "Oui" : "Non") + "]");

        burgerService.updateBurger(id,
                nom.isEmpty() ? burger.getNom() : nom,
                description.isEmpty() ? burger.getDescription() : description,
                prix,
                imageUrl.isEmpty() ? burger.getImageUrl() : imageUrl,
                disponible);

        System.out.println("\n✓ Burger modifié avec succès!");
    }

    private void supprimerBurger() {
        afficherBurgers();
        System.out.println("\n=== SUPPRESSION D'UN BURGER ===");
        String idStr = inputHelper.readString("ID du burger à supprimer: ");
        UUID id = UUID.fromString(idStr);

        Burger burger = burgerService.getBurgerById(id);
        System.out.println("Burger à supprimer: " + burger);

        if (inputHelper.readBoolean("Êtes-vous sûr de vouloir supprimer ce burger?")) {
            burgerService.deleteBurger(id);
            System.out.println("\n✓ Burger supprimé avec succès!");
        } else {
            System.out.println("\nSuppression annulée.");
        }
    }

    private void toggleDisponibiliteBurger() {
        afficherBurgers();
        System.out.println("\n=== CHANGER LA DISPONIBILITÉ ===");
        String idStr = inputHelper.readString("ID du burger: ");
        UUID id = UUID.fromString(idStr);

        burgerService.toggleAvailability(id);
        System.out.println("\n✓ Disponibilité modifiée avec succès!");
    }

    private void menuGestionComplements() {
        while (true) {
            System.out.println("\n╔════════════════════════════════════════╗");
            System.out.println("║      GESTION DES COMPLÉMENTS           ║");
            System.out.println("╚════════════════════════════════════════╝");
            System.out.println("  1. Créer un complément");
            System.out.println("  2. Afficher tous les compléments");
            System.out.println("  3. Afficher compléments par type");
            System.out.println("  4. Modifier un complément");
            System.out.println("  5. Supprimer un complément");
            System.out.println("  0. Retour au menu principal");
            System.out.println("─────────────────────────────────────────");

            int choix = inputHelper.readInt("Votre choix: ");

            try {
                switch (choix) {
                    case 1:
                        creerComplement();
                        break;
                    case 2:
                        afficherComplements();
                        break;
                    case 3:
                        afficherComplementsParType();
                        break;
                    case 4:
                        modifierComplement();
                        break;
                    case 5:
                        supprimerComplement();
                        break;
                    case 0:
                        return;
                    default:
                        System.out.println("\nChoix invalide.");
                }
            } catch (Exception e) {
                System.err.println("\nErreur: " + e.getMessage());
            }
            inputHelper.pause();
        }
    }

    private void creerComplement() {
        System.out.println("\n=== CRÉATION D'UN COMPLÉMENT ===");
        String nom = inputHelper.readString("Nom du complément: ");

        System.out.println("Type de complément:");
        System.out.println("  1. BOISSON");
        System.out.println("  2. FRITE");
        int typeChoix = inputHelper.readInt("Votre choix: ");
        TypeComplement type = typeChoix == 1 ? TypeComplement.BOISSON : TypeComplement.FRITE;

        BigDecimal prix = inputHelper.readBigDecimal("Prix (FCFA): ");

        Complement complement = complementService.createComplement(nom, type, prix);
        System.out.println("\n✓ Complément créé avec succès!");
        System.out.println(complement);
    }

    private void afficherComplements() {
        System.out.println("\n=== LISTE DES COMPLÉMENTS ===");
        List<Complement> complements = complementService.getAllComplements();

        if (complements.isEmpty()) {
            System.out.println("Aucun complément trouvé.");
            return;
        }

        System.out.println(String.format("%-36s | %-20s | %-10s | %10s | %11s",
                "ID", "Nom", "Type", "Prix", "Disponible"));
        System.out.println("─".repeat(95));

        for (Complement complement : complements) {
            System.out.println(String.format("%-36s | %-20s | %-10s | %10.2f | %11s",
                    complement.getId(),
                    complement.getNom(),
                    complement.getType(),
                    complement.getPrix(),
                    complement.isDisponible() ? "Oui" : "Non"));
        }
    }

    private void afficherComplementsParType() {
        System.out.println("\n=== FILTRER PAR TYPE ===");
        System.out.println("  1. BOISSON");
        System.out.println("  2. FRITE");
        int typeChoix = inputHelper.readInt("Votre choix: ");
        TypeComplement type = typeChoix == 1 ? TypeComplement.BOISSON : TypeComplement.FRITE;

        List<Complement> complements = complementService.getComplementsByType(type);

        if (complements.isEmpty()) {
            System.out.println("Aucun complément trouvé pour ce type.");
            return;
        }

        System.out.println("\n=== COMPLÉMENTS - " + type + " ===");
        for (Complement complement : complements) {
            System.out.println(complement.getNom() + " - " + complement.getPrix() + " FCFA" +
                    (complement.isDisponible() ? "" : " (Non disponible)"));
        }
    }

    private void modifierComplement() {
        afficherComplements();
        System.out.println("\n=== MODIFICATION D'UN COMPLÉMENT ===");
        String idStr = inputHelper.readString("ID du complément à modifier: ");
        UUID id = UUID.fromString(idStr);

        Complement complement = complementService.getComplementById(id);
        System.out.println("Complément actuel: " + complement);

        String nom = inputHelper.readString("Nouveau nom [" + complement.getNom() + "]: ");

        System.out.println("Nouveau type (1=BOISSON, 2=FRITE) [" + complement.getType() + "]: ");
        int typeChoix = inputHelper.readInt("Votre choix: ");
        TypeComplement type = typeChoix == 1 ? TypeComplement.BOISSON : TypeComplement.FRITE;

        BigDecimal prix = inputHelper.readBigDecimal("Nouveau prix [" + complement.getPrix() + "]: ");
        boolean disponible = inputHelper.readBoolean("Disponible [" + (complement.isDisponible() ? "Oui" : "Non") + "]");

        complementService.updateComplement(id,
                nom.isEmpty() ? complement.getNom() : nom,
                type,
                prix,
                disponible);

        System.out.println("\n✓ Complément modifié avec succès!");
    }

    private void supprimerComplement() {
        afficherComplements();
        System.out.println("\n=== SUPPRESSION D'UN COMPLÉMENT ===");
        String idStr = inputHelper.readString("ID du complément à supprimer: ");
        UUID id = UUID.fromString(idStr);

        Complement complement = complementService.getComplementById(id);
        System.out.println("Complément à supprimer: " + complement);

        if (inputHelper.readBoolean("Êtes-vous sûr de vouloir supprimer ce complément?")) {
            complementService.deleteComplement(id);
            System.out.println("\n✓ Complément supprimé avec succès!");
        } else {
            System.out.println("\nSuppression annulée.");
        }
    }

    private void menuGestionMenus() {
        while (true) {
            System.out.println("\n╔════════════════════════════════════════╗");
            System.out.println("║         GESTION DES MENUS              ║");
            System.out.println("╚════════════════════════════════════════╝");
            System.out.println("  1. Créer un menu");
            System.out.println("  2. Afficher tous les menus");
            System.out.println("  3. Afficher menus avec détails");
            System.out.println("  4. Modifier un menu");
            System.out.println("  5. Supprimer un menu");
            System.out.println("  0. Retour au menu principal");
            System.out.println("─────────────────────────────────────────");

            int choix = inputHelper.readInt("Votre choix: ");

            try {
                switch (choix) {
                    case 1:
                        creerMenu();
                        break;
                    case 2:
                        afficherMenus();
                        break;
                    case 3:
                        afficherMenusAvecDetails();
                        break;
                    case 4:
                        modifierMenu();
                        break;
                    case 5:
                        supprimerMenu();
                        break;
                    case 0:
                        return;
                    default:
                        System.out.println("\nChoix invalide.");
                }
            } catch (Exception e) {
                System.err.println("\nErreur: " + e.getMessage());
            }
            inputHelper.pause();
        }
    }

    private void creerMenu() {
        System.out.println("\n=== CRÉATION D'UN MENU ===");

        afficherBurgersDisponibles();
        String burgerIdStr = inputHelper.readString("\nID du burger: ");
        UUID burgerId = UUID.fromString(burgerIdStr);

        System.out.println("\n=== BOISSONS DISPONIBLES ===");
        List<Complement> boissons = complementService.getComplementsByType(TypeComplement.BOISSON);
        for (Complement b : boissons) {
            System.out.println(b.getId() + " - " + b.getNom());
        }
        String boissonIdStr = inputHelper.readString("\nID de la boisson: ");
        UUID boissonId = UUID.fromString(boissonIdStr);

        System.out.println("\n=== FRITES DISPONIBLES ===");
        List<Complement> frites = complementService.getComplementsByType(TypeComplement.FRITE);
        for (Complement f : frites) {
            System.out.println(f.getId() + " - " + f.getNom());
        }
        String friteIdStr = inputHelper.readString("\nID des frites: ");
        UUID friteId = UUID.fromString(friteIdStr);

        String nom = inputHelper.readString("\nNom du menu: ");
        String description = inputHelper.readString("Description: ");
        BigDecimal prixTotal = inputHelper.readBigDecimal("Prix total (FCFA): ");

        Menu menu = menuService.createMenu(nom, description, burgerId, boissonId, friteId, prixTotal);
        System.out.println("\n✓ Menu créé avec succès!");
        System.out.println(menu);
    }

    private void afficherMenus() {
        System.out.println("\n=== LISTE DES MENUS ===");
        List<Menu> menus = menuService.getAllMenus();

        if (menus.isEmpty()) {
            System.out.println("Aucun menu trouvé.");
            return;
        }

        System.out.println(String.format("%-36s | %-25s | %12s | %11s",
                "ID", "Nom", "Prix Total", "Disponible"));
        System.out.println("─".repeat(90));

        for (Menu menu : menus) {
            System.out.println(String.format("%-36s | %-25s | %12.2f | %11s",
                    menu.getId(),
                    menu.getNom(),
                    menu.getPrixTotal(),
                    menu.isDisponible() ? "Oui" : "Non"));
        }
    }

    private void afficherMenusAvecDetails() {
        System.out.println("\n=== MENUS AVEC DÉTAILS ===");
        List<Menu> menus = menuService.getAllMenusWithDetails();

        if (menus.isEmpty()) {
            System.out.println("Aucun menu trouvé.");
            return;
        }

        for (Menu menu : menus) {
            System.out.println("\n╔════════════════════════════════════════╗");
            System.out.println("  " + menu.getNom() + " - " + menu.getPrixTotal() + " FCFA");
            System.out.println("╚════════════════════════════════════════╝");
            if (menu.getDescription() != null) {
                System.out.println("  " + menu.getDescription());
            }
            if (menu.getBurger() != null) {
                System.out.println("  • Burger: " + menu.getBurger().getNom() + " (" + menu.getBurger().getPrix() + " FCFA)");
            }
            if (menu.getBoisson() != null) {
                System.out.println("  • Boisson: " + menu.getBoisson().getNom() + " (" + menu.getBoisson().getPrix() + " FCFA)");
            }
            if (menu.getFrite() != null) {
                System.out.println("  • Frites: " + menu.getFrite().getNom() + " (" + menu.getFrite().getPrix() + " FCFA)");
            }
            System.out.println("  Disponible: " + (menu.isDisponible() ? "Oui" : "Non"));
        }
    }

    private void modifierMenu() {
        afficherMenus();
        System.out.println("\n=== MODIFICATION D'UN MENU ===");
        String idStr = inputHelper.readString("ID du menu à modifier: ");
        UUID id = UUID.fromString(idStr);

        Menu menu = menuService.getMenuById(id);
        System.out.println("Menu actuel: " + menu);

        String nom = inputHelper.readString("Nouveau nom [" + menu.getNom() + "]: ");
        String description = inputHelper.readString("Nouvelle description [" + menu.getDescription() + "]: ");

        String burgerIdStr = inputHelper.readString("Nouvel ID burger [" + menu.getBurgerId() + "]: ");
        UUID burgerId = burgerIdStr.isEmpty() ? menu.getBurgerId() : UUID.fromString(burgerIdStr);

        String boissonIdStr = inputHelper.readString("Nouvel ID boisson [" + menu.getBoissonId() + "]: ");
        UUID boissonId = boissonIdStr.isEmpty() ? menu.getBoissonId() : UUID.fromString(boissonIdStr);

        String friteIdStr = inputHelper.readString("Nouvel ID frites [" + menu.getFriteId() + "]: ");
        UUID friteId = friteIdStr.isEmpty() ? menu.getFriteId() : UUID.fromString(friteIdStr);

        BigDecimal prixTotal = inputHelper.readBigDecimal("Nouveau prix total [" + menu.getPrixTotal() + "]: ");
        boolean disponible = inputHelper.readBoolean("Disponible [" + (menu.isDisponible() ? "Oui" : "Non") + "]");

        menuService.updateMenu(id,
                nom.isEmpty() ? menu.getNom() : nom,
                description.isEmpty() ? menu.getDescription() : description,
                burgerId, boissonId, friteId, prixTotal, disponible);

        System.out.println("\n✓ Menu modifié avec succès!");
    }

    private void supprimerMenu() {
        afficherMenus();
        System.out.println("\n=== SUPPRESSION D'UN MENU ===");
        String idStr = inputHelper.readString("ID du menu à supprimer: ");
        UUID id = UUID.fromString(idStr);

        Menu menu = menuService.getMenuById(id);
        System.out.println("Menu à supprimer: " + menu);

        if (inputHelper.readBoolean("Êtes-vous sûr de vouloir supprimer ce menu?")) {
            menuService.deleteMenu(id);
            System.out.println("\n✓ Menu supprimé avec succès!");
        } else {
            System.out.println("\nSuppression annulée.");
        }
    }
}
